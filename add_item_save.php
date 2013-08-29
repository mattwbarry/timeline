<?php

$editting = $_POST['editting'];
$year = $_POST['year'];
$month = $_POST['month'];
$day = $_POST['day'];
$hour = $_POST['hour'];
$minute = $_POST['minute'];
$second = $_POST['second'];
$text = $_POST['item_text'];
$type = $_POST['item_type'];
$image = $_POST['image'];
$title = $_POST['item_title'];

//get database info
include 'install/db_info.php';
//connect to database
try{
	$conn=new PDO('mysql:dbname='.$database.';host=localhost;port=3306',$username,$password);
}
catch(PDOException $ex){
    die('Could not connect: '.$ex->getMessage());
}

//add item to the timeline
$date_str = $month . ' ' . $day . ' ,' . $year;

$date = strtotime($date_str . ' ' . $hour . ':' . $minute . ':' . $second);

//get editting info
$query = $conn->prepare("INSERT INTO " . $editting . " (date, title, utcdate, maintext, image, type) VALUES(?,?,?,?,?,?)");
$query->execute(array($date_str, $title, $date, $text, $image, $type));

$arr = $query->errorInfo();
echo json_encode($arr[2]);

?>