<?php

namespace App\Http;

class GoogleService
{
    const googleApi = 'https://www.googleapis.com/books/v1';

    public function getVolumes(string $searchTerm)
    {
        $response = \Http::get(self::googleApi.'/volumes?q='.$searchTerm.'&key='.env('GOOGLE_API_KEY'));
        $response = json_decode($response->body());

        return $response;
    }

    public function getVolume(string $id)
    {
        $response = \Http::get(self::googleApi.'/volumes/'.$id.'?key='.env('GOOGLE_API_KEY'));
        $response = json_decode($response->body());

        return $response;
    }
}
