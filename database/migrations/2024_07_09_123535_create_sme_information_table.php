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
        Schema::create('sme_information', function (Blueprint $table) {
            $table->id();
            $table->string("smeName");
            $table->string("smeLocation")->nullable();
            $table->string("contactNumber")->nullable();
            $table->string("contactEmail")->nullable();
            $table->string("facebook")->nullable();
            $table->string("instagram")->nullable();
            $table->string("teamLeaderName")->nullable();
            $table->string("voen")->nullable();
            $table->unsignedInteger("teamLeader_id")->nullable();
            $table->unsignedInteger("user_id")->nullable();
            $table->string("executiveCompany")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sme_information');
    }
};
