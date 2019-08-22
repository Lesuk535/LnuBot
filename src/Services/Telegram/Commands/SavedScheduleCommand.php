<?php

namespace App\Services\Telegram\Commands;

use App\Models\TelegramUserSchedule as SavedSchedule;


class SavedScheduleCommand extends Command
{
    /**
     * @param array $params
     */
    public function handle($params = [])
    {
        $savedSchedule = new SavedSchedule();

        $messageId = $this->message->getMessageId();
        $chat = $this->message->getChat();
        $chatId = $chat->getId();

        if (empty($params) === false)
            $chatId = $params["chat_id"];

        $columnByChatId = $savedSchedule->getValueByColumn('chat_id', $chatId);

        if ($columnByChatId !== null) {

            $savedByChatId = $savedSchedule->getSavedByChatId($chatId);

            if (empty($params) === true) {
                $callbackData = [$chatId, $messageId];

                $this->telegram->sendMessage([
                    "chat_id" => $chatId,
                    "text" => $this->messageText['saved'],
                    "reply_markup" => $this->buttonsFaculties($savedByChatId, $callbackData),
                ]);
            } else {
                $callbackData = [$params["chat_id"], $params["message_id"]];

                $this->telegram->editMessageText([
                    "chat_id" => $params["chat_id"],
                    "message_id" => $params["message_id"] + 1,
                    "text" => $this->messageText["saved"],
                    "reply_markup" => $this->buttonsFaculties($savedByChatId, $callbackData),
                ]);
            }

            return;
        }

        if (empty($params) === true) {
            $this->telegram->sendMessage([
                "chat_id" => $chatId,
                "text" => $this->messageText['no_saved'],
                "reply_markup" => $this->buttonsAdd([$chatId, $messageId]),
            ]);
        } else {
            $this->telegram->editMessageText([
                "chat_id" => $params["chat_id"],
                "message_id" => $params["message_id"] + 1,
                "text" => $this->messageText["no_saved"],
                "reply_markup" => $this->buttonsAdd([$params["chat_id"], $params["message_id"]]),
            ]);
        }

    }

    /**
     * @param array $objects
     * @param array $params
     * @return string
     */
    private function buttonsFaculties(array $objects, array $params)
    {
        $day = strftime("%a", strtotime(date("Y-m-d")));

        $rand = rand(1, 1000);

        for ($i = 0; $i < count($objects); $i++) {
            $object = $objects[$i];

            $text = 'ФЕ';
            $callbackData = $params;

            foreach ($object as $key => $value) {
                if ($key !== "subgroup" && $key !== "weekType") {
                    $text = trim(sprintf("%s%s", $text, $value));
                }

                $callbackData[] = $value;
            }

            $callbackData[] = $day;

            $buttons["inline_keyboard"][] = array(
                [
                    "text" => $text , "callback_data" => "/schedule/" . $this->callbackData($callbackData) . $rand
                ]
            );
        }

        $buttons["inline_keyboard"][] = array(
            [
                "text" => $this->messageText["buttons_add"] ,
                "callback_data" => "/add_schedule/" . $this->callbackData([$params[0], $params[1]]). $rand
            ]
        );

        return $this->telegram->replyKeyboardMarkup($buttons);
    }

    /**
     * @param array $callbackData
     * @return string
     */
    private function buttonsAdd(array $callbackData)
    {
        $rand = rand(1000, 30000);

        $buttons = array(
            "inline_keyboard" => array(
                array(
                    [
                        "text" => $this->messageText["buttons_add"],
                        "callback_data" => '/add_schedule/' . $this->callbackData($callbackData) . $rand
                    ],
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
            "saved" => "📌 Збережені групи",
            "no_saved" => "Немає збережених груп 🙀",
            "buttons_add" => "➕ Додати"
        ];
    }

}