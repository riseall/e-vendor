<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorApplicationGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_application_generals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->unique()
                ->constrained('vendor_applications')
                ->onDelete('cascade');

            // A. Informasi Umum Perusahaan
            $table->string('nama_perusahaan')->nullable();
            $table->text('alamat_perusahaan')->nullable();
            $table->string('website')->nullable();
            $table->string('email_perusahaan')->nullable();
            $table->string('telepon_perusahaan')->nullable();
            $table->string('nib')->nullable();
            $table->string('npwp')->nullable();
            $table->string('pic_nama')->nullable();
            $table->string('pic_email')->nullable();
            $table->string('pic_telepon')->nullable();
            $table->enum('has_other_company', ['yes', 'no'])->default('no');
            $table->json('other_companies')->nullable();

            // B. Informasi Pembayaran
            $table->string('payment_term', 20)->nullable();
            $table->string('payment_term_other')->nullable();
            $table->string('pemegang_rekening')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('alamat_bank')->nullable();
            $table->string('swift_code')->nullable();

            // C. Komitmen Standar
            $table->json('iso_certificates')->nullable();
            $table->string('iso_other')->nullable();
            $table->enum('komitmen_kualitas', ['yes', 'no'])->nullable();
            $table->string('komitmen_kualitas_detail')->nullable();
            $table->enum('sertifikat_halal', ['yes', 'no'])->nullable();

            // D. Informasi Lain
            $table->string('lead_time')->nullable();
            $table->text('customer_list')->nullable();

            // E. Isian Pemasok Lokal
            $table->string('status_perusahaan', 20)->nullable();
            $table->string('status_pajak', 20)->nullable();
            $table->string('skala_perusahaan', 30)->nullable();
            $table->string('jenis_modal', 20)->nullable();
            $table->string('kbli')->nullable();
            // $table->enum('has_tkdn', ['yes', 'no'])->nullable();
            // $table->string('tkdn_detail')->nullable();
            // $table->enum('has_sni', ['yes', 'no'])->nullable();
            // $table->string('sni_detail')->nullable();

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
        Schema::dropIfExists('vendor_application_generals');
    }
}
