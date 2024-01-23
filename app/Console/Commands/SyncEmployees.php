<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Services\UserSyncService;
use Illuminate\Console\Command;

class SyncEmployees extends Command
{

    protected $signature = 'sync:employees';

    protected $description = 'Sync Employees';

    public function __construct(
        protected UserSyncService $userSyncServices,
        protected Role            $roles
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $teacher_role = $this->roles->where('name', 'teacher')->first();
        if ($teacher_role) {
            $page = 1;
            while (true) {
                $data_count = $this->userSyncServices->sync_employees($page);
                if ($data_count <= 0) break;
                $page++;
                sleep(2);
            }
        } else {
            $this->info('No teacher role');
        }
    }
}
