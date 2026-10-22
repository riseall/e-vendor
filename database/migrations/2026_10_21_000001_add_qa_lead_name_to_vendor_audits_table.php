<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQaLeadNameToVendorAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_audits', function (Blueprint $table) {
            $table->string('qa_lead_name', 120)->nullable()->after('qa_lead_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_audits', function (Blueprint $table) {
            $table->dropColumn('qa_lead_name');
        });
    }
}
