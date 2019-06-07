<?php

namespace App\Services\Curl;


class Curl
{

    /**
     * @var resource
     */
    private $ch;

    /**
     * @var string
     */
    private $host;

    /**
     * @var mixed
     */
    private $config;

    private $hostConfig;

    private $defaults;

    private $allHeaders;
    
    private $follow;

    private $posts;
    
    private $headerSwitch;

    private $parseResponse = [];

    private $responseHeaders;

    private $responseHtml;

    const DEFAULTS = 'defaults';

    private function __construct(string $host, string $file)
    {
        $this->ch = curl_init();
        $this->host = $host;
        $this->config = require_once("$file");
        $this->defaults = $this->config['defaults'];
        $this->hostConfig = $this->config[$host];
        $this->allHeaders = $this->hostConfig['setHeaders'];

        $this->set(CURLOPT_RETURNTRANSFER, true);

        $this->initConfig();
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }

    /**
     * @param string $host
     * @param string $file
     * @return Curl
     */
    public static function app(string $host, string $file): self
    {
        return new self($host, $file);
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return array|null
     */
    public function getHeaders(): ?array
    {
        if ($this->allHeaders === null)
            return $this->allHeaders = [];

        return $this->allHeaders;
    }

    /**
     * @return array|null
     */
    public function getResponseHeaders(): ?array
    {
        return $this->responseHeaders;
    }

    /**
     * @return mixed
     */
    public function getResponseHtml()
    {
        return $this->responseHtml;
    }

    /**
     * @return mixed
     */
    public function getFollow()
    {
        return $this->follow;
    }

    /**
     * @return mixed
     */
    public function getHeaderSwitch()
    {
        return $this->headerSwitch;
    }

    /**
     * @return array|null
     */
    public function getPosts(): ?array
    {
        return $this->posts;
    }

    /**
     * @param string $name
     * @param $value
     * @return $this
     */
    public function set(string $name, $value): Curl
    {
        curl_setopt($this->ch, $name, $value);
        return $this;
    }

    /**
     * @param bool $param
     * @return Curl
     */
    public function setFollow(bool $param = true): Curl
    {
        $this->follow;
        return $this->set(CURLOPT_FOLLOWLOCATION, $param);
    }

    /**
     * @param bool $param
     * @return Curl
     */
    public function headerSwitch(bool $param = true): Curl
    {
        $this->headerSwitch = $param;
        return $this->set(CURLOPT_HEADER, $param);
    }

    /**
     * @param string $header
     * @return Curl
     */
    public function setHeader(string $header): Curl
    {
        $this->allHeaders = $this->getHeaders();
        $this->allHeaders[] = $header;
        return $this->set(CURLOPT_HTTPHEADER, $this->allHeaders);
    }

    /**
     * @param array $headers
     * @return Curl
     */
    public function setHeaders(array $headers): Curl
    {
        $this->allHeaders = $this->getHeaders();

        foreach ($headers as $value)
            $this->allHeaders[] = $value;

        return $this->set(CURLOPT_HTTPHEADER, $this->allHeaders);
    }

    /**
     * @param string $headers
     * @return Curl
     */
    public function deleteHeader(string $headers): Curl
    {
        $this->allHeaders = $this->getHeaders();

        foreach ($this->allHeaders as $key => $value) {
            if ($value == $headers)
                unset($this->allHeaders[$key]);
        }

        return $this->set(CURLOPT_HTTPHEADER, $this->allHeaders);
    }

    /**
     * @return Curl
     */
    public function deleteHeaders(): Curl
    {
        $this->allHeaders = [];
        return $this->set(CURLOPT_HTTPHEADER, $this->allHeaders);

    }

    /**
     * @param string $url
     * @return Curl
     */
    public function setReferer(string $url): Curl
    {
        return $this->set(CURLOPT_REFERER, $url);
    }

    /**
     * @param bool $param
     * @return $this
     */
    public function ssl(bool $param = false): Curl
    {
        $this->set(CURLOPT_SSL_VERIFYHOST, $param)
            ->set(CURLOPT_SSL_VERIFYHOST, $param);

        return $this;
    }

    /**
     * @param $data
     * @return Curl
     */
    public function setPost(array $data): Curl
    {
        if (!$data)
            return $this->set(CURLOPT_POST, false);

        $this->posts = $data;

        $this->set(CURLOPT_POST, true)
            ->set(CURLOPT_POSTFIELDS, http_build_query($data));


        return $this;
    }

    /**
     * @param string $file
     * @return Curl
     */
    public function setCookie(string $file): Curl
    {
        $this->set(CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'] . '/' . $file);
        $this->set(CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'] . '/' . $file);
        return $this;
    }

    /**
     * @param string $agent
     * @return Curl
     */
    public function setUserAgent(string $agent): Curl
    {
        return $this->set(CURLOPT_USERAGENT, $agent);
    }

    private function initConfig()
    {
        if (isset($this->hostConfig['defaults']) && $this->hostConfig['defaults'] === true)
            $this->setConfig($this->defaults);

        var_dump($this->hostConfig);

        $this->setConfig($this->hostConfig);
    }

    /**
     * @param array $params
     */
    private function setConfig(array $params)
    {
        foreach ($params as $key => $value) {
            if ($key === static::DEFAULTS)
                continue;

            $this->$key($value);
        }
    }
    /**
     * @param string $url
     * @return mixed
     */
    public function request(string $url)
    {
        $this->set(CURLOPT_URL, $this->makeUrl($url));
        $this->set(CURLINFO_HEADER_OUT, true);
        $data = curl_exec($this->ch);
        $parseResponse = $this->parseResponse($data);
//        var_dump(curl_getinfo($this->ch));
        return $parseResponse;
    }

    /**
     * @param string $url
     * @return string
     */
    private function makeUrl(string $url): string
    {
        if ($url[0] !='/')
            $url = '/' . $url;

        return $this->host . $url;
    }

    /**
     * @param $data
     * @return array
     */
    private function parseResponse($data): array
    {
        if (!isset($this->headerSwitch) || !$this->headerSwitch) {
            $this->responseHtml = $data;

            return $this->parseResponse = [
                'headers' => [],
                'html' => $data
            ];
        }

        $curlInfo = curl_getinfo($this->ch);

        $headers = trim(substr($data, 0, $curlInfo['header_size']));
        $headers =str_replace('\r', '\n', $headers);
        $headers = str_replace('\r\n', 'n', $headers);
        $headers = explode('\n\n', $headers);
        $headersPart = end($headers);

        $bodyPart = trim(substr($data, $curlInfo['header_size']));

        $lines = explode('\n', $headersPart);

        $headersPart = [];
        $headersPart['start'] = $lines[0];

        for ($i = 1; $i < count($lines); $i++) {
            $colonPos = strpos($lines[$i], ':');
            $name = substr($lines[$i], 0, $colonPos);
            $value = trim(substr($lines[$i], $colonPos + 1));
            $headersPart[$name] = $value;
        }

        $this->responseHeaders = $headers;
        $this->responseHtml = $bodyPart;

        return $this->parseResponse = [
          'headers' => $headersPart,
          'html' => $bodyPart
        ];
    }

}

