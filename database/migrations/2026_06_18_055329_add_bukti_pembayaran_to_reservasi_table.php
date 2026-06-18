<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable()->after('status');
            $table->string('kode_reservasi')->nullable()->after('id_reservasi');
        });
    }

    public function down()
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropColumn(['bukti_pembayaran', 'kode_reservasi']);
        });
    }
};