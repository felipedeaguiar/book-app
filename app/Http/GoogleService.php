<?php

namespace App\Http;

class GoogleService
{

    const googleApi = 'https://www.googleapis.com/books/v1';
    const token     = 'AIzaSyDjb9aDd5icSPyzCHlZSdRvIhOghc00mN0';

    public function getVolumes(string $searchTerm)
    {
        $response = \Http::get(self::googleApi.'/volumes?q='.$searchTerm.'&key='.self::token);
        $response = json_decode($response->body());

        return $response;
    }

    public function getVolume(string $id)
    {
        $response = \Http::get(self::googleApi.'/volumes/'.$id.'?key='.self::token);
        $response = json_decode($response->body());

        return $response;
    }
}
