<?php

$id = $_POST['id'];
$editting = $_POST['timeline_editting'];

//get database info
include 'install/db_info.php';
//connect to database
try{
	$conn=new PDO('mysql:dbname='.$database.';host=localhost;port=3306',$username,$password);
}
catch(PDOException $ex){
    die('Could not connect: '.$ex->getMessage());
}

$query = $conn->prepare("SELECT * FROM " . $editting . " WHERE title=? ORDER BY utcdate ASC");
$query->execute(array($id));
$item = $query->fetch(PDO::FETCH_ASSOC);
$item_date = $item['utcdate'];
echo '
<div id="edit_timeline_item">
<form method="post" name="edit_timeline_item">
	<input type="hidden" id="editting_this_title" name="editting_this_title" value="' . $id .  '" />
	<label>year</label><input type="number" id="edit_year" name="edit_year" value="' . date('Y', $item_date) . '" />
	<br />
	<label>month</label><select id="edit_month" name="edit_month">
		<option value="January"'; if(date('m', $item_date) == '1') {echo 'selected="selected"';}  echo '>January</option>
		<option value="February"'; if(date('m', $item_date) == '2') {echo 'selected="selected"';}  echo '>February</option>
		<option value="March"'; if(date('m', $item_date) == '3') {echo 'selected="selected"';}  echo '>March</option>
		<option value="April"'; if(date('m', $item_date) == '4') {echo 'selected="selected"';}  echo '>April</option>
		<option value="May"'; if(date('m', $item_date) == '5') {echo 'selected="selected"';}  echo '>May</option>
		<option value="June"'; if(date('m', $item_date) == '6') {echo 'selected="selected"';}  echo '>June</option>
		<option value="July"'; if(date('m', $item_date) == '7') {echo 'selected="selected"';}  echo '>July</option>
		<option value="August"'; if(date('m', $item_date) == '8') {echo 'selected="selected"';}  echo '>August</option>
		<option value="September"'; if(date('m', $item_date) == '9') {echo 'selected="selected"';}  echo '>September</option>
		<option value="October"'; if(date('m', $item_date) == '10') {echo 'selected="selected"';}  echo '>October</option>
		<option value="November"'; if(date('m', $item_date) == '11') {echo 'selected="selected"';}  echo '>November</option>
		<option value="December"'; if(date('m', $item_date) == '12') {echo 'selected="selected"';}  echo '>December</option>
	</select>
	<br />
	<label>day</label><input type="number" id="edit_day" name="edit_day" value="' . date('d', $item_date) . '" />

	<br />
	
	<label>hour</label><input type="number" id="edit_hour" name="edit_hour" value="' . date('H', $item_date) . '" />
	<br />
	<label>minute</label><input type="number" id="edit_minute" name="edit_minute" value="' . date('i', $item_date) . '" />
	<br />
	<label>second</label><input type="number" id="edit_second" name="edit_second" value="' . date('s', $item_date) . '" />

	<br />

	<label>select display type</label>
	<select id="edit_item_type" name="edit_item_type">
		<option value="t"'; if($item['type'] == 't') {echo 'selected="selected"';}  echo '>text only</option>
		<option value="i"'; if($item['type'] == 'i') {echo 'selected="selected"';}  echo '>image only</option>
		<option value="ti"'; if($item['type'] == 'ti') {echo 'selected="selected"';}  echo '>text and image</option>
	</select>
	
	<br />
	
	<label for="edit_item_title">title</label>
	<input type="text" name="edit_item_title" id="edit_item_title" value="' . $item['title'] . '" />
	
	<br />
	
	<label for="edit_item_text">text</label>
	<textarea name="edit_item_text" id="edit_item_text">' . $item['maintext'] . '</textarea>
	
	<br />
	
	<label>image path</label><input type="text" id="edit_item_image" name="image" value="' . $item['image'] . '" />
	
	<br />
	
	<input type="button" name="edit_item_submit_changes" id="edit_item_submit_changes" value="save" />
	<input type="button" name="edit_item_cancel_changes" id="edit_item_cancel_changes" value="canel" />
	<input type="button" name="edit_items_delete" id="edit_item_delete" value="delete" />
</form>
</div>';
?>