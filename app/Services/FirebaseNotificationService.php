<?php

namespace App\Services;

class FirebaseNotificationService
{
    public static function sendPushNotification($fcmIds, $notificationBody, $data = [])
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'registration_ids' => $fcmIds,
            'notification' => $notificationBody,
            'data' => $data,
            'content_available' => true,
            'priority' => 'high',
        );
        $headers = array(
            'Authorization:key=' . config('constants.fcm_server_key'),
            'Content-Type:application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl Failed:' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
}
