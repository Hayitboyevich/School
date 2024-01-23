<?php

namespace App\Http\Controllers;

use App\Services\AcademicYearService;
use App\Services\DirectionService;
use App\Services\SubjectService;

class SehriyoSyncController extends Controller
{
    private DirectionService $directionService;

    private SubjectService $subjectService;

    private AcademicYearService $academicYearService;

    public function __construct(DirectionService $directionService, SubjectService $subjectService, AcademicYearService $academicYearService)
    {
        $this->directionService = $directionService;
        $this->subjectService = $subjectService;
        $this->academicYearService = $academicYearService;
    }

    public function sync_subjects()
    {
        $this->directionService->sync_sehriyo();
        $this->subjectService->sync_sehriyo();
        return redirect()->back();
    }

    public function sync_academic_years()
    {
        $this->academicYearService->sync_sehriyo();
        return redirect()->back();
    }
}
