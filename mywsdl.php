<?php
require("lib/nusoap.php");
require("access_reader.php");
require("sync_reader.php");
require("msg_service.php");
$namespace = "www.practical-limits.com/wsdl";

$server = new soap_server();
$server->debug_flag = false;
$server->configureWSDL("PpleWsdl", $namespace);
$server->wsdl->schemaTargetNamespace = $namespace;

$server->wsdl->addComplexType(
  'Result',
  'complexType',
  'struct',
  'all',
  '',
  array(
    'context' => array('name' => 'context',
         'type' => 'xsd:string'),
    'index' => array('name' => 'index',
         'type' => 'xsd:string'),
    'attribute' => array('name' => 'attribute',
         'type' => 'xsd:string'),
    'tag' => array('name' => 'tag',
         'type' => 'xsd:string'),
    'status' => array('name' => 'status', 
    	 'type' => 'xsd:string'),
    'value' => array('name' => 'value',
         'type' => 'xsd:string'),
    'oldValue' => array('name' => 'oldValue',
         'type' => 'xsd:string')
  )
);

$server->wsdl->addComplexType(
  'ResultList',
  'complexType',
  'array',
  '',
  'SOAP-ENC:Array',
  array(),
  array(
    array('ref' => 'SOAP-ENC:arrayType',
         'wsdl:arrayType' => 'tns:Result[]')
  ),
  'tns:Result'
);

$server->wsdl->addComplexType(
  'Influence',
  'complexType',
  'struct',
  'all',
  '',
  array(
  	'profile' => array('name' => 'profile',
         'type' => 'xsd:string'),
    'context' => array('name' => 'context',
         'type' => 'xsd:string'),
    'index' => array('name' => 'index',
         'type' => 'xsd:string'),
    'login' => array('name' => 'login',
         'type' => 'xsd:string'),
	'access' => array('name' => 'access',
		  'type' => 'xsd:string')
  )
);

$server->wsdl->addComplexType(
  'InfluenceList',
  'complexType',
  'array',
  '',
  'SOAP-ENC:Array',
  array(),
  array(
    array('ref' => 'SOAP-ENC:arrayType',
         'wsdl:arrayType' => 'tns:Influence[]')
  ),
  'tns:Influence'
);

$server->register('UpdateToken',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'token' => 'xsd:string'), // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#UpdateToken',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Update Token'        // documentation
);

function UpdateToken($profile, $login, $password, $token)
{
  //return load_main($login, $profile);
  $results = update_token($profile, $login, $password, $token);
  return $results;
}

