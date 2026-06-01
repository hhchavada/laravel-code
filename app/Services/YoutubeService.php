<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class YoutubeService
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    public static function get($videoId)
    {
        $url = 'https://www.youtube.com/watch?v=' . $videoId . '&bpctr=9999999999&has_verified=1&hl=en';

        $defaultCookies = 'PREF=hl=en&tz=UTC; SOCS=CAI; GPS=1';
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.18 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-us,en;q=0.5',
            'Sec-Fetch-Mode' => 'navigate',
            'Cookie' => $defaultCookies,
        ];

        $cookiesExp = '/(?:^|,)(\w.+?)=(.*?);/';

        try {
            $response = (new self())->httpClient->get($url, [
                'headers' => $headers,
                'cookies' => true,
            ]);

            $cookieHeader = $response->getHeader('Set-Cookie');
            $cookies = [];

            foreach ($cookieHeader as $header) {
                preg_match_all($cookiesExp, $header, $matches);
                foreach ($matches[0] as $index => $match) {
                    $cookies[$matches[1][$index]] = $matches[2][$index];
                }
            }

            // Add default cookies
            $cookies = array_merge($cookies, [
                'PREF' => 'hl=en',
                'SOCS' => 'CAI',
                'GPS' => '1',
            ]);

            // Parse the response body
            $result = WatchPage::parse((string)$response->getBody(), $videoId, $cookies);

            if (!$result->isOk) {
                throw new \Exception('Video watch page is broken.');
            }

            if (!$result->isVideoAvailable) {
                throw new \Exception('Video is unavailable: ' . $videoId);
            }

            return $result;

        } catch (RequestException $e) {
            Log::error('Error fetching video: ' . $e->getMessage());
            throw new \Exception('Failed to fetch video data.');
        }
    }
}