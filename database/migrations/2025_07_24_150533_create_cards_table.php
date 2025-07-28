<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->integer('ISS_ID');
            $table->integer('CD_ID');
            $table->integer('CRD_SNR');
            $table->string('DC_CODE', 10)->nullable();
            $table->integer('CTY_ID')->nullable();
            $table->integer('CRD_CHKDG')->nullable();
            $table->integer('CRD_INTSNR')->nullable();
            $table->integer('CRD_CERTIFICATE')->nullable();
            $table->string('CRD_STATUS', 1)->nullable();
            $table->dateTime('CRD_REGDATE')->nullable();
            $table->string('CRD_REGUSER', 9)->nullable();
            $table->integer('CRD_SECONDCOPYTAX')->nullable();
            $table->tinyInteger('is_personalized')->default(0);
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->primary(['ISS_ID', 'CD_ID', 'CRD_SNR'], 'PK_CARDS');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
