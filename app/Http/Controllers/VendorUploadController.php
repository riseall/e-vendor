<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VendorUploadPolicy;

class VendorUploadController extends Controller
{
    public function uploadTemp(Request $request)
    {
        // Validasi file tunggal menggunakan policy Anda
        $request->validate([
            'file' => 'required|' . VendorUploadPolicy::fileRule(),
            'field_name' => 'required|string'
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Simpan sementara di storage/app/public/temp_vendor
        $path = $file->storeAs('temp_vendor', $filename, 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
            'original_name' => $file->getClientOriginalName(),
            'field_name' => $request->field_name
        ]);
    }
}
