<?php


namespace App\Controller;


use App\Models\SubjectType;
use App\Services\Json\ScheduleLnuJson;

class SubjectTypeController
{
    public function addAction()
    {
        $subjectType = new SubjectType();

        $scheduleLnuJson = new ScheduleLnuJson();

        $scheduleLnuJson->insertSubjectType($subjectType);
    }
}