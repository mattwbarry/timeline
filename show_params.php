<?php

$edit = $_POST['edit'];
$editting = $_POST['editting'];

//get database info
include 'install/db_info.php';
//connect to database
try{
	$conn=new PDO('mysql:dbname='.$database.';host=localhost;port=3306',$username,$password);
}
catch(PDOException $ex){
    die('Could not connect: '.$ex->getMessage());
}

$query = $conn->prepare("SELECT * FROM meta WHERE timelineName=?");
$query->execute(array($editting));
$timeline = $query->fetch(PDO::FETCH_ASSOC);

echo '<div id="date_range">
		<b>date range</b>
		<form method="post" name="date_range">
			<b>start date</b>
			<br />
			<label>year</label><input type="number" id="start_year" name="start_year" value="' . date('Y', $timeline['beginDate']) . '" />
			<br />
			<label>month</label><select id="start_month" name="start_month">
				<option value="January"'; if(date('m', $timeline['beginDate']) == 1){ echo 'selected="selected"'; } echo '>January</option>
				<option value="February"'; if(date('m', $timeline['beginDate']) == 2){ echo 'selected="selected"'; } echo '>February</option>
				<option value="March"'; if(date('m', $timeline['beginDate']) == 3){ echo 'selected="selected"'; } echo '>March</option>
				<option value="April"'; if(date('m', $timeline['beginDate']) == 4){ echo 'selected="selected"'; } echo '>April</option>
				<option value="May"'; if(date('m', $timeline['beginDate']) == 5){ echo 'selected="selected"'; } echo '>May</option>
				<option value="June"'; if(date('m', $timeline['beginDate']) == 6){ echo 'selected="selected"'; } echo '>June</option>
				<option value="July"'; if(date('m', $timeline['beginDate']) == 7){ echo 'selected="selected"'; } echo '>July</option>
				<option value="August"'; if(date('m', $timeline['beginDate']) == 8){ echo 'selected="selected"'; } echo '>August</option>
				<option value="September"'; if(date('m', $timeline['beginDate']) == 9){ echo 'selected="selected"'; } echo '>September</option>
				<option value="October"'; if(date('m', $timeline['beginDate']) == 10){ echo 'selected="selected"'; } echo '>October</option>
				<option value="November"'; if(date('m', $timeline['beginDate']) == 11){ echo 'selected="selected"'; } echo '>November</option>
				<option value="December"'; if(date('m', $timeline['beginDate']) == 12){ echo 'selected="selected"'; } echo '>December</option>
			</select>
			<br />
			<label>day</label><input type="number" id="start_day" name="start_day" value="' . date('d', $timeline['beginDate']) . '" />
		
			<br />
			
			<label>hour</label><input type="number" id="start_hour" name="start_hour" value="' . date('H', $timeline['beginDate']) . '" />
			<br />
			<label>minute</label><input type="number" id="start_min" name="start_minute" value="' . date('i', $timeline['beginDate']) . '" />
			<br />
			<label>second</label><input type="number" id="start_sec" name="start_second" value="' . date('s', $timeline['beginDate']) . '" />
			
			<br />
			
			<b>end date</b>
			<br />
			<label>year</label><input type="number" id="end_year" name="end_year" value="' . date('Y', $timeline['endDate']) . '" />
			<br />
			<label>month</label><select id="end_month" name="end_month">
				<option value="January"'; if(date('m', $timeline['endDate']) == 1){ echo 'selected="selected"'; } echo '>January</option>
				<option value="February"'; if(date('m', $timeline['endDate']) == 2){ echo 'selected="selected"'; } echo '>February</option>
				<option value="March"'; if(date('m', $timeline['endDate']) == 3){ echo 'selected="selected"'; } echo '>March</option>
				<option value="April"'; if(date('m', $timeline['endDate']) == 4){ echo 'selected="selected"'; } echo '>April</option>
				<option value="May"'; if(date('m', $timeline['endDate']) == 5){ echo 'selected="selected"'; } echo '>May</option>
				<option value="June"'; if(date('m', $timeline['endDate']) == 6){ echo 'selected="selected"'; } echo '>June</option>
				<option value="July"'; if(date('m', $timeline['endDate']) == 7){ echo 'selected="selected"'; } echo '>July</option>
				<option value="August"'; if(date('m', $timeline['endDate']) == 8){ echo 'selected="selected"'; } echo '>August</option>
				<option value="September"'; if(date('m', $timeline['endDate']) == 9){ echo 'selected="selected"'; } echo '>September</option>
				<option value="October"'; if(date('m', $timeline['endDate']) == 10){ echo 'selected="selected"'; } echo '>October</option>
				<option value="November"'; if(date('m', $timeline['endDate']) == 11){ echo 'selected="selected"'; } echo '>November</option>
				<option value="December"'; if(date('m', $timeline['endDate']) == 12){ echo 'selected="selected"'; } echo '>December</option>
			</select>
			<br />
			<label>day</label><input type="number" id="end_day" name="end_day" value="' . date('d', $timeline['endDate']) . '" />
		
			<br />
			
			<label>hour</label><input type="number" id="end_hour" name="end_hour" value="' . date('H', $timeline['endDate']) . '" />
			<br />
			<label>minute</label><input type="number" id="end_min" name="end_minute" value="' . date('i', $timeline['endDate']) . '" />
			<br />
			<label>second</label><input type="number" id="end_sec" name="end_second" value="' . date('s', $timeline['endDate']) . '" />
			
		</form>
	</div>
	<div id="select_start_item">
		<form method="post" name="list_timeline_items_for_edit" id="list_timeline_items_for_params">
			<b>select initial timeline state</b><br />
			<select size="20" name="start_list_timeline_items" id="start_list_timeline_items">';
			
			for ($i = 0; $i < count($edit); $i++) {
					echo '<option value="' . $i . '"'; if($i == $timeline['startDate']){ echo 'selected="selected"'; } echo '>' . $edit[$i] . '</option>';
				}
			
	echo	'</select>
		</form>
	</div>
	<div id="zoom_settings">
		<script type="text/javascript">zoomMinDB = ' . $timeline['zoomMin'] . '; zoomMaxDB = ' . $timeline['zoomMax'] . '; zoomStartDB = ' . $timeline['zoomStart'] . ';</script>
		<div id="zoom_range">
		
		</div>
		<div id="zoom_start">
			
		</div>
		<div id="zoom_start_level"></div>
	</div>
	<input type="button" id="submit_params" name="submit_params" value="save" />
	<input type="button" id="cancel_submit_params" name="cancel_submit_params" value="cancel" />';

?>