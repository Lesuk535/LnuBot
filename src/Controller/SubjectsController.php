<?php


namespace App\Controller;


use App\Models\Subjects;
use App\Services\Json\ScheduleLnuJson;

class SubjectsController
{
    public function addAction()
    {
        $scheduleLnuJson = new ScheduleLnuJson();

        $subjects = new Subjects();

        $scheduleLnuJson->insertSubjects($subjects);
    }

}