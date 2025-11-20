<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('complaints', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // صاحب الشكوى
        $table->string('title');
        $table->text('description');
        $table->string('status')->default('new'); // new, in_progress, completed, rejected
        $table->string('department')->nullable(); // الجهة المسؤولة
        $table->string('reference_number')->unique();
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
