<?php

//get database info
include 'install/db_info.php';
//connect to database
try{
	$conn=new PDO('mysql:dbname='.$database.';host=localhost;port=3306',$username,$password);
}
catch(PDOException $ex){
    die('Could not connect: '.$ex->getMessage());
}

$timeline = $_POST['timelineName'];

//get metadata about timeline from db
$query = $conn->prepare("SELECT * FROM meta WHERE timelineName=?");
$query->execute(array($timeline));
$meta = $query->fetch(PDO::FETCH_ASSOC);

//get timeline info from db
$query = $conn->prepare("SELECT * FROM " . $timeline . " ORDER BY utcdate ASC");
$query->execute(array());
$apo = $query->fetchAll();

//define timeline frame
$start_date = $meta['beginDate'];//-220924800;//
$end_date = $meta['endDate'];//1682876184;//2934835200;//
$start_view = $meta['startDate'];
$zoom_start = $meta['zoomStart'];
$zoom_range_upper = $meta['zoomMax'];
$zoom_range_lower = $meta['zoomMin'];

//put utcdate's into their own array
$utc = array();
$apo_filtered = array();
foreach($apo as $utcdates) {
	if ($utcdates['utcdate'] >= $start_date) {
		if ($utcdates['utcdate'] <= $end_date) {
			array_push($utc, $utcdates['utcdate']);
			array_push($apo_filtered, $utcdates);
		}
	}
	else {
		$start_view -= 1;
	}
}

$results = array($start_date, $end_date, $start_view, $zoom_start, $zoom_range_upper, $zoom_range_lower, $utc, $apo_filtered);
echo json_encode($results);

?>