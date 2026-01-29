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
        Schema::create('advice', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->dateTime("dateTime")->nullable();
            $table->mediumText("description")->nullable();
            $table->mediumText("category")->nullable();
            $table->string("duration")->nullable();
            $table->unsignedBigInteger("application_id")->nullable();
            $table->unsignedInteger("excpert_id")->nullable();
            $table->tinyInteger("status")->nullable();
            $table->string("contactInfo")->nullable();
            $table->text("result")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advice');
    }
};
