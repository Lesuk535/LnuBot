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
     * @return string
     */
    protected static function getTableName()
    {
        return 'schedule';
    }
}