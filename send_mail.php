<?php
$included = ini_get("include_path");
ini_set("include_path", $included.":pear");
require_once("Mail.php");
require_once("config.php");

function send_email($to, $subject, $body){

	global $admin_smtp;
	global $admin_email;
	global $admin_email_password;

	$host = $admin_smtp;
	$username = $admin_email;
	$password = $admin_email_password;
echo $host." - ".$username;	
	$headers = array ('From' => $username,
				      'Return-Path' => $username,
	  				  'To' => $to,
	  				  'Content-Type' => "text/html; charset=iso-8859-1",
	  				  'Subject' => $subject);
	
	$smtp = Mail::factory('smtp',
	  						array ('host' => $host,
				     		   		'auth' => true,
			    			   		'username' => $username,
							   		'password' => $password
							  )
						);
	
	$mail = $smtp->send($to, $headers, $body);
	
	if (PEAR::isError($mail)) {
		return $mail->getMessage();
	} else {
		return true;
	}
}
?>