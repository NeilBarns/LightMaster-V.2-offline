<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $baseUri;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUri = env('FIREBASE_DATABASE_URL');
        $this->apiKey = env('FIREBASE_API_KEY');
    }

    public function sendRequest($method, $uri, $data = [])
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        try {
            $response = $client->request($method, $uri . '.json', [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Firebase error:', [$e->getMessage()]);
            return $e->getMessage();
        }

        // $url = rtrim($this->baseUri, '/') . '/' . ltrim($path, '/') . '.json';
        // Log::info('Firebase URL:', [$url]);  // Log the complete URL

        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . env('FIREBASE_API_KEY'),
        //     'Content-Type' => 'application/json',
        // ])->send($method, $url, ['json' => $data]);

        // return $response->json();
    }

    public function getData($path)
    {
        return $this->sendRequest('GET', $path);
    }

    public function setData($path, $data)
    {
        $response = $this->sendRequest('PUT', $path, $data);
        if (isset($response['error'])) {
            Log::error('Failed to set data in Firebase: ' . $response['error']);
            return false;
        }

        return true;
    }

    public function updateData($path, $data)
    {
        return $this->sendRequest('PATCH', $path, $data);
    }

    public function deleteData($path)
    {
        $response = $this->sendRequest('DELETE', $path);
        
        if (isset($response['error'])) {
            Log::error('Failed to delete data in Firebase: ' . $response['error']);
            return false;
        }

        return true;
    }
}
