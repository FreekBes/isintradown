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
?>