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
			// var data = google.visualization.arrayToDataTable([
			// ['Year', 'Sales', 'Expenses'],
			// ['2004',  1000,      400],
			// ['2005',  1170,      460],
			// ['2006',  660,       1120],
			// ['2007',  1030,      540]
			// ]);

			var data = google.visualization.arrayToDataTable(<?php
				$history = file_get_contents("history.json");
				$history_data = array(array('Time', 'Response Time'));
				if ($history !== false)
				{
					$json_history = json_decode($history, true);
					foreach ($json_history as &$status_item)
						array_push($history_data, array(date("Y-m-d H:i:s", $status_item["time"]), $status_item["res_time"]));
				}
					
				echo json_encode($history_data, JSON_PRETTY_PRINT);
			?>);

			var options = {
				title: 'Response time',
				curveType: 'function',
				legend: { position: 'bottom' },
				hAxis: { showTextEvery: 5 },
				vAxis: { minValue: 0}
			};

			var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
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
		<div id="curve_chart" style="width: 900px; height: 500px"></div>
		<a id="improved-intra-ad" href="https://github.com/FreekBes/improved_intra"><img src="style/assets/improved-intra-ad.png" /></a>
	</body>
</html>