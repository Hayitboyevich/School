<?php

namespace App\Services;

use App\Models\Enums\UserExternalType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UserSyncService
{
    public function __construct(
        protected User $users,
        protected Role $roles
    )
    {
    }

    public function sync_students($page)
    {
        $url = env('SEHRIYO_LMS_HOST') . '/students';
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('SEHRIYO_LMS_USER'), env('SEHRIYO_LMS_PASSWORD'))
            ->get($url, ['page' => $page]);
        $students_data = $response['data'];
        $student_role = $this->roles->where('name', 'student')->first();
        foreach ($students_data as $student_data) {
            $student_data['external_id'] = $student_data['id'];
            unset($student_data['id']);
            $student_data['password'] = Hash::make('12345678');
            $student_data['external_source'] = 'sehriyo_lms';
            $student_data['external_type'] = UserExternalType::STUDENT;
            $student = $this->users
                ->where('external_id', $student_data['external_id'])
                ->Where('external_type', UserExternalType::STUDENT)
                ->first();
            try {
                if ($student == null) {
                    $student = $this->users->create($student_data);
                } else {
                    $student->update($student_data);
                }
                $student->roles()->syncWithoutDetaching($student_role->id);
            } catch (\Throwable $e) {
                echo $student_data['name'] . " - " . $student_data['phone'] ."\r\n";
            }
        }
        return count($students_data);
    }

    public function sync_employees($page)
    {
        $url = env('SEHRIYO_LMS_HOST') . '/employees';
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('SEHRIYO_LMS_USER'), env('SEHRIYO_LMS_PASSWORD'))
            ->get($url, ['page' => $page]);
        $employees_data = $response['data'];
        $teacher_role = $this->roles->where('name', 'teacher')->first();
        foreach ($employees_data as $employee_data) {
            $employee_data['external_id'] = $employee_data['id'];
            unset($employee_data['id']);
            $employee_data['password'] = Hash::make('12345678');
            $employee_data['external_source'] = 'sehriyo_lms';
            $employee_data['external_type'] = UserExternalType::EMPLOYEE;
            $employee = $this->users
                ->where('external_id', $employee_data['external_id'])
                ->where('external_type', UserExternalType::EMPLOYEE)
                ->first();
            try {
                if ($employee == null) {
                    $employee = $this->users->create($employee_data);
                } else {
                    $employee->update($employee_data);
                }
                $employee->roles()->syncWithoutDetaching($teacher_role->id);
            } catch (\Throwable $e) {
                echo $employee_data['name'] . " - " . $employee_data['phone'] ."\r\n";
            }
        }
        return count($employees_data);
    }
}
