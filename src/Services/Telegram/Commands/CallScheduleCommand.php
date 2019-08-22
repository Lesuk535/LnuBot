<?php

namespace App\Services\Telegram\Commands;


class CallScheduleCommand extends Command
{

    /**
     * @return mixed|void
     */
    public function handle()
    {
        $chat = $this->message->getChat();
        $chatId = $chat->getId();

        $this->telegram->sendMessage([
            "chat_id" => $chatId,
            "text" => $this->messageText["text"]
        ]);
    }

    /**
     * @return array
     */
    protected function getMessageText(): array
    {
        return [
            'text' =>
                '1. 8:30 – 9:50' . PHP_EOL .
                '2. 10:10 – 11:30' . PHP_EOL .
                '3. 11:50 – 13:10' . PHP_EOL .
                '4. 13:30 – 14:50'. PHP_EOL .
                '5. 15:05 – 16:25'. PHP_EOL .
                '6. 16:40 – 18:00'. PHP_EOL
        ];
    }
}