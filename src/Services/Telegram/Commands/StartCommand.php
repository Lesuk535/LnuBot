<?php

namespace App\Services\Telegram\Commands;

use App\Models\TelegramUser;


class StartCommand extends Command
{
    public function handle()
    {
        $telegramUser = new TelegramUser();

        $chat = $this->message->getChat();

        $chatId = $chat->getId();

        $telegramUser->register($chatId);

        $this->telegram->sendMessage([
            "chat_id" => $chatId,
            "text" => $this->messageText['text'],
            "reply_markup" => $this->getKeyboard(),
        ]);
    }

    /**
     * @return array
     */
    protected function getMessageText(): array
    {
        return [
            "text" => "Знизу можна вибрати потрібний тобі пункт меню. 😺 ⤵️"
        ];
    }

    /**
     * @return string
     */
    private function getKeyboard()
    {
        $keyboard =  array(
            "keyboard" => array(
                [
                    "🗓 Мої розклади",
                    "🔔 Розклад дзвінків"
                ],
            ),
            "resize_keyboard" => true
        );

        return $this->telegram->replyKeyboardMarkup($keyboard);

    }


}