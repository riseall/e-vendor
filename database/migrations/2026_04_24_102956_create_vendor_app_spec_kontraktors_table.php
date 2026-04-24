<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAppSpecKontraktorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_app_spec_kontraktor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('vendor_applications')->onDelete('cascade');

            // Tenaga Profesional & K3
            $table->enum('k1_pro_staff', ['yes', 'no'])->default('no');
            $table->string('k1_cert_file')->nullable(); // Path sertifikat tenaga ahli
            $table->enum('k2_safety_commitment', ['yes', 'no'])->default('no');

            // BPJS & APD
            $table->enum('k3_bpjs', ['yes', 'no'])->default('no');
            $table->enum('k4_apd', ['yes', 'no'])->default('no');

            // Asosiasi
            $table->string('k5_association')->nullable();

            // Daftar Peralatan (Repeater)
            $table->json('k6_equipments')->nullable();

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
        Schema::dropIfExists('vendor_app_spec_kontraktors');
    }
}
