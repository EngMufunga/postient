<?php

namespace App\Lib;

class CurlRequest
{

    /**
    * GET request using curl
    *
    * @return mixed
    */
	public static function curlContent($url,$header = null)
	{
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    if ($header) {
	    	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    }
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result;
	}


    /**
    * POST request using curl
    *
    * @return mixed
    */
public static function curlPostContent($url, $postData = null, $headers = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);

    // If array, convert to http_build_query, else use as-is (JSON string)
    if (is_array($postData)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // already JSON
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new \Exception(curl_error($ch));
    }

    curl_close($ch);
    return $result;
}


	public static function curlPutBinary($url, $binaryData, $headers = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $binaryData);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }
}
