<?php

namespace App\Controller;

use App\Models\Audience;
use App\Services\Json\ScheduleLnuJson;


class AudienceController
{
    public function addAction()
    {
        $audience = new Audience();
        $scheduleLnuJson = new ScheduleLnuJson();

        $scheduleLnuJson->insertAudience($audience);
    }

}