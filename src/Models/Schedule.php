<?php


namespace App\Models;

use App\Services\DBConnect;


class Schedule extends ActiveRecordEntity
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
    protected $idDay;

    /**
     * @var int
     */
    protected $idLesson;

    /**
     * @var int
     */
    protected $idWeekType;

    /**
     * @var int
     */
    protected $idSubgroup;

    /**
     * @var int
     */
    protected $idSubject;

    /**
     * @var int
     */
    protected $idSubjectType;

    /**
     * @var int
     */
    protected $idTeacher;

    /**
     * @var int
     */
    protected $idHalfLesson;

    /**
     * @var int
     */
    protected $idAudience;

    public function __construct()
    {
        $db = DBConnect::connect();
        parent::__construct($db);
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
     * @param int $idDay
     */
    public function setIdDay(int $idDay)
    {
        $this->idDay = $idDay;
    }

    /**
     * @param int $idLesson
     */
    public function setIdLesson(int $idLesson)
    {
        $this->idLesson = $idLesson;
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
     * @param int $idSubject
     */
    public function setIdSubject(int $idSubject)
    {
        $this->idSubject = $idSubject;
    }

    /**
     * @param int $idSubjectType
     */
    public function setIdSubjectType(int $idSubjectType)
    {
        $this->idSubjectType = $idSubjectType;
    }

    /**
     * @param int $idTeacher
     */
    public function setIdTeacher(int $idTeacher)
    {
        $this->idTeacher = $idTeacher;
    }

    /**
     * @param int $idAudience
     */
    public function setIdAudience(int $idAudience)
    {
        $this->idAudience = $idAudience;
    }

    /**
     * @param int $idHalfLesson
     */
    public function setIdHalfLesson(int $idHalfLesson)
    {
        $this->idHalfLesson = $idHalfLesson;
    }


    public function getAllIdParams(
        $faculyName ,
        $courseNumber,
        $groupNumber,
        $dayName,
        $subgroupNumber,
        $weekType
    )
    {
        $db = DBConnect::connect();

        $sql =
            'SELECT
            faculties.id AS \'idFaculty\',
            courses.id AS \'idCourse\',
            groups.id AS \'idGroup\',
            days.id AS \'idDay\',
            subgroups.id AS \'idSubgroup\',
            week_type.id AS \'idWeekType\'
            FROM faculties
            JOIN courses
            JOIN groups
            JOIN days
            JOIN subgroups
            JOIN week_type
            WHERE faculties.name = :faculyName
            AND courses.number = :courseNumber
            AND groups.number = :groupNumber
            AND days.eng_name = :dayName
            AND subgroups.number = :subgroupNumber
            AND week_type.type = :weekType';


        return $entities = $db->dbQuery($sql,
            [
                'faculyName' => $faculyName,
                'courseNumber' => $courseNumber,
                'groupNumber' => $groupNumber,
                'dayName' => $dayName,
                'subgroupNumber' => $subgroupNumber,
                'weekType' => $weekType,
            ]
        );
    }

    public function getIdSchedule(
        $idFaculty,
        $idCourse,
        $idGroup,
        $idDay,
        $idSubgroup,
        $idWeekType
    )
    {
        $db = DBConnect::connect();

        $sql =
            "SELECT * FROM (
                SELECT * FROM(
                    SELECT *
                    FROM `schedule`
                    WHERE `id_faculty` = :idFaculty
                    AND `id_course` = :idCourse
                    AND`id_group` = :idGroup
                    AND`id_day` = :idDay
                ) AS first_table
                WHERE `id_week_type` IS NULL OR `id_week_type` = :idWeekType
            ) AS second_table
            WHERE id_subgroup IS NULL OR id_subgroup = :idSubgroup";

        return $entities = $db->dbQuery($sql,
            [
                'idFaculty' => $idFaculty,
                'idCourse' => $idCourse,
                'idGroup' => $idGroup,
                'idDay' => $idDay,
                'idWeekType' => $idWeekType,
                'idSubgroup' => $idSubgroup,
            ]
        );

    }

    public function getNameSchedule(
        $idSubject,
        $idSubjectType,
        $idTeacher,
        $idAudience,
        $idLesson
//        $halfLesson,
//        $idWeekType,
//        $idSubgroup
    )
    {
        $db = DBConnect::connect();
        $sql =
            'SELECT subjects.name AS \'subjects\',
            subject_type.name AS \'subjectType\',
            teachers.name AS \'teachers\',
            audience.name AS \'audience\',
            lessons.number AS \'lessons\'
            FROM subjects
            JOIN subject_type
            JOIN teachers
            JOIN audience
            JOIN lessons
            JOIN half_lesson
            JOIN week_type
            JOIN subgroups
            WHERE subjects.id = :idSubject 
            AND subject_type.id = :idSubjectType
            AND teachers.id = :idTeacher
            AND audience.id = :idAudience
            AND lessons.id = :idLesson';

        $params = [
            'idSubject' => $idSubject,
            'idSubjectType' => $idSubjectType,
            'idTeacher' => $idTeacher,
            'idAudience' => $idAudience,
            'idLesson' => $idLesson,
//            'halfLesson' => $halfLesson,
//            'idWeekType' => $idWeekType,
//            'idSubgroup' => $idSubgroup,
        ];

        return $entities = $db->dbQuery($sql, $params);
    }



    /**
     * @return string
     */
    protected static function getTableName()
    {
        return 'schedule';
    }

}

