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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger("sme_id")->nullable();
            $table->rememberToken();
            $table->string("fullName")->nullable();
            $table->integer("education")->nullable();
            $table->string("mainWorkplace")->nullable();
            $table->string("duty")->nullable();
            $table->string("contactEmail")->nullable();
            $table->string("contactNumber")->nullable();
            $table->string("city")->nullable();
            $table->string("fin")->nullable();
            $table->string("photo")->nullable();
            $table->string("address")->nullable();
            $table->string("date")->nullable();
            $table->string("serviceName")->nullable();
            $table->string("duration")->nullable();
            $table->tinyInteger("serviceForm")->nullable();
            $table->tinyInteger("status")->nullable();
            $table->string("serviceType")->nullable();
            $table->mediumText("note")->nullable();
            $table->unsignedBigInteger("user_id")->nullable();
            $table->string("trailer")->nullable();
            $table->tinyInteger('visibility')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
