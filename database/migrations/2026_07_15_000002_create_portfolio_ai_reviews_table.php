<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_ai_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->text('summary');
            $table->json('strengths');
            $table->json('weaknesses');
            $table->json('recommendations');
            $table->string('model', 80);
            $table->json('source_snapshot')->nullable();
            $table->timestamp('generated_at')->index();
            $table->timestamps();

            $table->index(['user_id', 'generated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_ai_reviews');
    }
};
