<?php

session_start();

//get database info
include 'install/db_info.php';
//connect to database
try{
	$conn=new PDO('mysql:dbname='.$database.';host=localhost;port=3306',$username,$password);
}
catch(PDOException $ex){
    die('Could not connect: '.$ex->getMessage());
}

//create new timeline
if(isset($_POST['create'])) {
	$create_timeline_name = str_replace(' ', '_', $_POST['timeline_name']);
	$query = $conn->prepare("CREATE TABLE " . $create_timeline_name . "(date VARCHAR(50) NOT NULL, text LONGTEXT NOT NULL, utcdate bigint(20) NOT NULL, maintext LONGTEXT NOT NULL, image VARCHAR(100) NOT NULL, type VARCHAR(10) NOT NULL, id int(50) AUTO_INCREMENT, INDEX (id))");
	$query->execute();
	
	$query = $conn->prepare("INSERT INTO meta (timelineName, zoomStart, beginDate, endDate, zoomMax, zoomMin, startDate) VALUES(?, 0, 0, 0, 0, 0, 0)");
	$query->execute(array($create_timeline_name));
	
	unset($_SESSION['editting']);
}

//get table names
$query = $conn->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = ? AND table_name <> 'meta'; ");
$query->execute(array($database));
$timelines = $query->fetchAll();

if(isset($_SESSION['editting']) && !isset($_POST['edit'])) {
	//get editting info
	$query = $conn->prepare("SELECT title FROM " . $_SESSION['editting'] . " ORDER BY utcdate ASC");
	$query->execute();
	$edit = $query->fetchAll();
}
//show timeline info to edit
if(isset($_POST['edit'])) {
	if(isset($_SESSION['editting'])) {
		unset($_SESSION['editting']);
	}
	
	$_SESSION['editting'] = $_POST['timeline_select_name'];
	
	//get editting info
	$query = $conn->prepare("SELECT title FROM " . $_SESSION['editting'] . " ORDER BY utcdate ASC");
	$query->execute();
	$edit = $query->fetchAll();
	
}

//print_r($_SESSION);

