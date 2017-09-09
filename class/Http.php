<?php

class Http
{


    private static $cookie_arr = array(
        '__utma' => '51854390.847096722.1463637721.1464335701.1464340069.13',
        '__utmb' => '51854390.26.10.1464340069',
        '__utmc' => '51854390',
        '__utmv' => '51854390.100-1|2=registration_date=20141017=1^3=entry_date=20141017=1',
        '__utmz' => '51854390.1464168187.9.3.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided)',
        '_xsrf' => 'adcfcf915f4506927b88d87646016dc2',
        '_za' => 'a2889ef9-c598-4e96-8ab5-9ca0a9f42e7e',
        '_zap' => '06c40d9b-e783-45c3-875b-b5def3690777',
        '_zap' => '9349f159-e616-4d41-9799-11ee42b5c5eb',
        'cap_id' => '"Y2ExNDNlMjU1MTk4NDRlYTgyMGZkMjc0NDBhNzliNTg=|1461919186|0dc2691feec234e052506642d742b21eb59be6e4"',
        'd_c0' => '"AGAAuXZTsAmPTiftYaWT02M1JeAkw0ewo9w=|1459231862"',
        'l_cap_id' => '"ZDI4ZjAzMTA3ODIxNGZmMWE3MmVlN2Q5OWMzYjhhZjY=|1461919186|84a64a2066181bd12069ecf4719366ac226e5da8"',
        'l_n_c' => '1',
        'login' => '"YjdjZjBmMmVlNTc1NDNlOGIxZDU5Yjg4MjhkZjJmZjU=|1461919194|a1cb43545b786305147c7c19b7b85111f810b4a5"',
        'q_c1' => '21fd5f4d6c3541aa873163af7517ab8d|1461908846000|1459231862000',
        's-i' => '6',
        's-q' => '%E6%85%A2%E6%80%A7%E8%83%83%E7%82%8E',
        's-t' => 'autocomplete',
        'sid' => 'e63rlk6q',
        'z_c0' => 'Mi4wQUFEQTRZbzZBQUFBWUFDNWRsT3dDUmNBQUFCaEFsVk4ycXBLVndCRktqRFFudXVaRzFyV3g1dUUwdkhsQ19UaEp3|1461919194|141c3c517d5be5e1e205e233f0d18c1f832a3806'
    );

    private static function genCookie() {
        $cookie = '';
        foreach (self::$cookie_arr as $key => $value) {
            if($key != 'z_c0')
                $cookie .= $key . '=' . $value . ';';
            else
                $cookie .= $key . '=' . $value;
        }
        return $cookie;
    }

    public static function get($url,$post='',$cookie='', $returnCookie=0)
    {
        $cookie_jar = '__cfduid=dd28b056011686532875601194d5628bb1500363937;remember_82e5d2c56bdd0811318f0cf078b78bfc=eyJpdiI6IjRvTDRaVDE5YXZUaGg2TnVuR2Y1MzFObUE5UVVmSldURnY1bVZodEVQbkU9IiwidmFsdWUiOiJNT0FET2w2TlJYZjB6bVNnRWxoQ3M5dXg0RGU2WFZMNGhrSnNVdjNvVlRwSEpkUXFLcUtMYzNvcTc4RHRuUTNReVROTDJZMGRIQ0wwTEZHYlYwbGFSRVZFdml4STJZb1dlSkd0ZzVldHo5RFwvWWlHRVhuTmoxQzM2bHllMW1JWUYiLCJtYWMiOiJlOTc1M2RkNmFlNDc2MjY1ZDg3MzQyOGMxMzU3NmViNWMzZWY1ZmVmNDZmY2Q4MTM2NjdjZWI1NGMzMWFjZWI0In0%3D;wallhaven_session=eyJpdiI6IlpmRHFYaFMrZnJGTDZYWmtaOVgrSzlvUHNYUjJERGlqSDNzK1JiR1piN2M9IiwidmFsdWUiOiJwMGdOUW1jdFwvSFh4SkkrbU41N2lvSU9ISkpzd2U4Y2E1cHBcL1l2VXE4bkUxWnBUdzl1bDVRWWloZlNMWUxtZmp6dGFsb2d1VVArUlwvTVJHQkpZNTFQZz09IiwibWFjIjoiYjRkNTlmYTc3ZmU0MjMzYmIyMDUzYTQxN2IyMTU3N2JhZmFjZmQ0MzUxOWJjYTA5YThmOWVhZjVkOTYwZDk5MyJ9;_ga=GA1.2.899816656.1500363932;_gid=GA1.2.1327554537.1504745505';
        $timeout = 20;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }

            curl_setopt($curl, CURLOPT_COOKIE, $cookie_jar);

//        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_jar);
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie']  = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        }else{
            return $data;
        }
    }
}