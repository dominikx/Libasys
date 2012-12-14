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

$ObjSaveParamter['maxpicsperpage']=intval($_POST['sppics']);
$ObjSaveParamter['imgheight']=intval($_POST['spthumb']);
$ObjSaveParamter['width']=intval($_POST['spwidth']);
$ObjSaveParamter['height']=intval($_POST['spheight']);

$ObjSaveJson=json_encode($ObjSaveParamter);
OC_Preferences::setValue( OC_User::getUser(), 'files_sharing_widget', 'parameter', $ObjSaveJson);

OCP\JSON::success(array("data" => array( "msg" =>'success')));