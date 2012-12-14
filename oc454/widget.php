<?php
/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */
 
$RUNTIME_NOAPPS = TRUE;
require_once 'lib/base.min.php';

$shareSecret=OCP\Config::getSystemValue('secretword');
if($shareSecret=='') $shareSecret='mySecretWord';
$getSchluessel= decrypt(rawurldecode($_GET['iToken']),$shareSecret);

$_GET['dir']=$getSchluessel;
$_GET['service']='pics';

$file='files_sharing_widget/public.php';
if(is_null($file)) {
	header('HTTP/1.0 404 Not Found');
	exit;
}

OC_Util::checkAppEnabled('files_sharing_widget');
OC_App::loadApp('files_sharing_widget');

require_once OC_App::getAppPath('files_sharing_widget') .'/public.php';
