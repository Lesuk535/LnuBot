<?php


namespace App\Controller;


use App\Models\Teachers;
use App\Services\Json\ScheduleLnuJson;

class TeachersController
{
    public function addAction()
    {
        $path = __DIR__.'/../services/parser/data/teacher.json';
        $teachers = new Teachers();

        $scheduleLnuJson = new ScheduleLnuJson();

        $scheduleLnuJson->insertTeachers($teachers, $path);
    }
}