<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNegaraGmpBseToVendorApplicationProductsTable extends Migration
{
    public function up()
    {
        Schema::table('vendor_application_products', function (Blueprint $table) {
            $table->string('negara')->nullable()->after('manufaktur');
            $table->string('gmp_file_path')->nullable()->after('file_surat_path');
            $table->string('has_bse_tse', 10)->default('no')->after('halal_file_path');
            $table->string('bse_tse_file_path')->nullable()->after('has_bse_tse');
        });
    }

    public function down()
    {
        Schema::table('vendor_application_products', function (Blueprint $table) {
            $table->dropColumn([
                'negara',
                'gmp_file_path',
                'has_bse_tse',
                'bse_tse_file_path',
            ]);
        });
    }
}
