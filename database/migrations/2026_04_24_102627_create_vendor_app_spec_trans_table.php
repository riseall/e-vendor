<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAppSpecTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_app_spec_trans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('vendor_applications')->onDelete('cascade');

            // Baris 1: Komitmen K3 & Asuransi
            $table->enum('t1_k3_commitment', ['yes', 'no'])->default('no');
            $table->string('t1_safety_file')->nullable(); // Path bukti komitmen K3
            $table->enum('t4_is_insured', ['yes', 'no'])->default('no');
            $table->string('t4_insurance_pct')->nullable(); // Persentase asuransi

            // Baris 2: Data Logger & Armada
            $table->enum('t2_truck_type', ['ac', 'non_ac'])->nullable();
            $table->enum('t3_has_logger', ['yes', 'no'])->default('no');

            // Baris 3: Armada Sendiri & Pihak Ketiga
            $table->enum('t5_own_fleet_outer_island', ['yes', 'no'])->default('no');
            $table->string('t6_3pl_darat')->nullable();
            $table->string('t6_3pl_laut')->nullable();
            $table->string('t6_3pl_udara')->nullable();

            // Baris 4: Asosiasi
            $table->string('t7_association')->nullable();

            // KHUSUS FORWARDER DAN PPJK
            $table->string('t8_customs_expert')->nullable();
            $table->string('t8_expert_cert')->nullable(); // Path sertifikat kepabeanan
            $table->enum('t9_has_intl_affiliate', ['yes', 'no'])->default('no');
            $table->string('t9_countries')->nullable();
            $table->text('t10_other_services')->nullable(); // Layanan lain

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
        Schema::dropIfExists('vendor_app_spec_trans');
    }
}
