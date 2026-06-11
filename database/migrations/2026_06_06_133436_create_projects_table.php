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
    Schema::create('projects', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('name');
        $table->string('category')->nullable();
        $table->string('client')->nullable();
        $table->string('status')->default('review');
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->string('image')->nullable();
        $table->text('description')->nullable();
        $table->json('tags')->nullable();
        $table->unsignedInteger('likes')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
