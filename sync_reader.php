<?php

include_once("config.php");

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

function get_object_array($resultset){
	$result = array();
	while($obj = mysql_fetch_object($resultset)){
		array_push($result, $obj);
	}
	return $result;
}

function get_assoc_array($resultset){
	$result = array();
	while($assoc = mysql_fetch_assoc($resultset)){
		array_push($result, $assoc);
	}
	return $result;
}

//Array Util
function print_result($result_array){
	foreach($result_array as $result){
		echo "$result->profile , $result->context, $result->index, $result->attribute, $result->value <br/>";
	}
}

function push_result($result_array, $push_array){
	if(count($push_array) == 0) return $result_array;
	if($result_array == null) $result_array = array();
	
	foreach($push_array as $item){
		if(!in_array($item, $result_array)){
			array_push($result_array, $item);
		}
	}
	return $result_array;
}

//PPLE Functions

function get_library($con, $profile){
	$ancestors = get_ancestor_library($con, $profile);
	$attributes = get_attributes_library($con, $profile);
	$influence = get_permission_library($con, $profile, 'influence');
	$view = get_permission_library($con, $profile, 'view');
	
	return (object)array('ancestors'=>$ancestors, 'attributes'=>$attributes, 'influence'=>$influence, 'view'=>$view);
}

function get_ancestor_library($con, $profile){
	//return array['context'] = ancestor
	$library = array();
	
	$sql = "select * from context where profile = 'profile' or profile = '$profile'";
	$resultset = mysql_query($sql, $con);
	$list = array();
	while($item = mysql_fetch_assoc($resultset)){
		$list[$item['id']] = $item['parent'];
	}
	
	foreach($list as $key=>$value){
		$library[$key] = get_ancestor($list, $key);
	}
	
	//print_r($library);
	
	return $library;
}

function get_ancestor($list, $subject){
	if($list[$subject] == 'context'){
		return $subject;
	}else{
		return get_ancestor($list, $list[$subject]);
	}
}

function get_permission_library($con, $profile, $permission){
	//return array['context']['target'] = array[object(drillable, index=1/0, attribute)];
	$sql = "select * from `permission` where permission = '$permission' and (profile = '$profile' or profile = 'profile')";
	$resultset = mysql_query($sql, $con);
	$library = array();
	while($row = mysql_fetch_object($resultset)){
		if(!array_key_exists($row->context, $library)){
			$library[$row->context] = array();
		}
		if(!array_key_exists($row->target, $library[$row->context])){
			$library[$row->context][$row->target] = array();
		}
		$obj = (object)array('drillable'=>$row->drillable, 'index'=>$row->index, 'attribute'=>$row->attribute);
		array_push($library[$row->context][$row->target], $obj);
	}
	//print_r($library);echo "<br/>";
	return $library;
}

function get_attributes_library($con, $profile){
	//returns array['context']['attribute'] -> object(type, tag);
	$context = array();
	
	$sql = 'select * from attribute';
	$resultset = mysql_query($sql, $con);
	while($obj = mysql_fetch_object($resultset)){
		if(!array_key_exists($obj->context, $context)){
			$context[$obj->context] = array();
		}
		$attr = (object)array('type'=>$obj->attribute_type);
		$context[$obj->context][$obj->attribute_name] = $attr;
	}
	
	$sql = 'select * from attribute_tag';
	$resultset = mysql_query($sql, $con);
	while($obj = mysql_fetch_object($resultset)){
		if(!array_key_exists($obj->context, $context)){
			$context[$obj->context] = array();
		}
		$attr = (object)array('type'=>$obj->attribute_type, 'tag'=>$obj->attribute_tag);
		$context[$obj->context][$obj->attribute_name] = $attr;
	}

	$sql = "select * from context where (profile = '$profile' and profile != 'profile') order by id";
	$resultset = mysql_query($sql, $con);
	$profile_contexts = get_object_array($resultset);
	$i = 1;

	for($x=0; $x<count($profile_contexts); $x++){
		$profile_context = $profile_contexts[$x];

		if(array_key_exists($profile_context->parent, $context) == true){
			if(!array_key_exists($profile_context->id, $context)){
				$context[$profile_context->id] = array();
			}
			$context[$profile_context->id] = $context[$profile_context->id] + $context[$profile_context->parent];
			//echo "$i $profile_context->id <br/>";$i++;
		}else{
			array_push($profile_contexts, $profile_context);
			//echo "$i $profile_context->id <br/>";$i++;
		}
	}

	//print_r($context);
	//echo "<br/>";
	
	return $context;
}

