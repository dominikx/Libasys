<?php

/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */

OC_Util::checkAdminUser();
$shareSecret=OCP\Config::getSystemValue('secretword');
if($shareSecret=='') $shareSecret='mySecretWord';

$siteEmail=OCP\Config::getSystemValue('siteemail');
if($siteEmail=='') $siteEmail='';
 
$tmpl = new OCP\Template( 'files_sharing_widget', 'admin');
$tmpl->assign('secretword', $shareSecret);
$tmpl->assign('siteemail', $siteEmail);

return $tmpl->fetchPage();
