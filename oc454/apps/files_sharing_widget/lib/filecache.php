<?php
class FileCache {
	const VERSION = '0';
	const ENABLED = TRUE;
	private static function isEnabled() {
		return ENABLED;
	}
	private static function isDisabled() {
		return !ENABLED;
	}

	private static function cacheDir(){
		$dir = sys_get_temp_dir() . '/' . 'FileCache';
		if (is_dir($dir))
			return $dir;
		else if (mkdir($dir, 0700))
			return $dir;
		else 
			return null;
	}

	// this function should be called periodicly, maybe by something like
	// a cron job	
	// parameter is max age of cached file to retain in seconds
	// e.g. 60*60*24*7 = 604800 is one week
	public static function cleanCache($maxAge=604800){
		$dir=scandir(self::cacheDir());
		foreach( $dir as $f ){
			$file = self::cacheDir() . '/' . $f;
			if(is_file($file)) {
				$age = time() - filemtime($file);
				if ($age > $maxAge ) {
					unlink($file);
				}
			}
		}
	}

	public static function log($msg){
		$myFile = "/tmp/log.txt";
		$fh = fopen($myFile, 'a'); 
		fwrite($fh, $msg);
		fclose($fh);
	}

	//  variable-length argument list: key($ocPath, ... )
	public static function key($ocPath){
		$key = self::VERSION;
		$localPath = \OC_Filesystem::getLocalFile($ocPath);
		$key = $key . filemtime($localPath);
		for($i = 1; $i < func_num_args(); $i++){
			$key = $key . func_get_arg($i);
		}
		return md5($key);
	}

	private static function getFilePath($cachekey){
		return self::cacheDir() . '/' . $cachekey;
	}

	public static function getFile($cachekey){
		if (self::isDisabled())
			return null;
		if ( is_file(self::getFilePath($cachekey)) ){
			$image = new \OC_Image();
			$image -> loadFromFile(self::getFilePath($cachekey));
			if ($image->valid())
				 return $image;
		}
		return null;
	}
	public static function setFile($cachekey, $ocImage){
		if (self::isDisabled())
			return;
		if ($cachekey == null )
			return;
		if ($ocImage == null )
			return;
		return $ocImage->save(self::getFilePath($cachekey));
	}
}

?>
