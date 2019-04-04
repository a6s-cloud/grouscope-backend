<?php
return [
    'api_key' => env('CONSUMER_KEY',''),
    'secret_key' => env('CONSUMER_SECRET',''),
    'access_token' => env('ACCESS_TOKEN',''),
    'token_secret' => env('ACCESS_TOKEN_SECRET',''),
    'call_back_url' => env('TWITTER_CALLBACK_URL', '')
];
