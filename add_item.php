<?php

echo '<form name="add_timeline_item">
		<label>year</label><input type="number" id="add_item_year" name="year" value="1970" />
		<br />
		<label>month</label><select id="add_item_month" name="month">
			<option value="January">January</option>
			<option value="February">February</option>
			<option value="March">March</option>
			<option value="April">April</option>
			<option value="May">May</option>
			<option value="June">June</option>
			<option value="July">July</option>
			<option value="August">August</option>
			<option value="September">September</option>
			<option value="October">October</option>
			<option value="November">November</option>
			<option value="December">December</option>
		</select>
		<br />
		<label>day</label><input type="number" id="add_item_day" name="day" value="1" />
	
		<br />
		
		<label>hour</label><input type="number" id="add_item_hour" name="hour" value="0" />
		<br />
		<label>minute</label><input type="number" id="add_item_minute" name="minute" value="0" />
		<br />
		<label>second</label><input type="number" id="add_item_second" name="second" value="0" />
	
		<br />
	
		<label for="add_item_type">select display type</label>
		<select id="add_item_type" name="item_type">
			<option value="t">text only</option>
			<option value="i">image only</option>
			<option value="ti">text and image</option>
		</select>
		
		<br />
		
		<label for="add_item_title">title</label>
		<input type="text" name="add_item_title" id="add_item_title" />
		
		<br />
		
		<label for="add_item_text">text</label>
		<textarea name="item_text" id="add_item_text"></textarea>
		
		<br />
		
		<label>image path</label><input type="text" id="add_item_image" name="image" value="images/" />
		
		<br />
		
		<input type="button" name="add_item" value="save" id="add_item_save" />
		<input type="button" name="cancel_add_item" id="cancel_add_item" value="cancel" />
	</form>';

?>