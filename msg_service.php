<?php

include_once("notification.php");

function send_event_notification($profile, $senderIndex, $senderPassword, $receiverContext, $receiverIndex, $message, $eventRid){
	
	$con = get_connection();
	if($con){ 
		
		$sql = "select token from login where `people` = '$senderIndex' and password = '".md5($senderPassword)."' and profile = '$profile'";
		$resultset = mysql_query($sql, $con);
		
		if(mysql_num_rows($resultset) == 0){
			return "login failed";
		}
		
		$sent = 0;
		if($receiverContext == 'group'){
			date_default_timezone_set('Asia/Singapore');
			$timestamp = date("YmdHis");
			
			$peopleList = getAllMembersPeopleIndexInGroup($profile, $receiverIndex);
			
			foreach($peopleList as $peopleIndex){
				$sql = "select token from login where profile = '$profile' and people = '$peopleIndex'";
				$tokenSet = mysql_query($sql, $con);
				while($tokenRow = mysql_fetch_row($tokenSet)){
					$token = $tokenRow[0];
					if($token != null && strlen($token) > 0){
						$sent += send_notification_for_event($token, $message, 0, $eventRid, $profile);	
					}
				}
			}
			
			$sql = "insert into group_msg(`profile`,`senderContext`,`senderIndex`,`receiverContext`,`receiverIndex`,`message`,`timestamp`,`delivered`)";
			$sql = $sql . "values('$profile','groupMember','$senderIndex','$receiverContext','$receiverIndex','$message','$timestamp','$sent')";
			mysql_query($sql, $con);
			
			$message = "sent";
			
		}else{
			$message = "sending message to individual is not supported yet";
		}
		
		mysql_close($con);
		return $message;
	}
	return "cannot connect";	
}

function send_group_message($profile, $senderIndex, $senderPassword, $receiverContext, $receiverIndex, $message){
	
	$con = get_connection();
	if($con){ 
		
		$sql = "select token from login where `people` = '$senderIndex' and password = '".md5($senderPassword)."' and profile = '$profile'";
		$resultset = mysql_query($sql, $con);
		
		if(mysql_num_rows($resultset) == 0){
			return "login failed";
		}
		
		$sent = 0;
		if($receiverContext == 'group'){
			date_default_timezone_set('Asia/Singapore');
			$timestamp = date("YmdHis");
			
			$peopleList = getAllMembersPeopleIndexInGroup($profile, $receiverIndex);
			
			foreach($peopleList as $peopleIndex){
				$sql = "select token from login where profile = '$profile' and people = '$peopleIndex'";
				$tokenSet = mysql_query($sql, $con);
				while($tokenRow = mysql_fetch_row($tokenSet)){
					$token = $tokenRow[0];
					if($token != null && strlen($token) > 0){
						$sent += send_notification($token, $message, 0);	
					}
				}
			}
			
			$sql = "insert into group_msg(`profile`,`senderContext`,`senderIndex`,`receiverContext`,`receiverIndex`,`message`,`timestamp`,`delivered`)";
			$sql = $sql . "values('$profile','groupMember','$senderIndex','$receiverContext','$receiverIndex','$message','$timestamp','$sent')";
			mysql_query($sql, $con);
			
			$message = "sent";
			
		}else{
			$message = "sending message to individual is not supported yet";
		}
		
		mysql_close($con);
		return $message;
	}
	return "cannot connect";	
}

function recursiveGetMembersinSubGroup($con, $groupIndex, $profile){
	$result = array();
	
	$result = $result + getMembersInGroup($con, $groupIndex, $profile);
	
	$sql = "select value from result where context = 'group' and attribute = '#subgroups' and `index` = '$groupIndex' and profile = '$profile'";
	$resultset = mysql_query($sql, $con);

	if(mysql_num_rows($resultset) == 0){
		return $result;
	}else{
		while($row = mysql_fetch_row($resultset)){
			$members = recursiveGetMembersinSubGroup($con, $row[0], $profile);
			foreach($members as $member){
				$result[$member] = $member;
			}
		}
		return $result;
	}
}

function getMembersInGroup($con, $groupIndex, $profile){
	$result = array();
	$sql = "select value from result where context = 'group' and attribute = '#members' and `index` = '$groupIndex' and profile = '$profile'";
	$resultset = mysql_query($sql, $con);
	
	while($row = mysql_fetch_row($resultset)){
		$result[$row[0]] = $row[0];
	}
	return $result;
}

function getAllMembersPeopleIndexInGroup($profile, $groupIndex){
	
	$con = get_connection();
	if($con){ 
		$result = recursiveGetMembersinSubGroup($con, $groupIndex, $profile);
		return $result;
	}else{
		return "cannot connect";
	}
	
}

?>