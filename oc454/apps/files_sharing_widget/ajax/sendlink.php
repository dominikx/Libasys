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

if( isset( $_POST['email'] ) && filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL) ) {
$toMail=trim($_POST['email']);
$mailTxt = isset($_POST['mailtxt']) ? strip_tags(nl2br($_POST['mailtxt'])) : false;	
$sharelink=$_POST['sharelink'];	
//$from = 'info@libasyscloud.de';
$from=OCP\Config::getSystemValue('siteemail');
if($from=='') $from='';
//OC_MAIL::setFooter("\n--\n LibasysCloud - More than a Cloud!");
OC_MAIL::send($toMail, $toMail, 'Share Link by ' . OCP\USER::getUser(), $mailTxt."\n\nShared Link:\n".$sharelink, $from, 'ownCloud');

OCP\JSON::success(array("data" => array( "msg" =>'success')));
}