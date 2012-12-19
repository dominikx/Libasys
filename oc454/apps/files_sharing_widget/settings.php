<?php
/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */
 
//Secret Word
$Param=OC_Preferences::getValue(OC_User::getUser(), 'files_sharing_widget', 'parameter','');	
if($Param) $ObjParamter=json_decode($Param,true);
else{
	$ObjParamter['maxpicsperpage']=10;
	$ObjParamter['imgheight']=150;
	$ObjParamter['width']=750;
	$ObjParamter['height']=550;
	$ObjParamter['watermark']=1;	
	$ObjParamter['watermarktxt']='';
	$ObjParamter['title']='';
}
if(!isset($ObjParamter['watermarktxt'])) $ObjParamter['watermarktxt']='';
if(!isset($ObjParamter['title'])) $ObjParamter['title']='';


$shareSecret=OCP\Config::getSystemValue('secretword');
if($shareSecret=='') $shareSecret='mySecretWord';

$allShares=OC_Widget_Helper::getAllSharesUser($shareSecret);
 
$tmpl = new OCP\Template( 'files_sharing_widget', 'settings');
$tmpl->assign('shares', $allShares);
$tmpl->assign('sharaparam', $ObjParamter);

return $tmpl->fetchPage();
