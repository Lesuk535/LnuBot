<?php


namespace App\Services\Parser;

use App\Services\Curl\Curl;


class LnuParser
{
    /**
     * @var HtmlParser
     */
    private $htmlParser;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var array
     */
    private $data;

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
    private $subjects;

    /**
     * @var array
     */
    private $teacher;

    /**
     * @var array
     */
    private $audience;

    /**
     * @var array
     */
    private $subjectType;

    public function __construct(HtmlParser $htmlParser, Curl $curl)
    {
        $this->htmlParser = $htmlParser;
        $this->curl = $curl;
        $this->days = [
            'Понеділок',
            'Вівторок',
            'Середа',
            'Четвер',
            'Пятниця'
        ];
        $this->faculties = [
            'FEM' => 'M',
            'FEC' => 'C',
            'FEI' => ''
        ];
    }

    public function run()
    {
        $this->parseFacultySubjects();
//        $this->parseWeekSubjects('M', '1', '1');
//        var_dump($this->data);
        $this->saveParseData(['data']);

//        var_dump($this->audience);

        return $this->data;
    }

    private function saveParseData(array $data)
    {
        foreach ($data as $value) {
            file_put_contents(__DIR__ . "/data/$value.json", json_encode($this->$value));
        }
    }

    /**
     * @param string $faculty
     * @param string $kurs
     * @param string $numGroup
     * @param string $day
     */
    private function parseClass(
        string $faculty,
        string $kurs,
        string $numGroup,
        string $day
    )
    {
        for ($i = 1; $i <= 6; $i++) {

            $this->curl->setPost([
                'stream'   => $faculty,
                'kurs'     => $kurs,
                'num_gr'   => $numGroup,
                'num[]:'   => $i,
                'day[]'    => $day,
            ]);

            if (empty($faculty))
                $faculty = 'I';

            $response = $this->getRozkResponse();
            $this->htmlParser->setString($response);
            $this->data[$faculty.$kurs.$numGroup][$this->days[$day-1]][$i] = $this->getData();



            if ($faculty === 'I')
                $faculty = '';
        }
    }

