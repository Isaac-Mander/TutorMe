<?php
//GLOBAL VARIABLES FOR DATABASE SETUP

//REMOVE BEFORE FINAL COMMIT


//Connect to database
include("db_connect.php");

//Get admin code
if(isset($_GET['school_name']) and isset($_GET['email']) and isset($_GET['password']) and isset($_GET['school_code']))
{
	//TABLE CREATION ======================================================================================================================
	//Code of school to be added to table name
	$school_name = $_GET['school_name'];
	$email = $_GET['email'];
	$password = $_GET['password'];
	$school_code = $_GET['school_code'];

	//School dependant sql query. Just adds school code to the name of each table
	$sql_admin_setup_query = "
	CREATE TABLE IF NOT EXISTS `{$school_code}_Teachers` (
		`id` int(4) NOT NULL AUTO_INCREMENT,
		`name` varchar(40) NOT NULL,
		`picture` varchar(100) NOT NULL,
		`description` varchar(140) NOT NULL,
		PRIMARY KEY (`id`)
	);
	CREATE TABLE IF NOT EXISTS `{$school_code}_Students` (
		`id` int(4) NOT NULL AUTO_INCREMENT,
		`name` varchar(40) NOT NULL,
		`picture` varchar(100) NOT NULL,
		`description` varchar(140) NOT NULL,
		`tutee_status` int(1) NOT NULL,
		PRIMARY KEY (`id`)
	);
	CREATE TABLE IF NOT EXISTS `{$school_code}_Subjects` (
		`id` int(4) NOT NULL AUTO_INCREMENT,
		`name` varchar(40) NOT NULL,
		PRIMARY KEY (`id`)
	);
	CREATE TABLE IF NOT EXISTS `{$school_code}_Year_Levels` (
		`id` int(4) NOT NULL AUTO_INCREMENT,
		`year` int(2) NOT NULL,
		PRIMARY KEY (`id`)
	);
	CREATE TABLE IF NOT EXISTS `{$school_code}_Ext_Tutor` (
		`id` int(4) NOT NULL AUTO_INCREMENT,
		`tutor_id` int(4) NOT NULL,
		PRIMARY KEY (`id`)
	);
	CREATE TABLE IF NOT EXISTS `{$school_code}_Tutor_Session` (
		`id` int(7) NOT NULL AUTO_INCREMENT,
		`tutee_id` int(4) NOT NULL,
		`tutor_id` int(4) NOT NULL,
		`teacher_id` int(4) NOT NULL,
		`ext_tutor_id` int(4) NOT NULL,
		`time` varchar(30) NOT NULL,
		`subject_id` int(4) NOT NULL,
		PRIMARY KEY (`id`)
	);
	CREATE TABLE IF NOT EXISTS `{$school_code}_Student_Teacher` (
		`id` int(4) NOT NULL AUTO_INCREMENT,
		`student_id` int(4) NOT NULL,
		`teacher_id` int(4) NOT NULL,
		PRIMARY KEY (`id`)
	);
	CREATE TABLE IF NOT EXISTS `{$school_code}_Subjects_Teacher` (
		`id` int(4) NOT NULL AUTO_INCREMENT,
		`teacher_id` int(4) NOT NULL,
		`global_subject_id` int(4) NOT NULL,
		`local_subject_id` int(4) NOT NULL,
		PRIMARY KEY (`id`)
	);
	CREATE TABLE IF NOT EXISTS `{$school_code}_Subjects_Tutee` (
		`id` int(4) NOT NULL AUTO_INCREMENT,
		`tutee_id` int(4) NOT NULL,
		`global_subject_id` int(4) NOT NULL,
		`local_subject_id` int(4) NOT NULL,
		PRIMARY KEY (`id`)
	);
	CREATE TABLE IF NOT EXISTS `{$school_code}_Subjects_Tutor` (
		`id` int(4) NOT NULL AUTO_INCREMENT,
		`tutor_id` int(4) NOT NULL,
		`global_subject_id` int(4) NOT NULL,
		`local_subject_id` int(4) NOT NULL,
		PRIMARY KEY (`id`)
	);";
	echo $sql_admin_setup_query;
	//Run tables creation query
	if(mysqli_query($conn, $sql_admin_setup_query))
	{


		//INSERT REQUIRED DATA INTO TABLES ======================================================================================================================
		$admin_insert_sql = "INSERT INTO `admin`(`school_name`, `email`, `password`, `school_code`) VALUES ('$school_name','$email','$password','$school_code')";
		echo $admin_insert_sql;
		mysqli_query($conn, $admin_insert_sql);


	}
	//If something went wrong
	else
	{
		header("Location: error.php?error=QueryFailed".mysqli_error($conn));
	}


	//Run insert query
}


//If school code not set redirect to error page
else
{
	header("Location: error.php?error=AdminCreationFormValuesNotSet");
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>admin_setup</title>
</head>
<body>
	<!-- REMOVE THIS AS OUTPUTS SQL QUERY SO SECURITY RISK -->
	<p><?php // echo $sql_admin_setup_query; ?></p>
</body>
</html>

