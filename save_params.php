<?php

$zoomStart = $_POST['zoomStart'];
$zoomMax = $_POST['zoomMax'];
$zoomMin = $_POST['zoomMin'];
$beginYear = $_POST['dateBeginyr'];
$beginMonth = $_POST['dateBeginmnth'];
$beginDay = $_POST['dateBegind'];
$beginHour = $_POST['dateBeginhr'];
$beginMin = $_POST['dateBeginmin'];
$beginSec = $_POST['dateBeginsec'];
$endYear = $_POST['dateEndyr'];
$endMonth = $_POST['dateEndmnth'];
$endDay = $_POST['dateEndd'];
$endHour = $_POST['dateEndhr'];
$endMin = $_POST['dateEndmin'];
$endSec = $_POST['dateEndsec'];
$startDate = $_POST['startDate'];
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


//edit the item
$beginDateStr = $beginMonth . ' ' . $beginDay . ' ,' . $beginYear;
$beginDate = strtotime($beginDateStr . ' ' . $beginHour . ':' . $beginMin . ':' . $beginSec);
$endDateStr = $endMonth . ' ' . $endDay . ' ,' . $endYear;
$endDate = strtotime($endDateStr . ' ' . $endHour . ':' . $endMin . ':' . $endSec);

if ($endDate < $beginDate) {
	$holder = $endDate;
	$endDate = $beginDate;
	$beginDate = $holder;
}

$query = $conn->prepare("UPDATE meta SET zoomStart=?, beginDate=?, endDate=?, zoomMax=?, zoomMin=?, startDate=? WHERE timelineName=?");
$query->execute(array($zoomStart, $beginDate, $endDate, $zoomMax, $zoomMin, $startDate, $editting));


?>