<?php

namespace App\Services;

use App\Models\User;
use App\Models\VendorApplication;
use App\Models\VendorApplicationFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Http\File;
use Illuminate\Contracts\Encryption\DecryptException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VendorFileService
{
    public const DISK = 'vendor_documents';

    public function store(
        VendorApplication $application,
        UploadedFile $file,
        string $directory,
        string $ownerType,
        string $fieldName,
        ?int $ownerId = null,
        ?User $uploader = null
    ): string {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . ($extension ? '.' . $extension : '');
        $path = Storage::disk(self::DISK)->putFileAs($directory, $file, $filename);

        VendorApplicationFile::where('application_id', $application->id)
            ->where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->where('field_name', $fieldName)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        VendorApplicationFile::create([
            'application_id' => $application->id,
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'field_name' => $fieldName,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'is_current' => true,
            'uploaded_by' => optional($uploader)->id,
        ]);

        return $path;
    }

    public function storeFromTempPath(
        VendorApplication $application,
        string $tempPath,
        string $directory,
        string $ownerType,
        string $fieldName,
        ?int $ownerId = null,
        ?User $uploader = null
    ): ?string {
        if (!Storage::disk('public')->exists($tempPath)) {
            return null;
        }

        // Wrap as UploadedFile so we can reuse store() logic.
        $absolutePath = Storage::disk('public')->path($tempPath);
        $tempName = basename($tempPath);
        $uploaded = new UploadedFile($absolutePath, $tempName, null, null, true);

        $path = $this->store($application, $uploaded, $directory, $ownerType, $fieldName, $ownerId, $uploader);

        // Clean up the temp file (best effort, no error if already gone).
        Storage::disk('public')->delete($tempPath);

        return $path;
    }

    public function url(VendorApplication $application, ?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return route('registrasi.files.show', [
            'application' => $application,
            'file' => Crypt::encryptString($path),
        ]);
    }

    public function deactivate(
        VendorApplication $application,
        string $ownerType,
        string $fieldName,
        ?int $ownerId = null
    ): void {
        VendorApplicationFile::where('application_id', $application->id)
            ->where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->where('field_name', $fieldName)
            ->where('is_current', true)
            ->update(['is_current' => false]);
    }

    public function response(VendorApplication $application, string $encryptedPath): StreamedResponse
    {
        try {
            $path = Crypt::decryptString($encryptedPath);
        } catch (DecryptException $e) {
            abort(404);
        }
        $disk = Storage::disk(self::DISK)->exists($path) ? self::DISK : 'public';

        abort_unless(Storage::disk($disk)->exists($path), 404);

        $version = VendorApplicationFile::where('application_id', $application->id)
            ->where('file_path', $path)
            ->latest()
            ->first();

        return Storage::disk($disk)->response(
            $path,
            optional($version)->original_name ?: basename($path),
            ['Content-Disposition' => 'inline']
        );
    }

    public function history(VendorApplication $application): Collection
    {
        return $application->fileVersions()
            ->with('uploader')
            ->latest()
            ->get()
            ->map(function (VendorApplicationFile $file) use ($application) {
                return [
                    'field' => $file->field_name,
                    'name' => $file->original_name,
                    'size' => $file->file_size,
                    'is_current' => $file->is_current,
                    'uploaded_at' => $file->created_at,
                    'uploaded_by' => optional($file->uploader)->name,
                    'url' => $this->url($application, $file->file_path),
                ];
            });
    }
}
