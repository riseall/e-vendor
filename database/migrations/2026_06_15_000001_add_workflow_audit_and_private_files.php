<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkflowAuditAndPrivateFiles extends Migration
{
    public function up()
    {

        Schema::create('vendor_application_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('vendor_applications')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['application_id', 'created_at']);
        });

        Schema::create('vendor_application_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('vendor_applications')->onDelete('cascade');
            $table->string('owner_type', 50);
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('field_name');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_current')->default(true);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['application_id', 'owner_type', 'owner_id', 'field_name'], 'vendor_files_owner_idx');
            $table->index(['application_id', 'is_current']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_application_files');
        Schema::dropIfExists('vendor_application_activity_logs');
    }
}
