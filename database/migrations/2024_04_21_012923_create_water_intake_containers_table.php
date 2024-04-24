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
        Schema::create('water_intake_containers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->integer('size');
            $table->enum('icon',[
                'glass-water',
                'bottle-water',
                'wine-glass',
                'whiskey-glass',
                'martini-glass',
                'mug-hot',
                'beer-mug-empty',
                'wine-bottle',
                'bottle-droplet'
            ])->default('glass-water');
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_intake_containers');
    }
};
