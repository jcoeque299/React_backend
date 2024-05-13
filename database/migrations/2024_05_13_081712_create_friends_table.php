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
        Schema::create('friends', function (Blueprint $table) {
            $table->unsignedBigInteger('parentId');
            $table->foreign('parentId')->references('id')->on('users');
            $table->unsignedBigInteger('childId');
            $table->foreign('childId')->references('id')->on('users');
            $table->timestamps();
            $table->boolean('accepted')->default(false);
            $table->primary(['parentId', 'childId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
};
