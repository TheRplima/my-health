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
            $table->string('email', 191)->unique();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->enum('gender',['M','F'])->nullable();
            $table->date('dob')->nullable();
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('daily_water_amount')->nullable();
            $table->enum('activity_level',[0.2,0.375,0.55,0.725,0.9])->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }
/**
*<select name="activity" class="dropauto">
*    	<option value="0.2">No sport/exercise</option>
*    	<option value="0.375">Light activity (sport 1-3 times per week)</option>
*    	<option value="0.55">Moderate activity (sport 3-5 times per week)</option>
*    	<option value="0.725">High activity (everyday exercise)</option>
*    	<option value="0.9">Extreme activity (professional athlete)</option>
*        </select>
     */



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
