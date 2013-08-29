<?php

session_start();

unset($_SESSION['editting']);

$timeline = $_POST['timeline'];

//get database info
include 'install/db_info.php';
//connect to database
try{
	$conn=new PDO('mysql:dbname='.$database.';host=localhost;port=3306',$username,$password);
}
catch(PDOException $ex){
    die('Could not connect: '.$ex->getMessage());
}

$query = $conn->prepare("DROP TABLE " . $timeline);
$query->execute();

$query = $conn->prepare("DELETE FROM meta WHERE timelineName=?");
$query->execute(array($timeline));

?>