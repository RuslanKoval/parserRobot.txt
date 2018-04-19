<?php

namespace app\helpers;


class UrlHelper
{
    private static $headers = null;

    public static  function getBaseUrl()
    {
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        }
        else{
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . '/';
    }

    public static function parseUrl($url, $scheme = 'http://')
    {
        $host = self::getHost($url);

        $url = $scheme.$host.'/robots.txt';
        $headers = self::getHeaders($url);


        if(self::getStatusCode($headers) == '301') {
            $url = $headers['Location'];
        }

        if(self::getStatusCode($headers) == '302') {
            $url = $headers['Location'];
        }

        return $url;
    }

    public static function getHeaders($url)
    {
        return @get_headers($url, 1);
    }

    public static function getStatusCode($headers)
    {
        return substr($headers[0], 9, 3);
    }


    private static function getHost($url) {
        $parseUrl = parse_url(trim($url));
        return trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2)));
    }
}