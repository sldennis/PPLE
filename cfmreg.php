<?php

include_once("config.php");
include_once("send_mail.php");

$q = $_GET['q'];

//Database util
function get_connection(){
	global $db;
	
	$con = mysql_connect($db['profile']['server'], $db['profile']['user'], $db['profile']['password']);	
	if(!$con){
		die('cannot connect: '.mysql_error());
	}
	
	$db_selected = mysql_select_db($db['profile']['database'], $con);
	if(!$db_selected){
		die('cannot select database: '.mysql_error());
	}
	
	return $con;
}

function sendResetPasswordEmail($email, $newPassword){
	$from = $admin_email;
	$header = "From: $from";
	$subject = "Pple Account Password Reset";
	$body = "<html><body>";
	$body = $body . "Your new password for login account $email is <br/>";
	$body = $body . "<p>$newPassword</p>";
	$body = $body . "<p>PPLE</p>";
	$body = $body . "</body></html>";
	$body = wordwrap($body, 70);
	
	return send_email($email, $subject, $body);
}

function resetPassword($login, $profile, $con){
	date_default_timezone_set('Asia/Singapore');
	$newPassword = $login.rand().date('YmdHis');
	$newPassword = md5($newPassword);
	$newPassword = substr($newPassword, 0, 5);
	$hashedPassword = md5($newPassword);
	$sql = "update login set password = '$hashedPassword' where id = '$login' and profile = '$profile'";
	mysql_query($sql, $con);
	sendResetPasswordEmail($login, $newPassword);
}

function verifyLogin($hash){
	$con = get_connection();
	if($con){
		$sql = "select `id`,`profile` from login where verified = '$hash'";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) > 0){
			$row = mysql_fetch_row($resultset);
			$login = $row[0];
			$profile = $row[1];
			$sql = "update login set verified = '1' where `id` = '$login' and profile = '$profile'";
			mysql_query($sql, $con);
			if(mysql_affected_rows() > 0){
				resetPassword($login, $profile, $con);
				return "Account verified successfully, Thank you!";
			}else{
				return "Error verifying account";
			}
		}else{
			return "The url you entered does not exist in our database";
		}
	}	
}

echo verifyLogin($q);

?>