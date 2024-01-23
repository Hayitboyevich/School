<?php

namespace App\Console\Commands;

use App\Services\GroupService;
use Illuminate\Console\Command;

class SyncStudentGroups extends Command
{

    protected $signature = 'sync:student-groups';

    protected $description = 'Sync student groups';

    public function __construct(protected GroupService $groupLevelServices)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->groupLevelServices->sync_student_groups();
    }
}
