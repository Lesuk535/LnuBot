<?php

namespace App\Services\Telegram\Commands;

use App\Models\Schedule;


class ScheduleCommand extends Command
{
    private $scheduleModel;

    public function __construct()
    {
        parent::__construct();
        $this->scheduleModel = new Schedule();
    }

    /**
     * @param array $params
     * @return mixed|void
     */
    public function handle($params = [])
    {
        $allIdParams = $this->scheduleModel->getAllIdParams(
            $params["faculty"],
            $params["course"],
            $params["group"],
            $params["day"],
            $params["subgroup"],
            $params["weekType"]
        )[0];

        $scheduleId = $this->scheduleModel->getIdSchedule(
            $allIdParams->idFaculty,
            $allIdParams->idCourse,
            $allIdParams->idGroup,
            $allIdParams->idDay,
            $allIdParams->idSubgroup,
            $allIdParams->idWeekType
        );

        $paramsId = [
            $params["chat_id"],
            $params["message_id"],
            $allIdParams->idFaculty,
            $allIdParams->idCourse,
            $allIdParams->idGroup,
            $allIdParams->idSubgroup,
            $allIdParams->idWeekType
        ];

        $activeDay[$allIdParams->idDay] = '✔️';
        $activeSubgroup[$allIdParams->idSubgroup] = '✔️';
        $activeWeekType[$allIdParams->idWeekType] = '✔️';

        $this->sendSchedule($scheduleId, $params, $activeDay,$activeWeekType, $activeSubgroup, $paramsId);
    }

    private function sendSchedule($objects, $params, $activeDay, $activeWeekType, $activeSubgroup, $paramsId)
    {
        $text = "";
        $halfLesson = '';
        $scheduleInfo = "Розклад: ФЕ" . $params["faculty"] . $params["course"] . $params["group"];


        for ($i = 0; $i < count($objects); $i++) {
            $object = $objects[$i];

            $scheduleName = $this->scheduleModel->getNameSchedule(
                $object->id_subject,
                $object->id_subject_type,
                $object->id_teacher,
                $object->id_audience,
                $object->id_lesson
            )[0];

            if ($object->id_half_lesson !== null)
                $halfLesson = " (" . $object->id_half_lesson . " півпара)";

            $firstLine = $scheduleName->lessons. ". ". $scheduleName->subjects . $halfLesson . PHP_EOL;
            $secondLine = "🎓 " . $scheduleName->teachers . PHP_EOL;
            $thirdLine = "📍 " . $scheduleName->subjectType . PHP_EOL;
            $fourthLine = "🚪 " . $scheduleName->audience . PHP_EOL;
            $fifthLine = "---------------------------------------------------------" . PHP_EOL;

            $text = $text . $firstLine . $secondLine . $thirdLine . $fourthLine . $fifthLine;
        }

        $text .= $scheduleInfo;

        $this->telegram->editMessageText([
            "chat_id" => $params["chat_id"],
            "message_id" => $params["message_id"] + 1,
            "text" => $text,
            "reply_markup" => json_encode(
                $this->getButtons($activeDay, $activeWeekType, $activeSubgroup, $params, $paramsId)
            ),
        ]);
    }

    private function getButtons($activeDay, $activeWeekType, $activeSubgroup, array $params, $paramsId)
    {
        $day = $params["day"];

        unset($params["day"]);
        unset($paramsId[5]);
        unset($paramsId[6]);

        $rand = rand(1, 1000);

        var_dump('/change_week_type/' . $this->callbackData($params) . "$day/2/" . $rand);

        return $buttons = [
            "inline_keyboard" => [[
                [
                    "text" => $activeDay[1] . "Пн",
                    "callback_data" => "/schedule/" . $this->callbackData($params) . "Mon/" . $rand
                ],
                [
                    "text" => $activeDay[2] . "Вт",
                    "callback_data" => "/schedule/" . $this->callbackData($params) . "Tue/" . $rand
                ],
                [
                    "text" =>  $activeDay[3] . "Ср",
                    "callback_data" => "/schedule/" . $this->callbackData($params) . "Wed/" . $rand
                ],
                [
                    "text" => $activeDay[4] . "Чт",
                    "callback_data" => "/schedule/" . $this->callbackData($params) . "Thu/" . $rand
                ],
                [
                    "text" => $activeDay[5] . "Пт",
                    "callback_data" => "/schedule/" . $this->callbackData($params) . "Fri/" . $rand
                ]
            ],
            [
                [
                    "text" => $activeWeekType[1] . "Чисельник",
                    "callback_data" => '/change_week_type/' . $this->callbackData($params) . "$day/1/". $rand
                ],
                [
                    "text" => $activeWeekType[2] ."Знаменник",
                    "callback_data" => '/change_week_type/' . $this->callbackData($params) . "$day/2/" . $rand
                ]
            ],
            [
                [
                    "text" => $activeSubgroup[1] . "1 підгрупа",
                    "callback_data" => '/change_subgroup/' . $this->callbackData($params) . "$day/1/".  $rand
                ],
                [
                    "text" => $activeSubgroup[2]  . "2 підгрупа",
                    "callback_data" => '/change_subgroup/' . $this->callbackData($params). "$day/2/" . $rand
                ]
            ],
            [[
                "text" => "🧨 Видалити",
                "callback_data" => '/delete/' . $this->callbackData($paramsId) . $rand
            ]],
            [[
                "text" => 'Вернутись',
                "callback_data" => '/return_back_schedule/'
                    . $this->callbackData([$params["chat_id"], $params["message_id"]]) . $rand
            ]]
            ],
        ];

    }

    /**
     * @return array
     */
    protected function getMessageText(): array
    {
        return [

        ];
    }
}