<?php

namespace Ephect\Web;

class Curl {
    //put your code here
    
    public function request($uri, $header = [], $data = []) : object
    {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($ch, CURLOPT_CAINFO, $certpath);
//            curl_setopt($ch, CURLOPT_CAPATH, $certpath);
        if(count($header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if(count($data) > 0) {
            $queryString = http_build_query($data);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        }
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $content = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        $info = curl_getinfo($ch);

        $header = (isset($info['request_header'])) ? $info['request_header'] : '';

        if($errno > 0) {
            throw new \Exception($error, $errno);
        }
        if($header == '') {
            throw new \Exception("Curl is not working fine for some reason. Are you using Android ?");
        }

        $code = $info['http_code'];
        curl_close($ch);

        $result = (object) ['code' => (int)$code, 'header' => $header, 'content' => $content];
        
        return $result;
    }
}
