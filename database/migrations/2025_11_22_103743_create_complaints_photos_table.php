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
        Schema::create('complaints_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complaintID');
            $table->foreign('complaintID')->references('id')->on('complaints')->onDelete('cascade');
            $table->string('photo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints_photos');
    }
};
