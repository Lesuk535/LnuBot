<?php
use App\Services\Curl\Curl;
use App\Services\Parser;
use App\Services\Telegram\Telegram;
use App\Services\Telegram\Objects\Message;
use App\Services\Routing\SiteRouter;
use App\Services\Routing\ApiRouter;
use App\Models\ActiveRecordEntity;
use App\Services\DBConnect;
use App\Services\Parser\LnuParser;

ini_set('max_execution_time', 900000000000);

require_once __DIR__ . '/../vendor/autoload.php';
//ngrok http --host-header=parser.com 80

//
//$telegramConfig = __DIR__ . '/../config/telegram.php';
//$host = 'http://elct.lnu.edu.ua';
//$curlConfig = __DIR__ . '/../config/curl.php';
////
//$curl = Curl::app($host, $curlConfig);
//
//$_POST['chat_id'] = 'sdfsdfsdf';


$routes = __DIR__ . '/../config/bot_routes.php';
new SiteRouter($routes);
new ApiRouter($routes);


//$curlConfig = __DIR__ . '/../config/curl.php';
//$telegramConfig = __DIR__ . '/../config/telegram.php';
//
//$host = 'https://api.telegram.org';
//
//$curl = Curl::app($host, $curlConfig);
//
//
//$telegram = new Telegram($telegramConfig, $curl);

//
//$telegram->sendMessage([
//    "chat_id" => '366895633',
//    "text" => 'pokemon',
//]);


////$telegram->setWebhook();
//$callback = new \App\Services\Telegram\Objects\CallbackQuery();
//
//
//
////$keyboard = [
////    "keyboard" => [
////            ['/7', '/8', '/9'],
////    ],
////];
//
//
//
//if ($callback->getData() === 'fei') {
//    $telegram->sendMessage([
//        'chat_id' => "366895633",
//        'text' => $callback->getData(),
//    ]);
//}


//$keyboard = [
//    "inline_keyboard" => [[[
//        "text" => "button",
//        "callback_data" => "button_0"
//    ]]]
//];


//print_r(json_encode($keyboard));

//$keyboard = [
//    "inline_keyboard" => [[[
//        "text" => "button",
//        "callback_data" => "button_0"
//    ]]]
//];
//$postfields = [
//    'chat_id' => "$chat_id",
//    'text' => "$reply",
//    'reply_markup' => json_encode($keyboard)
//];

$keyboard = [];

for ($i = 0; $i <= 6; $i++) {
    if (($i % 2) !== 0) {
        $keyboard["inline_keyboard"][][][] = [
            "text" => $i,
            "callback_data" => "/courses/" . $i
        ];
    } else {
        $keyboard["inline_keyboard"][][] = [
            "text" => $i,
            "callback_data" => "/courses/" . $i
        ];
    }
}

var_dump($keyboard);