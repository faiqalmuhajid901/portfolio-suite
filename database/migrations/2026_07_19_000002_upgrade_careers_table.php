<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('careers', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('employment_type', 60)->nullable()->after('company');
            $table->date('start_date')->nullable()->after('period');
            $table->date('end_date')->nullable()->after('start_date');
            $table->boolean('is_current')->default(false)->after('end_date');
            $table->json('achievements')->nullable()->after('description');
            $table->json('technologies')->nullable()->after('achievements');
            $table->boolean('is_public')->default(false)->index()->after('technologies');
            $table->unsignedSmallInteger('sort_order')->default(0)->index()->after('is_public');
        });

        $firstUserId = DB::table('users')->orderBy('id')->value('id');

        if ($firstUserId !== null) {
            DB::table('careers')
                ->whereNull('user_id')
                ->update([
                    'user_id' => $firstUserId,
                    'is_public' => true,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('careers', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'employment_type',
                'start_date',
                'end_date',
                'is_current',
                'achievements',
                'technologies',
                'is_public',
                'sort_order',
            ]);
        });
    }
};
