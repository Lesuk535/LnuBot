<?php
use App\Services\Routing\SiteRouter;
use App\Services\Routing\ApiRouter;
use App\Services\Telegram\Telegram;
use App\Services\Curl\Curl;


ini_set('max_execution_time', 900000000000);

require_once __DIR__ . '/../vendor/autoload.php';
$routes = __DIR__ . '/../config/bot_routes.php';

//new SiteRouter($routes);
new ApiRouter($routes);

$telegramConfig = __DIR__ . '/../config/telegram.php';
$curlConfig = __DIR__ . '/../config/curl.php';
$host = 'https://api.telegram.org';


$curl = Curl::app($host, $curlConfig);
$telegram = new Telegram($telegramConfig, $curl);

$telegram->setWebhook();

