<?php

namespace App\Services\Telegram\Commands;

use App\Models\TelegramUserSchedule as SavedSchedule;


class SavedSuccessfullyCommand extends Command
{
    /**
     * @param array $params
     * @return mixed|void
     */
    public function handle($params = [])
    {
        $savedSchedule = new SavedSchedule();

        $paramsId = $savedSchedule->getFacultiesCoursesGroupsId(
            $params['faculty'],
            $params['course'],
            $params['group']
        )[0];

        $hasParams = $savedSchedule->getAllByFacultyCourseGroup(
                $paramsId->facultyId,
                $paramsId->courseId,
                $paramsId->groupId,
                $params["chat_id"]
        );

        if ($hasParams === null) {
            $text = $this->messageText["successful"];
            $this->insertToTable(
                $savedSchedule,
                $params["chat_id"],
                $paramsId->facultyId,
                $paramsId->courseId,
                $paramsId->groupId
            );
        } else {
            $text = $this->messageText["already_saved"];
        }

        $this->telegram->editMessageText([
            "chat_id" => $params["chat_id"],
            "message_id" => $params["message_id"] + 1,
            "text" => $text,
            "reply_markup" => $this->buttonsReturnBack(array_values($params)),
        ]);
    }

    /**
     * @param array $params
     * @return string
     */
    private function buttonsReturnBack(array $params)
    {
        $rand = rand(1, 1000);

        $buttons = array(
            "inline_keyboard" => array(
                array(
                    [
                        "text" =>  $this->messageText["return_back"],
                        "callback_data" => "/return_back_schedule/" . $this->callbackData([$params[0], $params[1]]) . $rand
                    ]
                )
            )
        );

        return $this->telegram->replyKeyboardMarkup($buttons);
    }

    /**
     * @param SavedSchedule $savedSchedule
     * @param $chatId
     * @param $facultyId
     * @param $courseId
     * @param $groupId
     */
    private function insertToTable(
        SavedSchedule $savedSchedule,
        $chatId,
        $facultyId,
        $courseId,
        $groupId
    )
    {
        $savedSchedule->setChatId($chatId);
        $savedSchedule->setIdFaculty($facultyId);
        $savedSchedule->setIdCourse($courseId);
        $savedSchedule->setIdGroup($groupId);
        $savedSchedule->save();
    }

    /**
     * @return array
     */
    protected function getMessageText(): array
    {
        return [
            "successful" => "Група успішно збережена",
            "already_saved" => "Вже є у вашому списку",
            "return_back" => "Вернутись"
        ];
    }
}