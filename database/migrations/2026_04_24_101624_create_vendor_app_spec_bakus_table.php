<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vendor_app_spec_baku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('vendor_applications')->onDelete('cascade');

            // Baris 1: Produsen & Agen Tunggal
            $table->enum('q1_is_manufacturer', ['yes', 'no'])->default('no');
            $table->string('q1_manufacturer_name')->nullable();

            $table->enum('q2_is_sole_agent', ['yes', 'no'])->default('no');
            $table->string('q2_auth_letter')->nullable(); // Menyimpan path file Surat Penunjukkan

            // Baris 2: Transportasi & Gudang
            $table->enum('q3_transportation', ['owned', '3pl'])->nullable();
            $table->string('q3_3pl_name')->nullable();

            $table->enum('q4_has_warehouse', ['yes', 'no'])->default('no');
            $table->text('q4_warehouse_address')->nullable();
            $table->string('q4_warehouse_condition')->nullable(); // cold, ac, ambient, grey

            // Baris 3: Sertifikat CDOB
            $table->string('q5_num')->nullable();
            $table->date('q5_date')->nullable();

            // Khusus Pemasok Lokal: SIPA APJ
            $table->string('q6_name')->nullable();
            $table->string('q6_num')->nullable();
            $table->date('q6_date')->nullable();

            // Khusus Pemasok Bahan Kemas: Peralatan (JSON Repeater)
            $table->json('q7_equipments')->nullable();

            // Baris 5: Material Impor
            $table->enum('q8_is_import', ['yes', 'no'])->default('no');
            $table->string('q8_country_name')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_app_spec_baku');
    }
};