function select_result($con, $index = '', $context = '', $attribute = '', $value = '', $not_attribute = ''){
	$sql = "select * from result";
	
	$where_clause = '';
	
	if($index != ''){
		$where_clause = $where_clause . " `index` = '$index'";
	}
	if($context != ''){
		if(strlen($where_clause) > 0){ $where_clause = $where_clause . " and"; }
		$where_clause = $where_clause . " context = '$context'";
	}
	if($attribute != ''){
		if(strlen($where_clause) > 0){ $where_clause = $where_clause . " and"; }
		$where_clause = $where_clause . " attribute = '$attribute'";
	}
	if($value != ''){
		if(strlen($where_clause) > 0){ $where_clause = $where_clause . " and"; }
		$where_clause = $where_clause . " value = '$value'";
	}
	
	if($not_attribute != ''){
		if(strlen($where_clause) > 0){ $where_clause = $where_clause . " and"; }
		$where_clause = $where_clause . " attribute != '$not_attribute'";
	}
	
	if(strlen($where_clause) > 0){
		$where_clause = " where" . $where_clause;
	}
	$sql = $sql . $where_clause;
	
//echo $sql.'<br/>';
	$result = mysql_query($sql, $con);
	$untagged_results = get_object_array($result);
	
	$sql = "select * from result_tag";
	
	$where_clause = '';
	
	if($index != ''){
		$where_clause = $where_clause . " `index` = '$index'";
	}
	if($context != ''){
		if(strlen($where_clause) > 0){ $where_clause = $where_clause . " and"; }
		$where_clause = $where_clause . " context = '$context'";
	}
	if($attribute != ''){
		if(strlen($where_clause) > 0){ $where_clause = $where_clause . " and"; }
		$where_clause = $where_clause . " attribute = '$attribute'";
	}
	if($value != ''){
		if(strlen($where_clause) > 0){ $where_clause = $where_clause . " and"; }
		$where_clause = $where_clause . " value = '$value'";
	}
	
	if($not_attribute != ''){
		if(strlen($where_clause) > 0){ $where_clause = $where_clause . " and"; }
		$where_clause = $where_clause . " attribute != '$not_attribute'";
	}
	
	if(strlen($where_clause) > 0){
		$where_clause = " where" . $where_clause;
	}
	$sql = $sql . $where_clause;
	
//echo $sql.'<br/>';
	$result = mysql_query($sql, $con);
	$tagged_results = get_object_array($result);
	
	$answer = push_result($answer, $untagged_results);
	$answer = push_result($answer, $tagged_results);
	return $answer;
}

function can_drill_down($library, $result_row, $influence_context){
	$attributes_library = $library->attributes;
	$ancestors_library = $library->ancestors;
	$att_type = $attributes_library[$result_row->context][$result_row->attribute]->type;
	//echo "$result_row->context -> $result_row->attribute == $att_type <br/>";
	//if($att_type == null){echo $result_row->context."--haha<br/>";}
	
	//echo "$result_row->context $result_row->attribute -> $att_type<br/>";
	if(substr($att_type, 0, 7) == 'context'){ //echo "1<br/>";
		$type = substr($att_type, strpos($att_type, ':') + 1);
		$ancestor = $ancestors_library[$type];
		if($ancestor == $influence_context){ //echo "2 $ancestor = $influence_context<br/>";
			return true;
		}else{ 
			//echo "$att_type -> $ancestor <> $influence_context<br/>"; 
		}
	}
	return false;
}

function is_drilling_allowed($influence_library, $context, $target){
	foreach($influence_library[$context][$target] as $item){
		if($item->drillable == 1){
			return true;
		}
	}
	return false;
}

function get_permission_rows_with_attributes($influence_library, $context, $target){
	$result1 = array();
	//echo "$context - $target<br/>";
	
	foreach($influence_library[$context][$target] as $line){ 
		if($line->attribute != null){
			array_push($result1, $line);
		}
	}
	
	//echo ":::::";print_r($result1);echo "))))";
	return $result1;
}

