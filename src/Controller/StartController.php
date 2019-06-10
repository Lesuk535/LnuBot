<?php


namespace App\Controller;

use App\Models\TelegramUser;
use App\Services\Telegram\Objects\CallbackQuery;
use App\Services\Telegram\Objects\Message;


class StartController extends BaseController
{
    public function startAction()
    {
        $message = new Message();
        $telegramUser = new TelegramUser();

        $chat = $message->getChat();
        $chatId = $chat->getId();
        $messageId = $message->getMessageId();
        $callbackData = $chatId . "/" . $messageId;


        $telegramUser->register($chatId);

        $keyboard = [
            "inline_keyboard" => [
                [["text" => "Комп'ютерні науки (ФЕІ)", "callback_data" => "/fei/" . $callbackData]],
                [["text" => "Мікро та нано електроніка (ФЕМ)", "callback_data" => "/fem/" . $callbackData]],
                [["text" => "Інформаційні системи (ФЕС)", "callback_data" => "/fec/" . $callbackData]]
            ],
        ];

        $this->telegram->sendMessage([
            "chat_id" => $chatId,
            "text" => $message->getMessageId(),
            "reply_markup" => json_encode($keyboard),
        ]);

    }
}