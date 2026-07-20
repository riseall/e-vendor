<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorApplicationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_application_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('vendor_applications')->onDelete('cascade');

            $table->string('erp_product_id');
            $table->string('product_name');
            $table->string('manufaktur')->nullable();
            $table->string('negara')->nullable();
            $table->string('rantai_pasok')->nullable();
            $table->string('file_surat_path')->nullable();
            $table->string('gmp_file_path')->nullable();

            $table->string('has_tkdn', 10)->default('no');
            $table->string('tkdn_value')->nullable();
            $table->string('tkdn_file_path')->nullable();
            $table->string('has_sni', 10)->default('no');
            $table->string('sni_number')->nullable();
            $table->string('sni_file_path')->nullable();
            $table->string('has_halal', 10)->default('no');
            $table->string('halal_number')->nullable();
            $table->string('halal_file_path')->nullable();
            $table->string('has_bse_tse', 10)->default('no');
            $table->string('bse_tse_file_path')->nullable();

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
        Schema::dropIfExists('vendor_application_products');
    }
}
