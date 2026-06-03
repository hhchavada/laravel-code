<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class YoutubeApiClient
{
    protected $baseUrl;
    protected $context;

    public function __construct()
    {

    }

    public function getVideoData($videoId)
    {
        $response =  [
            'payload' => [
                'context' => [
                    'client' => [
                        'clientName' => 'WEB_CREATOR',
                        'clientVersion' => '1.20240723.03.00',
                        'hl' => 'en',
        'timeZone' => 'UTC',
        'utcOffsetMinutes' => 0
                    ],
                ],
            ],
            'apiUrl' => 'https://www.youtube.com/youtubei/v1/player?prettyPrint=false',
        ];
        return $response;
    }
}
