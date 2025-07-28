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
        Schema::create('document_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->nullable();
            $table->tinyInteger('status')->default(1);
        });

        Schema::create('gender', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 50)->nullable();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('name', 50);
            $table->string('last_name', 50);
            $table->tinyInteger('gender_id')->unsigned();
            $table->integer('document_type_id')->unsigned();
            $table->unsignedBigInteger('document_number');
            $table->string('email', 100)->unique();
            $table->unsignedBigInteger('telephone');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('address')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->date('birth_date')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('gender_id')->references('id')->on('gender');
            $table->foreign('document_type_id')->references('id')->on('document_type');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('document_type');
        Schema::dropIfExists('gender');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
