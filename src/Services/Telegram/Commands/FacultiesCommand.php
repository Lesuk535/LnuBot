<?php


namespace App\Services\Telegram\Commands;


class FacultiesCommand extends Command
{
    public function handle($params = [])
    {
        $this->telegram->editMessageText([
            "chat_id" => $params['chat_id'],
            "message_id" => $params["message_id"] + 1,
            "text" => $this->messageText['text'],
            "reply_markup" => $this->buttonsCourse(array_values($params)),
        ]);
    }

    private function buttonsCourse($callbackData)
    {
        $rand = rand(1, 1000);

        $buttons = array(
            "inline_keyboard" => array(
                array(
                    [
                        "text" => "1",
                        "callback_data" => "/groups/" . $this->callbackData($callbackData) . "1/" . $rand
                    ],
                    [
                        "text" => "2",
                        "callback_data" => "/groups/" . $this->callbackData($callbackData) . "2/" . $rand
                    ]
                ),
                array(
                    [
                        "text" => "3",
                        "callback_data" => "/groups/" . $this->callbackData($callbackData) . "3/" . $rand
                    ],
                    [
                        "text" => "4",
                        "callback_data" => "/groups/" . $this->callbackData($callbackData) . "4/" . $rand
                    ]
                ),
                array(
                    [
                        "text" =>  "5",
                        "callback_data" => "/groups/" . $this->callbackData($callbackData) . "5/" . $rand
                    ],
                    [
                        "text" =>  "6",
                        "callback_data" => "/groups/" . $this->callbackData($callbackData) . "6/" . $rand
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
            "text" => "Обери курс"
        ];
    }
}