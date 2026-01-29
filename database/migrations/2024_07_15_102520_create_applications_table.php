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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string("applicationNumber")->nullable();
            $table->string("applicationType")->nullable();
            $table->string("voen")->nullable();
            $table->string("fin")->nullable();
            $table->string("voenPersonName")->nullable();
            $table->string("voenMeyar")->nullable();
            $table->string("voenAddress")->nullable();
            $table->string("voenActivityName")->nullable();
            $table->json("voenContactInfo")->nullable();
            $table->string("voenFieldActivity")->nullable();
            $table->tinyInteger("employeeType");
            $table->string("employeeCount")->nullable();
            $table->string("fullName");
            $table->string("education");
            $table->string("fieldActivity")->nullable();
            $table->string("fieldWantAct")->nullable();
            $table->string("otherFieldActivity")->nullable();
            $table->tinyInteger("actualResidentialAddress")->nullable();
            $table->string("actualCity")->nullable();
            $table->string("actualAddress")->nullable();
            $table->mediumText("mainPlaceWork")->nullable();
            $table->mediumText("duty")->nullable();
            $table->string("contactNumber");
            $table->string("contactEmail")->nullable();
            $table->string("signatureNumber")->nullable();
            $table->string("city")->nullable();
            $table->string("address")->nullable();
            $table->mediumText("note")->nullable();
            $table->unsignedBigInteger("user_id")->nullable();
            $table->unsignedBigInteger('accepted_user_id')->nullable();
            $table->foreign('accepted_user_id')->references('id')->on('users')->onDelete('set null');
            $table->integer("status")->nullable();
            $table->mediumText("statusNote")->nullable();
            $table->tinyInteger("serviceType")->nullable()
            ->comment('
            1 =>"Təlim",
            2 =>"Məsləhət",
            3 =>"Şəbəkələşmə"');
            $table->unsignedBigInteger("sme_id")->nullable();
            $table->unsignedBigInteger("training_id")->nullable();           
            $table->unsignedBigInteger("advice_id")->nullable();           
            $table->unsignedBigInteger("networking_id")->nullable();  

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
