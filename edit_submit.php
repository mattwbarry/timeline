<?php
$id = $_POST['id'];
$editting = $_POST['timeline_editting'];
$year = $_POST['year'];
$month = $_POST['month'];
$day = $_POST['day'];
$hour = $_POST['hour'];
$minute = $_POST['minute'];
$second = $_POST['second'];
$title = $_POST['title'];
$text = $_POST['text'];
$image = $_POST['image'];
$type = $_POST['type'];

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
$date_str = $month . ' ' . $day . ' ,' . $year;

$date = strtotime($date_str . ' ' . $hour . ':' . $minute . ':' . $second);

$query = $conn->prepare("UPDATE " . $editting . " SET date=?, title=?, utcdate=?, maintext=?, image=?, type=? WHERE title='" . $id . "'");
$query->execute(array($date_str, $title, $date, $text, $image, $type));

if ($query->errorCode() != 0) {
	$arr = $query->errorInfo();
	echo $arr[2];	
}

?>