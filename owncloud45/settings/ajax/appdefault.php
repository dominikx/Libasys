<?php

// Init owncloud
require_once '../../lib/base.php';
OC_JSON::checkAdminUser();
OCP\JSON::callCheck();

if(isset($_POST['a'])){
    $app = trim($_POST['a']);
    OCP\Config::setSystemValue('defaultapp', $app);
    
    OC_JSON::success(array("data" => array( "app" => $app )));
}

