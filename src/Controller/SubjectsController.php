<?php


namespace App\Controller;


use App\Models\Subjects;
use App\Services\Json\ScheduleLnuJson;

class SubjectsController
{
    public function addAction()
    {
        $scheduleLnuJson = new ScheduleLnuJson();

        $data = __DIR__.'/../services/parser/data/subjects.json';

        $subjects = new Subjects();

        $scheduleLnuJson->insertSubjects($subjects, $data);
    }

}