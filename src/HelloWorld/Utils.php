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

  public static function decrypt($base64Str, $merchantEncryptKey) {
    $dataObj = null;
    try {
      $key = substr($merchantEncryptKey, 0, 32);
      $vector = substr($merchantEncryptKey, 32);

      $binaryKey = hex2bin($key);
      $binaryVector = hex2bin($vector);

      $jsonDecrypted = openssl_decrypt(base64_decode($base64Str), "aes-128-cbc", $binaryKey, 1, $binaryVector);
      if ($jsonDecrypted == false) {
        echo "Error here";
      }

      $dataObj = json_decode($jsonDecrypted,true);
    } catch (Exception $e) {
      echo "Error";
    }
    return $dataObj;
  }

}
