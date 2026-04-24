<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAppSpecFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_app_spec_facility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('vendor_applications')->onDelete('cascade');

            // Asosiasi & BPJS
            $table->string('f1_association')->nullable();
            $table->enum('f2_bpjs', ['yes', 'no'])->default('no');

            // Ijin Permenaker
            $table->string('f3_permenaker_ijin')->nullable();

            // Sertifikat Keterampilan Khusus (Repeater)
            $table->json('f4_certs')->nullable();

            // Khusus Katering / Boga
            $table->enum('f5_hygiene_guarantee', ['yes', 'no'])->default('no');

            $table->enum('f6_sanitation_cert', ['yes', 'no'])->default('no');
            $table->string('f6_file')->nullable(); // Path Sertifikat Sanitasi

            $table->enum('f7_kitchen_facility', ['sendiri', 'subkontrak'])->nullable();
            $table->enum('f8_transport_facility', ['sendiri', 'subkontrak'])->nullable();

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
        Schema::dropIfExists('vendor_app_spec_facilities');
    }
}
