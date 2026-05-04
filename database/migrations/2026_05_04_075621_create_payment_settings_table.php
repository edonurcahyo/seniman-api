// database/migrations/xxxx_create_payment_settings_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabang_id'); // per cabang
            $table->string('bank_bca')->nullable();
            $table->string('bank_mandiri')->nullable();
            $table->string('bank_bni')->nullable();
            $table->string('bank_bri')->nullable();
            $table->string('qr_code')->nullable(); // path file QR
            $table->timestamps();
            
            $table->foreign('cabang_id')->references('id_cabang')->on('cabang')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_settings');
    }
};