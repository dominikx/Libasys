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

$delId = isset($_POST["delid"])?$_POST["delid"]:'';
OC_Widget_Helper::delShare($delId);
OCP\JSON::success(array("data" => array( "msg" =>$delId)));