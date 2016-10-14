<?php

class WargamingAuth {

	/*
	 * Wargaming Authentication - PHP OAuth api
	 * @see https://developers.wargaming.net/
     * For application authentication
     * @see https://developers.wargaming.net/applications/
	 * Copyright 2016 Viktor Golubev
	 * @copyright Viktor Golubev
	 * @website http://www.viktorsgolubevs.lv
	 * @author me@viktorsgolubevs.lv
	 */
	
    const AUTH_URL = 'https://api.worldoftanks.eu/wot/auth/login/';

    const PROLONGATE = 'https://api.worldoftanks.eu/wot/auth/prolongate/';

    const ACCESS_TOKEN_URL = 'https://api.worldoftanks.eu/wot/auth/login/?application_id={application_id}&expires_at={expires_at}&redirect_uri={redirect_uri}&nofollow={nofollow}&display={display}';

    private static $application_id = '2812b0747ac2e2a8fda6c548f16eabc7';

    private static $nofollow = 1;

    private static $redirect_url = 'localhost/wargaming/auth.php';

    private static $expires = 86400; // 60*60*24*1 = 86400 - one day

    private function redirect($url)
    {
        header('HTTP/1.1 301 Moved Permanently');
        header("Location:".$url);
        exit();
    }

    private function call($params = array())
    {
        if (empty($params)) {
            exit('Wrong params');
        }

        return $context = stream_context_create(
            array('http' =>
                array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query($params)
                )
            )
        );
    }

    public function get_token()
    {
        $json = file_get_contents(self::AUTH_URL, false, $this->call(array(
            'nofollow' => self::$nofollow,
            'expires_at' => 300,
            'redirect_uri' => self::$redirect_url,
            'application_id' => self::$application_id,
            'display' => 'popup')));

        $json = json_decode($json, true);

        if ($json['status'] == 'ok')
        {
            $this->redirect($json['data']['location']);
        }
        else
        {
            die('Link was not received for redirecting');
        }
    }

    public function get_auth_data()
    {
        if($_GET['status'] != 'ok')
        {
            $error_code = 500;
            if (preg_match('/^[0-9]+$/u', $_GET['code']))
            {
                $error_code = $_GET['code'];
            }
            exit("Ошибка авторизации. Код ошибки: $error_code");
        }
        elseif($_GET['expires_at'] < time())
        {
            exit("Ошибка авторизации. Срок действия access_token истек.");
        }
        else
        {
            //подтверждаем правдивость полученных параметров
            $json = file_get_contents(self::PROLONGATE, false, $this->call(array(
                'expires_at' => time() + self::$expires,
                'access_token' => $_GET['access_token'],
                'application_id' => self::$application_id
            )));

            return json_decode($json, true);
        }
    }

}