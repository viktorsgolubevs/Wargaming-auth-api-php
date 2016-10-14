<?php

    require_once('wargaming.php');

    $wargaming = new WargamingAuth();

    if (empty($_GET['status']))
    {
        $wargaming->get_token();
    }

    if (isset($_GET['status']) && isset($_GET['access_token']) && isset($_GET['nickname']) && isset($_GET['account_id']) && isset($_GET['expires_at']))
    {
        $data = $wargaming->get_auth_data();

        if($data['status'] == 'ok')
        {
            echo '<pre>';
			print_r($data);
			echo '</pre>';

            $access_token = $data['data']['access_token'];
            $expires_at = $data['data']['expires_at'];
            $account_id = $data['data']['account_id'];

            echo 'User id <b>'.$account_id.'</b><br />Token <b>'.$access_token.'</b>, is activated and expire <b>'.date("d.m.Y H:i:s",$expires_at).'</b>';
        }
        else
        {
            exit('access_token not confirmed');
        }
    }
    else
    {
        $error_code = 500;
        if (preg_match('/^[0-9]+$/u', $_GET['code']))
        {
            $error_code = $_GET['code'];
        }
        exit("Error!. Error: $error_code");
    }

?>
