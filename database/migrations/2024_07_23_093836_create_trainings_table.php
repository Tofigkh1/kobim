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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("date")->nullable();
            $table->string("hour")->nullable();
            $table->string("duration");
            $table->tinyInteger("serviceType");
            $table->unsignedBigInteger("executive_id")->nullable();
            $table->string("scope")->comment("
                        '0' => 'Korporativ şirkət əməkdaşları',
                        '1' => 'Sahibkarlıq subyektləri',
                        '2' => 'Sahibkar olmaq istəyən şəxslər'
            ")->nullable();
            $table->text("note")->nullable();
            $table->tinyInteger("certificate")->nullable();
            $table->text("haveSkills")->nullable();
            $table->string("link")->nullable();
            $table->string("address")->nullable();
            $table->integer("status")->nullable();
            $table->unsignedBigInteger("user_id")->nullable();
            $table->unsignedBigInteger("sme_id")->nullable();
            $table->string("photo")->nullable();
            $table->string("includesBusiness")->nullable();
            $table->mediumText('statusNote')->nullable();
            $table->mediumText("orderNote")->nullable();
            $table->longText("sessions")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