?>
<!DOCTYPE html>
<html>
	<head>
		<title>timeline backend</title>
		<link rel="stylesheet" type="text/css" href="http://www.oryxwebstudio.com/js/jQRangeSlider-5.1/css/iThing.css" />
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
		<script type="text/javascript" src="http://www.oryxwebstudio.com/js/jQRangeSlider-5.1/jQRangeSlider-min.js"></script>
		<script type="text/javascript" src="http://www.oryxwebstudio.com/js/animate-colors-min.js"></script>
		<script type="text/javascript" src="http://www.oryxwebstudio.com/js/animate-color.js"></script>
		<script type="text/javascript">
			
				<?php
					if(isset($_SESSION['editting'])) {
						echo 'editting = "' . $_SESSION['editting'] . '";';
						echo 'the_edit_array = new Array(';
						foreach ($edit as $edit_sub) {
							if ($edit_sub != end($edit)) {
								echo '"' . $edit_sub[0] . '", ';
							}
							else {
								echo '"' . $edit_sub[0] . '"';
							}
						}
						echo ')';
					}
				?>
			
			$(document).ready(function() {
				
				$('#add_timeline_item').html('');
				//toggle add new
				$('#add_new_item').live('click', function() {
					if ($('#add_timeline_item').html() == '') {
						$.ajax({
							type: 'POST',
							url: 'add_item.php',
							success: function(data) {
								$('#add_timeline_item').html(data);
							}
						})
					}
					else {
						$('#add_timeline_item').html('');
					}
				})
				$('#cancel_add_item').live('click', function() {
					$('#add_timeline_item').html('');
				})
				$('#add_item_save').live('click', function() {
					if ($('#item_title').val() != '' && $('#add_item_text').val() != '') {
						$.ajax({
							type: 'POST',
							url: 'add_item_save.php',
							data: {
								'editting': editting,
								'year': $('#add_item_year').val(),
								'month': $('#add_item_month').val(),
								'day': $('#add_item_day').val(),
								'hour': $('#add_item_hour').val(),
								'minute': $('#add_item_minute').val(),
								'second': $('#add_item_second').val(),
								'item_title': $('#add_item_title').val(),
								'item_text': $('#add_item_text').val(),
								'image': $('#add_item_image').val(),
								'item_type': $('#add_item_type').val()
							},
							success: function(data) {
								console.log('  data:  ' + JSON.stringify(data));
								console.log(editting);
								alert('item added')
								$('#add_timeline_item').html('')
							},
							error: function(data) {
								console.log('  data:  ' + JSON.stringify(data));
							}
						})
					}
					else {
						alert('make sure everything is filled out!');
					}
				})
				
				$('#list_timeline_items').html('');
				//toggle edit select
				$('#show_items_list').live('click', function() {
					if ($('#list_timeline_items').html() == '') {
						$.ajax({
							type: 'POST',
							url: 'edit_show.php',
							data: {'edit': the_edit_array},
							success: function(data) {
								$('#list_timeline_items').html(data);
							}
						})
					}
					else {
						$('#list_timeline_items').html('');
						$('#edit_timeline_item_wrapper').html('');
					}
				})
				$('#cancel_edit_item').live('click', function() {
					$('#list_timeline_items').html('');
				})
				
				//submit edit selection
				$('#edit_item_submit').live('click', function() {
					$.ajax({
						type: 'POST',
						url: 'edit_select.php',
						data: {
							'id': $('#list_timeline_items :selected').val(),
							'timeline_editting': editting
						},
						success: function(data) {
							$('#edit_timeline_item_wrapper').html(data);
						}
					})
				})
				
				//submit changes to the item 
				$('#edit_item_submit_changes').live('click', function() {
					$.ajax({
						type: 'POST',
						url: 'edit_submit.php',
						data: {
							'id': $('#editting_this_title').val(),
							'timeline_editting': editting,
							'year': $('#edit_year').val(),
							'month': $('#edit_month').val(),
							'day': $('#edit_day').val(),
							'hour': $('#edit_hour').val(),
							'minute': $('#edit_minute').val(),
							'second': $('#edit_second').val(),
							'title': $('#edit_item_title').val(),
							'type': $('#edit_item_type').val(),
							'image': $('#edit_item_image').val(),
							'text': $('#edit_item_text').val()
						},
						success: function(data) {
							$('#edit_timeline_item_wrapper').html('');
							$.ajax({
								type: 'POST',
								url: 'get_list_of_items.php',
								data: {'editting': editting},
								success: function(data) {
									the_edit_array = $.parseJSON(data);
									$.ajax({
										type: 'POST',
										url: 'edit_show.php',
										data: {'edit': the_edit_array},
										success: function(data) {
											$('#list_timeline_items').html(data);
										}
									})
								}
							})
							alert('item editted')
						}
					})
				})
				$('#edit_item_cancel_changes').live('click', function() {
					$('#edit_timeline_item_wrapper').html('');
				})
				$('#edit_item_delete').live('click', function() {
					deleting = $('#list_timeline_items :selected').val();
					$.ajax({
						type: 'POST',
						url: 'delete_item.php',
						data: {
							'editting': editting,
							'deleting': deleting
						},
						success: function() {
							alert(deleting + ' removed');
							window.location.reload;
						}
					})
				})
				
				$('#set_params').html('');
				//show timeline parameters
				$('#show_set_params').live('click', function() {
					if ($('#set_params').html() == '') {
						$.ajax({
							type: 'POST',
							url: 'show_params.php',
							data: {
								'edit': the_edit_array,
								'editting': editting
							},
							success: function(data) {
								$('#set_params').html(data);
								//timeline parameter sliders
								//150000, 75000, 25000, 10000, 2500, 1750, 1000, 800, 600, 500, 400, 300, 250, 200, 150, 100, 75, 50, 25, 15, 10, 5, 3, 2, 1, (100/365.25), , (50/365.25), (7/365.25), (1/365.25), (0.25/365.25), (0.10417/365.25), (0.04167/365.25), (0.0139/365.25), (0.00347/365.25), (0.00069/365.25);
								ranges = new Array('150000yr', '75000yr', '25000yr', '10000yr', '2500yr', '1750yr', '1000yr', '800yr', '600yr', '500yr', '400yr', '300yr', '250yr', '200yr', '150yr', '100yr', '75yr', '50yr', '25yr', '15yr', '10yr', '5yr', '3yr', '2yr', '1yr', '100d', '50d', '7d', '1d', '6hr');//, '2.5hr', '1hr', '20min', '5min', '1min');
								$('#zoom_range').rangeSlider({
									arrows: false,
									range: {min:0, max:ranges.length-1},
									bounds: {min:0, max:ranges.length-1},
									defaultValues: {min: zoomMinDB, max: zoomMaxDB},
									formatter: function(val) {
										return ranges[val]
									},
									step: 1
								});
								
								$('#zoom_start').slider({
									min: 0,
									max: ranges.length-1,
									step: 1,
									value: zoomStartDB,
									slide: function(event, ui) {
										$('#zoom_start_level').html('start zoom level:' + ranges[ui.value]);
									}
								})
								//set start level
								$('#zoom_start_level').html('start zoom level:' + ranges[$('#zoom_start').slider('value')]);
								
								//make sure zoom start is always in range bounds
								$('#zoom_range').bind('valuesChanging', function(e, data) {
									zoom_max = $('#zoom_range').rangeSlider('values').max; 
									zoom_min = $('#zoom_range').rangeSlider('values').min;
									startDate = $('#zoom_start').slider('value');
									if (zoom_max < startDate) {
										$('#zoom_start').slider('value', zoom_max);
									}
									if (zoom_min > startDate) {
										$('#zoom_start').slider('value', zoom_min);
									}
								})
							}
						})
					}
					else {
						$('#set_params').html('');
					}
				})
				
				//save timeline params
				$('#submit_params').live('click', function() {
					zoomMax = $('#zoom_range').rangeSlider('values').max; 
					zoomMin = $('#zoom_range').rangeSlider('values').min;
					zoomStart = $('#zoom_start').slider('value');
					if (zoomMin > zoomStart || zoomStart > zoomMax) {
						alert('the zoom start date is not in bounds');
					}
					else {
						$.ajax({
							type: 'POST',
							url: 'save_params.php',
							data: {
								'zoomMax': zoomMax,
								'zoomMin': zoomMin,
								'zoomStart': zoomStart,
								'dateBeginyr': $('#start_year').val(),
								'dateBeginmnth': $('#start_month').val(),
								'dateBegind': $('#start_day').val(),
								'dateBeginhr': $('#start_hour').val(),
								'dateBeginmin': $('#start_min').val(),
								'dateBeginsec': $('#start_sec').val(),
								'dateEndyr': $('#end_year').val(),
								'dateEndmnth': $('#end_month').val(),
								'dateEndd': $('#end_day').val(),
								'dateEndhr': $('#end_hour').val(),
								'dateEndmin': $('#end_min').val(),
								'dateEndsec': $('#end_sec').val(),
								'startDate': $('#start_list_timeline_items').val(),
								'editting': editting
							},
							success: function() {
								alert('parameters saved');
								$('#set_params').html('');
							}
						})
					}
				})
				$('#cancel_submit_params').live('click', function() {
					$('#set_params').html('');
				})
				
				//delete timeline
				$('#delete_timeline').live('click', function() {
					deleting = $('#timeline_delete_name').val();
					confirmed = confirm('Are you sure you want to delete the timeline: ' + deleting);
					if (confirmed == true) {
						$.ajax({
							type: 'POST',
							url: 'delete_timeline.php',
							data: {
								'timeline': deleting
							},
							success: function() {
								window.location.reload();
							}
						})
						alert(deleting + ' has been deleted');
					}
				})
				
			})
		</script>
		<style type="text/css">
		body {
			margin: 0;
			padding: 0;
			font-family: helvetica;
		}
		#wrapper {
			width: 800px;
			margin: 0px auto;
			border: 1px solid #777;
			border-radius: 10px;
			padding: 20px;
		}
			#create {
				width: 700px;
			}
			#edit {
				
			}
			#select_image {
				display: inline-block;
				height: 100px;
				overflow: scroll;
				border: 1px solid #bcbcbc;
			}
			h3:hover {
				cursor: pointer;
			}
			#list_timeline_items {
				min-width: 150px;
			}
			#zoom_settings {
				padding-top: 40px;
				height: 100px;
				width: 100%;
			}
			#zoom_start {
				margin: 45px auto 0px;
				width: 98%;
			}
			#submit_params, #cancel_submit_params {
				position: relative;
				top: -10px;
			}
			label {
				display: inline-block;
				width: 200px;
				border-bottom: 1px solid;
			}
		</style>
	</head>
	
	<body>
		<div id="wrapper">
			<div id="edit">
				<h1>edit</h1>
				<form method="post" name="select_timeline">
					<label for="timeline_select_name">select timeline</label>
					<select id="timeline_select_name" name="timeline_select_name">
						<?php
						foreach($timelines as $timeline) {
							echo '<option value="' . $timeline[0] . '"'; if(isset($_SESSION['editting']) && $_SESSION['editting'] == $timeline[0]) { echo 'selected="selected"'; } echo '>' . $timeline[0] . '</option>';
						}
						?>
					</select>
					<input type="submit" name="edit" value="select" />
				</form>
				<?php
					if(isset($_SESSION['editting'])) {
						echo '<h3 id="add_new_item">add item</h3>
							<div id="add_timeline_item">
								
							</div>
							<h3 id="show_items_list">edit item</h3>
							<div id="list_timeline_items">
								
							</div>
							<div id="edit_timeline_item_wrapper"></div>
							<h3 id="show_set_params">timeline settings</h3>
							<div id="set_params">
								
							</div>';
					}
				?>
			</div>
			<div id="create">
				<h1>new</h1>
				<form method="post" name="create_timeline">
					<label for="timeline_name">timeline name</label>
					<input type="text" id="timeline_name" name="timeline_name" />
					<input type="submit" name="create" value="create" />
				</form>
			</div>
			<div id="delete">
				<h1>delete</h1>
				<form method="post" name="delete_timeline">
					<label for="timeline_name">timeline name</label>
					<select id="timeline_delete_name" name="timeline_select_name">
						<?php
						foreach($timelines as $timeline) {
							echo '<option value=' . $timeline[0] . '>' . $timeline[0] . '</option>';
						}
						?>
					</select>
					<input type="submit" name="delete" value="delete" id="delete_timeline" />
				</form>
			</div>
		</div>
		
		<!--
			edit colors
			custom markers & arrows?
		-->

	</body>
</html>
