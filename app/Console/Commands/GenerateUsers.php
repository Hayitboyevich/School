<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate initial admin and manager roles and users';

    private Role $roles;
    private User $users;

    public function __construct(Role $roles, User $users)
    {
        parent::__construct();
        $this->roles = $roles;
        $this->users = $users;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $roles = $this->generateRoles('admin', 'manager', 'teacher', 'student');
        $users = $this->generateUsers(
            [
                'name' => 'Admin',
                'email' => 'admin@sehriyo.uz',
                'phone' => '998911359595',
                'password' => Hash::make('admin')
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@sehriyo.uz',
                'phone' => '998901860947',
                'password' => Hash::make('manager')
            ]
        );

        $admin_role = $roles->firstWhere('name', 'admin');
        $admin_user = $users->firstWhere('email', 'admin@sehriyo.uz');

        $manager_role = $roles->firstWhere('name', 'manager');
        $manager_user = $users->firstWhere('email', 'manager@sehriyo.uz');

        $admin_user->roles()->syncWithoutDetaching($admin_role);
        $manager_user->roles()->syncWithoutDetaching($manager_role);
    }

    private function generateRoles(string ...$role_names): \Illuminate\Support\Collection
    {
        $roles = collect();
        foreach ($role_names as $role_name) {
            $roles->add($this->getOrCreateRole($role_name));
        }
        return $roles;
    }

    private function getOrCreateRole(string $role_name)
    {
        $role = $this->roles->where('name', $role_name)->first();
        if ($role == null) {
            $role = $this->roles->create([
                'label' => mb_convert_case($role_name, MB_CASE_TITLE),
                'name' => $role_name
            ]);
        }
        return $role;
    }

    private function generateUsers(array ...$users_data): \Illuminate\Support\Collection
    {
        $users = collect();
        foreach ($users_data as $user_data) {
            $users->add($this->getOrCreateUser($user_data));
        }
        return $users;
    }

    private function getOrCreateUser(array $user_data)
    {
        $user = $this->users->where('email', $user_data['email'])->first();
        if ($user == null) {
            $user = $this->users->create($user_data);
        }
        return $user;
    }
}
