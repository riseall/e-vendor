<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQadSupplierFieldsToVendorApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_applications', 'qad_supplier_code')) {
                $table->string('qad_supplier_code')->nullable()->after('user_id')->index();
            }
            if (!Schema::hasColumn('vendor_applications', 'supplier_type')) {
                $table->string('supplier_type')->nullable()->after('qad_supplier_code');
            }
            if (!Schema::hasColumn('vendor_applications', 'currency')) {
                $table->string('currency', 10)->nullable()->default('IDR')->after('supplier_type');
            }
        });

        Schema::table('vendor_application_generals', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_application_generals', 'qad_supplier_code')) {
                $table->string('qad_supplier_code')->nullable()->after('nama_perusahaan')->index();
            }
            if (!Schema::hasColumn('vendor_application_generals', 'supplier_type')) {
                $table->string('supplier_type')->nullable()->after('qad_supplier_code');
            }
            if (!Schema::hasColumn('vendor_application_generals', 'currency')) {
                $table->string('currency', 10)->nullable()->default('IDR')->after('supplier_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_applications', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('vendor_applications', 'currency')) $cols[] = 'currency';
            if (Schema::hasColumn('vendor_applications', 'supplier_type')) $cols[] = 'supplier_type';
            if (Schema::hasColumn('vendor_applications', 'qad_supplier_code')) $cols[] = 'qad_supplier_code';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('vendor_application_generals', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('vendor_application_generals', 'currency')) $cols[] = 'currency';
            if (Schema::hasColumn('vendor_application_generals', 'supplier_type')) $cols[] = 'supplier_type';
            if (Schema::hasColumn('vendor_application_generals', 'qad_supplier_code')) $cols[] = 'qad_supplier_code';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
}
