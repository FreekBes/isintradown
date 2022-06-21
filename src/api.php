<?php
require_once("config.php");



// Because french people find it funny: toto
// hon hon hon

// Fetches the application token
//{"access_token":"nice","token_type":"bearer","expires_in":7200,"scope":"public","created_at":1655316276}
function get_token()
{
	global $clientID, $clientSecret;

	$token = array(
		'expires_at' => ""
	);

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

// returns true if Intra API is up
function is_up($accessToken)
{
	global $clientID, $clientSecret, $shm;

	// placeholder in case something fucks up
	$api_status = array(
		"last_up" => 0,
		"online" => false,
		"last_check" => 0,
		"res_time" => 0
	);

	if (!empty($api_status["last_check"]))
	{
		if ($api_status["last_check"] >= time() - 8)
			return ($api_status);
	}
	$api_status["last_check"] = time();

	// Fetch the smallest and nicest thing
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.intra.42.fr/v2/groups/69");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer ".$accessToken));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$req_start = microtime(true);
	$response = curl_exec($ch);
	if ($response !== false)
	{
		$req_end = microtime(true);
		$api_status["res_time"] = intval(($req_end - $req_start) * 1000);
		try
		{
			// needed to check if api actually responded with a JSON
			$json = json_decode($response);
			if (array_key_exists("error", $json))
				$api_status["online"] = false;
			else
			{
				$api_status["last_up"] = time();
				$api_status["online"] = true;
			}
		}
		catch (Exception $e) { $api_status["online"] = false; }
	}
	else
		$api_status["online"] = false;
	return ($api_status);
}
?>