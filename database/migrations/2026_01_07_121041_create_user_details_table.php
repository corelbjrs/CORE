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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->string('idno')->unique();
            $table->string('firstname',50);
            $table->string('lastname',50);
            $table->string('middlename', 50)->nullable();
            $table->string('ext', 10)->nullable();
            $table->date('date_of_birth');
            $table->string('province',50)->nullable();
            $table->string('town',50)->nullable();
            $table->string('brgy',50)->nullable();
            $table->string('address',50)->nullable();
            $table->decimal('latitude',10,7)->nullable();
            $table->decimal('longitude',11,7)->nullable();
            $table->string('tel_no',15)->nullable();
            $table->string('mobile_no',15)->nullable();
            $table->string('sex',1)->nullable();
            $table->string('gender',15)->nullable();
            $table->string('civil_status',15)->nullable();
            $table->string('educational_level',60)->nullable();
            //$table->bigInteger('course_id',20)->nullable();
            $table->foreignId('course_id')->nullable()->constrained('courses');
            $table->text('about_me')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
