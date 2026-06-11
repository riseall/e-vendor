<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorApplicationVerificationItemsTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_application_verification_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->constrained('vendor_applications')
                ->onDelete('cascade');
            $table->string('section_key', 50);
            $table->string('section_label');
            $table->string('item_key', 80);
            $table->string('item_label');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->json('revision_fields')->nullable();
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['application_id', 'item_key'], 'vendor_app_verification_unique_item');
            $table->index(['application_id', 'section_key'], 'vendor_app_verif_section_idx');
            $table->index(['application_id', 'status'], 'vendor_app_verif_status_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_application_verification_items');
    }
}
