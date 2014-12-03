<html>
  <head>
	<title>Abakaffe - Statistikk</title>
	
	<!--Makes viewport optimized for all devices-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
	<!--Javascripts - Taken from Founder assets folder-->
	<script type="text/javascript" src="http://assets.founder.no/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jquery/jquery.corner.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jquery/jquery-ui.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/jquery.jqplot.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/foundation/js/foundation.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/foundation/js/vendor/modernizr.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/foundation/js/vendor/fastclick.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/plugins/jqplot.logAxisRenderer.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/plugins/jqplot.highlighter.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/plugins/jqplot.donutRenderer.min.js"></script>
	<script type="text/javascript" src="http://assets.founder.no/js/jqplot/plugins/jqplot.cursor.min.js"></script>
	
	<!--Stylesheets-->
	<link rel="stylesheet" type="text/css" href="http://assets.founder.no/js/foundation/css/foundation.css" />
	<link rel="stylesheet" type="text/css" href="http://assets.founder.no/js/jqplot/jquery.jqplot.css" />
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
	
	<?

	//configure - SQLI
	$con = new mysqli('***************', '***************', '***************', '***************');
	 
	//connect to database
	if($con->connect_errno > 0){
		die('Unable to connect to database [' . $con->connect_error . ']');
	}
     
    //-------------------------------------------Total YOs sent---------------------------------
    //get all sent YOs
    $query = "
    	SELECT 
			COUNT(*) as count, 
			date(date_sent) AS date
		FROM 
			coffee 
		WHERE 
			date_sent IS NOT NULL
		GROUP BY 
			date 
		ORDER BY 
			date 
		DESC LIMIT 
			30 
		";
		
	$result = mysqli_query($con, $query);
	
	$yos_sent = '[';
	while ($row = mysqli_fetch_assoc($result)) {
		$yos_sent.= '["'. $row['date'] . '", ' . $row['count'] . '],';		
	}
	$yos_sent= trim($yos_sent, ",");
	$yos_sent.= "]";	
      
    //---------------------------------------Total Abakus drinkers------------------------------
    //get all sent YOs
    $query = "
    	SELECT	
			sender,
			COUNT(*) as count
		FROM 
			coffee 
		WHERE 
			date_sent IS NOT NULL
		GROUP BY 
			sender 
		ORDER BY 
			count
		DESC LIMIT 
			10
		";
		
	$result = mysqli_query($con, $query);
	
	$data_score = '[';
	while ($row = mysqli_fetch_assoc($result)) {
		$data_score.= '["'. $row['sender'] . '", ' . $row['count'] . '],';		
	}
	$data_score= trim($data_score, ",");
	$data_score.= "]";	

    //------------------------------------------Last receivers------------------------------
	// get all users who have asked for a YO
	$query = " SELECT * FROM coffee  WHERE date_sent IS NOT NULL OR sent = 0 ORDER BY date_made DESC "; 
	$result = mysqli_query($con, $query);
	
    ?>
	 
	<!--Define and configure analytic plots-->
	<script>
		$(function() { 
			$(".row").corner();
			
  			$.jqplot._noToImageButton = true;
   			$.jqplot.config.enablePlugins = true;
    		$(document).foundation();
    		
			var data_yos = <? echo $yos_sent; ?>;
    		
    		var plot = $.jqplot("plot", [data_yos], {
				seriesColors: ["rgb(211, 235, 59)", "rgb(252, 181, 136)"],
				cursor: {
					show: false,
					tooltipLocation:'sw', 
					zoom:false
				}, 
				highlighter: {
					show: true,
					sizeAdjust: 1,
					tooltipOffset: 9
				},
				grid: {
					background: 'rgba(57,57,57,0.0)',
					drawBorder: false,
					shadow: false,
					gridLineColor: '#666666',
					gridLineWidth: 1
				},
				legend: {
					show: true,
					placement: 'inside'
				},
				seriesDefaults: {
					rendererOptions: {
						smooth: true,
						animation: {
							show: true
						}
					},
					showMarker: true
				},
				series: [
					{
						fill: false,
						label: 'Sent YOs'
					}
				],
				axesDefaults: {
					rendererOptions: {
						baselineWidth: 1.5,
						baselineColor: '#444444',
						drawBaseline: false
					}
				},
				axes: {
					xaxis: {
						renderer: $.jqplot.DateAxisRenderer,
						tickRenderer: $.jqplot.CanvasAxisTickRenderer,
						tickOptions: {
							formatString: "%b %e",
							angle: -30
						},
						drawMajorGridlines: false
					},
					yaxis: {
						renderer: $.jqplot.LogAxisRenderer,
						pad: 0,
						rendererOptions: {
							minorTicks: 1
						},
						tickOptions: {
							formatString: "%'d",
							showMark: false
						}
					}
				}
			});
			
			var data_score = <? echo $data_score; ?>; 
 
			var score = $.jqplot('score', [data_score], {
				title: 'Top Abakus coffee drinkers',
				grid: {
					background: 'rgba(57,57,57,0.0)',
					drawBorder: false,
					shadow: false,
					gridLineColor: '#666666',
					gridLineWidth: 1
				},
				series:[{renderer:$.jqplot.BarRenderer}],
				axesDefaults: {
					tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
					tickOptions: {
					  angle: -30,
					  fontSize: '10pt'
					}
				},
				axes: {
				  xaxis: {
					renderer: $.jqplot.CategoryAxisRenderer
				  }
				}
			 });
			
    		$('.jqplot-highlighter-tooltip').addClass('ui-corner-all')
    		$('.jqplot-data-label').css("color","white");
		});
	</script>
	<!--Send evey call to google analytics-->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-46210875-1', 'auto');
	  ga('send', 'pageview');

	</script>
	</head>
  <body>  
	<!--Display logo-->
    <div> 
  		<a href="http://morten.founder.no"><img src="../images/logo.png" /></a>
	</div>
	<div class="row"> 
		<div class="large-5 columns">
		<h2>Senders</h2>
		<ul class="small-block-grid-3">
		  <li><h3>Nickname</h3></li>
		  <li><h3>Received</h3></li>
		  <li><h3>Sent YO</h3></li>
		</ul>
		<?
		// get result and send yos
		if ($result) { 

			/* determine number of rows result set */
			$count = mysqli_num_rows($result);
	 
			if($count > 0 ){
	
				//define counter
				$i = 0;
				
				/* fetch associative array */
				while ($row = mysqli_fetch_assoc($result)) {	
						$date_made = new DateTime($row["date_made"], new DateTimeZone("Europe/Oslo"));
						$date_sent = new DateTime($row["date_sent"], new DateTimeZone("Europe/Oslo"));
						
						if($i < 10){
							?>
							<ul class="small-block-grid-3">
							  <li><? echo $row["sender"]; ?></li>
							  <li><? echo $date_made->format("H:i:s"); ?></li>
							  <li><? if((bool) $row["sent"]) echo $date_sent->format("H:i:s"); ?>
							  </li>
							</ul>
							<?
						}
						
						$i++;
				}
			}
		}
		?>
		</div>
		<div class="large-6 columns" ><h2>Statistics</h2>
				<div style="width:100%" id="plot"></div>
				<div style="width:100%" id="score"></div></div>
		</div>
	</div>
	<div> 
  				<h4>Logs</h4>
	</div>
	<!--Display logs-->
	<div> 
  			<a target="_blank" href="http://yo.founder.no/log/abakake.txt">Abakake</a> | 
  			<a target="_blank" href="http://yo.founder.no/log/abakaffe.txt">Abakaffe</a>
	</div>
	
	<!--Display Founder logo-->
  	<div> 
  			<a href="http://founder.no">
  				<img src="http://www.founder.no/wp-content/uploads/2012/01/founder2252.png" />
  			</a>
	</div>
	
	<!--Display fork me button-->
	<a href="https://github.com/mjansrud"><img style="position: absolute; top: 0; left: 0; border: 0;" src="https://camo.githubusercontent.com/8b6b8ccc6da3aa5722903da7b58eb5ab1081adee/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f6c6566745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_left_orange_ff7600.png"></a>
 </body>
</html>
