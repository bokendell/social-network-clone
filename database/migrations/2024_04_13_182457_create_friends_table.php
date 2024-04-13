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
            $table->id();
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('accepter_id');
            $table->enum('status', ['pending', 'accepted', 'declined', 'blocked'])->default('pending');
            $table->timestamps();

            $table->foreign('requester_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('accepter_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['requester_id', 'accepter_id']);

            $table->index(['requester_id', 'accepter_id']);

            $table->index('status');
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
