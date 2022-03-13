<?php

return [
    'filters' => [
        \LaravelQueryFilter\Filters\InitFilter::class,
        \LaravelQueryFilter\Filters\ColumnValuesFilter::class,
        \LaravelQueryFilter\Filters\WithRelationsFilter::class,
        \LaravelQueryFilter\Filters\SelectColumnsFilter::class,
        \LaravelQueryFilter\Filters\OrderFilter::class,
        \LaravelQueryFilter\Filters\WithCountRelationsFilter::class,
        \LaravelQueryFilter\Filters\HasRelationsFilter::class,
        \LaravelQueryFilter\Filters\HasNotRelationsFilter::class,
    ],
    'model_settings' => [
//        \App\Models\Post::class => function () {
//            return [
//                'columns' => ['id', 'title', 'text', 'user_id'],
//                'relations' => ['comments', 'user']
//            ];
//        },
//        \App\Models\Comment::class => [
//            'columns' => ['id', 'user_id', 'post_id', 'text'],
//            'relations' => ['user']
//        ],
//        \App\Models\User::class => [
//            'columns' => ['id', 'name', 'email']
//        ]
    ]
];
