<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_likes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->char('visitor_hash', 64);

            $table->timestamps();

            /*
             * Satu visitor hanya boleh memiliki satu record
             * untuk satu project.
             */
            $table->unique(
                ['project_id', 'visitor_hash'],
                'project_likes_project_visitor_unique'
            );

            $table->index(
                'visitor_hash',
                'project_likes_visitor_hash_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_likes');
    }
};
