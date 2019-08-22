<?php
    return [
        'ScheduleLnuJson' => [

            'path' => [
                'audience' => __DIR__.'/../src/services/parser/data/audience.json',
                'subjects' => __DIR__.'/../src/services/parser/data/subjects.json',
                'teachers' => __DIR__.'/../src/services/parser/data/teacher.json',
                'subjectType' => __DIR__.'/../src/services/parser/data/subjectType.json',
                'data' => __DIR__.'/../src/services/parser/data/data.json',
            ],

            'days' => [
                'Пн',
                'Вт',
                'Ср',
                'Чт',
                'Пт'
            ],

            'faculties' => [
                'FEM' => 'M',
                'FEC' => 'C',
                'FEI' => 'I'
            ],

            'weekType' => [
                'firstWeekType',
                'secondWeekType'
            ],

            'subgroups' => [
                'firstSubgroup' => '1',
                'secondSubgroup' => '2',
            ]

        ]
    ];