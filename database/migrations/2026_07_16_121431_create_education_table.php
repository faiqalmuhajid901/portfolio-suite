<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('educations', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('level', 50);
            $table->string('institution');
            $table->string('major')->nullable();

            /*
             * Contoh nilai: 3.75 atau 4.00.
             */
            $table->decimal('gpa', 3, 2)->nullable();

            $table->unsignedSmallInteger('start_year')->nullable();
            $table->unsignedSmallInteger('end_year')->nullable();

            $table->string('status', 100)->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);

            $table->timestamps();

            $table->index([
                'profile_id',
                'is_visible',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};
