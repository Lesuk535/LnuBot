<?php


namespace App\Services\Json;

use App\Models\ActiveRecordEntity;
use App\Models\Audience;
use App\Models\Courses;
use App\Models\Days;
use App\Models\Faculties;
use App\Models\Groups;
use App\Models\HalfLesson;
use App\Models\Lessons;
use App\Models\Subgroups;
use App\Models\Subjects;
use App\Models\SubjectType;
use App\Models\Teachers;
use App\Models\WeekType;
use App\Models\Schedule;


class ScheduleLnuJson
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var mixed
     */
    private $jsonData;

    /**
     * @var array
     */
    private $days;

    /**
     * @var array
     */
    private $faculties;

    /**
     * @var array
     */
    private $weekType;

    /**
     * @var array
     */
    private $subgroups;

    public function __construct()
    {
        $this->config = require_once (__DIR__ . '/../../../config/json.php');

        $className = $this->getClassName();

        $this->config = $this->config[$className];

        $this->jsonData = $this->getData();

        $this->days = $this->config['days'];

        $this->faculties = $this->config['faculties'];

        $this->weekType = $this->config['weekType'];

        $this->subgroups = $this->config['subgroups'];
    }

    /**
     * @return array
     */
    public function getAudience(): ?array
    {
        return json_decode(
            file_get_contents($this->config['path']['audience']),
            true
        );
    }

    /**
     * @return array
     */
    public function getSubjects(): ?array
    {
        return json_decode(
            file_get_contents($this->config['path']['subjects']),
            true
        );
    }

    /**
     * @return array
     */
    public function getSubjectType(): ?array
    {
        return json_decode(
            file_get_contents($this->config['path']['subjectType']),
            true
        );
    }

    /**
     * @return array
     */
    public function getTeachers(): ?array
    {
        return json_decode(
            file_get_contents($this->config['path']['teachers']),
            true
        );
    }

    /**
     * @return array
     */
    public function getData(): ?array
    {
        return json_decode(
            file_get_contents($this->config['path']['data']),
            true
        );
    }

    /**
     * @param Audience $audience
     */
    public function insertAudience(Audience $audience)
    {
        $this->insertArrayToDb($audience, 'name', $this->getAudience());
    }

    /**
     * @param Subjects $subjects
     */
    public function insertSubjects(Subjects $subjects)
    {
        $this->insertArrayToDb($subjects, 'name', $this->getSubjects());
    }

    /**
     * @param SubjectType $subjectType
     */
    public function insertSubjectType(SubjectType $subjectType)
    {
        $this->insertArrayToDb($subjectType, 'name', $this->getSubjectType());
    }

    /**
     * @param Teachers $teachers
     */
    public function insertTeachers(Teachers $teachers)
    {
        $this->insertArrayToDb($teachers, 'name', $this->getTeachers());
    }

    public function insertSchedule()
    {
        $this->getFaculty();
    }

    private function getFaculty()
    {
        foreach ($this->faculties as $value) {
            $this->getCourse($value);
        }
    }

    /**
     * @param string $faculty
     */
    private function getCourse(string $faculty)
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->getGroup($faculty, $i);
        }
    }

    /**
     * @param string $faculty
     * @param string $course
     */
    private function getGroup(string $faculty, string $course)
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->getDay($faculty, $course, $i);
        }
    }

    /**
     * @param string $faculty
     * @param string $course
     * @param string $group
     */
    private function getDay(string $faculty, string $course, string $group)
    {
        foreach ($this->days as $value) {
            $this->getLesson($faculty, $course, $group, $value);
        }
    }

    /**
     * @param string $faculty
     * @param string $course
     * @param string $group
     * @param string $day
     */
    private function getLesson(
        string $faculty,
        string $course,
        string $group,
        string $day
    )
    {
        $schedule = new Schedule();

        for ($i = 1; $i <= 6; $i++) {

            $lessons = $this->jsonData[$faculty.$course.$group][$day][$i];

            $hasLessonType = $this->hasLessonType($lessons);

            if (empty($lessons))
                continue;

            if ($hasLessonType === true) {
                $this->getLessonType($faculty, $course, $group, $day, $i, $lessons);
            } else {
                $id = $this->getIdByArray($faculty, $course, $group, $day, $i, $lessons);
                $this->insertScheduleToDb($schedule, $id);
            }
        }

    }

    public function getLessonType(
        string $faculty,
        string $course,
        string $group,
        string $day,
        int $lesson,
        array $data
    )
    {

        $schedule = new Schedule();

        foreach ($data as $key => $value ) {

            if (empty($value))
                continue;

            $id = $this->getIdByArray($faculty, $course, $group, $day, $lesson, $value);
            $this->insertScheduleToDb($schedule, $id);

        }

    }

    public function hasLessonType(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param string $faculty
     * @param string $course
     * @param string $group
     * @param string $day
     * @param string $lesson
     * @param array $lessonInfo
     * @return array
     */
    private function getIdByArray(
        string $faculty,
        string $course,
        string $group,
        string $day,
        string $lesson,
        array $lessonInfo
    ): array
    {
        return [
            'id_faculty' => $this->getFacultyId($faculty),
            'id_course' => $this->getCourseId($course),
            'id_group' => $this->getGroupId($group),
            'id_day' => $this->getDayId($day),
            'id_lesson' => $this->getLessonId($lesson),
            'id_week_type' => $this->getWeekTypeId($lessonInfo['weekType']),
            'id_subgroup' => $this->getSubgroupId($lessonInfo['subgroup']),
            'id_subject' => $this->getSubjectId($lessonInfo['subject']),
            'id_subject_type' => $this->getSubjectTypeId($lessonInfo['subjectType']),
            'id_teacher' => $this->getTeachersId($lessonInfo['teacher']),
            'id_audience' => $this->getAudienceId($lessonInfo['audience']),
            'id_half_lesson' => $this->getHalfLessonId($lessonInfo['halfLesson'])
        ];
    }


    /**
     * @param string $faculty
     * @return int
     */
    private function getFacultyId(string $faculty)
    {
        $facultyModel = new Faculties();
        return $this->getId($facultyModel, 'name', $faculty);
    }

    /**
     * @param string $course
     * @return int
     */
    private function getCourseId(string $course)
    {
        $courseModel = new Courses();
        return $this->getId($courseModel,'number', $course);
    }

    /**
     * @param string $group
     * @return int
     */
    private function getGroupId(string $group)
    {
        $groupModel = new Groups();
        return $this->getId($groupModel,'number', $group);

    }

    /**
     * @param string $day
     * @return int
     */
    private function getDayId(string $day)
    {
        $dayModel = new Days();
        return $this->getId($dayModel,'name', $day);
    }

    /**
     * @param string $lesson
     * @return int
     */
    private function getLessonId(string $lesson)
    {
        $lessonModel = new Lessons();
        return $this->getId($lessonModel,'number', $lesson);
    }

    /**
     * @param string $weekType
     * @return int
     */
    private function getWeekTypeId(string $weekType)
    {
        if ($weekType === 'чис.') {
            $weekType = 'numerator';
        } elseif ($weekType === 'знам.') {
            $weekType = 'denominator';
        }

        $weekTypeModel = new WeekType();
        return $this->getId($weekTypeModel, 'type', $weekType);
    }

    /**
     * @param string $subgroup
     * @return int
     */
    private function getSubgroupId(string $subgroup)
    {
        $subgroupsModel = new Subgroups();
        return $this->getId($subgroupsModel, 'number', $subgroup);
    }

    /**
     * @param string $subject
     * @return int
     */
    private function getSubjectId(string $subject)
    {
        $subjectModel = new Subjects();
        return $this->getId($subjectModel, 'name', $subject);
    }

    /**
     * @param string $subjectType
     * @return int
     */
    private function getSubjectTypeId(string $subjectType)
    {
        $subjectTypeModel = new SubjectType();
        return $this->getId($subjectTypeModel, 'name', $subjectType);
    }

    /**
     * @param string $teachers
     * @return int
     */
    private function getTeachersId(string $teachers)
    {
        $teachersModel = new Teachers();
        return $this->getId($teachersModel, 'name', $teachers);
    }

    /**
     * @param string $audience
     * @return int
     */
    private function getAudienceId(string $audience)
    {
        $audienceModel = new Audience();
        return $this->getId($audienceModel, 'name', $audience);
    }

    private function getHalfLessonId(string $halfLesson)
    {
        $halfLessonModel = new HalfLesson();
        return $this->getId($halfLessonModel, 'number', $halfLesson);
    }

    /**
     * @param ActiveRecordEntity $model
     * @param string $columnName
     * @param string $value
     * @return int
     */
    private function getId(ActiveRecordEntity $model, string $columnName, string $value): ?int
    {
        $model = $model->getValueByColumn($columnName, $value);
        if (empty($model) || !isset($model))
            return null;
        return $model->getId();
    }

    /**
     * @param ActiveRecordEntity $model
     * @param $column
     * @param $data
     */
    private function insertArrayToDb(ActiveRecordEntity $model, $column, $data)
    {
        $data = array_unique($data, SORT_REGULAR);
        $setColumn = 'set' . ucfirst($column);

        foreach ($data as $value) {
            $model->$setColumn($value);

            $id = $model->getIdByColumn('name', $value);

            if ($id !== null) {
                $id = $id->getId();
                $model->setId($id);
            }

            $model->save();
            $model->setId(null);
        }
    }

    /**
     * @param ActiveRecordEntity $schedule
     * @param array $id
     */
    private function insertScheduleToDb(ActiveRecordEntity $schedule, array $id)
    {
        foreach ($id as $key => $value) {

            if ($value === null)
                continue;

            $column = 'set_' . $key;
            $column = $this->underscoreToCamelCase($column);

            $schedule->$column($value);
            $schedule->setId(null);
        }

        $schedule->save();

    }

    /**
     * @param string $source
     * @return string
     */
    private function underscoreToCamelCase(string $source): string
    {
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    /**
     * @return string
     */
    private function getClassName()
    {
        return 'ScheduleLnuJson';
    }

}