<?php
/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */
$cleanText='widgetloader.php';
$pathload=str_replace($cleanText,'',$_SERVER['REQUEST_URI']);
$pathload= webUrl().$pathload."js/widget.js";
//print $pathload;
header ("Content-type: text/javascript");
readfile($pathload);


  function webUrl() {
		
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
			$proto = 'https';	
			$host=$_SERVER['HTTP_X_FORWARDED_HOST'].'/'.$_SERVER['SERVER_NAME'];
		}else{
			if(isset($_SERVER['HTTPS']) and !empty($_SERVER['HTTPS']) and ($_SERVER['HTTPS']!='off')) {
                      $proto = 'https';
                  }else{
                      $proto = 'http';
                  }	
			$host = $_SERVER['HTTP_HOST'];
		}
		return $proto.'://'.$host;
	}

?>