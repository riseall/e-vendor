<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAppSpecPengujiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_app_spec_pengujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('vendor_applications')->onDelete('cascade');

            // 1. Layanan Jasa
            $table->json('l1_services')->nullable(); // Array checkbox (klinik, mikro, fisika, sertifikasi, kalibrasi)
            $table->string('l1_kalibrasi_scope')->nullable(); // Input teks jika kalibrasi dipilih

            // 2. Sertifikasi yang Dimiliki
            $table->json('l2_selected_certs')->nullable(); // Array checkbox (kan, cukb, iso17025, glp, bapeten)

            // Kolom detail per sertifikat (Flat Columns agar file gampang di-handle)
            $table->string('l2_kan_no')->nullable();
            $table->date('l2_kan_date')->nullable();
            $table->string('l2_kan_file')->nullable();

            $table->string('l2_cukb_no')->nullable();
            $table->date('l2_cukb_date')->nullable();
            $table->string('l2_cukb_file')->nullable();

            $table->string('l2_iso17025_no')->nullable();
            $table->date('l2_iso17025_date')->nullable();
            $table->string('l2_iso17025_file')->nullable();

            $table->string('l2_glp_no')->nullable();
            $table->date('l2_glp_date')->nullable();
            $table->string('l2_glp_file')->nullable();

            $table->string('l2_bapeten_no')->nullable();
            $table->date('l2_bapeten_date')->nullable();
            $table->string('l2_bapeten_file')->nullable();

            // 3. Authorized Agent
            $table->enum('l3_is_agent', ['yes', 'no'])->default('no');
            $table->string('l3_principal_name')->nullable();

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
        Schema::dropIfExists('vendor_app_spec_pengujians');
    }
}
