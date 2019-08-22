<?php

namespace App\Services\Telegram\Commands;


class AddScheduleCommand extends Command
{

    public function handle(array $params = [])
    {
        $this->telegram->editMessageText([
            "chat_id" => $params['chat_id'],
            "message_id" => $params["message_id"] + 1,
            "text" => $this->messageText["text"],
            "reply_markup" => $this->buttonsFaculty(array_values($params)),
        ]);
    }

    private function buttonsFaculty(array $callbackData)
    {
        $rand = rand(1, 3000);

        $buttons = array(
            "inline_keyboard" => array(
                array(
                    [
                        "text" => $this->messageText["buttons_fei"],
                        "callback_data" => "/faculties/" . $this->callbackData($callbackData) . "I/" . $rand
                    ]
                ),
                array(
                    [
                        "text" => $this->messageText["buttons_fem"],
                        "callback_data" => "/faculties/" . $this->callbackData($callbackData) . "M/" . $rand
                    ]
                ),
                array(
                    [
                        "text" => $this->messageText["buttons_fec"],
                        "callback_data" => "/faculties/" . $this->callbackData($callbackData) . "C/" . $rand
                    ]
                )
            )
        );

        return $this->telegram->replyKeyboardMarkup($buttons);
    }

    /**
     * @return array
     */
    protected function getMessageText(): array
    {
        return [
            "text" => "Обери напрям",
            "buttons_fei" => "Комп'ютерні науки (ФЕІ)",
            "buttons_fem" => "Мікро та нано електроніка (ФЕМ)",
            "buttons_fec" => "Інформаційні системи (ФЕС)"
        ];
    }

}