<?php

$editting = $_POST['editting'];
$deleting = $_POST['deleting'];

//get database info
include 'install/db_info.php';
//connect to database
try{
	$conn=new PDO('mysql:dbname='.$database.';host=localhost;port=3306',$username,$password);
}
catch(PDOException $ex){
    die('Could not connect: '.$ex->getMessage());
}

$query = $conn->prepare("DELETE FROM " . $editting . " WHERE title=?");
$query->execute(array($deleting));

?>