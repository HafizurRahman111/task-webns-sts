<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('description');
            $table->enum('category', ['technical', 'billing', 'general'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['open', 'in-progress', 'resolved', 'closed'])->default('open');
            $table->string('attachment')->nullable()->comment('Optional attachment file');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('category');
            $table->index('priority');
            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
