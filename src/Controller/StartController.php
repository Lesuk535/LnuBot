<?php


namespace App\Controller;

use App\Models\TelegramUser;
use App\Services\Telegram\Telegram;
use App\Services\Curl\Curl;
use App\Services\Telegram\Objects\Message;
use App\Services\Telegram\Objects\Chat;


class StartController
{
    public function startAction()
    {
        $curlConfig = __DIR__ . '/../../config/curl.php';
        $telegramConfig = __DIR__ . '/../../config/telegram.php';
        $host = 'https://api.telegram.org';

        $curl = Curl::app($host, $curlConfig);
        $telegram = new Telegram($telegramConfig, $curl);

        $message = new Message();

        $chat = $message->getChat();

        $chatId = $chat->getId();

        $telegramUser = new TelegramUser();

        $telegramUser->register($chatId);

        $keyboard = [
            "inline_keyboard" => [[
                [
                    "text" => "Комп'ютерні науки (ФЕІ)",
                    "callback_data" => "/fei"
                ],
                [
                    "text" => "Мікро та нано електроніка (ФЕМ)",
                    "callback_data" => "/fem"
                ],
                [
                    "text" => "Інформаційні системи (ФЕС)",
                    "callback_data" => "/fec"
                ],
            ]],
        ];


        $telegram->sendMessage([
            "chat_id" => $chatId,
            "text" => "Виберіть напрям",
            "reply_markup" => json_encode($keyboard),
        ]);



    }
}