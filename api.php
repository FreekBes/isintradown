<?php

function get_user_info($accessToken)
{
	global $clientID, $clientSecret, $redirectURL;

	if (isset($_SESSION["user"]) && $accessToken == $_SESSION["user_from_access_token"])
		return ($_SESSION["user"]);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.intra.42.fr/v2/me");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json" , "Authorization: Bearer ".$accessToken));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	if ($response !== false) 
	{
		try 
		{
			$userInfo = json_decode($response, true);
			if (isset($userInfo["error"])) {
				return (null);
			}
			$userInfo = reduce_user_info($userInfo);
			$_SESSION["user"] = $userInfo;
			$_SESSION["user_from_access_token"] = $accessToken;
			return ($userInfo);
		}
		catch (Exception $e) 
		{
			return (null);
		}
	}
	return (null);
}

?>