function drill_result($con, $library, $result, $influence_context){
	$answer = array();
	$attributes_library = $library->attributes;
	$ancestors_library = $library->ancestors;
	$influence_library = $library->influence;
	
	foreach($result as $result_row){
		$influence_target = $ancestors_library[$result_row->context];
		if(can_drill_down($library, $result_row, $influence_context)){
			
			$drilled = select_result($con, $result_row->value);
			
			//echo "##sub target start<br/>";
			foreach($influence_library[$influence_context] as $target=>$target_object){
				foreach($target_object as $target_object_option){
					if($target_object_option->attribute != null && $target_object_option->attribute != ''){
						$child_contexts = array_keys($ancestors_library, $target);
						foreach($child_contexts as $child_context){
							$subTarget = select_result($con, '', $child_context, $target_object_option->attribute, $result_row->value);
							if(count($subTarget) > 0){
								$answer = push_result($answer, $subTarget);	
								foreach($subTarget as $subTargetItem){
									$answer = push_result($answer, select_result($con, $subTargetItem->index, $child_context));
								}
							}
						}
						
						/*$subTarget = select_result($con, '', $target, $target_object_option->attribute, $result_row->value);
						if(count($subTarget) > 0){
							$answer = push_result($answer, $subTarget);	
							$answer = push_result($answer, select_result($con, $subTarget[0]->index, $target));
						}*/
					}
				}
			}
			//echo "##sub target end<br/>";

			$answer = push_result($answer, $drilled);
			if(is_drilling_allowed($influence_library, $influence_context, $influence_target)){
				$answer = push_result($answer, drill_result($con, $library, $drilled, $influence_context));
			}
		}
	}
	return $answer;
}

function get_influence($con, $influence, $library){
	$result = array();
	$influence_library = $library->influence;
	$ancestor_library = $library->ancestors;
	
	$context_ancestor = $ancestor_library[$influence->context];
	foreach($influence_library[$context_ancestor] as $target=>$targetArray){ 
		foreach($targetArray as $targetObject){
			if($targetObject->index == 1){
				$result = push_result($result, select_result($con, $influence->index, $influence->context));
			}else{
				$subTargets = array_keys($ancestor_library,$target);
				foreach($subTargets as $subTarget){
					//here -> the influence->index, so select result change to in-
					//$result = push_result($result, select_result($con, '', $subTarget, $targetObject->attribute, $influence->index));
					$subResultKeys = select_result($con, '', $subTarget, $targetObject->attribute, $influence->index);
					$result = push_result($result, $subResultKeys);
					if(count($subResultKeys) > 0){
						foreach($subResultKeys as $subResultKey){
							$result = push_result($result, select_result($con, $subResultKey->index, $subTarget,'','', $targetObject->attribute));
						}
					}
				}
			}
		}
	}
	
	//$result = select_result($con, $influence->index, $influence->context);
	
	/*if($influence->context == 'group'){
		//get the events with owner = index
		$events = array_keys($ancestors, 'event');
		foreach($events as $event){
			$addon = select_result($con, '', $event, 'owner', $influence->index);
			$result = push_result($result, $addon);
		}
	}*/
		
	return $result;
}

function get_radar($con, $radar, $library){
	$result = array();
	$radar_library = $library->view;
	$ancestor_library = $library->ancestors;

	$context_ancestor = $ancestor_library[$radar->context];

	foreach($radar_library[$context_ancestor] as $target=>$targetArray){ 
		foreach($targetArray as $targetObject){
			if($targetObject->index == 1){
				$result = push_result($result, select_result($con, $radar->index, $radar->context));
			}else{
				$subTargets = array_keys($ancestor_library,$target);
				foreach($subTargets as $subTarget){
					//here -> the influence->index, so select result change to in-
					//$result = push_result($result, select_result($con, '', $subTarget, $targetObject->attribute, $influence->index));
					$subResultKeys = select_result($con, '', $subTarget, $targetObject->attribute, $radar->index);
					$result = push_result($result, $subResultKeys);
					if(count($subResultKeys) > 0){
						foreach($subResultKeys as $subResultKey){
							$result = push_result($result, select_result($con, $subResultKey->index, $subTarget,'','', $targetObject->attribute));
						}
					}
				}
			}
		}
	}
	
	//$result = select_result($con, $influence->index, $influence->context);
	
	/*if($influence->context == 'group'){
		//get the events with owner = index
		$events = array_keys($ancestors, 'event');
		foreach($events as $event){
			$addon = select_result($con, '', $event, 'owner', $influence->index);
			$result = push_result($result, $addon);
		}
	}*/
	
	return $result;
}

