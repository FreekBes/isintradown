#!/usr/bin/php
<?php
require_once("config.php");

function get_token($token)
{
	global $clientID, $clientSecret;

	if (!isset($token['expires_at']))
		$token['expires_at'] = 0;

	if (!empty($token['expires_at']))
	{
		if ($token["expires_at"] < time() - 5)
			return ($token);
	}

	$ch = curl_init();

	// Setup post data
	$postData = array(
		"client_id" => $clientID,
		"client_secret" => $clientSecret,
		"grant_type" => "client_credentials",
	);

	// Setup curl
	curl_setopt($ch, CURLOPT_URL,"https://api.intra.42.fr/oauth/token");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);

	// Check response
	if ($response !== false)
	{
		try
		{
			$token = json_decode($response, true);
			$token["expires_at"] = $token["created_at"] + $token["expires_in"];
			return ($token);
		}
		catch (Exception $e)
		{
			return (null);
		}
	}
	return (null);
}

function is_up()
{
    global $clientID, $clientSecret;

    $data_file = fopen("data.json", "r");
    try
    {
        $data = fread($data_file, filesize("data.json"));
		if (!$data)
			throw new Exception("failed read");
        $data = json_decode($data, true);
    }
    catch (Exception $e) {$data = array();}

    // echo $data;
    fclose($data_file);
	if (isset($data["expires_at"]) || !isset($data["access_token"]))
	{
		if ($data["expires_at"] < time() - 5 || empty($data["expires_at"]) || !isset($data["access_token"]))
		{
            $token_data = get_token($data);
			echo "why";
            if (!$token_data)
                exit(1);
			if (isset($data["last_up"]))
				$temp = $data["last_up"];
            $data = json_decode(json_encode($token_data), true);
			$data["last_up"] = $temp;
        }	       
	}
    $data["last_check"] = time();
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.intra.42.fr/v2/groups/69");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer ".$data["access_token"]));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$req_start = microtime(true);
	$response = curl_exec($ch);
	if ($response !== false)
	{
        $req_end = microtime(true);
        $data["res_time"] = intval(($req_end - $req_start) * 1000);
		try
		{
			// needed to check if api actually responded with a JSON
			$json = json_decode($response, true);
			if (array_key_exists("error", $json))
                $data["online"] = false;
			else
			{
				$data["last_up"] = time();
				$data["online"] = true;
			}
		}
		catch (Exception $e) { $data["online"] = false; }
	}
	else
		$data["online"] = false; //curl_exec errored

    $data_file = fopen("data.json", "w");
    fwrite($data_file, json_encode($data, JSON_PRETTY_PRINT));
    fclose($data_file);
}

while (1)
{
    is_up();
    sleep(5);
}

?>