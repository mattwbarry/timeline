<?php

//get database info
include 'install/db_info.php';
//connect to database
try{
	$conn=new PDO('mysql:dbname='.$database.';host=localhost;port=3306',$username,$password);
}
catch(PDOException $ex){
    die('Could not connect: '.$ex->getMessage());
}

$timeline = 'timeline';

//get metadata about timeline from db
$query = $conn->prepare("SELECT * FROM meta WHERE timelineName=?");
$query->execute(array($timeline));
$meta = $query->fetch(PDO::FETCH_ASSOC);

//get timeline info from db
$query = $conn->prepare("SELECT * FROM " . $timeline . " ORDER BY utcdate ASC");
$query->execute(array());
$apo = $query->fetchAll();

//define timeline frame
$start_date = $meta['beginDate'];//-220924800;//
$end_date = $meta['endDate'];//1682876184;//2934835200;//
$start_view = $meta['startDate'];
$zoom_start = $meta['zoomStart'];
$zoom_range_upper = $meta['zoomMax'];
$zoom_range_lower = $meta['zoomMin'];

//put utcdate's into their own array
$utc = array();
$apo_filtered = array();
foreach($apo as $utcdates) {
	if ($utcdates['utcdate'] >= $start_date) {
		if ($utcdates['utcdate'] <= $end_date) {
			array_push($utc, $utcdates['utcdate']);
			array_push($apo_filtered, $utcdates);
		}
	}
	else {
		$start_view -= 1;
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>test page</title>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
		<script type="text/javascript" src="timeline.js"></script>
		<script type="text/javascript">

			//create array to hold utc dates
			utc = new Array(<?php foreach($utc as $utc) { echo $utc . ', '; } ?> 0);
			timejump_array = new Array(150000, 75000, 25000, 10000, 2500, 1750, 1000, 800, 600, 500, 400, 300, 250, 200, 150, 100, 75, 50, 25, 15, 10, 5, 3, 2, 1, (100/365.25), (50/365.25), (7/365.25), (1/365.25), (0.25/365.25));//, (0.10417/365.25), (0.04167/365.25), (0.0139/365.25), (0.00347/365.25), (0.00069/365.25));
			monthArray = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

			$(document).ready(function() {
				
				//$('#plug_wrap').timeline('timeline')

				//set time range and start dates
				secPerYear = 31556925.9936;
				startDate = <?php echo $start_date; ?> / secPerYear;
				endDate = <?php echo $end_date; ?> / secPerYear;
				startView = <?php echo $start_view; ?>;
				timespan = endDate - startDate;
				timejump_array_pos = <?php echo $zoom_start; ?>;
				timejump = timejump_array[timejump_array_pos];

				//get timelines utc time range
				startDateUTC = <?php echo $start_date; ?>;//-220924800//Date.parse('January 1, ' + startDate + ' GMT') / 1000;
				endDateUTC = <?php echo $end_date; ?>;//2934835200//Date.parse('January 1, ' + endDate + ' GMT') / 1000;
				dateFrameUTC = endDateUTC - startDateUTC;

				//set zoom levels
				zoomLvl = <?php echo $zoom_start; ?>;
				zoomRangeUpper = <?php echo $zoom_range_upper; ?>;
				zoomRangeLower = <?php echo $zoom_range_lower; ?>;

				//set timeline length relative to timespan
				timelineCardWidth = timespan * 20 * (10 / timejump);
				//Math.floor(timespan * 20 * (10 / timejump));
				$('#timeline_card').css('width', timelineCardWidth + 'px');

				//put numbers out onto the line
				$('#the_line').append('<div class="timeblock" id="year_empty"></div>');
				drawLine(startDateUTC, endDateUTC, timejump)

				//set line minimum width
				$('#the_line').css('min-width', $('#timeline_wrapper').width());

				//style the date markers
				spanWidth = $('#timeline_card').width() / ((timespan / timejump));
				$('.timeblock').css('width', /*spanWidth +*/ '200px');

				//set marker positions
				setMarkerPosition();

				//increase timeline_card by width of 1 item to give space for last marker
				$('#timeline_card').css('width', timelineCardWidth + $('.timeblock').width() + ($('.item_wrapper').width()*2) + 'px')

				//set width of infobox to width of timeline
				$('.info_item_wrapper').width($('#plug_wrap').width());
				//set infobox card witdth to fit items
				$('#info_card').width($('#info_card').children().length * $('.info_item_wrapper').width());

				//set start positions
				current = startView;
				setInfocardPosition();
				setTimelinePosition();
				setMarkerPosition();
				setAsActive();
				setAsOpaque();
				setCurrentCounter();

				//zoom controls
				$('#zoom_in').click(function() {
					if (zoomLvl < zoomRangeUpper) {
						timejump_array_pos += 1

						zoom(1);

						setMarkerPosition()

						setTimeout(function() {setTimelinePosition()}, 200);
					}
				})
				$('#zoom_out').click(function() {
					if (zoomLvl > zoomRangeLower && $('#the_line div').length * $('.timeblock').width() > $('#timeline_wrapper').width()) {
						timejump_array_pos -= 1;

						zoom(-1);

						setMarkerPosition()

						setTimeout(function() {setTimelinePosition()}, 200);
					}
				})

				//infobox controls, timeline autoscroll & opacity
				$('#next').click(function() {
					if (current < utc.length - 2) {
						setAsInactive();
						setAsClear();

						current += 1;

						setInfocardPosition();
						setTimelinePosition();

						setAsActive();

						setAsOpaque();

						setCurrentCounter();
					}
				})
				$('#prev').click(function() {
					if (current > 0) {
						setAsInactive();
						setAsClear();

						current -= 1;

						setInfocardPosition();
						setTimelinePosition();

						setAsActive();

						setAsOpaque();

						setCurrentCounter();
					}
				})

				//marker hover opacity
				$('.item_marker').hover(
					function() {
						time = $(this).attr('data-time');
						$('#bubble_' + time).css({opacity: '1.0', 'z-index': '2'});
						$('#arrow_' + time).css({opacity: '1.0', 'z-index': '2'});
					},
					function() {
						time = $(this).attr('data-time');
						$('#bubble_' + time + ':not(.active_time)').css({opacity: '0.03'});
						$('#arrow_' + time + ':not(.active_time)').css({opacity: '0.03'});
						$('#bubble_' + time).css({'z-index': '1'});
						$('#arrow_' + time).css({'z-index': '1'});
					}
				)

				//marker click make current
				$('.item_marker').click(function() {
					setAsClear();

					setAsInactive();

					utc_id = $(this).attr('data-time');
					current = $('.item_wrapper').index($('#' + utc_id));
					setCurrentCounter();
					setInfocardPosition();

					setAsActive();

					setAsOpaque();
				})

				//functions
				function zoom(zoomlvl) {

					timejumpholder = timejump;

					//timejump = current timejump_array value
					//level == current jump value / new jump value
					level = timejump/timejump_array[timejump_array_pos];
					timejump = timejump_array[timejump_array_pos];

					timelineCardWidth *= level;

					zoomLvl += zoomlvl;

					width_holder = $('.timeblock').width();
					$('#the_line').html('');
					$('#the_line').append('<div class="timeblock" id="year_empty"></div>');
					drawLine(startDateUTC, endDateUTC, timejump)

					$('.timeblock').width(width_holder);
					$('#timeline_card').animate({width: width_holder * ($('#the_line .timeblock').length /*+ 1*/)}, 1000);

					//timelineCardWidth *= level;
				}
				function drawLine(startDate, endDate, timejump) {

					startDate = new Date((startDate) * 1000)
					endDate = new Date((endDate) * 1000 )

					//if we are dealing in years
					if (timejump >= 1) {

						//convert to years for iteration in for loop
						startDate = startDate.getFullYear() + 1
						endDate = endDate.getFullYear() + 1
						//no need to edit timejump as it is already expressed in years

						for (i = startDate; i <= endDate; i+=timejump) {
							$('#the_line').append('<div class="timeblock" id="year_' + i + '">' + i + '</div>');
						}
					}
					//if we are dealing in months / days
					else if (timejump < 1 && timejump >= (1/365.25)) {

						//convert to seconds for iteration in for loop
						startDateSec = startDate.getTime() / 1000
						endDateSec = endDate.getTime() / 1000
						timejump *= secPerYear

						console.log('timejump: ' + timejump)

						for (i = startDateSec + 86400; i <= endDateSec; i+=timejump) {
							//get number month to display
							thisDate = new Date(i * 1000)
							dateDay = thisDate.getDate()
							dateMonth = thisDate.getMonth()
							dateYear = thisDate.getFullYear()

							$('#the_line').append('<div class="timeblock" id="year_' + i + '">' + monthArray[dateMonth] + ' ' + dateDay + ', ' + dateYear + '</div>');
						}
					}
					else if (timejump < (1/365.25)) {

						//convert to seconds for iteration in for loop
						startDateSec = startDate.getTime() / 1000
						endDateSec = endDate.getTime() / 1000
						timejump *= secPerYear

						console.log('timejump: ' + timejump)

						for (i = startDateSec + 21600; i <= endDateSec; i+=timejump) {
							//get number month to display
							thisDate = new Date(i * 1000)
							dateHour = thisDate.getHours()
							if (dateHour > 12) {
								dateHour -= 12
								dateHour += 'pm'
							}
							else if (dateHour == 0) {
								dateHour = 12
								dateHour += 'am'
							}
							else {
								dateHour += 'am'
							}
							dateDay = thisDate.getDate()
							dateMonth = thisDate.getMonth()
							dateYear = thisDate.getFullYear()

							$('#the_line').append('<div class="timeblock" id="year_' + i + '">' + dateHour + ' ' + monthArray[dateMonth] + ' ' + dateDay + ', ' + dateYear + '</div>');
						}
					}
					//if we are dealing in hours
					//if we are dealing in minutes
					//if we are dealing in seconds
				}
				function setMarkerPosition() {
					for (i = 0; i < utc.length-1; i++) {
						//this calculation is off by a tiny bit - negligible except when zoomed to days
						$('#' + utc[i]).css('left', ((utc[i] - startDateUTC)/dateFrameUTC) * timelineCardWidth - ($('.item_wrapper').width()/2) + $('.timeblock').width());
						$('#' + utc[i]).attr('data-left', ((utc[i] - startDateUTC)/dateFrameUTC) * timelineCardWidth - ($('.item_wrapper').width()/2) + $('.timeblock').width());
					}
				}
				function setAsActive() {
					$('#bubble_' + utc[current]).addClass('active_time');
					$('#arrow_' + utc[current]).addClass('active_time');
				}
				function setAsInactive() {
					$('#bubble_' + utc[current]).removeClass('active_time');
					$('#arrow_' + utc[current]).removeClass('active_time');
				}
				function setAsOpaque() {
					$('#bubble_' + utc[current]).css('opacity', 1.0);
					$('#arrow_' + utc[current]).css('opacity', 1.0);
				}
				function setAsClear() {
					$('#bubble_' + utc[current]).css('opacity', 0.03);
					$('#arrow_' + utc[current]).css('opacity', 0.03);
				}
				function setCurrentCounter() {
					$('#next_count').html(utc.length-2-current);
					$('#prev_count').html(current);
				}
				function setInfocardPosition() {
					$('#info_card').css('left', -$('.info_item_wrapper').width() * current);
				}
				function setTimelinePosition() {
					$('#timeline_wrapper').animate(
						{scrollLeft: $('#' + utc[current]).attr('data-left') - ($('#timeline_wrapper').width()/2) + $('.item_wrapper').width()},
						{duration: 1000, queue: false}
					);
				}
			})
		</script>
		<!--<link rel="stylesheet" type="text/css" href="timeline.css" />-->
		<style type="text/css">
			body {
	margin: 0;
	padding: 0;
	font-family: helvetica;
}
::-webkit-scrollbar {
	display: none;
}
#wrapper {
	height: 100%;
	width: 100%;
	background: green;
}
	#plug_wrap {
		height: 500px;
		width: 100%;/*500px;*/
		background: #a0a0a0;
		position: relative;
		top: 0px;
	}
	#info_wrapper {
		width: 100%;
		height: 400px;
		background: #ccc;
		overflow: hidden;
	}
		#info_card {
			position: relative;
			-moz-transition: left 1s;
			-webkit-transition: left 1s;
			-o-transition: left 1s;
		}
			.info_item_wrapper {
				float: left;
				background: #aaa;
				border-left: 1px solid #888;
				margin-left: -1px;
				height: 337px;
			}
				.info_bubble {
					margin: 20px auto;
					text-align: center;
					height: 287px;
					width: 50%;
					background: #ccc;
				}
					.headline {
						padding: 20px 0px;
						margin: 0;
					}
					.content_wrapper_wo_image {

					}
						.content_wrapper_wo_image .info_text {
							width: 80%;
							margin: 0px auto;
						}
					.content_wrapper_w_image_text {

					}
						.content_wrapper_w_image_text .info_image {
							width: 50%;
							float: left;
						}
							.info_image img {
								max-width: 200px;
								max-height: 200px;
							}
						.content_wrapper_w_image_text .info_text {
							width: 50%;
							float: right;
						}
					.content_wrapper_w_image {

					}
						.content_wrapper_w_image .info_image {
							width: 90%;
							margin: 0px auto
						}
							.info_image img {
								max-width: 400px;
								max-height: 200px;
							}
			#seek_controls {
				width: 120px;
				height: 60px;
				margin: 0px auto;
				background: #777777;
			}
				.seek_controls_button {
					width: 40px;
					height: 40px;
					margin: 10px;
					float: left;
					background: #DDDDDD;
				}
	#timeline_wrapper {
		height: 152px;
		width: 100%;
		background: #eee;
		overflow: scroll;
	}
	#timeline_card {
		position: relative;
		left: 0px;
		height: 100%;
		min-width: 100%;
		background: #ddd;
	}
		#timeline_controls {
			height: 125px;
			width: 30px;
			background: #777;
			position: absolute;
			z-index: 2;
		}
			.timeline_control_button {
				height: 20px;
				width: 20px;
				background: #aaa;
				margin: 3px;
			}
			.item_wrapper {
				position: absolute;
				width: 200px;
				height: 125px;
				-moz-transition: left 1s;
				-webkit-transition: left 1s;
				-o-transition: left 1s;
			}
				.item_bubble {
					position: relative;
					border-radius: 12px;
					padding: 10px 9px;
					border-left: 1px solid black;
					border-right: 1px solid black;
					height: 50px;
					text-align: center;
					opacity: 0.03;
					background: green;
					-moz-transition: opacity 1s;
					-webkit-transition: opacity 1s;
					-o-transition: opacity 1s;
				}
				.down_arrow {
					position: absolute;
					left: 90px;
					width: 0;
					height: 0;
					border-left: 10px solid transparent;
					border-right: 10px solid transparent;
					opacity: 0.03;
					border-top: 10px solid green;
					-moz-transition: opacity 1s;
					-webkit-transition: opacity 1s;
					-o-transition: opacity 1s;
				}
				.item_marker {
					margin: 0px auto;
					height: 20px;
					width: 9px;
					position: relative;
					top: 35px;
					text-align: center;
					z-index: 1;
				}
				.item_marker:hover {
					cursor: pointer;
				}
			#the_line {
				width: 100%;
				height: 10px;
				position: absolute;
				top: 125px;
				background: url('images/timeline_notch.png') repeat-x #eee;
				-moz-transition: width 1s;
				-webkit-transition: width 1s;
				-o-transition: width 1s;
			}
			.timeblock {
				float: left;
				padding-top: 9px;
				border-left: 1px solid #999;
				margin-left: -1px;
			}
		</style>
	</head>

	<body>
		<div id="wrapper">
			<div id="plug_wrap">
				<div id="info_wrapper">
					<div id="info_card">
						<?php

						foreach ($apo_filtered as $item) {
							if ($item['type'] == 'ti') {
								echo '<div class="info_item_wrapper" id="info_' . $item['utcdate'] . '">
										<div class="info_bubble">
											<h2 class="headline">' . $item['title'] . '</h2>
											<div class="content_wrapper_w_image_text">
												<div class="info_image">
													<img src="images/' . $item['image'] . '" alt="image" class="info_image_image" />
												</div>
												<div class="info_text">
													' . $item['maintext'] . '
												</div>
											</div>
										</div>
									</div>';
							}
							if ($item['type'] == 't') {
								echo '<div class="info_item_wrapper" id="info_' . $item['utcdate'] . '">
										<div class="info_bubble">
											<h2 class="headline">' . $item['title'] . '</h2>
											<div class="content_wrapper_wo_image">
												<div class="info_text">
													' . $item['maintext'] . '
												</div>
											</div>
										</div>
									</div>';
							}
							if ($item['type'] == 'i') {
								echo '<div class="info_item_wrapper" id="info_' . $item['utcdate'] . '">
										<div class="info_bubble">
											<h2 class="headline">' . $item['title'] . '</h2>
											<div class="content_wrapper_w_image">
												<div class="info_image">
													<img src="images/' . $item['image'] . '" alt="image" class="info_image_image" />
												</div>
											</div>
										</div>
									</div>';
							}
							if ($item['type'] == NULL) {
								echo '<div class="info_item_wrapper" id="info_' . $item['utcdate'] . '">
										<div class="info_bubble">
											<h2 class="headline">' . $item['title'] . '</h2>
										</div>
									</div>';
							}
						}

						?>
					</div>
					<div id="seek_controls">
						<div id="prev" class="seek_controls_button">
							prev
							<div id="prev_count">

							</div>
						</div>
						<div id="next" class="seek_controls_button">
							next
							<div id="next_count">

							</div>
						</div>
					</div>
				</div>
				<div id="timeline_wrapper">
					<div id="timeline_controls">
						<div id="zoom_in" class="timeline_control_button">

						</div>
						<div id="zoom_out" class="timeline_control_button">

						</div>
					</div>
					<div id="timeline_card">
						<?php

						foreach ($apo_filtered as $item) {
							echo '<div class="item_wrapper ellipsis" id=' . $item['utcdate'] . ' data-left="">
									<div class="item_bubble" id="bubble_' . $item['utcdate'] . '">
										' . $item['title'] . '
									</div>
									<div class="down_arrow" id="arrow_' . $item['utcdate'] . '">
									</div>
									<div class="item_marker" data-time=' . $item['utcdate'] . '>
										<img src="images/timeline_marker.png" class="timeline_marker" alt="marker" />
									</div>
								</div>';
						}

						?>
						<div id="the_line"></div>
					</div>
				</div>
			</div>
		</div>

	</body>
</html>

