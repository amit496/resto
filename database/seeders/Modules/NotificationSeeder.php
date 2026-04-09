<?php

namespace Database\Seeders\Modules;

use App\Models\NotificationLog;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['title' => 'High order volume', 'message' => 'Branch 2 crossed 120 orders today.', 'audience' => 'admin', 'channel' => 'dashboard'],
            ['title' => 'Kitchen rush alert', 'message' => 'Branch 4 is running at 90% capacity.', 'audience' => 'staff', 'channel' => 'dashboard'],
            ['title' => 'Customer offer live', 'message' => 'New loyalty offer is now active for all branches.', 'audience' => 'all', 'channel' => 'dashboard'],
        ];

        foreach ($samples as $sample) {
            NotificationLog::query()->firstOrCreate(
                ['title' => $sample['title'], 'message' => $sample['message']],
                [
                    'audience' => $sample['audience'],
                    'channel' => $sample['channel'],
                    'sent_at' => now(),
                ]
            );
        }
    }
}
