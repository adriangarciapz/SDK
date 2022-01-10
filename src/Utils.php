<?php

namespace UDT;

class Utils {

  public static function isValidUrl($url) {
    $url = filter_var($url, FILTER_SANITIZE_URL);
    if (!str_starts_with($url, "undostres")){
      return false;
    }
    return filter_var($url, FILTER_VALIDATE_URL);
  }
  
  public static function request($url, $payloadJSON, $appKey, $appToken) {
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
        CURLOPT_POSTFIELDS => $payloadJSON,
        CURLOPT_HTTPHEADER => array(
            "superappkey" . ": " . $appKey,
            "superapptoken" . ": " . $appToken,
            "Content-Type: application/json",
            "Accept: application/json"
        ),
    ));
    $result = curl_exec($curl);

    if (curl_errno($curl))
      throw new \Exception("cURL error :: " . curl_error($curl));

    if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
      throw new \Exception("cURL unexpected HTTP code :: " . $result);

    curl_close($curl);

    $decodedJSON = json_decode($result);
    if (json_last_error() != JSON_ERROR_NONE)
      throw new \Exception("The response data is not JSON decodable :: " . json_last_error_msg());

    return $decodedJSON;
  }

  public static function decryptPaymentData($base64Str, $encrypKey) {
    $dataObj = null;
    try {
      $key = substr($encrypKey, 0, 32);
      $vector = substr($encrypKey, 32);

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