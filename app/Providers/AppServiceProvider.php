<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        try {
            if (Schema::hasTable('projects') && !Schema::hasColumn('projects', 'is_active')) {
                Schema::table('projects', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true)->after('name');
                });
            }

            if (Schema::hasTable('tasks') && !Schema::hasColumn('tasks', 'is_active')) {
                Schema::table('tasks', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true)->after('priority');
                });
            }
        } catch (\Throwable $e) {
            // Silently continue if database is not reachable at boot time
        }
    }
}
