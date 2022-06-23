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
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			var source = <?php
				$history = file_get_contents("history.json");
				$history_data = array(array('Time', 'Response Time'));
				if ($history !== false)
				{
					$json_history = array_reverse(json_decode($history, true));
					foreach ($json_history as &$status_item)
						array_push($history_data, array(date("Y-m-d H:i:s", $status_item["time"]), $status_item["res_time"]));
				}

				echo json_encode($history_data);
			?>;
			for (var i = 1; i < source.length; i++) {
				source[i][0] = new Date(source[i][0]);
			}
			var data = google.visualization.arrayToDataTable(source);
			var dateFormatter = new google.visualization.DateFormat({ pattern: 'd MMMM, HH:mm' });
			var msFormatter = new google.visualization.NumberFormat({ pattern: '#ms' });
			dateFormatter.format(data, 0);
			msFormatter.format(data, 1);

			var options = {
				title: 'Response Time History',
				curveType: 'function',
				legend: 'none',
				hAxis: {
					showTextEvery: 24,
					format: 'd MMM HH:mm'
				},
				vAxis: {
					minValue: 0,
					format: '#ms'
				},
				width: window.innerWidth,
				height: 500
			};

			var chart = new google.visualization.LineChart(document.getElementById('res_time_chart'));
			chart.draw(data, options);
		}
		</script>
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
		<div id="res_time_chart"></div>
		<a id="improved-intra-ad" href="https://github.com/FreekBes/improved_intra"><img src="style/assets/improved-intra-ad.png" /></a>
	</body>
</html>
