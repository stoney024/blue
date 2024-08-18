<?php

namespace App\Repositories;

use Exception;
use GuzzleHttp\Exception\ClientException;
use App\Interfaces\ApiRepositoryInterface;

class ApiRepository implements ApiRepositoryInterface
{
    /**
     * Calls the external api endpoint 
     */
    public function fetch($method = 'GET', $params = null, $query_string = '') {

        $url = config('app.apiUrl');

        if ($query_string ) {
            $url = $url.'?'.$query_string;
        }

        //set the timeout - this could also to go the the .env or to the db as config variable

        $timeout = 50;
        //Creating the request 
        try {
            $client = new \GuzzleHttp\Client();

            $client_params = [
                'headers' => ['Content-Type' => 'application/json','charset' => 'UTF-8'],
                'timeout' => $timeout,
                'connect_timeout' => $timeout,
                'verify' => false,
            ];

            if ($params) {
                $client_params['body'] = json_encode($params);
            }
           
            $res = $client->request($method, $url, $client_params);
            $response = json_decode($res->getBody(), true);
            \Log::info($url);

        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            \Log::error($e);
            throw new \Exception($responseBodyAsString);
        } catch (Exception $e) {
            \Log::error($e);
            throw new \Exception($e);
        }

        return $response;
    }
}