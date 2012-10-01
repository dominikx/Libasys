<?php
// Check if we are a user
OCP\User::checkLoggedIn();
OCP\App::checkAppEnabled('kunden');

OCP\App::setActiveNavigationEntry( 'kunden' );

// Load the files we need


OCP\Util::addStyle('kunden', 'style');
OCP\Util::addscript('kunden','mootools-core');
OCP\Util::addscript('kunden','mootools-more');
OCP\Util::addscript('kunden','ownWindoo');
OCP\Util::addscript('kunden','scrollbar');
OCP\Util::addscript('kunden','functions');


//SQL


$tmpl = new OCP\Template( 'kunden', 'info', "user");

if(isset($_GET['kundenId'])>0) {
    $tmpl->assign('kunden_id', intval($_GET['kundenId']), false);
}

$tmpl->printPage();

?>
