<?php

namespace App\Traits;

use Exception;
use Google\Client;
use Illuminate\Support\Facades\Log;

trait FcmTrait
{
    protected function sendFcmNotification($token, $title, $body, $data = [])
    {
        try {
            $client = new Client;

            // Get firebase credentials from plugin
            $firebase = plugin_active('Firebase');
            $firebaseData = json_decode($firebase->data, true);

            $json = base_path('assets/'.$firebaseData['upload_account_json']);
            $client->setAuthConfig($json);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $httpClient = $client->authorize();

            $projectData = json_decode(file_get_contents($json), true);
            $projectId = data_get($projectData, 'project_id');

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'id' => '1',
                        'status' => 'done',
                    ],
                ],
            ];

            $response = $httpClient->post($url, [
                'json' => $payload,
            ]);
        } catch (Exception $e) {
            Log::error('FCM Notification Error: '.$e->getMessage());
        }
    }
}
