<?php



 OCP\App::checkAppEnabled('kunden');   

OCP\App::register( array( 
  'order' => 73,
  'id' => 'kunden',
  'name' => 'Kunden' ));


OCP\App::addNavigationEntry( array( 'id' => 'kunden', 'order' => 73, 'href' => OCP\Util::linkTo( 'kunden', 'index.php' ), 'icon' => OCP\Util::imagePath( 'settings', 'users.svg' ), 'name' => 'Kunden'));
OC::$CLASSPATH['OC_Search_Provider_Kunden'] = 'apps/kunden/lib/search.php';
OC::$CLASSPATH['OC_Kunden_Util'] = 'apps/kunden/lib/util.php';

OC_Search::registerProvider('OC_Search_Provider_Kunden');
//OCP\CONFIG::setAppValue('core', 'serverinfo', '/apps/serverinfo/serverinfo.php');


