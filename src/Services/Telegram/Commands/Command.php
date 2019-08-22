<?php

namespace App\Services\Telegram\Commands;


use App\Services\Curl\Curl;
use App\Services\Telegram\Objects\Message;
use App\Services\Telegram\Telegram;

abstract class Command
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var Telegram
     */
    protected $telegram;

    /**
     * @var array
     */
    protected $messageText;

    public function __construct()
    {
        $this->message = new Message();

        $curlConfig = __DIR__ . '/../../../../config/curl.php';
        $telegramConfig = __DIR__ . '/../../../../config/telegram.php';

        $host = 'https://api.telegram.org';

        $this->curl = Curl::app($host, $curlConfig);
        $this->telegram = new Telegram($telegramConfig, $this->curl);
        $this->messageText = static::getMessageText();
    }

    /**
     * @param array $data
     * @return string
     */
    protected function callbackData(array $data)
    {
        $callbackData = '';

        foreach ($data as $value) {
            $callbackData = trim(sprintf('%s%s/', $callbackData, $value));
        }

        return $callbackData;
    }

    /**
     * @return mixed
     */
    abstract public function handle();

    /**
     * @return array
     */
    abstract protected function getMessageText(): array;

}