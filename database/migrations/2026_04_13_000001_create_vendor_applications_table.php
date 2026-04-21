<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorApplicationsTable extends Migration
{
    public function up()
    {
        // Tabel utama permohonan
        Schema::create('vendor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['draft', 'submitted', 'need_revision', 'verified', 'approved', 'rejected'])
                ->default('draft');
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->text('admin_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // Tabel kategori yang dipilih vendor
        Schema::create('vendor_application_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->constrained('vendor_applications')
                ->onDelete('cascade');
            $table->unsignedTinyInteger('category_id'); // 1-8
            $table->timestamps();

            $table->unique(['application_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_application_categories');
        Schema::dropIfExists('vendor_applications');
    }
}