    /**
     * @param string $faculty
     * @param string $kurs
     * @param string $numGroup
     */
    private function parseWeekSubjects(
        string $faculty,
        string $kurs,
        string $numGroup
    )
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->parseClass($faculty, $kurs, $numGroup, $i);
        }
    }

    /**
     * @param string $faculty
     * @param string $kurs
     */
    private function parseGroupSubjects(
        string $faculty,
        string $kurs
    )
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->parseWeekSubjects($faculty, $kurs, $i);
        }
    }

    /**
     * @param string $faculty
     */
    private function parseCourseSubjects(string $faculty)
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->parseGroupSubjects($faculty, $i);
        }
    }

    private function parseFacultySubjects()
    {
        foreach ($this->faculties as $value) {
            $this->parseCourseSubjects($value);
        }
    }

    /**
     * @return mixed|string
     */
    private function getRozkResponse()
    {
        $this->curl->request('rozk/create_query.php');

        $fei = $this->curl->getPosts()['stream'];

        if (empty($fei) || !isset($fei)) {
            $response = $this->curl->getResponseHtml();
            $response = mb_convert_encoding($response,"UTF-8","Windows-1251");

            $this->htmlParser->setString($response);
            $response = $this->getFEI();

        } else {
            $response = $this->curl->getResponseHtml();
            $response = mb_convert_encoding($response,"UTF-8","Windows-1251");

        }

        sleep(mt_rand(0, 1));

        return $response;
    }

    /**
     * @return array|bool|null
     */
    private function getData()
    {
        $data = $this->getBigBlock();

        if ($data === false)
            $data = $this->getBigBlock();

        if ($data === false)
            $data = $this->getSubgroupBlock();

        if ($data === false)
            $data = $this->getWeekTypeBlock();

        if ($data === false)
            $data = $this->getSubgroupAndWeekTypeBlock();

        return $data;
    }

    /**
     * @return bool|string
     */
    public function getFEI()
    {
        $moveParams = [
            '</tr>', '</td>', '</table>', '</table>'
        ];

        $faculty = false;

        $pos = count($moveParams);

        $i = 0;

        while ($faculty === false && $i <= 3) {

            $this->htmlParser->defaultCursor();

            $this->htmlParser->moveTo(
                '<table border="0" cellpadding="0" cellspacing="1" width="100%" height="100%"'
            );

            $this->htmlParser->moveAfterSeveralTimes($moveParams);
            $faculty = $this->htmlParser->subTag('<table','table');

            unset($moveParams[$pos--]);
            $i++;

        }

        if ($this->parseLastNameFaculty() !== 'ФеІ')
            $faculty = false;


        return $faculty;

    }

    /**
     * @return bool|string
     */
    private function parseLastNameFaculty()
    {
        $blockName = '';

        $moveParams = [
            '<tr>', '</table>', '</table>', '<table>', '<th>', '<th align=center>', '<font color=\'#FFFFFF\'>'
        ];

        $i = 0;

        while (empty($blockName) && $i <= 2) {

            $this->htmlParser->defaultCursor();

            $this->htmlParser->moveTo('<table border=0 width=100% cellspacing=1 cellpadding=0>');
            $this->htmlParser->moveAfterSeveralTimes($moveParams);

            $blockName = $this->htmlParser->readTo('<');

            $i++;
            unset($moveParams[$i]);

        }

        $blockName = substr($blockName, 0, strrpos($blockName, '-'));


        return $blockName;
    }

    /**
     * @return array|bool|null
     */
    private function getBigBlock()
    {
        $block = $this->htmlParser->moveAfter(
            '<td class=big bgcolor=#EEEEEE nowrap width=100% colspan=2  height=100% rowspan=2 >'
        );

        if ($block === false)
            return false;

        $data = $this->getCenterBlock();

        return $data;
    }

    /**
     * @return array|bool|null
     */
    private function getSubgroupBlock()
    {
        $block = $this->htmlParser->moveTo(
            '<td  bgcolor=#EEEEEE nowrap width=50% height=100% rowspan=2 >'
        );

        if ($block === false)
            return false;
        
        $all = $this->getSubBlock('Subgroup', 'td');
        return $this->getSubBlockData($all);
    }

    /**
     * @return array|bool|null
     */
    private function getWeekTypeBlock()
    {
        $block = $this->htmlParser->moveTo(
            '<td  bgcolor=#EEEEEE nowrap width=100% colspan=2  height=50% >'
        );

        if ($block === false)
            return false;

        $all = $this->getSubBlock('WeekType', 'td');
        return $this->getSubBlockData($all);

    }

    /**
     * @return array
     */
    private function getSubgroupAndWeekTypeBlock(): array
    {
        $this->htmlParser->moveAfter('<table border=0 cellpadding=0 cellspacing=1 width=100% height=100%>');
        $this->htmlParser->moveAfter('<tbody>');

        $types = $this->getSubBlock('WeekType', 'tr');

        $data = [];

        foreach ($types as $key => $value) {
            $this->htmlParser->setString($types[$key]);
            $SubBlocks = $this->getSubBlock('Subgroup', 'td');
            $data[$key] = $this->getSubBlockData($SubBlocks);
        }

        return $data;
    }

    /**
     * @param string $dataName
     * @param string $subTag
     * @return array
     */
    private function getSubBlock(string $dataName, string $subTag): array
    {
        $first = $this->htmlParser->subTag("<$subTag", $subTag);

        $this->htmlParser->moveAfter("</$subTag>");

        $second = $this->htmlParser->subTag("<$subTag", $subTag);

        return [
            'first' . $dataName => $first,
            'second'. $dataName=> $second
        ];
    }

    /**
     * @param array $all
     * @return array|null
     */
    private function getSubBlockData(array $all): ?array
    {
        foreach ($all as $key => $value) {
            $this->htmlParser->setString($value);
            $center = $this->htmlParser->moveAfter('<center>');

            if ($center === false) {
                $all[$key] = [];
            } else {
                $data = $this->getCenterBlock();
                $all[$key] = $data;
            }
        }

        return $all;
    }

    /**
     * @return array|null
     */
    private function getCenterBlock(): ?array
    {
        $this->htmlParser->moveAfter('<b>');
        $this->htmlParser->moveAfter('<center>');

        $subjectAndSubType = $this->htmlParser->readTo('<');

        $this->htmlParser->moveAfter('<br>');
        $this->htmlParser->moveAfter('<br>');

        $teacher = $this->htmlParser->readTo('<');

        $this->htmlParser->moveAfter('<font color=red>');
        $this->htmlParser->moveAfter('<i>');

        $audience = $this->htmlParser->readTo('<');

        preg_match('#\((.*?)\)#', $subjectAndSubType, $match);

        $subjectPos = strpos($subjectAndSubType, '(');

        $subject = trim(substr($subjectAndSubType, 0, $subjectPos));

        $this->subjects[] = $subject;
        $this->teacher[] = $teacher;
        $this->audience[] = $audience;

        $subjectType = $match[1];

        $this->subjectType[] = $subjectType;

        return [
            'subject' => $subject,
            'subjectType' => $subjectType,
            'teacher' => $teacher,
            'audience' => $audience
        ];
    }

}
