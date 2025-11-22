<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up() {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userID');
            $table->foreign('userID')->references('id')->on('users')->onDelete('cascade');
            $table->string('type');
            $table->enum('department', ['Interior','Health','Education','Justice','AntiCorruption','Communications','Labor','ConsumerProtection']);
            $table->string('location');
            $table->text('description');
            $table->enum('status',['new', 'inProgress', 'completed', 'rejected'])->default('new');
            $table->timestamps();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
