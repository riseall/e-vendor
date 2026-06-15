<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCertificatePathsToVendorApplicationProductsTable extends Migration
{
    public function up()
    {
        Schema::table('vendor_application_products', function (Blueprint $table) {
            $table->string('tkdn_file_path')->nullable()->after('tkdn_value');
            $table->string('sni_file_path')->nullable()->after('sni_number');
            $table->string('halal_file_path')->nullable()->after('halal_number');
        });
    }

    public function down()
    {
        Schema::table('vendor_application_products', function (Blueprint $table) {
            $table->dropColumn([
                'tkdn_file_path',
                'sni_file_path',
                'halal_file_path',
            ]);
        });
    }
}
