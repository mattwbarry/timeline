<?php

$edit = $_POST['edit'];

echo '<form method="post" name="list_timeline_items_for_edit" id="list_timeline_items_for_edit">
		<b>select item to edit</b><br />
		<select size="20" name="list_timeline_items" id="list_timeline_items">';
		
		for ($i = 0; $i < count($edit); $i++) {
				echo '<option value="' . $edit[$i] . '">' . $edit[$i] . '</option>';
			}
		
echo	'</select>
		<input type="button" id="edit_item_submit" name="edit_item" value="select" />
		<input type="button" id="cancel_edit_item" value="cancel" />
	</form>';
								
?>