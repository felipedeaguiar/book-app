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
        Schema::create('my_books', function (Blueprint $table) {
            $table->id();
            $table->integer('current_page')->default(0);
            $table->string('status')->default('in_progress');
            $table->string('notes')->nullable();


            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('book_id');


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_books');
    }
};
