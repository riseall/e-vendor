<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAppSpecPelatihansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_app_spec_pelatihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('vendor_applications')->onDelete('cascade');

            // Umum
            $table->string('g1_association')->nullable();

            // Jasa Pelatihan
            $table->enum('g2_trainer_cert', ['yes', 'no'])->default('no');
            $table->string('g2_cert_source')->nullable();

            // Jasa Konsultan dan Notaris (Repeater)
            $table->json('g3_permits')->nullable();

            // Jasa Alih Daya Tenaga Kerja
            $table->string('g4_labor_permit')->nullable();
            $table->enum('g5_bpjs', ['yes', 'no'])->default('no');

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
        Schema::dropIfExists('vendor_app_spec_pelatihans');
    }
}
