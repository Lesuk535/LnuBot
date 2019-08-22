<?php

namespace App\Models;

use App\Services\DBConnect;


class TelegramUserSchedule extends ActiveRecordEntity
{

    /**
     * @var int
     */
    protected $idFaculty;

    /**
     * @var int
     */
    protected $idCourse;

    /**
     * @var int
     */
    protected $idGroup;

    /**
     * @var int
     */
    protected $idWeekType;

    /**
     * @var int
     */
    protected $chatId;

    /**
     * @var int
     */
    protected $idSubgroup;

    public function __construct()
    {
        $db = DBConnect::connect();
        parent::__construct($db);
    }

    /**
     * @param int $chatId
     */
    public function setChatId(int $chatId)
    {
        $this->chatId = $chatId;
    }

    /**
     * @param int $idFaculty
     */
    public function setIdFaculty(int $idFaculty)
    {
        $this->idFaculty = $idFaculty;
    }

    /**
     * @param int $idCourse
     */
    public function setIdCourse(int $idCourse)
    {
        $this->idCourse = $idCourse;
    }

    /**
     * @param int $idGroup
     */
    public function setIdGroup(int $idGroup)
    {
        $this->idGroup = $idGroup;
    }

    /**
     * @param int $idWeekType
     */
    public function setIdWeekType(int $idWeekType)
    {
        $this->idWeekType = $idWeekType;
    }

    /**
     * @param int $idSubgroup
     */
    public function setIdSubgroup(int $idSubgroup)
    {
        $this->idSubgroup = $idSubgroup;
    }

    /**
     * @return int
     */
    public function getFacultyId()
    {
        return $this->idFaculty;
    }

    /**
     * @return int
     */
    public function getIdCourse()
    {
        return $this->idCourse;
    }

    /**
     * @return int
     */
    public function getIdGroup()
    {
        return $this->idGroup;
    }

    /**
     * @param int $chatId
     * @return array|bool|null
     */
    public function getSavedByChatId(int $chatId)
    {
        $db = DBConnect::connect();
        $sql =
            "SELECT 
            faculties.name AS 'faculty',
            courses.number AS 'course',
            groups.number AS 'group',
            subgroups.number AS 'subgroup',
            week_type.type AS 'weekType'
            FROM telegram_user_schedule 
            JOIN faculties
            ON faculties.id = telegram_user_schedule.id_faculty
            JOIN courses
            On courses.id = telegram_user_schedule.id_course
            JOIN groups
            ON groups.id = telegram_user_schedule.id_group
            JOIN subgroups
            ON subgroups.id = telegram_user_schedule.id_subgroup
            JOIN week_type
            ON week_type.id = telegram_user_schedule.id_week_type
            WHERE telegram_user_schedule.chat_id = :chatId";

        return $entities = $db->dbQuery($sql, ["chatId" => $chatId]);
    }

    /**
     * @param $faculty
     * @param $course
     * @param $group
     * @return array|bool|null
     */
    public function getFacultiesCoursesGroupsId(
        $faculty,
        $course,
        $group
    )
    {
        $db = DBConnect::connect();

        $sql =
            "SELECT faculties.id AS 'facultyId',
            courses.id AS 'courseId',
            groups.id AS 'groupId'
            FROM faculties
            JOIN courses
            JOIN groups
            WHERE faculties.name = :faculty
            AND courses.number = :course
            AND groups.number = :group";

        return $entities = $db->dbQuery($sql, [
            'faculty' => $faculty,
            'course' => $course,
            'group' => $group,
        ]);
    }

    // TODO витягувати по чат айді

    public function getAllByFacultyCourseGroup(
        $idFaculty,
        $idCourse,
        $idGroup,
        $chatId
    )
    {
        $db = DBConnect::connect();

        $sql =
            "SELECT * 
            FROM `telegram_user_schedule` 
            WHERE `id_faculty` = :idFaculty
            AND `id_course` = :idCourse
            AND `id_group` = :idGroup
            AND `chat_id` = :chatId";

        $results = $entities = $db->dbQuery($sql, [
            'idFaculty' => $idFaculty,
            'idCourse' => $idCourse,
            'idGroup' => $idGroup,
            'chatId' => $chatId,
        ]);

        return $results ? $results[0] : null;
    }

    public function getAllIdParams(
        $faculty,
        $course,
        $group,
        $subgroup,
        $weekType
    )
    {
        $db = DBConnect::connect();

        $sql =
            "SELECT faculties.id AS 'facultyId',
            courses.id AS 'courseId',
            groups.id AS 'groupId',
            subgroups.id AS 'subgroupsId',
            week_type.id AS 'weekTypeId'
            FROM faculties
            JOIN courses
            JOIN groups
            JOIN subgroups
            JOIN week_type
            WHERE faculties.name = :faculty
            AND courses.number = :course
            AND groups.number = :group
            AND subgroups.number = :subgroup
            AND week_type.type = :weekType";

        $results = $entities = $db->dbQuery($sql, [
            'faculty' => $faculty,
            'course' => $course,
            'group' => $group,
            'subgroup' => $subgroup,
            'weekType' => $weekType,
        ]);

        return $results ? $results[0] : null;
    }

    /**
     * @param $idFaculty
     * @param $idCourse
     * @param $idGroup
     * @param $idSubgroup
     * @param $idWeekType
     * @param $chatId
     * @return static
     */
    public function getAllByColumns(
        $idFaculty,
        $idCourse,
        $idGroup,
        $idSubgroup,
        $idWeekType,
        $chatId
    )
    {
        $db = DBConnect::connect();

        $sql =
            "SELECT * 
            FROM `telegram_user_schedule` 
            WHERE `id_faculty` = :idFaculty
            AND `id_course` = :idCourse
            AND `id_group` = :idGroup
            AND `id_subgroup` = :idSubgroup
            AND `id_week_type` = :idWeekType
            AND `chat_id` = :chatId";

        $results = $entities = $db->dbQuery($sql, [
            'idFaculty' => $idFaculty,
            'idCourse' => $idCourse,
            'idGroup' => $idGroup,
            'idSubgroup' => $idSubgroup,
            'idWeekType' => $idWeekType,
            'chatId' => $chatId,
        ], static::class);

        return $results ? $results[0] : null;
    }


    /**
     * @param $chatId
     * @param $idFaculty
     * @param $idCourse
     * @param $idGroup
     * @return array|bool|null
     */
    public function delete(
        $chatId,
        $idFaculty,
        $idCourse,
        $idGroup
    )
    {
        $db = DBConnect::connect();

        $sql = 'DELETE FROM `telegram_user_schedule` 
        WHERE chat_id = :chatId
        AND id_faculty = :idFaculty
        AND id_course = :idCourse
        AND id_group = :idGroup
        ';

        return $entities = $db->dbQuery($sql, [
            'chatId' => $chatId,
            'idFaculty' => $idFaculty,
            'idCourse' => $idCourse,
            'idGroup' => $idGroup,
        ]);
    }

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return 'telegram_user_schedule';
    }
}
