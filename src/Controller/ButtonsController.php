<?php


namespace App\Controller;

use App\Models\Faculties;
use App\Models\TelegramUserSchedule;


class ButtonsController extends BaseController
{
    public function feiAction($chatId, $messageId)
    {

        $telegramUserSchedule = new TelegramUserSchedule();
        $faculty = new Faculties();

        $faculty = $faculty->getValueByColumn('name', 'M');

        $id = $faculty->getId();

        $telegramUserSchedule->getValueByColumn('chat_id', $chatId);

        $keyboard = [
            "inline_keyboard" => [
                [
                    ["text" => "1", "callback_data" => "/courses/1" . $chatId],
                    ["text" => "2", "callback_data" => "/courses/2" . $chatId]
                ],
                [
                    ["text" => "2", "callback_data" => "/courses/2" . $chatId],
                    ["text" => "3", "callback_data" => "/courses/3" . $chatId]
                ],
                [
                    ["text" => "4", "callback_data" => "/courses/4" . $chatId],
                    ["text" => "5", "callback_data" => "/courses/5" . $chatId]
                ],
                [
                    ["text" => "6", "callback_data" => "/courses/6" . $chatId]
                ]
            ],
        ];

        $this->telegram->editMessageText([
            "chat_id" => $chatId,
            "message_id" => $messageId + 1,
            "text" => 'Обери курс',
            "reply_markup" => json_encode($keyboard),
        ]);

        $telegramUserSchedule->setIdFaculty($id);
        $telegramUserSchedule->save();
    }
}