<?php

return [
    'front' => [
        'transport' => 'front',
        'api_base_url' => 'https://api2.frontapp.com',
        'api_token' => env('FRONTAPP_API_TOKEN'),
        'senders' => [

            /*
             * You must specify all desired senders.
             *
             * Specify shared inboxes as senders
             * with their channel_id
             *
             */

             'info@example.com' => [
                'channel_id' => 'cha_XXXXX'
             ]

             /*
             * Specify personal inboxes as senders
             * additionally with their author_id
             *
             * 'name@example.com' => [
             *      'channel_id' => 'cha_XXXXX',
             *      'author_id' => 'tea_XXXXX'
             * ]
             *
             */

        ]
    ],
];
