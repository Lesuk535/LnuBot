<?php


namespace App\Controller;


use App\Models\Subgroups;
use App\Models\TelegramUserSchedule;
use App\Models\WeekType;
use App\Services\Telegram\Commands\FacultiesCommand;
use App\Services\Telegram\Commands\GroupsCommand;
use App\Services\Telegram\Commands\SavedScheduleCommand;
use App\Services\Telegram\Commands\SavedSuccessfullyCommand;
use App\Services\Telegram\Commands\ScheduleCommand;
use App\Services\Telegram\Commands\DeleteCommand;
use App\Services\Telegram\Commands\WeekTypeCommand;
use App\Services\Telegram\Commands\SubgroupCommand;


class ButtonsController
{
    public function facultiesAction($chatId, $messageId, $faculty)
    {
        $params = [
            "chat_id" => $chatId,
            "message_id" => $messageId,
            "faculty" => $faculty
        ];

        $facultiesCommand = new FacultiesCommand();
        $facultiesCommand->handle($params);
    }

    /**
     * @param $chatId
     * @param $messageId
     * @param $faculty
     * @param $course
     */
    public function groupsAction($chatId, $messageId, $faculty, $course)
    {
        $params = [
            "chat_id" => $chatId,
            "message_id" => $messageId,
            "faculty" => $faculty,
            "course" => $course
        ];

        $groupsCommand = new GroupsCommand();
        $groupsCommand->handle($params);
    }

    /**
     * @param $chatId
     * @param $messageId
     * @param $faculty
     * @param $course
     * @param $group
     */
    public function saveScheduleAction($chatId, $messageId, $faculty, $course, $group)
    {
        $params = [
            "chat_id" => $chatId,
            "message_id" => $messageId,
            "faculty" => $faculty,
            "course" => $course,
            "group" => $group
        ];

        $savedSuccessfullyCommand = new SavedSuccessfullyCommand();
        $savedSuccessfullyCommand->handle($params);
    }

    /**
     * @param $chatId
     * @param $messageId
     * @var $columnByChatId TelegramUserSchedule
     */
    public function returnBackScheduleAction($chatId, $messageId)
    {
        $params = [
            "chat_id" => $chatId,
            "message_id" => $messageId,
        ];

        $savedScheduleCommand = new SavedScheduleCommand();
        $savedScheduleCommand->handle($params);

    }

    /**
     * @param $chatId
     * @param $messageId
     * @param $faculty
     * @param $course
     * @param $group
     * @param $subgroup
     * @param $weekType
     * @param $day
     */
    public function scheduleAction($chatId, $messageId,$faculty, $course, $group, $subgroup, $weekType, $day)
    {
        $params = [
            "chat_id" => $chatId,
            "message_id" => $messageId,
            "faculty" => $faculty,
            "course" => $course,
            "group" => $group,
            "day" => $day,
            "subgroup" => $subgroup,
            "weekType" => $weekType,
        ];

        $scheduleCommand = new ScheduleCommand();
        $scheduleCommand->handle($params);
    }

    /**
     * @param $chatId
     * @param $messageId
     * @param $idFaculty
     * @param $idCourse
     * @param $idGroup
     */
    public function deleteAction($chatId, $messageId, $idFaculty, $idCourse, $idGroup)
    {
        $params = [
            "chat_id" => $chatId,
            "message_id" => $messageId,
            "faculty" => $idFaculty,
            "course" => $idCourse,
            "group" => $idGroup,
        ];

        $deleteCommand = new DeleteCommand();
        $deleteCommand->handle($params);

        $telegramUserSchedule = new TelegramUserSchedule();
        $telegramUserSchedule->delete($chatId, $idFaculty, $idCourse, $idGroup);
    }

    /**
     * @param $chatId
     * @param $messageId
     * @param $faculty
     * @param $course
     * @param $group
     * @param $subgroup
     * @param $weekType
     * @param $day
     * @param $weekTypeId
     */
    public function changeWeekTypeAction(
        $chatId, $messageId, $faculty, $course, $group, $subgroup, $weekType, $day, $weekTypeId
    )
    {
        $weekTypeModel = new WeekType();

        $weekTypeModel = $weekTypeModel->getValueByColumn("id", $weekTypeId);
        $weekTypeName = $weekTypeModel->getType();

        $params = [
            "chat_id" => $chatId,
            "message_id" => $messageId,
            "faculty" => $faculty,
            "course" => $course,
            "group" => $group,
            "day" => $day,
            "subgroup" => $subgroup,
            "weekType" => $weekType,
            "week_type_id" => $weekTypeId,
        ];

        $weekTypeCommand = new WeekTypeCommand();
        $weekTypeCommand->handle($params);

        unset($params["week_type_id"]);
        $params["weekType"] = $weekTypeName;

        $scheduleCommand = new ScheduleCommand();
        $scheduleCommand->handle($params);
    }

    /**
     * @param $chatId
     * @param $messageId
     * @param $faculty
     * @param $course
     * @param $group
     * @param $subgroup
     * @param $weekType
     * @param $day
     * @param $subgroupId
     */
    public function changeSubgroupAction(
        $chatId, $messageId, $faculty, $course, $group, $subgroup, $weekType, $day, $subgroupId
    )
    {
        $subgroupModel = new Subgroups();

        $subgroupModel = $subgroupModel->getValueByColumn("id", $subgroupId);
        $subgroupNumber = $subgroupModel->getNumber();


        $params = [
            "chat_id" => $chatId,
            "message_id" => $messageId,
            "faculty" => $faculty,
            "course" => $course,
            "group" => $group,
            "day" => $day,
            "subgroup" => $subgroup,
            "weekType" => $weekType,
            "subgroup_id" => $subgroupId,
        ];

        $subgroupCommand = new SubgroupCommand();
        $subgroupCommand->handle($params);

        unset($params["subgroup_id"]);
        $params["subgroup"] = $subgroupNumber;

        $scheduleCommand = new ScheduleCommand();
        $scheduleCommand->handle($params);
    }

}