function get_all_influence($id, $profile, $password){
	$con = get_connection();
	if($con){
		if(login($con, $id, $profile, $password) == false){
			return "Login Failed";
		}
		
		$sql = "delete from reset_profile where `id` = '$id' and `profile` = '$profile'";
		mysql_query($sql, $con);
		
		$library = get_library($con, $profile);
		$ancestors_library = $library->ancestors;

		$result_array = array();
		$sql = "select * from influence where id = '$id' and profile = '$profile'"; 
		//echo $sql."<br/>";
		$result = mysql_query($sql, $con);
		while($influence = mysql_fetch_object($result)){
			$influence_result = get_influence($con, $influence, $library);
			$result_array = push_result($result_array, $influence_result);
			$influence_context = $ancestors_library[$influence->context];
			$drilled = drill_result($con, $library, $influence_result, $influence_context);
			$result_array = push_result($result_array, $drilled);
		}
		//print_result($result_array);
		mysql_close($con);
		return $result_array;
	}
	return "Error encountered while retrieving records";
}

function get_all_radar($id, $profile, $password){
	$con = get_connection();
	if($con){
		if(login($con, $id, $profile, $password) == false){
			return "Login Failed";
		}
		
		$sql = "delete from reset_profile where `id` = '$id' and `profile` = '$profile'";
		mysql_query($sql, $con);
		
		$library = get_library($con, $profile);
		$ancestors_library = $library->ancestors;

		$result_array = array();
		$sql = "select * from radar where id = '$id' and profile = '$profile'"; 
		//echo $sql."<br/>";
		$result = mysql_query($sql, $con);
		while($radar = mysql_fetch_object($result)){
			$radar_result = get_radar($con, $radar, $library);
			$result_array = push_result($result_array, $radar_result);
			$radar_context = $ancestors_library[$radar->context];
			$drilled = drill_result($con, $library, $radar_result, $radar_context);
			$result_array = push_result($result_array, $drilled);
		}
		//print_result($result_array);
		mysql_close($con);
		return $result_array;
	}
	return "Error encountered while retrieving records";
}

function login($con, $user, $profile, $password){
	$sql = "select * from login where id = '$user' and profile = '$profile' and password = '".md5($password)."'";
	$query = mysql_query($sql, $con);
	if(mysql_num_rows($query) == 0){
		return false;
	}else{
		return true;
	}
}

function load_main($user, $profile, $password, $timestamp){
	$result = array();
	$influences = get_all_influence($user, $profile, $password);
	foreach($influences as $influence){
		array_push($result, (array)$influence);
	}

	$filtered = array();
	foreach($result as $item){
		if($item['timestamp'] > $timestamp){
			array_push($filtered, $item);
		}
	}	
	return $filtered;
}

function load_radar($user, $profile, $password, $timestamp){
	$result = array();
	$radars = get_all_radar($user, $profile, $password);
	foreach($radars as $radar){
		array_push($result, (array)$radar);
	}
	
	$filtered = array();
	foreach($result as $item){
		if($item['timestamp'] > $timestamp){
			array_push($filtered, $item);
		}
	}
	
	return $filtered;
}

function load_radar_only($user, $profile, $password, $timestamp){
	
	$influence = load_main($user, $profile, $password, $timestamp);
	$influenceMR = array_map("reduce", $influence);
	
	$radar = load_radar($user, $profile, $password, $timestamp);
	$radarMR = array_map("reduce", $radar);
	
	$difference = array_diff($radarMR, $influenceMR);

	$answer = array();
	foreach($difference as $diff){
		foreach($radar as $item){
			$string = array_reduce($item, "stringify");
			if($diff === $string){
				array_push($answer, $item);
				break;
			}
		}
	}
	return $answer;
}

