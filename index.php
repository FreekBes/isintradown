<?php
	$file_name = "status.json";
	$status = file_get_contents($file_name);
	if ($status == false)
	{
		$status = array(
			"time" => 0,
			"online" => false,
			"res_time" => -1
		);
	}
	else
		$status = json_decode($status, true);
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
		Last checked: <span id="lasttime" data-timestamp="<?php echo $status["time"]; ?>"><?php date('l jS \of F Y h:i:s A', $status["time"]); ?></span>
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