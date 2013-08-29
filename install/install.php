<?php

//get database info
include 'db_info.php';
//connect to database
try{
	$conn=new PDO('mysql:dbname='.$database.';host=localhost;port=3306',$username,$password);
}
catch(PDOException $ex){
    die('Could not connect: '.$ex->getMessage());
}

//get metadata about timeline from db
$query = $conn->prepare("CREATE TABLE meta (timelineName varchar(200), zoomStart int(5), beginDate bigint(20), endDate bigint(20), zoomMax int(5), zoomMin int(5), startDate int(3))");
$query->execute(array());

$e1 = $query->errorInfo();

$query = $conn->prepare("INSERT INTO meta (timelineName, zoomStart, beginDate, endDate, zoomMax, zoomMin, startDate) VALUES (?,?,?,?,?,?,?)");
$query->execute(array('timeline', 0, 0, 0, 0, 0, 0));

$query = $conn->prepare("CREATE TABLE timeline (date varchar(50), title longtext, utcdate bigint(20), maintext longtext, image varchar(200), type varchar(10), id int(50) AUTO_INCREMENT, INDEX (id))");
$query->execute(array());

$e2 = $query->errorInfo();

echo 'errors: ' . json_encode($e1[2]) . '<br />';
echo 'errors: ' . json_encode($e2[2]) . '<br />';
echo 'if errors are null, the database has been set up properly';

?>