<?php

namespace App\Services\Telegram\Commands;


use App\Models\TelegramUserSchedule;

class DeleteCommand extends Command
{
    /**
     * @param array $params
     * @return mixed|void
     */
    public function handle($params = [])
    {
        $telegramUserSchedule = new TelegramUserSchedule();
        $telegramUserSchedule->delete(
            $params["chat_id"],
            $params["faculty"],
            $params["course"],
            $params["group"]
        );

        $this->telegram->editMessageText([
            "chat_id" => $params["chat_id"],
            "message_id" => $params["message_id"] + 1,
            "text" => "Група успішно видалена",
            "reply_markup" => $this->getButtonDelete($params),
        ]);
    }

    private function getButtonDelete($params)
    {
        $rand = rand(1, 1000);

        $keyboard["inline_keyboard"]= [[
            [
                "text" => $this->messageText["delete"],
                "callback_data" => "/return_back_schedule/"
                    . $this->callbackData([$params["chat_id"], $params["message_id"]]) . $rand
            ],
        ]];

        return $this->telegram->replyKeyboardMarkup($keyboard);
    }

    /**
     * @return array
     */
    protected function getMessageText(): array
    {
        return [
            "delete" => "Вернутись"
        ];
    }
}