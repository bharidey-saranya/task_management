<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $website = Project::firstOrCreate(['name' => 'Website Redesign']);
        $mobile = Project::firstOrCreate(['name' => 'Mobile App Launch']);
        $ops = Project::firstOrCreate(['name' => 'Internal Operations']);

        $websiteTasks = [
            ['name' => 'Review Figma mockups with the design team', 'priority' => 1],
            ['name' => 'Implement responsive landing page with Tailwind', 'priority' => 2],
            ['name' => 'Set up automated Lighthouse performance audits', 'priority' => 3],
        ];

        foreach ($websiteTasks as $item) {
            Task::firstOrCreate(
                ['name' => $item['name'], 'project_id' => $website->id],
                ['priority' => $item['priority']]
            );
        }

        $mobileTasks = [
            ['name' => 'Configure push notification service credentials', 'priority' => 1],
            ['name' => 'Prepare App Store & Google Play submission assets', 'priority' => 2],
            ['name' => 'Perform end-to-end beta test with internal testers', 'priority' => 3],
        ];

        foreach ($mobileTasks as $item) {
            Task::firstOrCreate(
                ['name' => $item['name'], 'project_id' => $mobile->id],
                ['priority' => $item['priority']]
            );
        }

        $opsTasks = [
            ['name' => 'Upgrade staging server to PHP 8.3 / Laravel 11', 'priority' => 1],
            ['name' => 'Audit database indexes and slow query log', 'priority' => 2],
        ];

        foreach ($opsTasks as $item) {
            Task::firstOrCreate(
                ['name' => $item['name'], 'project_id' => $ops->id],
                ['priority' => $item['priority']]
            );
        }

        $standaloneTasks = [
            ['name' => 'Organize weekly team standup notes', 'priority' => 1],
            ['name' => 'Review quarterly security checklist', 'priority' => 2],
        ];

        foreach ($standaloneTasks as $item) {
            Task::firstOrCreate(
                ['name' => $item['name'], 'project_id' => null],
                ['priority' => $item['priority']]
            );
        }
    }
}

