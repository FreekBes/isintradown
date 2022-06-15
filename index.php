<?php
	require_once("api.php");
	$token = get_token();
	$status = is_up($token["access_token"]);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'>
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<title>Is Intra down?</title>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<link rel='stylesheet' type='text/css' media='screen' href='main.css'>
	</head>
	<body>
		<h1>Is Intra down?</h1>
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
	</body>
</html>