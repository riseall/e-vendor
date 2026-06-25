<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestructureQ6FieldsToVendorAppSpecBaku extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_app_spec_baku', function (Blueprint $table) {
            $table->string('q6_document')->nullable()->after('q6_num');
            $table->date('q6_issue_date')->nullable()->after('q6_document');
            $table->date('q6_valid_until')->nullable()->after('q6_issue_date');
            $table->dropColumn('q6_date');
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
            $table->date('q6_date')->nullable()->after('q6_num');
            $table->dropColumn(['q6_document', 'q6_issue_date', 'q6_valid_until']);
        });
    }
}
