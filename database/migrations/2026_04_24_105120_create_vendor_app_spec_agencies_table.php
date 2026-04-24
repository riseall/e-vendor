<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAppSpecAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_app_spec_agency', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('vendor_applications')->onDelete('cascade');

            // Keanggotaan Asosiasi
            $table->string('h1_association')->nullable();
            $table->string('h1_association_file')->nullable(); // Path bukti asosiasi

            // Kategori Spesialisasi
            $table->string('h2_specialization')->nullable();

            // Pengalaman Project
            $table->text('h3_project_experience')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_app_spec_agencies');
    }
}
