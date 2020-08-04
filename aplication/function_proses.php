<?php
	//Database initialized
	
	session_start();
	require_once('../db/config.php');
	$errmsg_arr = array();
	$errflag = false;
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}
	$db = mysql_select_db(DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	
	//Sanitize the POST values
	$nidlog = clean($_GET['nid']);
	$passlog = clean($_GET['password']);
	$nid = clean($_GET['nidsign']);
	$pass= clean($_GET['passwordsign']);
	$email = clean($_GET['emailsign']);
	$name = clean($_GET['namesign']);
	
	if($nidlog == ''){
	
		//Check for duplicate NID
		if($nid != '') {
			$quer = "SELECT * FROM user WHERE nid='$nid'";
			$res = mysql_query($quer);
			$baris = mysql_num_rows($res);
			
			if($baris > 0) {
				echo "<script>alert('NID already in use..');location='index.php';</script>";
			}
			else {
				$qry = "INSERT INTO user(nid, pass, name, email, role_user, status_user) VALUES('$nid','".md5($pass)."','$email', '$name', '2','0')";
				$result = @mysql_query($qry);
				
				//Check whether the query was successful or not
				if($result) {
					echo "<script>location='../index.html';</script>";
					//belum bisa tampilkan alert di index.html
					echo "<script>alert('Your Request Has Been Sent, Please Contact Administrator');location='../index.php';</script>";
					exit();
				}
			}
		}
	}
	else {
			$qry="SELECT * FROM user WHERE nid='$nidlog' AND pass='".md5($passlog)."' AND status_user='1'";
			$result=mysql_query($qry);
		
			if(mysql_num_rows($result) == 1) {
				
				//Login Successful
				session_regenerate_id();
				$member = mysql_fetch_assoc($result);
				$_SESSION['SESS_NID'] = $member['nid'];
				$_SESSION['SESS_NAME'] = $member['name'];
				$_SESSION['SESS_ROLE'] = $member['role_user'];
				session_write_close();
				header("location: ../dashboard.php");
				exit();
			}
			else{
				echo "<script>alert('Check Your Login...');document.location='../index.php';</script>";
				exit();
			}
	}

?>