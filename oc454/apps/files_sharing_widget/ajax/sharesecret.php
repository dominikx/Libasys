<?php

/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */
 
OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('files_sharing_widget');
OCP\JSON::callCheck();

// Get data
if(isset( $_POST['mySecretWord'] ) && $_POST['mySecretWord'] != '') {
	$secretWord=trim($_POST['mySecretWord']);
	 OCP\Config::setSystemValue('secretword', $secretWord);
	
	OC_JSON::success(array("data" => array( "message" => 'Saved')));
}elseif(isset( $_POST['siteemail'] ) && filter_var( $_POST['siteemail'], FILTER_VALIDATE_EMAIL)) {
	$siteEmail=trim($_POST['siteemail']);
	 OCP\Config::setSystemValue('siteemail', $siteEmail);
	OC_JSON::success(array("data" => array( "message" => 'Saved')));
}
else{
	OC_JSON::error(array("data" => array( "message" => 'Error' )));
}
