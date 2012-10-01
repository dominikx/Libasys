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
* Inspired by files apps ajax/upload.php
* 
*/

OCP\User::checkLoggedIn();
OCP\APP::checkAppEnabled('themes_manager');

?>

<html>
<head></head>
<body style="color:#555;text-shadow:0 1px 0 #FFF;font:0.8em/1.6em 'Lucida Grande',Arial,Verdana,sans-serif"><?php
if(!isset($_FILES['file'])){
	die('Please, indicate a ZIP file to upload ...</body></html>');
}

$error = $_FILES['file']['error'];
if($error != 0){
	$l = OC_L10N::get('files');
	$errors = Array(
		UPLOAD_ERR_OK => $l->t("There is no error, the file uploaded with success"),
		UPLOAD_ERR_INI_SIZE => $l->t("The uploaded file exceeds the upload_max_filesize directive in php.ini").ini_get('upload_max_filesize'),
		UPLOAD_ERR_FORM_SIZE => $l->t("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"),
		UPLOAD_ERR_PARTIAL => $l->t("The uploaded file was only partially uploaded"),
		UPLOAD_ERR_NO_FILE => $l->t("No file was uploaded"),
		UPLOAD_ERR_NO_TMP_DIR => $l->t("Missing a temporary folder"),
		UPLOAD_ERR_CANT_WRITE => $l->t('Failed to write to disk'),
	);
	die($errors[$error].'</body></html>');
}
$file = $_FILES['file'];

$dir = OC::$SERVERROOT.'/themes';
$error = '';

if($file['size'] > OC_Filesystem::free_space('/')){
	die('Not enough space available</body></html>');
}

$result = Array();
if(strpos($dir, '..') === false){
	if($file['type'] != 'application/zip'){
		$error = 'Please upload a ZIP file with the theme folder inside.';
	}else{
		if(is_uploaded_file($file['tmp_name']) && !file_exists($dir.'/'.$file['name'])){
			move_uploaded_file($file['tmp_name'], $dir.'/'.$file['name']);
			if(!in_array('zip', get_loaded_extensions())){
				unlink($dir.'/'.$file['name']);
				die('Please enable PHP Zip extension ...');
			}
			$zip = new ZipArchive;
			$res = $zip->open($dir.'/'.$file['name']);
			if($res === TRUE){
			    $zip->extractTo($dir);
			    $zip->close();
				unlink($dir.'/'.$file['name']);
				OCP\Config::setSystemValue('theme',substr($file['name'],0,strrpos($file['name'],'.')));
				die('Upload OK ! Reload the page ...</body></html>');
			}else{
				unlink($dir.'/'.$file['name']);
			    die('Failed opening the zip file ...');
			}
		}else{
			$error = 'Error when uploading the file or the file already exists in the themes folder.';
		}
	}
}else{
	$error = 'Invalid destination dir.';
}

die($error.'</body></html>');