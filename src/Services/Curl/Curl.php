<?php

namespace App\Services\Curl;

/**
 * cURL - це встроєна бібліотека в пхп, яка допомагає робити HTTP запити на сервер.
 * потрібно для того, щоб при парсингу видавати себе за реального користувача, а не скрипт.
 *
 */
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

    /**
     * ініціалізуємо конфіг та підключаємо конфігураційний файл
     * параметр $host - це базаво частина урла без слешу на кінці, передаємо його, щоб з конфігу витягнути
     * конкретно цей сайт, що нам потрібно, а не якийсь інший.
     *
     * @param string $host
     * @param string $file
     */
    private function __construct(string $host, string $file)
    {
        $this->ch = curl_init();
        $this->host = $host;
        $this->config = require_once("$file");
        $this->defaults = $this->config['defaults']; // дефолтні настройки
        $this->hostConfig = $this->config[$host]; // сайт на який ми робимо запити
        $this->allHeaders = $this->hostConfig['setHeaders']; // витягуємо хедери

        $this->set(CURLOPT_RETURNTRANSFER, true); // так курл повертатиме інформацію, а не пичататиме напряму

        $this->initConfig();
    }

    //при завершенні скрипта завершає сеан з кУРЛ.
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
     *
     * гетери говорять самі за себе, тому не бачу сенсу їх описувати
     *
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
     *
     * метод встановлює параметер для сеансу з курл і повертає сам себе, що дозволяє як в джейквері ланцюжком
     * сетерети параметри, типу ->setFollow()->setHeader;
     *
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
     *
     * це, щоб курл міг працювати з перенаправленнями
     *
     * @param bool $param
     * @return Curl
     */
    public function setFollow(bool $param = true): Curl
    {
        $this->follow = $param;
        return $this->set(CURLOPT_FOLLOWLOCATION, $param);
    }

    /**
     *
     * якщо тру, шо хедер буде включений у вивід
     *
     * @param bool $param
     * @return Curl
     */
    public function headerSwitch(bool $param = true): Curl
    {
        $this->headerSwitch = $param;
        return $this->set(CURLOPT_HEADER, $param);
    }

    /**
     *
     * тут ми встановлюємо окремо один хедер
     *
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
     *
     * тут ми передаємо масив полів HTTP для їх встановлення
     *
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
     *
     * видаляємо окремий хедер
     *
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
     *
     * видаляємо всі хедери
     *
     * @return Curl
     */
    public function deleteHeaders(): Curl
    {
        $this->allHeaders = [];
        return $this->set(CURLOPT_HTTPHEADER, $this->allHeaders);

    }

    /**
     *
     * реферер говорить з якої сторінки прийшов на сайт користувач.
     *
     * @param string $url
     * @return Curl
     */
    public function setReferer(string $url): Curl
    {
        return $this->set(CURLOPT_REFERER, $url);
    }

    /**
     *
     * ця штука, вроді, дозволяє ігнорувати наявність чи відсутність ссл сертифікату,
     *
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
     *
     * масивом передає пост запити
     *
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
     *
     * зберігаємо куки
     *
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
     *
     * каже серверу з якого браузера ти сидиш
     *
     * @param string $agent
     * @return Curl
     */
    public function setUserAgent(string $agent): Curl
    {
        return $this->set(CURLOPT_USERAGENT, $agent);
    }

    /**
     * ініціалізуємо конфіг
     */
    private function initConfig()
    {
        /**
         * провірка чи є включені дефолтні настройки, якщо тру, то запускаємо до них сетКонфіг
         * далі запускаємо setConfig для настройок конкретного хаста
         */

        if (isset($this->hostConfig['defaults']) && $this->hostConfig['defaults'] === true)
            $this->setConfig($this->defaults);

        $this->setConfig($this->hostConfig);
    }

    /**
     *
     * передаємо масив з параметрами до певного сайту, або дефолтних настройок, проходимо циклом, якщо ключ дорівнєю
     * 'defaults', то пропускаємо ітерацію. у всіх інших параметрах, ключ виступає у ролі методу в цьому класі, а
     * значення у ролі параметра метода, типу у конфігу в нас є
        'setPost' => [
            'objall'   => 'all',
            'audall'   => 'all',
            'teachall' => 'all',
            'search'   => 'Search'
        ],
     *
     * в даному випадку строчк $this->$key($value); означатиме $this->setPost(['objall'   => 'all','audall'   => 'all',])
     *
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
     *
     * робить запит на сторінку та повертає результат, в урл передається все, що після $host
     *
     * @param string $url
     * @return mixed
     */
    public function request(string $url)
    {
        $this->set(CURLOPT_URL, $this->makeUrl($url));
        $this->set(CURLINFO_HEADER_OUT, true);
        $data = curl_exec($this->ch);
        $parseResponse = $this->parseResponse($data);
        return $parseResponse;
    }

    /**
     *
     * робить поноцынну урл силку
     *
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
     *
     * тут ми парсимо відповідь від курла.
     *
     * @param $data
     * @return array
     */
    private function parseResponse($data): array
    {
        /**
         * якщо хедер не є включений у вивід, то повертаємо масив де по ключу хедер пустий масив, а по ключу хтмл
         * дані з курл запиту.
         */
        if (!isset($this->headerSwitch) || !$this->headerSwitch) {
            $this->responseHtml = $data;

            return $this->parseResponse = [
                'headers' => [],
                'html' => $data
            ];
        }

        $curlInfo = curl_getinfo($this->ch); // дістаємо інформацію про останню операцію

        /**
         *
         * $curlInfo['header_size'] - говорить де розділяються заголовки від тіла повідомлення
         *
         * Якщо курл слідкує за рідеректом, то ми отримуємо декілька заголовків, які відділяються переносом рядка
         */

        $headers = trim(substr($data, 0, $curlInfo['header_size'])); // витягаємо всі хедери
        $headers = str_replace('\r', '\n', $headers); // перетворюємо віндовський перенос в ніксовий
        $headers = str_replace('\r\n', 'n', $headers); // перетворюємо мак в ніксову
        $headers = explode('\n\n', $headers); // розбиваємо по переносу рядка хедери на массив
        $headersPart = end($headers); // забераємо останній хедер

        $bodyPart = trim(substr($data, $curlInfo['header_size'])); // вирізаємо боді та видаляємо пробіли

        // Парсимо хедерс

        $lines = explode('\n', $headersPart); // дістаємо массив з кожним рядком

        $headersPart = [];
        $headersPart['start'] = $lines[0]; // зберігаємо перший рядок

        // count($lines) - кількість елементів в масиві
        for ($i = 1; $i < count($lines); $i++) {

            /**
             * імя кожного окремого хедера в нас розділяється двома крапками
             * 'Connection: keep-alive',
             */
            $colonPos = strpos($lines[$i], ':'); //  позиція першого входження двох крапок
            $name = substr($lines[$i], 0, $colonPos); // вирізуємо до позиції першого входження (до двох крапок)
            $value = trim(substr($lines[$i], $colonPos + 1)); // вирізуємо значення хедера після двох крапок (саме тому $colonPos + 1). трім видаляє пробіли
            $headersPart[$name] = $value; // заносимо це все в масив де ключ імя хедера, а значення - його дані після двох крапок
        }

        $this->responseHeaders = $headers; // заносимо у властивості суто хедери
        $this->responseHtml = $bodyPart; // заносимо суто боді, це для того щоб мотім гетерами їх зручно витягувати

        // повертаємо масив з даними, хоча по факту цього можна і не робити, в нас уже є гетери для цього
        return $this->parseResponse = [
          'headers' => $headersPart,
          'html' => $bodyPart
        ];
    }

}

