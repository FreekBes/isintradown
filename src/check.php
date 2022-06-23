#!/usr/bin/php
<?php
require_once("config.php");
require_once("token.php");

function is_up($group_id)
{
	$file_name = "data.json";
	try
	{
		if (!file_exists($file_name))
			throw new Exception("no data.json file found");
		$file = file_get_contents($file_name);
		if ($file === false)
			throw new Exception("get_contents failed");
        $data = json_decode($file, true);
    }
	catch (Exception $e) { $data = array(
		"last_up" => 0,
	); }

	$data = get_token($data);
    $data["last_check"] = time();

	//curl init;
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
	{
		$data["online"] = false; //curl_exec errored
		$data["res_time"] = -1;
	}

	file_put_contents($file_name, json_encode($data, JSON_PRETTY_PRINT));
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