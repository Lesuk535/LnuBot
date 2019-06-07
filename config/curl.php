<?php
return [

    'defaults' => [
        'setFollow' => true,
        'headerSwitch' => true,
        'ssl' => false,
        'setUserAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36',
    ],

    'https://api.telegram.org' => [
        'defaults' => true,
    ],

    'https://webshake.ru/' => [
        'defaults' => true,
    ],

    'http://elct.lnu.edu.ua' => [
        'defaults' => true,
        'setHeaders' => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: en-US,en;q=0.9,uk;q=0.8,ru;q=0.7',
            'Cache-Control: max-age=0',
            'Connection: keep-alive',
            'Cookie: b=b; _ga=GA1.3.1569740324.1558689160; _gid=GA1.3.1024647690.1558689160; fe_typo_user=56718bbe5e0c1821b60af74f29d9b08b',
            'Host: elct.lnu.edu.ua',
            'Upgrade-Insecure-Requests: 1',
        ],
        'setPost' => [
//            'dayall'   => 'all',
//            'numall'   => 'all',
            'objall'   => 'all',
            'audall'   => 'all',
            'teachall' => 'all',
            'search'   => 'Search'
        ],
        'setCookie' => 'b=b; _ga=GA1.3.1569740324.1558689160; _gid=GA1.3.1024647690.1558689160; fe_typo_user=56718bbe5e0c1821b60af74f29d9b08b',
        'setReferer' => 'http://elct.lnu.edu.ua/',
    ],

];