/**
SELECT subjects.name AS 'subjects',
subject_type.name AS 'subjectType',
teachers.name AS 'teachers',
audience.name AS 'audience',
lessons.number AS 'lessons'
FROM subjects
JOIN subject_type
JOIN teachers
JOIN audience
JOIN lessons
WHERE subjects.id = 1
AND subject_type.id = 1
AND teachers.id = 1
AND audience.id = 2
AND lessons.id = 3
 */
//
//$sql =
//    'SELECT
//            id_subject AS \'idSubject\',
//            id_subject_type AS \'idSubjectType\',
//            id_teacher AS \'idTeacher\',
//            id_audience AS \'idAudience\',
//            id_lesson AS \'idlesson\'
//            FROM schedule
//            WHERE id_faculty = :idFaculty
//            AND id_course = :idCourse
//            AND id_group = :idGroup
//            AND id_day = :idDay
////            OR  id_week_type IS NULL
////            AND id_week_type = :idWeekType
////            OR id_subgroup IS NULL
////            AND id_subgroup = :idSubgroup';
///
/// SELECT `id_day`, `id_lesson`, `id_subject`, `id_subject_type`, `id_teacher`, `id_audience`
//FROM `schedule` WHERE `id_week_type` = 1
//OR `id_week_type` IS NULL
//AND `id_subgroup` = 1
//OR`id_subgroup` IS NULL
//AND `id_faculty` = 1
//AND `id_course` = 1
//AND`id_group` = 1

//SELECT *
//FROM (
//    SELECT `id_day`, `id_lesson`, `id_subject`, `id_subject_type`, `id_teacher`, `id_audience`, `id_week_type`,`id_subgroup`
//	FROM `schedule`
//	WHERE `id_week_type` IS NULL OR `id_week_type` = 1
//AND `id_faculty` = 1
//AND `id_course` = 1
//AND`id_group` = 1
//) AS week_table
//WHERE week_table.id_subgroup IS NULL OR week_table.id_subgroup = 1


//SELECT * FROM (
//    SELECT * FROM
//    (
//        SELECT `id_faculty`,`id_course`,`id_group`,`id_day`, `id_lesson`, `id_subject`, `id_subject_type`, `id_teacher`, `id_audience`, `id_week_type`,`id_subgroup`
//        FROM `schedule`
//        WHERE `id_faculty` = 1
//AND `id_course` = 1
//AND`id_group` = 1
//    ) AS first_table
//    WHERE `id_week_type` IS NULL OR `id_week_type` = 1
//) AS second_table
//WHERE id_subgroup IS NULL OR id_subgroup = 1


//`id_faculty`,
//                    `id_course`,
//                    `id_group`,
//                    `id_day`,
//                    `id_lesson`,
//                    `id_half_lesson`,
//                    `id_subject`,
//                    `id_subject_type`,
//                    `id_teacher`,
//                    `id_audience`,
//                    `id_week_type`,
//                    `id_subgroup`