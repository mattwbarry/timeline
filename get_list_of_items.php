<?php

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

//get editting info
$query = $conn->prepare("SELECT title FROM " . $editting . " ORDER BY utcdate ASC");
$query->execute();
$edit = $query->fetchAll();

$edit_arr = Array();
foreach ($edit as $edit_sub) {
	array_push($edit_arr, $edit_sub[0]);
/*	if ($edit_sub != end($edit)) {
		echo '"' . $edit_sub[0] . '", ';
	}
	else {
		echo '"' . $edit_sub[0] . '"';
	}*/
}
echo json_encode($edit_arr);

?>