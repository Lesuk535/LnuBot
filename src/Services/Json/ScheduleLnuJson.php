<?php


namespace App\Services\Json;

use App\Models\ActiveRecordEntity;
use App\Models\Audience;
use App\Models\Courses;
use App\Models\Days;
use App\Models\Faculties;
use App\Models\Groups;
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

    public function __construct(string $config)
    {
        $this->config = require_once ("$config");

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
        $this->insertArrayToDb($audience, 'setName', $this->getAudience());
    }

    /**
     * @param Subjects $subjects
     */
    public function insertSubjects(Subjects $subjects)
    {
        $this->insertArrayToDb($subjects, 'setName', $this->getSubjects());
    }

    /**
     * @param SubjectType $subjectType
     */
    public function insertSubjectType(SubjectType $subjectType)
    {
        $this->insertArrayToDb($subjectType, 'setName', $this->getSubjectType());
    }

    /**
     * @param Teachers $teachers
     */
    public function insertTeachers(Teachers $teachers)
    {
        $this->insertArrayToDb($teachers, 'setName', $this->getTeachers());
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
        for ($i = 1; $i <= 6; $i++) {
            $weekTypes = $this->getWeekTypesKey(
                $faculty, $course, $group, $day, $i
            );

            $subgroups = $this->getSubgroupsKeyByLesson(
                $faculty, $course, $group, $day, $i
            );

            $lessonInfo = $this->jsonData[$faculty.$course.$group][$day][$i];

            if (!empty($weekTypes) || count($weekTypes) > 0) {
                foreach ($weekTypes as $key => $value) {
                    if ($value === 'firstWeekType') {
                        $this->getWeekType(
                            $faculty, $course, $group, $day, $i, 'firstWeekType'
                        );
                    } elseif ($value === 'secondWeekType') {
                        $this->getWeekType(
                            $faculty, $course, $group, $day, $i, 'secondWeekType');
                    }
                }
            } elseif (!empty($subgroups) || count($subgroups) > 0) {
                $subgroups = $this->getSubgroupsKeyByLesson(
                    $faculty, $course, $group, $day, $i
                );

                foreach ($subgroups as $value) {
                    if ($value === 'firstSubgroup') {
                        $this->getSubgroupByLesson(
                            $faculty, $course, $group, $day, $i, 'firstSubgroup'
                        );
                    } else {
                        $this->getSubgroupByLesson(
                            $faculty, $course, $group, $day, $i, 'secondSubgroup'
                        );
                    }
                }
            } else {

                $id = $this->getIdByArray(
                    $faculty, $course, $group, $day, $i, '', '', $lessonInfo
                );

                $schedule = new Schedule();


                $this->insertScheduleToDb($schedule,$id);

                //TODO Кінець, треба метод, що інсертить всі дані
            }

        }
    }

    /**
     * @param string $faculty
     * @param string $course
     * @param string $group
     * @param string $day
     * @param string $lesson
     * @return array
     */
    private function getWeekTypesKey(
        string $faculty,
        string $course,
        string $group,
        string $day,
        string $lesson
    )
    {
        $weekTypes =[];

        foreach ($this->weekType as $value) {

            $data = $this->jsonData[$faculty.$course.$group][$day][$lesson][$value];

            if (!empty($data))
                $weekTypes[] = $value;
        }

        return $weekTypes;
    }

    /**
     * @param string $faculty
     * @param string $course
     * @param string $group
     * @param string $day
     * @param string $lesson
     * @return array
     */
    private function getSubgroupsKeyByLesson(
        string $faculty,
        string $course,
        string $group,
        string $day,
        string $lesson
    )
    {
        $subgroups = [];

        foreach ($this->subgroups as $key => $value) {
            $data = $this->jsonData[$faculty.$course.$group][$day][$lesson][$key];

            if (!empty($data)) {
                $subgroups[] = $key;
            }

        }

        return $subgroups;
    }

    /**
     * @param string $faculty
     * @param string $course
     * @param string $group
     * @param string $day
     * @param string $lesson
     * @param string $weekType
     */
    private function getWeekType(
        string $faculty, 
        string $course, 
        string $group, 
        string $day, 
        string $lesson,
        string $weekType
    )
    {
        $subgroups = $this->getSubgroupsKeyByWeekType(
            $faculty, $course, $group, $day, $lesson, $weekType
        );

        $lessonInfo = $this->jsonData[$faculty.$course.$group][$day][$lesson][$weekType];
        $lessonInfoKeys= array_keys($lessonInfo);

        if (!empty($subgroups) || count($subgroups) > 0) {
            foreach ($subgroups as $value) {
                if ($value === 'firstSubgroup') {
                    $this->getSubgroupByWeekType(
                        $faculty, $course, $group, $day, $lesson, $weekType, 'firstSubgroup'
                    );
                } else {
                    $this->getSubgroupByWeekType(
                        $faculty, $course, $group, $day, $lesson, $weekType, 'secondSubgroup'
                    );
                }
            }
        } elseif (count($lessonInfoKeys) > 2) {

            $id = $this->getIdByArray(
                $faculty, $course, $group, $day, $lesson, $weekType, '', $lessonInfo
            );

            $schedule = new Schedule();


            $this->insertScheduleToDb($schedule,$id);



            //TODO Кінець, треба метод, що інсертить всі дані
        }
    }

    /**
     * @param string $faculty
     * @param string $course
     * @param string $group
     * @param string $day
     * @param string $lesson
     * @param string $subgroup
     */
    private function getSubgroupByLesson(
        string $faculty,
        string $course,
        string $group,
        string $day,
        string $lesson,
        string $subgroup
    )
    {
        $lessonInfo = $this->jsonData[$faculty.$course.$group][$day][$lesson][$subgroup];

        $id = $this->getIdByArray(
            $faculty, $course, $group, $day, $lesson, '', $subgroup, $lessonInfo
        );

        $schedule = new Schedule();

        $this->insertScheduleToDb($schedule, $id);

        //TODO Кінець, треба метод, що інсертить всі дані
    }

    /**
     * @param string $faculty
     * @param string $course
     * @param string $group
     * @param string $day
     * @param string $lesson
     * @param string $weekType
     * @param string $subgroup
     */
    private function getSubgroupByWeekType(
        string $faculty,
        string $course,
        string $group,
        string $day,
        string $lesson,
        string $weekType,
        string $subgroup
    )
    {
        $lessonInfo = $this->jsonData[$faculty.$course.$group][$day][$lesson][$weekType][$subgroup];

        $id = $this->getIdByArray(
            $faculty, $course, $group, $day, $lesson, $weekType, $subgroup, $lessonInfo
        );

        $schedule = new Schedule();

        $this->insertScheduleToDb($schedule, $id);

        //TODO Кінець, треба метод, що інсертить всі дані
    }

    /**
     * @param string $faculty
     * @param string $course
     * @param string $group
     * @param string $day
     * @param string $lesson
     * @param string $weekType
     * @param string $subgroup
     * @param array $lessonInfo
     * @return array
     */
    private function getIdByArray(
        string $faculty,
        string $course,
        string $group,
        string $day,
        string $lesson,
        string $weekType,
        string $subgroup,
        array $lessonInfo
    ): array
    {
        return [
            'id_faculty' => $this->getFacultyId($faculty),
            'id_course' => $this->getCourseId($course),
            'id_group' => $this->getGroupId($group),
            'id_day' => $this->getDayId($day),
            'id_lesson' => $this->getLessonId($lesson),
            'id_week_type' => $this->getWeekTypeId($weekType),
            'id_subgroup' => $this->getSubgroupId($subgroup),
            'id_subject' => $this->getSubjectId($lessonInfo['subject']),
            'id_subject_type' => $this->getSubjectTypeId($lessonInfo['subjectType']),
            'id_teacher' => $this->getTeachersId($lessonInfo['teacher']),
            'id_audience' => $this->getAudienceId($lessonInfo['audience'])
        ];
    }

    /**
     * @param string $faculty
     * @param string $course
     * @param string $group
     * @param string $day
     * @param string $lesson
     * @param string $weekType
     * @return array
     */
    private function getSubgroupsKeyByWeekType(
        string $faculty, 
        string $course, 
        string $group, 
        string $day, 
        string $lesson, 
        string $weekType
    )
    {
        $subgroups = [];

        foreach ($this->subgroups as $key => $value) {
            $data = $this->jsonData[$faculty.$course.$group][$day][$lesson][$weekType][$key];

            if (!empty($data)) {
                $subgroups[] = $key;
            }

        }

        return $subgroups;
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
        if ($weekType === 'firstWeekType') {
            $weekType = 'numerator';
        } elseif ($weekType === 'secondWeekType') {
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
        if ($subgroup === 'firstSubgroup') {
            $subgroup = '1';
        } elseif ($subgroup === 'secondSubgroup') {
            $subgroup = '2';
        }

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
     * @param $setColumn
     * @param $data
     */
    private function insertArrayToDb(ActiveRecordEntity $model, $setColumn, $data)
    {
        $data = array_unique($data, SORT_REGULAR);

        foreach ($data as $value) {
            $model->$setColumn($value);
            $model->save();
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
        }

//        $schedule->save();

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