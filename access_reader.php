<?php

include_once("config.php");
include_once("send_mail.php");

//PPLE Functions

function update_token($profile, $login, $password, $token){
	$con = get_connection();
	if($con){ 
		if(login($con, $login, $profile, $password) == false){
			$message = "Error: Login failed";
		}
		
		$sql = "update login set token = '$token' where profile = '$profile' and id = '$login' and password = '". md5($password) ."'";
		mysql_query($sql, $con);
		if(mysql_affected_rows() > 0){
			$message = "Successful";
		}else{
			$sql = "select * from login where token = '$token' and profile = '$profile' and id = '$login' and password = '".md5($password)."'";
			$resultset = mysql_query($sql, $con);
			if(mysql_num_rows($resultset) > 0){
				$message = "Successful";
			}else{
				$message = "Update Token Fail";
			}
		}
		
		mysql_close($con);
		return $message;
	}
	return "Error: Cannot Connect";
}

function signup_pple($email){
	$con = get_connection();
	if($con){ 
		
		$sql = "select * from login where profile = 'profile' and id = '$email'";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) > 0){
			return "A user with the same email address already exists";
		}
		
		date_default_timezone_set('Asia/Singapore');
		$newPassword = $newLogin.rand().date('YmdHis');
		$newPassword = md5($newPassword);
		$newPassword = substr($newPassword, 0, 5);
		$hashedPassword = md5($newPassword);
		
		$hash = md5(date('YmdHis').$newLogin);
		
		$message = "";
		$sql = "insert into login(id, password, profile, people, verified) values ('$email', '$hashedPassword', 'profile', '$email', '$hash')";
		mysql_query($sql, $con);
		
		$timestamp = date('YmdHis');
		$sql = "insert into result(`context`,`attribute`,`index`,`value`,`profile`,`timestamp`,`status`) values ('people','#login','$email','$email','profile','$timestamp','I')";
		mysql_query($sql, $con);
		
		if(mysql_affected_rows() > 0){
			$result = sendConfirmationEmail($email, $hash);
			$message = "Thank you for signing up, an email will be sent to the account for verification";
		}else{
			$message = "Creation of the login is unsuccessful";
		}
		
		mysql_close($con);
		return $message;
	}
	return "Error: Cannot Connect";
}

function create_login($profile, $login, $password, $newLogin, $peopleIndex){
	$con = get_connection();
	if($con){ 
		if(login($con, $login, $profile, $password) == false){
			return "Error: Login failed";
		}
		
		$sql = "select * from login where profile = '$profile' and id = '$newLogin'";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) > 0){
			return "Error: Login ".$newLogin." already exists";
		}
		
		date_default_timezone_set('Asia/Singapore');
		$newPassword = $newLogin.rand().date('YmdHis');
		$newPassword = md5($newPassword);
		$newPassword = substr($newPassword, 0, 5);
		$hashedPassword = md5($newPassword);
		
		$hash = md5(date('YmdHis').$newLogin);
		
		$message = "";
		$sql = "insert into login(id, password, profile, people, verified) values ('$newLogin', '$hashedPassword', '$profile', '$peopleIndex', '$hash')";
		mysql_query($sql, $con);
		
		$timestamp = date('YmdHis');
		$sql = "insert into result(`context`,`attribute`,`index`,`value`,`profile`,`timestamp`,`status`) values ('groupMember','#login','$peopleIndex','$newLogin','$profile','$timestamp','I')";
		mysql_query($sql, $con);
		
		if(mysql_affected_rows() > 0){
			$result = sendConfirmationEmail($newLogin, $hash);
			$message = "An email will be sent to the account for verification";
		}else{
			$message = "Creation of the login is unsuccessful";
		}
		
		mysql_close($con);
		return $message;
	}
	return "Error: Cannot Connect";
}

