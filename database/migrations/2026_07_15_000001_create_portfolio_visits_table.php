<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_visits', function (Blueprint $table): void {
            $table->id();
            $table->uuid('visitor_id')->index();
            $table->string('session_id', 120)->nullable()->index();
            $table->char('ip_hash', 64)->nullable()->index();
            $table->string('path', 512)->index();
            $table->string('route_name', 120)->nullable()->index();
            $table->string('referrer_host', 191)->nullable()->index();
            $table->string('device_type', 20)->default('desktop')->index();
            $table->string('browser', 40)->nullable();
            $table->string('operating_system', 40)->nullable();
            $table->char('country_code', 2)->nullable()->index();
            $table->string('region', 100)->nullable();
            $table->string('city', 150)->nullable();
            $table->timestamp('last_seen_at')->index();
            $table->timestamps();

            $table->index(['visitor_id', 'created_at']);
            $table->index(['path', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_visits');
    }
};
