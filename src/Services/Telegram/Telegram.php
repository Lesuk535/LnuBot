<?php


namespace App\Services\Telegram;

use App\Services\Curl\Curl;


class Telegram
{
    /**
     * @var mixed
     */
    private $config;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var mixed
     */
    private $postResponse;

    /**
     * @var Curl
     */
    private $curl;

    public function __construct(string $file, Curl $curl)
    {
        $this->config = include("$file");
        $this->baseUrl = $this->makeBaseUrl();
        $this->postResponse = $this->getPhpInput();
        $this->curl = $curl;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->config['token'];
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->config['site'];
    }

    /**
     * @return mixed
     */
    public function getPostResponse()
    {
        return $this->postResponse;
    }

    /**
     * @return mixed
     */
    public function getMe()
    {
        return json_decode($this->sendRequest('getMe',[]),true);
    }

    /**
     * @return mixed
     */
    public function getCallbackQuery()
    {
        return $this->postResponse['callback_query']['message']['text'];
    }

    /**
     * @param array $params
     * @return string
     */
    public function replyKeyboardMarkup(array $params)
    {
        return json_encode($params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function sendMessage(array $params)
    {
        return $this->sendRequest('sendMessage', $params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteMessage(array $params)
    {
        return $this->sendRequest('deleteMessage', $params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function editMessageText(array $params)
    {
        return $this->sendRequest('editMessageText', $params);
    }

    /**
     * @return mixed
     */
    public function setWebhook()
    {
        $params = ['url' => $this->config['site']];

        return $this->sendRequest('setWebhook', $params);
    }

    /**
     * @return mixed
     */
    public function removeWebhook()
    {
        return $this->sendRequest('setWebhook', []);
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function sendRequest(string $method, array $params)
    {
        if (!empty($params)) {
            $url = $this->baseUrl . $method . '?' . http_build_query($params);
        } else {
            $url = $this->baseUrl . $method;
        }

        $this->curl->request($url);

        return $this->curl->getResponseHtml();
    }

    /**
     * @return string
     */
    private function makeBaseUrl()
    {
        $format = '/bot%s/';
        return sprintf($format, $this->getToken());
    }

    /**
     * @return mixed
     */
    private function getPhpInput()
    {
        return json_decode(file_get_contents('php://input'), JSON_OBJECT_AS_ARRAY);
    }

}