function set_login_radar_groups($profile, $login, $password, $targetLogin, $influenceList){
	$con = get_connection();
	if($con){
		if(login($con, $login, $profile, $password) == false){
			return "Error: Login failed";
		}
		
		$sql = "insert into reset_profile(`id`,`profile`) values ('$targetLogin', '$profile')";
		mysql_query($sql, $con);
		
		foreach($influenceList as $influence){
			if($influence["access"] == "1"){
				$sql = "select * from radar where `id` = '".$influence["login"]."' ";
				$sql = $sql . "and `profile` = '".$influence["profile"]."' ";
				$sql = $sql . "and `context` = '".$influence["context"]."' ";
				$sql = $sql . "and `index` = '".$influence["index"]."' ";
				mysql_query($sql, $con);
				
				if(mysql_affected_rows() == 0){
					$sql = "insert into radar (`id`, `profile`, `context`, `index`) values (";
					$sql = $sql . "'" . $influence["login"] . "',";
					$sql = $sql . "'" . $influence["profile"] . "',";
					$sql = $sql . "'" . $influence["context"] . "',";
					$sql = $sql . "'" . $influence["index"] . "'";
					$sql = $sql . ")";
				}
			}else{
				$sql = "delete from radar where `id` = '".$influence["login"]."' ";
				$sql = $sql . "and `profile` = '".$influence["profile"]."' ";
				$sql = $sql . "and `context` = '".$influence["context"]."' ";
				$sql = $sql . "and `index` = '".$influence["index"]."' ";
			}
			mysql_query($sql, $con);
		}
		
		mysql_close($con);
		return "OK";
	}
	return "Error: Cannot Connect";
}

function set_login_influence_groups($profile, $login, $password, $targetLogin, $influenceList){
	$con = get_connection();
	if($con){
		if(login($con, $login, $profile, $password) == false){
			return "Error: Login failed";
		}
		
		$sql = "insert into reset_profile(`id`,`profile`) values ('$targetLogin', '$profile')";
		mysql_query($sql, $con);
		
		foreach($influenceList as $influence){
			if($influence["access"] == "1"){
				
				$sql = "select * from influence where `id` = '".$influence["login"]."' ";
				$sql = $sql . "and `profile` = '".$influence["profile"]."' ";
				$sql = $sql . "and `context` = '".$influence["context"]."' ";
				$sql = $sql . "and `index` = '".$influence["index"]."' ";
				mysql_query($sql, $con);
				
				if(mysql_affected_rows() == 0){
					$sql = "insert into influence (`id`, `profile`, `context`, `index`) values (";
					$sql = $sql . "'" . $influence["login"] . "',";
					$sql = $sql . "'" . $influence["profile"] . "',";
					$sql = $sql . "'" . $influence["context"] . "',";
					$sql = $sql . "'" . $influence["index"] . "'";
					$sql = $sql . ")";
				}
				
			}else{
				$sql = "delete from influence where `id` = '".$influence["login"]."' ";
				$sql = $sql . "and `profile` = '".$influence["profile"]."' ";
				$sql = $sql . "and `context` = '".$influence["context"]."' ";
				$sql = $sql . "and `index` = '".$influence["index"]."' ";
			}
			mysql_query($sql, $con);
		}
		
		mysql_close($con);
		return "OK";
	}
	return "Error: Cannot Connect";
}

function remove_login($profile, $login, $password, $targetLogin){
	$con = get_connection();
	if($con){
		if(login($con, $login, $profile, $password) == false){
			return "Error: Login failed";
		}
		
		$sql = "delete from influence where profile = '$profile' and id = '$targetLogin'";
		mysql_query($sql, $con);
		
		$sql = "delete from login where profile = '$profile' and id = '$targetLogin'";
		mysql_query($sql, $con);
		
		mysql_close($con);
		return "OK";
	}
	return "Error: Cannot Connect";
}

function set_reset_profile($login, $profile){
	$con = get_connection();
	if($con){
		$sql = "insert into reset_profile(`id`,`profile`) values ('$login', '$profile')";
		mysql_query($sql, $con);
		
		return mysql_affected_rows();
	}
	return 0;
}

function clear_reset_profile($login, $profile){
	$con = get_connection();
	if($con){
		$sql = "delete from reset_profile where `id` = '$login' and profile = '$profile'";
		mysql_query($sql, $con);
		return mysql_affected_rows();
	}
	return 0;
}

function check_reset_profile_needed($login, $profile){
	$con = get_connection();
	if($con){
		$sql = "select * from reset_profile where `id` = '$login' and `profile` = '$profile'";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) > 0){
			return 1;
		}else{
			return 0;
		}
	}
	return 1;
}

function get_login_access_groups($login, $profile){
	$con = get_connection();
	if($con){
		$sql = "(select `id`, `profile`, `context`, `index`, 'influence' as `access` from influence where id = '$login' and profile = '$profile')";
		$sql = $sql . " union ";
		$sql = $sql . "(select `id`, `profile`, `context`, `index`, 'radar' as `access` from radar where id = '$login' and profile = '$profile')";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) > 0){
			$result = array();
			while($obj = mysql_fetch_assoc($resultset)){
				array_push($result, $obj);
			}
			return $result;
		}
	}
	return "";
}

