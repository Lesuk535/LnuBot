<?php

namespace App\Services\Telegram\Commands;

use App\Models\TelegramUserSchedule as SavedSchedule;


class SubgroupCommand extends Command
{
    /**
     * @param array $params
     * @return mixed|void
     */
    public function handle($params = [])
    {
        $savedSchedule = new SavedSchedule();

        $paramsId = $savedSchedule->getAllIdParams(
            $params["faculty"],
            $params["course"],
            $params["group"],
            $params["subgroup"],
            $params["weekType"]
        );

        $allByColumns = $savedSchedule->getAllByColumns(
            $paramsId->facultyId,
            $paramsId->courseId,
            $paramsId->groupId,
            $paramsId->subgroupsId,
            $paramsId->weekTypeId,
            $params["chat_id"]
        );

        $allByColumns->setIdSubgroup($params["subgroup_id"]);
        $allByColumns->save();
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