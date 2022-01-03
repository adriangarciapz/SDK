<?php

namespace HelloWorld;

class Utils {
  
  public static function request($url, $payloadString, $appKey, $appToken) {
    $serverResponse = null;
    $curl = null;
    try {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>$payloadString,
            CURLOPT_HTTPHEADER => array(
                "superappkey" . ": " . $appKey,
                "superapptoken" . ": " . $appToken,
                "Content-Type: application/json",
                "Accept: application/json"
            ),
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        $serverResponse = json_decode($resp);
    } catch (Exception $e) {
        if ($curl) {
            echo "Curl error\n";
            echo curl_error($curl);
        }
        echo "Curl error\n";
        echo $e->getMessage();
        echo $e->getTraceAsString();
    }
    return $serverResponse;
  }

}