function stringify($v1, $v2){
	return $v1.$v2;
}

function reduce($v){
	return array_reduce($v, "stringify");
}

function test_connection($login, $profile, $password){
	$result = 0;
	$con = get_connection();
	if($con){
		if(login($con, $id, $profile, $password) == true){
			$result = 1;
		}
		mysql_close($con);
	}
	return result;
}

//Updating part

function do_extra_for_insert_result($result, $profile, $con){
	if($result["context"] == "group" && $result["attribute"] == "#creator"){
		$sql = "insert into influence(`id`,`profile`,`context`,`index`) Values (";
		$sql .= "'" . $result["value"] . "',";
		$sql .= "'" . $profile . "',";
		$sql .= "'" . $result["context"] . "',";
		$sql .= "'" . $result["index"] . "')";
		mysql_query($sql, $con);
	}
}

function do_extra_for_update_result($result, $profile, $con){
	if($result["context"] == "group" && $result["attribute"] == "#creator"){
		$sql = "insert into influence(`id`,`profile`,`context`,`index`) Values (";
		$sql .= "'" . $result["value"] . "',";
		$sql .= "'" . $profile . "',";
		$sql .= "'" . $result["context"] . "',";
		$sql .= "'" . $result["index"] . "')";
		mysql_query($sql, $con);
	}
}

function do_extra_for_delete_result($result, $profile, $con){
	if($result["context"] == "group" && $result["attribute"] == "#creator"){
		$sql = "delete from influence where ";
		$sql .= "id = '" . $result["oldValue"] . "' ";
		$sql .= "and profile = '" . $profile . "' ";
		$sql .= "and context = '" . $result["context"] . "' ";
		$sql .= "and `index` = '" . $result["index"] . "'";
		mysql_query($sql, $con);
	}
}

function add_items_to_main($profile, $login, $password, $results, $timestamp){
	$con = get_connection();
	if($con){
		if(login($con, $login, $profile, $password) == false){
			return "Login Failed";
		}
		
		$insert_count = 0;
		foreach($results as $result){
			if($result["tag"] == nil || $result["tag"] == ""){
				$sql = "insert into result(`context`, `attribute`, `index`, `value`, `profile`, `timestamp`, `status`) values(";
					$sql = $sql . "'" . $result["context"] . "', ";
					$sql = $sql . "'" . $result["attribute"] . "', ";
					$sql = $sql . "'" . $result["index"] . "', ";
					$sql = $sql . "'" . $result["value"] . "', ";
					$sql = $sql . "'" . $profile . "', ";
					$sql = $sql . "'" . $timestamp . "', ";
					$sql = $sql . "'I')";
				if(mysql_query($sql, $con)){
					$insert_count ++;
				}
			}else{
				$sql = "insert into result_tag(`context`, `attribute`, `index`, `tag`, `value`, `profile`, `timestamp`, `status`) values(";
					$sql = $sql . "'" . $result["context"] . "', ";
					$sql = $sql . "'" . $result["attribute"] . "', ";
					$sql = $sql . "'" . $result["index"] . "', ";
					$sql = $sql . "'" . $result["tag"] . "', ";
					$sql = $sql . "'" . $result["value"] . "', ";
					$sql = $sql . "'" . $profile . "', ";
					$sql = $sql . "'" . $timestamp . "',";
					$sql = $sql . "'I')";
				if(mysql_query($sql, $con)){
					$insert_count ++;
				}
			}	
			
			do_extra_for_insert_result($result, $profile, $con);
			
		}
		
		mysql_close($con);
		return $insert_count;
	}
	return "-1";
}

