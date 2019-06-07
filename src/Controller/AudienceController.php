<?php


namespace App\Controller;


use App\Models\Audience;
use App\Services\Json\ScheduleLnuJson;

class AudienceController
{
    public function addAction()
    {

        $path = __DIR__.'/../services/parser/data/audience.json';
        $audience = new Audience();

        $scheduleLnuJson = new ScheduleLnuJson();

        $scheduleLnuJson->insertAudience($audience, $path);
    }

}