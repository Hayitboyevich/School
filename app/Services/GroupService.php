<?php

namespace App\Services;

use App\Models\Enums\UserExternalType;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GroupService
{

    public function __construct(protected Group $groups, protected User $users)
    {
    }

    public function sync_student_groups()
    {
        $total = $this->users
            ->where('external_type', UserExternalType::STUDENT)
            ->where('external_source', 'sehriyo_lms')
            ->count();
        $count = 0;
        $this->users
            ->where('external_type', UserExternalType::STUDENT)
            ->where('external_source', 'sehriyo_lms')
            ->chunk(50, function ($students) use (&$count, $total) {
                $count = min($total, $count + 50);
                error_log("Processing: $count/$total");
                try {
                    $this->sync_student_groups_batch($students);
                } catch (ConnectionException $e) {
                    error_log('Request timed out. Retry in 2 seconds.');
                    sleep(2);
                    $count -= 50;
                    $this->sync_student_groups_batch($students);
                }
                error_log("Processed: $count/$total");
            });
    }

    private function sync_student_groups_batch($students)
    {
        $student_ids = $students->pluck('external_id')->toArray();

        $url = env('SEHRIYO_LMS_HOST') . '/students/groups';
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('SEHRIYO_LMS_USER'), env('SEHRIYO_LMS_PASSWORD'))
            ->post($url, ['student_ids' => $student_ids]);
        $students_data = $response['data'];

        foreach ($students_data as $student_data) {
            $student = $this->users
                ->where('external_id', $student_data['student_id'])
                ->where('external_type', UserExternalType::STUDENT)
                ->where('external_source', 'sehriyo_lms')
                ->first();

            if ($student === null) continue;

            $group_ids = [];

            foreach ($student_data['groups'] as $group_data) {
                $group = $this->getGroup($group_data);
                $group_ids[] = $group->id;
            }

            $student->groups()->sync($group_ids);
        }
    }

    public function sync_employee_groups()
    {
        $total = $this->users
            ->where('external_type', UserExternalType::EMPLOYEE)
            ->where('external_source', 'sehriyo_lms')
            ->count();
        $count = 0;
        $this->users
            ->where('external_type', UserExternalType::EMPLOYEE)
            ->where('external_source', 'sehriyo_lms')
            ->chunk(50, function ($employees) use (&$count, $total) {
                $count = min($total, $count + 50);
                error_log("Processing: $count/$total");
                try {
                    $this->sync_employee_groups_batch($employees);
                } catch (ConnectionException $e) {
                    error_log('Request timed out. Retry in 2 seconds.');
                    sleep(2);
                    $count -= 50;
                    $this->sync_employee_groups_batch($employees);
                }
                error_log("Processed: $count/$total");
            });
    }

    private function sync_employee_groups_batch($employees)
    {
        $employee_ids = $employees->pluck('external_id')->toArray();

        $url = env('SEHRIYO_LMS_HOST') . '/employees/groups';
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('SEHRIYO_LMS_USER'), env('SEHRIYO_LMS_PASSWORD'))
            ->post($url, ['employee_ids' => $employee_ids]);
        $employees_data = $response['data'];

        foreach ($employees_data as $employee_data) {
            $employee = $this->users
                ->where('external_id', $employee_data['employee_id'])
                ->where('external_type', UserExternalType::EMPLOYEE)
                ->where('external_source', 'sehriyo_lms')
                ->first();

            if ($employee === null) continue;

            $group_ids = [];

            foreach ($employee_data['groups'] as $group_data) {
                $group = $this->getGroup($group_data);
                $group_ids[] = $group->id;
            }

            $employee->groups()->sync($group_ids);
        }
    }

    private function getGroup(mixed $group_data)
    {
        $group = new Group();
        $group->group_id = $group_data['group_id'];
        $group->group_level = $group_data['level'];
        $group->group_letter = $group_data['letter'];
        $group->academic_year_ids = json_decode($group_data['academic_year_ids']);

        $group = $this->groups->updateOrCreate(
            [
                'group_id' => $group->group_id
            ],
            [
                'group_level' => $group->group_level,
                'group_letter' => $group->group_letter,
                'academic_year_ids' => $group->academic_year_ids
            ]
        );
        return $group;
    }
}