$server->register('TestConnection',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string'),          // input parameters
  array('return' => 'xsd:int'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#TestConnection',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Test Connection'        // documentation
);

function TestConnection($profile, $login, $password)
{
  //return load_main($login, $profile);
  $results = test_connection($login, $profile, $password);
  return $results;
}

$server->register('SetResetProfile',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string'),          // input parameters
  array('return' => 'xsd:int'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#SetResetProfile',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Set Reset Profile'        // documentation
);

function SetResetProfile($profile, $login)
{
  //return load_main($login, $profile);
  $results = set_reset_profile($login, $profile);
  return $results;
}

$server->register('ClearResetProfile',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string'),          // input parameters
  array('return' => 'xsd:int'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#ClearResetProfile',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Clear Reset Profile'        // documentation
);

function ClearResetProfile($profile, $login)
{
  //return load_main($login, $profile);
  $results = clear_reset_profile($login, $profile);
  return $results;
}

$server->register('CheckResetProfileNeeded',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string'),          // input parameters
  array('return' => 'xsd:int'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#CheckResetProfileNeeded',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'CheckResetProfileNeeded'        // documentation
);

function CheckResetProfileNeeded($profile, $login)
{
  //return load_main($login, $profile);
  $results = check_reset_profile_needed($login, $profile);
  return $results;
}

$server->register('GetProfileRadarOnly',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'timestamp' => 'xsd:string'), // input parameters
  array('return' => 'tns:ResultList'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#GetProfileRadarOnly',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Get Profile Radar Only'        // documentation
);

function GetProfileRadarOnly($profile, $login, $password, $timestamp)
{
  //return load_main($login, $profile);
  $results = load_radar_only($login, $profile, $password, $timestamp);
  
  return $results;
}

$server->register('GetProfileRadar',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'timestamp' => 'xsd:string'), // input parameters
  array('return' => 'tns:ResultList'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#GetProfileRadar',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Get Profile Radar'        // documentation
);

function GetProfileRadar($profile, $login, $password, $timestamp)
{
  //return load_main($login, $profile);
  $results = load_radar($login, $profile, $password, $timestamp);
  
  return $results;
}

$server->register('GetProfileDump',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'timestamp' => 'xsd:string'), // input parameters
  array('return' => 'tns:ResultList'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#GetProfileDump',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Get Profile Dump'        // documentation
);

function GetProfileDump($profile, $login, $password, $timestamp)
{
  //return load_main($login, $profile);
  $results = load_main($login, $profile, $password, $timestamp);
  return $results;
}

$server->register('AddItems',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'items' => 'tns:ResultList', 'timestamp' => 'xsd:string'),  // input parameters
  array('return' => 'xsd:int'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#AddItems',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Add Items'        // documentation
);

function AddItems($profile, $login, $password, $items, $timestamp)
{
	return add_items_to_main($profile, $login, $password, $items, $timestamp);
}

$server->register('UpdateItems',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'items' => 'tns:ResultList', 'timestamp' => 'xsd:string'),  // input parameters
  array('return' => 'xsd:int'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#UpdateItems',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Update Items'        // documentation
);

function UpdateItems($profile, $login, $password, $items, $timestamp)
{
	return update_items_to_main($profile, $login, $password, $items, $timestamp);
}

$server->register('DeleteItems',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'items' => 'tns:ResultList', 'timestamp' => 'xsd:string'),  // input parameters
  array('return' => 'xsd:int'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#DeleteItems',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Delete Items'        // documentation
);

function DeleteItems($profile, $login, $password, $items, $timestamp)
{
	return delete_items_from_main($profile, $login, $password, $items, $timestamp);
}

$server->register('SignUpPple',                    // method name
  array('email' => 'xsd:string'),  // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#SignUpPple',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'SignUpPple'        // documentation
);

function SignUpPple($email)
{
	return signup_pple($email);
}

$server->register('CreateLogin',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'newLogin' => 'xsd:string', 'peopleIndex' => 'xsd:string'),  // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#CreateLogin',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Create Login'        // documentation
);

function CreateLogin($profile, $login, $password, $newLogin, $peopleIndex)
{
	return create_login($profile, $login, $password, $newLogin, $peopleIndex);
}

$server->register('SetLoginInfluenceGroups',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'targetLogin' => 'xsd:string', 'items' => 'tns:InfluenceList'),  // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#SetLoginInfluenceGroups',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Set Login Influence Groups'        // documentation
);

function SetLoginInfluenceGroups($profile, $login, $password, $targetLogin, $items)
{
	return set_login_influence_groups($profile, $login, $password, $targetLogin, $items);
}

$server->register('SetLoginRadarGroups',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'targetLogin' => 'xsd:string', 'items' => 'tns:InfluenceList'),  // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#SetLoginRadarGroups',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Set Login Radar Groups'        // documentation
);

function SetLoginRadarGroups($profile, $login, $password, $targetLogin, $items)
{
	return set_login_radar_groups($profile, $login, $password, $targetLogin, $items);
}

$server->register('RemoveLogin',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string', 'password' => 'xsd:string', 'targetLogin' => 'xsd:string'),  // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#RemoveLogin',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Remove Login'        // documentation
);

function RemoveLogin($profile, $login, $password, $targetLogin)
{
	return remove_login($profile, $login, $password, $targetLogin);
}

$server->register('RetrieveLogin',                    // method name
  array('peopleIndex' => 'xsd:string', 'profile' => 'xsd:string'),          // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#RetrieveLogin',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Get login-ID of the people'        // documentation
);

function RetrieveLogin($peopleIndex, $profile)
{
  $results = get_login_for_people($peopleIndex, $profile);
  return $results;
}

$server->register('GetLoginAccessGroups',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string'),  // input parameters
  array('return' => 'tns:InfluenceList'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#GetLoginAccessGroups',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'GetLoginAccessGroups'        // documentation
);

function GetLoginAccessGroups($profile, $login)
{
	return get_login_access_groups($login, $profile);
}

$server->register('GetLoginInfluenceGroups',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string'),  // input parameters
  array('return' => 'tns:InfluenceList'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#GetLoginInfluenceGroups',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Get Login Influence Groups'        // documentation
);

function GetLoginInfluenceGroups($profile, $login)
{
	return get_influence_for_login($login, $profile);
}

$server->register('GetLoginRadarGroups',                    // method name
  array('profile' => 'xsd:string', 'login' => 'xsd:string'),  // input parameters
  array('return' => 'tns:InfluenceList'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#GetLoginRadarGroups',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Get Login Radar Groups'        // documentation
);

function GetLoginRadarGroups($profile, $login)
{
	return get_radar_for_login($login, $profile);
}

$server->register('LoginExistPple',                    // method name
  array('peopleIndex' => 'xsd:string', 'profile' => 'xsd:string'),          // input parameters
  array('return' => 'xsd:int'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#LoginExistPple',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Check if login account exists in Pple'        // documentation
);

function LoginExistPple($peopleIndex, $profile)
{
  $results = login_exist($peopleIndex, $profile);
  return $results;
}

$server->register('LoginPple',                    // method name
  array('login' => 'xsd:string', 'password' => 'xsd:string', 'profile' => 'xsd:string'), // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#LoginPple',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Log in to Pple account'        // documentation
);

function LoginPple($login, $password, $profile)
{
  //return load_main($login, $profile);
  $results = login_user($login, $password, $profile);
  return $results;
}

$server->register('ResendVerification',                    // method name
  array('login' => 'xsd:string', 'profile' => 'xsd:string'),          // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#ResendVerification',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Resend Verification'        // documentation
);

function ResendVerification($login, $profile)
{
  //return load_main($login, $profile);
  $results = send_verification($login, $profile);
  return $results;
}

$server->register('ChangePassword',                    // method name
  array('login' => 'xsd:string', 'old_password' => 'xsd:string', 'new_password' => 'xsd:string', 'new_password2' => 'xsd:string', 'profile' => 'xsd:string'), // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#ChangePassword',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'ChangePassword'        // documentation
);

function ChangePassword($login, $old_password, $new_password, $new_password2, $profile)
{
  //return load_main($login, $profile);
  $results = change_password($login, $old_password, $new_password, $new_password2, $profile);
  return $results;
}

$server->register('ResetPassword',                    // method name
  array('login' => 'xsd:string', 'profile' => 'xsd:string'),          // input parameters
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#ResetPassword',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'ResetPassword'        // documentation
);

function ResetPassword($login, $profile)
{
  //return load_main($login, $profile);
  $results = reset_password($login, $profile);
  return $results;
}

$server->register('SendGroupMessage',                    // method name
  array('profile' => 'xsd:string', 'senderIndex' => 'xsd:string', 'senderPassword' => 'xsd:string', 'receiverContext'=>'xsd:string', 'receiverIndex'=>'xsd:string', 'message' => 'xsd:string'),
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#SendGroupMessage',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'SendGroupMessage'        // documentation
);

function SendGroupMessage($profile, $senderIndex, $senderPassword, $receiverContext, $receiverIndex, $message)
{
	$results = send_group_message($profile, $senderIndex, $senderPassword, $receiverContext, $receiverIndex, $message);
	return $results;
}


$server->register('SendEventNotification',                    // method name
  array('profile' => 'xsd:string', 'senderIndex' => 'xsd:string', 'senderPassword' => 'xsd:string', 'receiverContext'=>'xsd:string', 'receiverIndex'=>'xsd:string', 'message' => 'xsd:string', 'eventRid' => 'xsd:string'),
  array('return' => 'xsd:string'),    // output parameters
  $namespace,                         // namespace
  $namespace . '#SendEventNotification',                   // soapaction
  'rpc',                                    // style
  'encoded',                                // use
  'Send EventNotification'        // documentation
);

function SendEventNotification($profile, $senderIndex, $senderPassword, $receiverContext, $receiverIndex, $message, $eventRid)
{
	$results = send_event_notification($profile, $senderIndex, $senderPassword, $receiverContext, $receiverIndex, $message, $eventRid);
	return $results;
}

$HTTP_RAW_POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
$server->service($HTTP_RAW_POST_DATA);
exit();

?>