function update_items_to_main($profile, $login, $password, $results, $timestamp){
	$con = get_connection();
	if($con){
		if(login($con, $login, $profile, $password) == false){
			return "Login Failed";
		}
		
		$update_count = 0;
		foreach($results as $result){
			if($result["tag"] == nil || $result["tag"] == ""){
				$sql = "update result set `value` = '" . $result["value"] . "', `timestamp` = '". $timestamp ."', `status` = 'U' where ";
					$sql = $sql . "`context` = '" . $result["context"] . "' and ";
					$sql = $sql . "`attribute` = '" . $result["attribute"] . "' and ";
					$sql = $sql . "`index` = '" . $result["index"] . "' and ";
					$sql = $sql . "`profile` = '" . $profile . "' and ";
					$sql = $sql . "`value` = '" . $result["oldValue"] . "'";
				if(mysql_query($sql, $con)){
					$update_count = $update_count + mysql_affected_rows();
				}
			}else{
				$sql = "update result_tag set `value` = '" . $result["value"] . "', `timestamp` = '". $timestamp ."', `status` = 'U' where ";
					$sql = $sql . "`context` = '" . $result["context"] . "' and ";
					$sql = $sql . "`attribute` = '" . $result["attribute"] . "' and ";
					$sql = $sql . "`index` = '" . $result["index"] . "' and ";
					$sql = $sql . "`tag` = '" . $result["tag"] . "' and ";
					$sql = $sql . "`profile` = '" . $profile . "' and ";
					$sql = $sql . "`value` = '" . $result["oldValue"] . "'";
				if(mysql_query($sql, $con)){
					$update_count = $update_count + mysql_affected_rows();
				}
			}
			do_extra_for_update_result($result, $profile, $con);	
		}
		
		mysql_close($con);
		return $update_count;
	}
	return "-1";
}

function delete_items_from_main($profile, $login, $password, $results, $timestamp){
	$con = get_connection();
	if($con){
		if(login($con, $login, $profile, $password) == false){
			return "Login Failed";
		}
		
		$delete_count = 0;
		/*foreach($results as $result){
			if($result["tag"] == nil || $result["tag"] == ""){
				$sql = "delete from result where ";
					$sql = $sql . "`context` = '" . $result["context"] . "' and ";
					$sql = $sql . "`attribute` = '" . $result["attribute"] . "' and ";
					$sql = $sql . "`index` = '" . $result["index"] . "' and ";
					$sql = $sql . "`value` = '" . $result["value"] . "' and ";
					$sql = $sql . "`profile` = '" . $profile . "'";
				if(mysql_query($sql, $con)){
					$delete_count ++;
				}
			}else{
				$sql = "delete from result where ";
					$sql = $sql . "`context` = '" . $result["context"] . "' and ";
					$sql = $sql . "`attribute` = '" . $result["attribute"] . "' and ";
					$sql = $sql . "`index` = '" . $result["index"] . "' and ";
					$sql = $sql . "`value` = '" . $result["value"] . "' and ";
					$sql = $sql . "`tag` = '" . $result["tag"] . "' and ";
					$sql = $sql . "`profile` = '" . $profile . "'";
				if(mysql_query($sql, $con)){
					$delete_count ++;
				}
			}	
		}*/
		foreach($results as $result){
			if($result["tag"] == nil || $result["tag"] == ""){
				$sql = "update result set `timestamp` = '". $timestamp ."', `status` = 'D' where ";
					$sql = $sql . "`context` = '" . $result["context"] . "' and ";
					$sql = $sql . "`attribute` = '" . $result["attribute"] . "' and ";
					$sql = $sql . "`index` = '" . $result["index"] . "' and ";
					$sql = $sql . "`profile` = '" . $profile . "' and ";
					$sql = $sql . "`value` = '" . $result["oldValue"] . "'";
				if(mysql_query($sql, $con)){
					$delete_count = $delete_count + mysql_affected_rows();
				}
			}else{
				$sql = "update result_tag set `timestamp` = '". $timestamp ."', `status` = 'D' where ";
					$sql = $sql . "`context` = '" . $result["context"] . "' and ";
					$sql = $sql . "`attribute` = '" . $result["attribute"] . "' and ";
					$sql = $sql . "`index` = '" . $result["index"] . "' and ";
					$sql = $sql . "`tag` = '" . $result["tag"] . "' and ";
					$sql = $sql . "`profile` = '" . $profile . "' and ";
					$sql = $sql . "`value` = '" . $result["oldValue"] . "'";
				if(mysql_query($sql, $con)){
					$delete_count = $delete_count + mysql_affected_rows();
				}
			}	
			do_extra_for_delete_result($result, $profile, $con);
		}
		
		mysql_close($con);
		return $delete_count;
	}
	return "-1";
}

?>