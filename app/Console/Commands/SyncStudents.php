<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Services\UserSyncService;
use Illuminate\Console\Command;

class SyncStudents extends Command
{

    protected $signature = 'sync:students';

    protected $description = 'Sync Students';

    public function __construct(
        protected UserSyncService $userSyncServices,
        protected Role            $roles
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $student_role = $this->roles->where('name', 'student')->first();
        if ($student_role) {
            $page = 1;
            while (true) {
                $data_count = $this->userSyncServices->sync_students($page);
                if ($data_count <= 0) break;
                $page++;
                sleep(2);
            }
        } else {
            $this->info('No student role');
        }
    }
}
