<?php
	$file_name = "src/data.json";
	try
	{
		$file = fopen($file_name, "r");
		$size = filesize($file_name);
		if (!$size)
			throw new Exception("bad filesize");
		$data = fread($file, $size);
		if (!$data)
			throw new Exception("failed read");
		$status = json_decode($data, true);
	}
	catch (Exception $e)
	{
		$status = array(
			"online" => "false",
			"last_check" => time(),
			"res_time" => "check failed"
		);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'>
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<title>Is Intra down?</title>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<link rel='stylesheet' type='text/css' media='screen' href='style/main.css'>
	</head>
	<body>
		<h1 id="title">Is Intra down?</h1>
		Status: <?php echo ($status["online"] ? "Online" : "Offline"); ?>
		<br>
		Last checked: <span id="lasttime" data-timestamp="<?php echo $status["last_check"]; ?>"><?php date('l jS \of F Y h:i:s A', $status["last_check"]); ?></span>
		<br>
		Last response time: <?php echo $status["res_time"]; ?>ms
		<script>
			const timestampElem = document.getElementById("lasttime");
			const timestamp = timestampElem.getAttribute("data-timestamp");
			const date = new Date(parseInt(timestamp) * 1000);
			timestampElem.innerText = date.toLocaleString();
		</script>
		<a id="improved-intra-ad" href="https://github.com/FreekBes/improved_intra"><img src="style/assets/improved-intra-ad.png" /></a>
	</body>
</html>