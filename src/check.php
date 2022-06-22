#!/usr/bin/php
<?php
require_once("config.php");

function get_token($token)
{
	global $clientID, $clientSecret;

	if (isset($token["expires_at"]) && $token["expires_at"] > time() + 5)
		return ($token);

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
			$token["expires_at"] = time() + $token["expires_in"];
			return ($token);
		}
		catch (Exception $e)
		{
			return (null);
		}
	}
	return (null);
}

function is_up($group_id)
{
    global $clientID, $clientSecret;

	$file_name = "data.json";
	try
	{
		$file = fopen($file_name, "r");
		$size = filesize($file_name);
		if (!$size)
			throw new Exception("bad filesize");
		$data = fread($file, $size);
		if (!$data)
			throw new Exception("failed read");
        $data = json_decode($data, true);
		fclose($file);
    }
	catch (Exception $e) {$data = array();fclose($file);}

	$data = get_token($data);
    $data["last_check"] = time();
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.intra.42.fr/v2/groups/{$group_id}");
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

$group_id = 0;

while (1)
{
    is_up($group_id);
	$group_id++;
	if ($group_id > 69)
		$group_id = 0;
    sleep(10);
}

?>