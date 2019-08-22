<?php


namespace App\Controller;

use App\Services\Curl\Curl;
use App\Services\Parser\HtmlParser;
use App\Services\Parser\LnuParser;


class lnuController
{
    public function parseAction()
    {
        $host = 'http://elct.lnu.edu.ua';
        $curlConfig = __DIR__ . '/../../config/curl.php';

        $curl = Curl::app($host, $curlConfig);


        $data = file_get_contents(__DIR__ . "/../services/parser/data/data.json");

        var_dump(json_decode($data, true));


        $parser = new HtmlParser();
        $lnuParser = new LnuParser($parser, $curl);


//        $lnuParser->run();

    }


}
