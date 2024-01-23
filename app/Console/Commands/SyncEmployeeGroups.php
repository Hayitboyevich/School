<?php

namespace App\Console\Commands;

use App\Services\GroupService;
use Illuminate\Console\Command;

class SyncEmployeeGroups extends Command
{

    protected $signature = 'sync:employee-groups';

    protected $description = 'Sync employee groups';

    public function __construct(protected GroupService $groupLevelServices)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->groupLevelServices->sync_employee_groups();
    }
}
