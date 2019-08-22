<?php


namespace App\Controller;


use App\Models\Teachers;
use App\Services\Json\ScheduleLnuJson;

class TeachersController
{
    public function addAction()
    {
        $teachers = new Teachers();

        $scheduleLnuJson = new ScheduleLnuJson();

        $scheduleLnuJson->insertTeachers($teachers);
    }
}