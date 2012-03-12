<?php
/**
 * Usage Examples
 *
 * @author  Sebastian Borggrewe <me@sebastianborggrewe.de>
 * @since   2010/01/24
 * @package APNP
 */

//error_reporting(E_ALL | E_STRICT);
include("APNSBase.php");
include("APNotification.php");
include("APFeedback.php");

function send_notification_for_event($device, $message, $badge, $eventRid, $profile){
	try{
		# Notification Example
		$notification = new APNotification('production');
		$notification->setDeviceToken($device);
		$notification->setMessage($message);
		$notification->setBadge($badge);
		$notification->addProperty('type', 'event');
		$notification->addProperty('rid', $eventRid);
		$notification->addProperty('profile', $profile);
		$notification->setPrivateKey('cert/pple_prod.pem');
		$notification->setPrivateKeyPassphrase('password');
		$notification->send();
		
		/*$feedback = new APFeedback('development');
		$feedback->setPrivateKey('cert/pple_dev.pem');
		$feedback->setPrivateKeyPassphrase('pple87RKHW');
		$feedback->receive();*/
		
		return 1;	
	}catch(Exception $e){
		//echo $e->getLine().': '.$e->getMessage();
		return 0;
	}
}


function send_notification($device, $message, $badge){
	try{
		# Notification Example
		$notification = new APNotification('production');
		$notification->setDeviceToken($device);
		$notification->setMessage($message);
		$notification->setBadge($badge);
		$notification->setPrivateKey('cert/pple_prod.pem');
		$notification->setPrivateKeyPassphrase('password');
		$notification->send();
		
		/*$feedback = new APFeedback('development');
		$feedback->setPrivateKey('cert/pple_dev.pem');
		$feedback->setPrivateKeyPassphrase('pple87RKHW');
		$feedback->receive();*/
		
		return 1;	
	}catch(Exception $e){
		//echo $e->getLine().': '.$e->getMessage();
		return 0;
	}
}


?>
