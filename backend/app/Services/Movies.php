<?php
namespace App\Services;

class Movies {

  private $curl;

  public function __construct()
  {
    $this->curl = \curl_init();

    $query = "https://unogs-unogs-v1.p.rapidapi.com/aaapi.cgi?q=get%3Anew7%3SE&p=1&t=ns&st=adv";
    $host = "unogs-unogs-v1.p.rapidapi.com";

    \curl_setopt_array($this->curl, [
      CURLOPT_URL => $query ,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "x-rapidapi-host:" . $host,
        "x-rapidapi-key:" . config('api_key')
      ],
    ]);
  }

  public function get()
  {
    $response = \curl_exec($this->curl);
    $err = \curl_error($this->curl);

    \curl_close($this->curl);

    if ($err) {
      return $err;
    }
    return $response;

  }
}
