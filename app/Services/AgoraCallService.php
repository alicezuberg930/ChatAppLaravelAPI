<?php

namespace App\Services;

class AgoraCallService
{
    public function createAgoraMeeting($channelName)
    {
        $credentials = env('CUSTOMER_KEY') . ":" . env('CUSTOMER_SECRET');
        $base64credentials = base64_encode($credentials);
        $fields = array(
            'name' => $channelName,
            'enable_sign_key' => true,
        );
        $headers = array(
            'Authorization: Basic ' . $base64credentials,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.agora.io/dev/v1/project');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl Failed:' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
}
