<?php

namespace App\Controller;

use App\Services\Telegram\Commands\StartCommand;

class StartController
{
    public function startAction()
    {
        $start = new StartCommand();
        $start->handle();
    }
}