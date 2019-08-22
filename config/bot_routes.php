<?php

return [
    '/start' => 'start/start/',

    '🔔 Розклад дзвінків' => 'keyboard/callSchedule/',

    '🗓 Мої розклади' => 'keyboard/savedSchedule/',

    '/delete/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/?' => 'buttons/delete/$1/$2/$3/$4/$5/$6',

    '/change_week_type/([0-9]+)/([0-9]+)/([A-Z]+)/([0-9]+)/([0-9]+)/([0-9]+)/([A-Za-z]+)/([A-Za-z]+)/([0-9]+)/([0-9]+)/?'
    => 'buttons/changeWeekType/$1/$2/$3/$4/$5/$6/$7/$8/$9/$10',

    '/change_subgroup/([0-9]+)/([0-9]+)/([A-Z]+)/([0-9]+)/([0-9]+)/([0-9]+)/([A-Za-z]+)/([A-Za-z]+)/([0-9]+)/([0-9]+)/?'
    => 'buttons/changeSubgroup/$1/$2/$3/$4/$5/$6/$7/$8/$9/$10',

    '/schedule/([0-9]+)/([0-9]+)/([A-Z]+)/([0-9]+)/([0-9]+)/([0-9]+)/([A-Za-z]+)/([A-Za-z]+)/([0-9]+)/?'
    => 'buttons/schedule/$1/$2/$3/$4/$5/$6/$7/$8/$9',

    '/faculties/([0-9]+)/([0-9]+)/([A-Z]+)/([0-9]+)?' => 'buttons/faculties/$1/$2/$3/$4',

    '/groups/([0-9]+)/([0-9]+)/([A-Z]+)/([0-9]+)/([0-9]+)/?' => 'buttons/groups/$1/$2/$3/$4/$5',

    '/save_schedule/([0-9]+)/([0-9]+)/([A-Z]+)/([0-9]+)/([0-9]+)/([0-9]+)/?' => 'buttons/saveSchedule/$1/$2/$3/$4/$5/$6',

    '/return_back_schedule/([0-9]+)/([0-9]+)/([0-9]+)/?' => 'buttons/returnBackSchedule/$1/$2/$3',

    '/add_schedule/([0-9]+)/([0-9]+)/([0-9]+)/?' => 'keyboard/addSchedule/$1/$2/$3',
];