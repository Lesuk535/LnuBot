<?php

namespace App\Controller;

use App\Services\Telegram\Commands\SavedScheduleCommand;
use App\Services\Telegram\Commands\AddScheduleCommand;
use App\Services\Telegram\Commands\CallScheduleCommand;


class KeyboardController
{
    public function savedScheduleAction()
    {
        $savedScheduleCommand = new SavedScheduleCommand();
        $savedScheduleCommand->handle();
    }

    /**
     * @param $chatId
     * @param $messageId
     */
    public function addScheduleAction($chatId, $messageId)
    {
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId
        ];

        $addScheduleCommand = new AddScheduleCommand();
        $addScheduleCommand->handle($params);
    }

    public function callScheduleAction()
    {
        $callScheduleCommand = new CallScheduleCommand();
        $callScheduleCommand->handle();
    }

}
