<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueOnDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_application_documents', function (Blueprint $table) {
            // MySQL menolak drop index yang jadi satu-satunya index
            // yang dipakai oleh foreign key. Lepas FK dulu, ganti
            // unique jadi index biasa, lalu pasang FK lagi.
            $table->dropForeign(['application_id']);
            $table->dropUnique(['application_id', 'field_name']);
            $table->index(['application_id', 'field_name'], 'vad_app_field_idx');
            $table->foreign('application_id')
                ->references('id')->on('vendor_applications')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_application_documents', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropIndex('vad_app_field_idx');
            $table->unique(['application_id', 'field_name']);
            $table->foreign('application_id')
                ->references('id')->on('vendor_applications')
                ->onDelete('cascade');
        });
    }
}
