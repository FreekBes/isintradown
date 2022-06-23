#!/usr/bin/php
<?php
require_once("config.php");
require_once("token.php");

$file_name = "../history.json";
if (!file_exists($file_name))
	$status_hist = array();
else
{
	$status_hist = file_get_contents($file_name);
	$status_hist = json_decode($status_hist, true);
}

$data = get_token(array());

function is_up($group_id)
{
	global $data;

	$data = get_token($data);
	$status = array();

	//curl init;
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.intra.42.fr/v2/groups/$group_id");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer ".$data["access_token"]));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $status["time"] = time();
	$req_start = microtime(true);
	$response = curl_exec($ch);
	if ($response !== false)
	{
        $req_end = microtime(true);
        $status["res_time"] = intval(($req_end - $req_start) * 1000);
		try
		{
			// needed to check if api actually responded with a JSON
			$json = json_decode($response, true);
			if (array_key_exists("error", $json))
                $status["online"] = false;
			else
			{
				$status["online"] = true;
			}
		}
		catch (Exception $e) { $status["online"] = false; }
	}
	else
	{
		$status["online"] = false; //curl_exec errored
		$status["res_time"] = -1;
	}
	file_put_contents("../status.json", json_encode($status, JSON_PRETTY_PRINT));
	return ($status);
}

$group_id = 0;
$counter = 0;

while (1)
{
    $status = is_up($group_id);
	$counter++;
	$group_id++;
	if ($group_id > 69)
		$group_id = 0;
	if ($counter == 30) //saves every 5 min (30 runs)
	{
		array_unshift($status_hist, $status);
		array_slice($status_hist, 0, 864, false); //864 == amount of 5 minute segments in 3 days
		file_put_contents($file_name, json_encode($status_hist, JSON_PRETTY_PRINT)); //saves last 3 days of statuses
		$counter = 0;
	}
    sleep(10);
}
?>