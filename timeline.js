(function($) {
	$.fn.timeline = function(timelineName) {
		
		theItem = this
		theItem = theItem.attr('id')
		//MAKE AJAX REQUEST TO PULL INFO FROM PHP
		$.ajax({
			type: 'POST',
			url: 'initTimeline.php',
			data: {
				'timelineName': timelineName
			},
			success: function(data) {
				//put php results into arrays etc.
				data = JSON.parse(data)
				
				utc_length = data[6].length
				utc = []
				for (i = 0; i < utc_length; i++) {
					utc.push(data[6][i])
				}
				utc.push(0)
				//utc = new Array(<?php foreach($utc as $utc) { echo $utc . ', '; } ?> 0);
				timejump_array = new Array(150000, 75000, 25000, 10000, 2500, 1750, 1000, 800, 600, 500, 400, 300, 250, 200, 150, 100, 75, 50, 25, 15, 10, 5, 3, 2, 1, (100/365.25), (50/365.25), (7/365.25), (1/365.25), (0.25/365.25));//, (0.10417/365.25), (0.04167/365.25), (0.0139/365.25), (0.00347/365.25), (0.00069/365.25));
				monthArray = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
				
				//set time range and start dates
				secPerYear = 31556925.9936;
				startDate = data[0] / secPerYear;
				endDate = data[1] / secPerYear;
				startView = data[2];
				timespan = endDate - startDate;
				timejump_array_pos = parseInt(data[3]);
				timejump = timejump_array[timejump_array_pos];
	
				//get timelines utc time range
				startDateUTC = data[0];
				endDateUTC = data[1];
				dateFrameUTC = endDateUTC - startDateUTC;
	
				//set zoom levels
				zoomLvl = data[3];
				zoomRangeUpper = data[4];
				zoomRangeLower = data[5];
				
				//CREATE THE TIMELINE ITSELF IN THE DOM
				$('#' + theItem).append('<div id="info_wrapper"> \
						<div id="info_card">')
						for (i = 0; i < data[7].length; i++) {
							if (data[7][i]['type'] == 'ti') {
								$('#info_card').append('<div class="info_item_wrapper" id="info_' + data[7][i]['utcdate'] + '"> \
									<div class="info_bubble"> \
										<h2 class="headline">' + data[7][i]['title'] + '</h2> \
										<div class="content_wrapper_w_image_text"> \
											<div class="info_image"> \
												<img src="'  + data[7][i]['image'] + '" alt="image" class="info_image_image" /> \
											</div> \
											<div class="info_text"> \
												' + data[7][i]['maintext'] + ' \
											</div> \
										</div> \
									</div> \
								</div>')
							}
							else if (data[7][i]['type'] == 't') {
								$('#info_card').append('<div class="info_item_wrapper" id="info_' + data[7][i]['utcdate'] + '"> \
										<div class="info_bubble"> \
											<h2 class="headline">' + data[7][i]['title'] + '</h2> \
											<div class="content_wrapper_wo_image"> \
												<div class="info_text"> \
													' + data[7][i]['maintext'] + ' \
												</div> \
											</div> \
										</div> \
									</div>')
							}
							else if (data[7][i]['type'] == 'i') {
								$('#info_card').append('<div class="info_item_wrapper" id="info_' + data[7][i]['utcdate'] + '"> \
										<div class="info_bubble"> \
											<h2 class="headline">' + data[7][i]['title'] + '</h2> \
											<div class="content_wrapper_w_image"> \
												<div class="info_image"> \
													<img src="' + data[7][i]['image'] + '" alt="image" class="info_image_image" /> \
												</div> \
											</div> \
										</div> \
									</div>')
							}
							else if (data[7][i]['type'] == null) {
								$('#info_card').append('<div class="info_item_wrapper" id="info_' + data[7][i]['utcdate'] + '"> \
										<div class="info_bubble"> \
											<h2 class="headline">' + data[7][i]['title'] + '</h2> \
										</div> \
									</div>')
							}
						}
						
					$('#info_wrapper').append('</div> \
					<div id="seek_controls"> \
						<div id="prev" class="seek_controls_button"> \
							prev \
							<div id="prev_count"> \
							</div> \
						</div> \
						<div id="next" class="seek_controls_button"> \
							next \
							<div id="next_count"> \
							</div> \
						</div> \
					</div> \
				</div>')
				$('#' + theItem).append('<div id="timeline_wrapper"> \
					<div id="timeline_controls"> \
						<div id="zoom_in" class="timeline_control_button"> \
						</div> \
						<div id="zoom_out" class="timeline_control_button"> \
						</div> \
					</div> \
					<div id="timeline_card">')
						for (i = 0; i < data[7].length; i++) {
							$('#timeline_card').append('<div class="item_wrapper ellipsis" id=' + data[7][i]['utcdate'] + ' data-left=""> \
									<div class="item_bubble" id="bubble_' + data[7][i]['utcdate'] + '"> \
										' + data[7][i]['title'] + ' \
									</div> \
									<div class="down_arrow" id="arrow_' + data[7][i]['utcdate'] + '"> \
									</div> \
									<div class="item_marker" data-time=' + data[7][i]['utcdate'] + '> \
										<img src="images/timeline_marker.png" class="timeline_marker" alt="marker" /> \
									</div> \
								</div>')
						}
						
						$('#timeline_card').append('<div id="the_line"></div> \
					</div> \
				</div>')
				
				//SET INITIAL VALUES, CREATE LISTENERS

				//$(document).ready(function() {
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
				$('.timeblock').css('width', '200px');
		
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
			}
		})
		
		//CREATE LISTENERS

		//zoom controls
		$('#zoom_in').live('click', function() {
			if (zoomLvl < zoomRangeUpper) {
				timejump_array_pos += 1
				
				zoom(1);

				setMarkerPosition()

				setTimeout(function() {setTimelinePosition()}, 200);
			}
		})
		$('#zoom_out').live('click', function() {
			if (zoomLvl > zoomRangeLower && $('#the_line div').length * $('.timeblock').width() > $('#timeline_wrapper').width()) {
				timejump_array_pos -= 1;

				zoom(-1);

				setMarkerPosition()

				setTimeout(function() {setTimelinePosition()}, 200);
			}
		})

		//infobox controls, timeline autoscroll & opacity
		$('#next').live('click', function() {
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
		$('#prev').live('click', function() {
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
		$('.item_marker').live({
			mouseenter:
			function() {
				time = $(this).attr('data-time');
				$('#bubble_' + time).css({opacity: '1.0', 'z-index': '2'});
				$('#arrow_' + time).css({opacity: '1.0', 'z-index': '2'});
			},
			mouseleave:
			function() {
				time = $(this).attr('data-time');
				$('#bubble_' + time + ':not(.active_time)').css({opacity: '0.03'});
				$('#arrow_' + time + ':not(.active_time)').css({opacity: '0.03'});
				$('#bubble_' + time).css({'z-index': '1'});
				$('#arrow_' + time).css({'z-index': '1'});
			}
		})

		//marker click make current
		$('.item_marker').live('click', function() {
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
			level = timejump / timejump_array[timejump_array_pos];
			timejump = timejump_array[timejump_array_pos];
			timelineCardWidth *= level;

			zoomLvl += zoomlvl;

			width_holder = $('.timeblock').width();
			$('#the_line').html('');
			$('#the_line').append('<div class="timeblock" id="year_empty"></div>');
			drawLine(startDateUTC, endDateUTC, timejump)

			$('.timeblock').width(width_holder);
			$('#timeline_card').animate({width: width_holder * ($('#the_line .timeblock').length)}, 1000);
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
		//})

	}
})(jQuery)
