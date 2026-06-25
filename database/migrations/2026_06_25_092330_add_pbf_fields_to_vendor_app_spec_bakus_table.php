<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPbfFieldsToVendorAppSpecBakusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_app_spec_baku', function (Blueprint $table) {
            $table->string('pbf_document')->nullable()->after('q5_valid_until');
            $table->string('pbf_num')->nullable()->after('pbf_document');
            $table->date('pbf_issue_date')->nullable()->after('pbf_num');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_app_spec_baku', function (Blueprint $table) {
            $table->dropColumn(['pbf_document', 'pbf_num', 'pbf_issue_date']);
        });
    }
}
