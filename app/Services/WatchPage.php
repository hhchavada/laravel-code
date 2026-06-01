<?php

namespace App\Services;

class WatchPage
{
    protected $body;
    protected $videoId;
    protected $cookies;

    public $isOk;
    public $isVideoAvailable;

    public function __construct($body, $videoId, $cookies, $isOk, $isVideoAvailable)
    {
        $this->body = $body;
        $this->videoId = $videoId;
        $this->cookies = $cookies;
        $this->isOk = $isOk;
        $this->isVideoAvailable = $isVideoAvailable;
    }

    public static function parse($raw, $videoId, $cookies)
    {
        // Example parsing logic
        $isOk = true; // Assume the page is okay
        $isVideoAvailable = true; // Assume the video is available

        if (strpos($raw, 'Video unavailable') !== false) {
            $isVideoAvailable = false;
        }

        return new self($raw, $videoId, $cookies, $isOk, $isVideoAvailable);
    }

    // Getter method for $body
    public function getBody()
    {
        return $this->body;
    }

    // Getter method for $cookies
    public function getCookies()
    {
        return $this->cookies;
    }
}