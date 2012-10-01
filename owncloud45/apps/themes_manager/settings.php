<?php

/**
* ownCloud - DjazzLab Themes Manager plugin
*
* @author Xavier Beurois
* @copyright 2012 Xavier Beurois www.djazz-lab.net
* 
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either 
* version 3 of the License, or any later version.
* 
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*  
* You should have received a copy of the GNU Lesser General Public 
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
* 
*/

OCP\User::checkAdminUser();
OCP\App::checkAppEnabled('themes_manager');

OCP\Util::addScript('themes_manager', 'settings');

$tmpl = new OCP\Template('themes_manager', 'settings.tpl');

$themes = Array();
$dir = OC::$SERVERROOT.'/themes';
if(is_dir($dir)){
    if($dh = opendir($dir)){
        while(($subdir = readdir($dh)) !== false){
            if(is_dir($dir.'/'.$subdir) && $subdir != '..' && $subdir != '.'){
            	$themes[] = $subdir;
            }
        }
        closedir($dh);
    }
}
$tmpl->assign('themes', $themes);

$current = trim(OCP\Config::getSystemValue('theme', '0'));
if($current==''){
	$current = '0';
}
$tmpl->assign('current', $current);

return $tmpl->fetchPage();
