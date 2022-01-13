<?php

namespace UDT;

use Opis\JsonSchema\{
  Validator, Helper
};

class Utils {

  const SCHEMAS_DIR = __DIR__ . "/Schemas/";

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
      throw new \Exception("cURL error :: " . curl_error($curl), 400);

    curl_close($curl);

    $decodedJSON = json_decode($result);
    if (json_last_error() != JSON_ERROR_NONE)
      throw new \Exception("The response data is not JSON decodable :: " . json_last_error_msg(), 500);

    $resultArray = json_decode($result, true);
    if ($resultArray["code"] != 200)
      throw new \Exception($resultArray["message"], $resultArray["code"]);

    return $decodedJSON;
  }

  // public static function validateResponse($data, $schemaFile) {
  //   $validator = new Validator();

  //   $schemaPath = self::SCHEMAS_DIR . $schemaFile;
  //   $schemaJSON = file_get_contents($schemaPath);
  //   $schema = Helper::toJSON($schemaJSON);

  //   $result = $validator->validate($data, $schema);

  //   if (!$result->isValid())
  //       throw new \Exception($result->error());
  // }

}
