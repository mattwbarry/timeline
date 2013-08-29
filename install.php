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

//if config file exists, redirect to login page

//if info is submitted, go to 
if(isset($_POST['submit'])) {
	if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['database'])) {
		
	}
	else {
		echo '<script type="text/javascript">alert("make sure all the information is filled out!")</script>';
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>test page</title>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
		<script type="text/javascript" src="http://www.oryxwebstudio.com/js/animate-colors-min.js"></script>
		<script type="text/javascript" src="http://www.oryxwebstudio.com/js/animate-color.js"></script>
		<script type="text/javascript">
			
			$(document).ready(function() {
				
				
			})
		</script>
		<style type="text/css">
		body {
			margin: 0;
			padding: 0;
			font-family: helvetica;
			background: #eeeeee;
		}
		#install_wrapper {
			width: 800px;
			border: 1px solid black;
			border-radius: 10px;
			margin: 30px auto 0px;
			background: #ffffff;
		}
			#inner_wrapper {
				width: 700px;
				margin: 0px auto;
			}
				#info_wrapper {
					
				}
					#login {
						height: 300px;
					}
					#login #label {
						float: left;
					}
					#login #input {
						float: right;
					}
					
		</style>
	</head>
	
	<body>
		<div id="install_wrapper">
			<div id="inner_wrapper">
				<h2>
					Database login info
				</h2>
				<div id="info_wrapper">
					<form id="login" name="login" method="post">
						<div id="label">
							<label for="username">username</label>
							<br />
							<label for="password">password</label>
							<br />
							<label for="database">database name</label>
						</div>
						<div id="input">
							<input type="text" id="username" name="username" />
							<br />
							<input type="text" id="password" name="password" />
							<br />
							<input type="text" id="database" name="database" />
							<br />
							<input type="submit" value="submit" name="submit" />
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--
		get login info
		create database
		name first timeline
		create table
		
		go to backend.php
		-->

	</body>
</html>