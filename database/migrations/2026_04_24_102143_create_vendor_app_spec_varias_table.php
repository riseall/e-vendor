<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAppSpecVariasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_app_spec_varia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('vendor_applications')->onDelete('cascade');

            // SUB-SECTION: VARIA TEKNIK & REAGEN
            $table->enum('v1_is_sole_agent', ['yes', 'no'])->default('no');
            $table->string('v1_auth_letter')->nullable(); // Path file surat penunjukan

            $table->enum('v2_has_special_license', ['yes', 'no'])->default('no');
            $table->string('v2_license_file')->nullable(); // Path dokumen perijinan khusus (B3)

            // SUB-SECTION: GAS & SOLAR
            $table->enum('v3_iso_b3', ['yes', 'no'])->default('no');
            $table->enum('v4_driver_training', ['yes', 'no'])->default('no');
            $table->enum('v5_valid_license', ['yes', 'no'])->default('no');

            $table->enum('v6_kir', ['yes', 'no'])->default('no');
            $table->string('v6_kir_file')->nullable(); // Path surat KIR

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
        Schema::dropIfExists('vendor_app_spec_varias');
    }
}