function get_influence_for_login($login, $profile){
	$con = get_connection();
	if($con){
		$sql = "select `id`, `profile`, `context`, `index`, 'influence' as `access` from influence where id = '$login' and profile = '$profile'";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) > 0){
			$result = array();
			while($obj = mysql_fetch_assoc($resultset)){
				array_push($result, $obj);
			}
			return $result;
		}
	}
	return "";
}

function get_radar_for_login($login, $profile){
	$con = get_connection();
	if($con){
		$sql = "select `id`, `profile`, `context`, `index`, 'radar' as `access` from radar where id = '$login' and profile = '$profile'";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) > 0){
			$result = array();
			while($obj = mysql_fetch_assoc($resultset)){
				array_push($result, $obj);
			}
			return $result;
		}
	}
	return "";
}

function get_login_for_people($peopleIndex, $profile){
	$con = get_connection();
	if($con){
		$sql = "select id from login where people = '$peopleIndex' and profile = '$profile'";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) > 0){
			$row = mysql_fetch_row($resultset);
			return $row[0];
		}
	}
	return "";
}

function login_exist($peopleIndex, $profile){
	$con = get_connection();
	if($con){
		$sql = "select * from login where people = '$peopleIndex' and profile = '$profile'";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) > 0){
			return 1;
		}
	}
	return 0;
}

function login_user($login, $password, $profile){
	$con = get_connection();
	if($con){
		$password = md5($password);
		$sql = "select id, password, verified from login where id = '$login' and password = '$password' and profile = '$profile'";
		$resultset = mysql_query($sql, $con);
		$message = "";
		if(mysql_num_rows($resultset) > 0){
			$row = mysql_fetch_row($resultset);
			if($row[2] == '1'){
				$message = "OK";
			}else{
				$message = "The user is not yet verified, please verify account first before logging in";
			}
		}else{
			$message = "Login Failed";
		}
		mysql_close($con);
		return $message;
	}
	return "Failed";
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

function reset_password($login, $profile){
	echo "1";
	$con = get_connection(); echo "2";
	if($con){echo "3";
		date_default_timezone_set('Asia/Singapore');
		$newPassword = $login.rand().date('YmdHis');
		$newPassword = md5($newPassword);
		$newPassword = substr($newPassword, 0, 5);
		$hashedPassword = md5($newPassword);
		$sql = "update login set password = '$hashedPassword' where id = '$login' and profile = '$profile'";
	echo $sql;
		mysql_query($sql, $con);
		sendResetPasswordEmail($login, $newPassword);
		
		return "A new password has been sent to your registered email account";
	}else{
		echo "4";
	}
}

function change_password($login, $old_password, $new_password, $new_password2, $profile){
	$con = get_connection();
	if($con){
		if($new_password != $new_password2){
			return "Error: Passwords does not match";
		}
		
		$old_password = md5($old_password);
		$sql = "select id, password from login where id = '$login' and password = '$old_password' and profile = '$profile'";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) == 0){
			return "Existing password is not correct";
		}else{
			$new_password = md5($new_password);
			$sql = "update login set password = '$new_password' where id = '$login' and profile = '$profile'";
			mysql_query($sql, $con);
			if(mysql_affected_rows() == 0){
				return "Error encountered while changing the password. Please try again later";
			}else{
				return "Password changed successfully";
			}
		}
	}
}

function sendConfirmationEmail($email, $hash){
	global $admin_domain;	
	$cfmreg = $admin_domain . "cfmreg.php?q=$hash";
	
	$subject = "Pple Account Confirmation";
	$body = "<html><body>";
	$body = $body . "This is a confirmation email to verify your registration for the Pple App<br/>";
	$body = $body . "Please copy and paste the link below to your browser to confirm your registration: <br/>";
	$body = $body . "$cfmreg";
	$body = $body . "<p>PPLE</p>";
	$body = $body . "</body></html>";
	$body = wordwrap($body, 70);
		
	return send_email($email, $subject, $body);
}

function send_verification($login, $profile){
	$con = get_connection();
	if($con){
		if(strpos($login, "@") >= 1 && strlen($login) >= 3 && strpos($login, "@") < (strlen($login) - 1)){
		}else{
			return "Email is invalid";
		}
		
		$password = md5($password);
		$sql = "select id, password, verified from login where id = '$login' and profile = '$profile'";
		$resultset = mysql_query($sql, $con);
		if(mysql_num_rows($resultset) > 0){
			$row = mysql_fetch_row($resultset);
			$hash = $row[2];
			if($hash == '1'){
				return "The account has already been verified, and we cannot send another verification";
			}else{
				$result = sendConfirmationEmail($login, $hash);
				return "An email will be sent to your account for verification";	
			}
		}else{
			return "The login account $login does not exists";
		}
	}
	return "Error connecting";
}

?>