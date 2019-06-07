<?php


namespace App\Controller;

use App\Models\WeekType;
use App\Services\Curl\Curl;
use App\Services\Parser\HtmlParser;
use App\Services\Parser\LnuParser;
use App\Services\Json\ScheduleLnuJson;
use App\Models\Schedule;


class lnuController
{
    public function parseAction()
    {
        $host = 'http://elct.lnu.edu.ua';
        $curlConfig = __DIR__ . '/../../config/curl.php';

        $curl = Curl::app($host, $curlConfig);
//        $curl->setPost([
//            'stream'   => 'M',
//            'kurs'     => '4',
//            'num_gr'   => '1',
//            'num[]:'   => '6',
//            'day[]'    => '1',
//        ]);
//
//        $curl->request('rozk/create_query.php');
//
//        print_r(mb_convert_encoding($curl->getResponseHtml(),"UTF-8","Windows-1251"));






        $parser = new HtmlParser();
        $lnuParser = new LnuParser($parser, $curl);


//        var_dump(count($test1));

//        $lnuParser->run();
//        file_put_contents(__DIR__ . "/../services/parser/data/data.json", json_encode($data));


//        $weekTypeModel = new WeekType();
//        $weekTypeModel = $weekTypeModel->getValueByColumn('type', null);
//        var_dump($weekTypeModel->getId());


        $configJson = __DIR__ . '/../../config/json.php';

        $scheduleLnuJson = new ScheduleLnuJson($configJson);
        $scheduleLnuJson->insertSchedule();

        $data = file_get_contents(__DIR__ . "/../services/parser/data/data.json");

        var_dump(array_unique(json_decode($data, true), SORT_REGULAR));


//        $data = json_decode($data,JSON_PRETTY_PRINT);
//        var_dump($data['M11']['Понеділок']['4']['firstSubgroup']);
//
//        var_dump($data);

        $schedule = new Schedule();
        $schedule->getValueByColumn();


    }


}
