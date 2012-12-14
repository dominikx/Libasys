<?php
/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 * modifed lib/base.php for Picture Widget use
 *
 */
 
class OC{
	/**
	 * Assoziative array for autoloading. classname => filename
	 */
	public static $CLASSPATH = array();
	/**
	 * The installation path for owncloud on the server (e.g. /srv/http/owncloud)
	 */
	public static $SERVERROOT = '';
	/**
	 * the current request path relative to the owncloud root (e.g. files/index.php)
	 */
	private static $SUBURI = '';
	/**
	 * the owncloud root path for http requests (e.g. owncloud/)
	 */
	public static $WEBROOT = '';
	/**
	 * The installation path of the 3rdparty folder on the server (e.g. /srv/http/owncloud/3rdparty)
	 */
	public static $THIRDPARTYROOT = '';
	/**
	 * the root path of the 3rdparty folder for http requests (e.g. owncloud/3rdparty)
	 */
	public static $THIRDPARTYWEBROOT = '';
	/**
	 * The installation path array of the apps folder on the server (e.g. /srv/http/owncloud) 'path' and
	 * web path in 'url'
	 */
	public static $APPSROOTS = array();
	/*
	 * requested app
	 */
	public static $REQUESTEDAPP = '';
	/*
	 * requested file of app
	 */
	public static $REQUESTEDFILE = '';
	/**
	 * check if owncloud runs in cli mode
	 */
	public static $CLI = false;
	/**
	 * MULTI INSTANCE OF cloud installation
	 */
	 
	 public static $SESSIONPREFIX = '';
	 
	/**
	 * SPL autoload
	 */
	public static function autoload($className) {
			
		if(array_key_exists($className, OC::$CLASSPATH)) {
			/** @TODO: Remove this when necessary
			 Remove "apps/" from inclusion path for smooth migration to mutli app dir
			*/
			
			$path = str_replace('apps/', '', OC::$CLASSPATH[$className]);
			require_once $path;
		}
		elseif(strpos($className, 'OC_')===0) {
				
			$path = strtolower(str_replace('_', '/', substr($className, 3)) . '.php');
			
		}
		elseif(strpos($className, 'OCP\\')===0) {
			$path = 'public/'.strtolower(str_replace('\\', '/', substr($className, 3)) . '.php');
		}
		elseif(strpos($className, 'OCA\\')===0) {
			$path = 'apps/'.strtolower(str_replace('\\', '/', substr($className, 3)) . '.php');
		}
		else{
			return false;
		}

		if($fullPath = stream_resolve_include_path($path)) {
			require_once $path;
		}
		return false;
	}

 public static function initPaths() {
		// calculate the root directories
		//OC::$SERVERROOT='/opt/local/apache2/htdocs/oc45/';
		OC::$SERVERROOT=str_replace("\\", '/', substr(__FILE__, 0, -16));
		//OC::$SERVERROOT='/is/htdocs/wp11048482_6MQ454GFGE/www/demo';
		OC::$SUBURI= str_replace("\\", "/", substr(realpath($_SERVER["SCRIPT_FILENAME"]), strlen(OC::$SERVERROOT)));
		$scriptName=$_SERVER["SCRIPT_NAME"];
		if(substr($scriptName, -1)=='/') {
			$scriptName.='index.php';
			//make sure suburi follows the same rules as scriptName
			if(substr(OC::$SUBURI, -9)!='index.php') {
				if(substr(OC::$SUBURI, -1)!='/') {
					OC::$SUBURI=OC::$SUBURI.'/';
				}
				OC::$SUBURI=OC::$SUBURI.'index.php';
			}
		}
		
		OC::$WEBROOT=substr($scriptName, 0, strlen($scriptName)-strlen(OC::$SUBURI));	
		
        if ( isset($_SERVER['HTTP_X_FORWARDED_SERVER']) ) {
           OC::$WEBROOT=$_SERVER['SERVER_NAME']. OC::$WEBROOT;
			OC_Log::write('core', 'WEBROOT: '. OC::$WEBROOT, OC_Log::DEBUG);
         }	
			
			if(OC::$WEBROOT!='' and OC::$WEBROOT[0]!=='/') {
				OC::$WEBROOT='/'.OC::$WEBROOT;
			
		}
			
		// ensure we can find OC_Config
		set_include_path(
			OC::$SERVERROOT.'/lib'.PATH_SEPARATOR.
			get_include_path()
		);

		// search the 3rdparty folder
		if(OC_Config::getValue('3rdpartyroot', '')<>'' and OC_Config::getValue('3rdpartyurl', '')<>'') {
			OC::$THIRDPARTYROOT=OC_Config::getValue('3rdpartyroot', '');
			OC::$THIRDPARTYWEBROOT=OC_Config::getValue('3rdpartyurl', '');
		}elseif(file_exists(OC::$SERVERROOT.'/3rdparty')) {
			OC::$THIRDPARTYROOT=OC::$SERVERROOT;
			OC::$THIRDPARTYWEBROOT=OC::$WEBROOT;
		}elseif(file_exists(OC::$SERVERROOT.'/../3rdparty')) {
			OC::$THIRDPARTYWEBROOT=rtrim(dirname(OC::$WEBROOT), '/');
			OC::$THIRDPARTYROOT=rtrim(dirname(OC::$SERVERROOT), '/');
		}else{
			echo("3rdparty directory not found! Please put the ownCloud 3rdparty folder in the ownCloud folder or the folder above. You can also configure the location in the config.php file.");
			exit;
		}
		if(file_exists(OC::$SERVERROOT.'/apps')) {
			OC::$APPSROOTS[] = array('path'=> OC::$SERVERROOT.'/apps', 'url' => '/apps', 'writable' => true);
		}elseif(file_exists(OC::$SERVERROOT.'/../apps')) {
			OC::$APPSROOTS[] = array('path'=> rtrim(dirname(OC::$SERVERROOT), '/').'/apps', 'url' => '/apps', 'writable' => true);
		}

		if(empty(OC::$APPSROOTS)) {
			echo("apps directory not found! Please put the ownCloud apps folder in the ownCloud folder or the folder above. You can also configure the location in the config.php file.");
			exit;
		}
		$paths = array();
		foreach( OC::$APPSROOTS as $path)
			$paths[] = $path['path'];

		// set the right include path
		set_include_path(
			OC::$SERVERROOT.'/lib'.PATH_SEPARATOR.
			OC::$SERVERROOT.'/config'.PATH_SEPARATOR.
			OC::$THIRDPARTYROOT.'/3rdparty'.PATH_SEPARATOR.
			implode($paths,PATH_SEPARATOR).PATH_SEPARATOR.
			get_include_path().PATH_SEPARATOR.
			OC::$SERVERROOT
		);
	}

      public static function initSession() {
		ini_set('session.cookie_httponly', '1;');
		
		session_start();
	}
 
	public static function init() {
		// register autoloader
		spl_autoload_register(array('OC','autoload'));
		setlocale(LC_ALL, 'en_US.UTF-8');

		// set some stuff
		//ob_start();
		error_reporting(E_ALL | E_STRICT);
		if (defined('DEBUG') && DEBUG) {
			ini_set('display_errors', 1);
		}
		self::$CLI=(php_sapi_name() == 'cli');

		date_default_timezone_set('UTC');
		ini_set('arg_separator.output', '&amp;');

		

		//try to configure php to enable big file uploads.
		//this doesn´t work always depending on the webserver and php configuration.
		//Let´s try to overwrite some defaults anyways

		//try to set the maximum execution time to 60min
		set_time_limit(3600);
		ini_set('max_execution_time', 3600);
		ini_set('max_input_time', 3600);

		//try to set the maximum filesize to 10G
		@ini_set('upload_max_filesize', '10G');
		@ini_set('post_max_size', '10G');
		@ini_set('file_uploads', '50');
		//print OC::$SERVERROOT;
		OC::$SESSIONPREFIX=OC_Config::getValue('SESSIONPREFIX');
       // session_save_path('/is/htdocs/wp11048482_6MQ454GFGE/www/demo/tmp');
		//try to set the session lifetime to 60min
		@ini_set('gc_maxlifetime', '3600');


	
		self::initPaths();

		

		// register the stream wrappers
		require_once 'streamwrappers.php';
		stream_wrapper_register("fakedir", "OC_FakeDirStream");
		stream_wrapper_register('static', 'OC_StaticStreamWrapper');
		stream_wrapper_register('close', 'OC_CloseStreamWrapper');

		
		self::initSession();
		

		//$errors=OC_Util::checkServer();
		


		OC_User::useBackend(new OC_User_Database());
		OC_Group::useBackend(new OC_Group_Database());

		// Load Apps
		// This includes plugins for users and filesystems as well
		global $RUNTIME_NOAPPS;
		global $RUNTIME_APPTYPES;
		if(!$RUNTIME_NOAPPS ) {
			if($RUNTIME_APPTYPES) {
				OC_App::loadApps($RUNTIME_APPTYPES);
			}else{
				OC_App::loadApps();
			}
		}

		//setup extra user backends
		OC_User::setupBackends();

		// register cache cleanup jobs
		OC_BackgroundJob_RegularTask::register('OC_Cache_FileGlobal', 'gc');
		OC_Hook::connect('OC_User', 'post_login', 'OC_Cache_File', 'loginListener');

		// Check for blacklisted files
		OC_Hook::connect('OC_Filesystem', 'write', 'OC_Filesystem', 'isBlacklisted');
		OC_Hook::connect('OC_Filesystem', 'rename', 'OC_Filesystem', 'isBlacklisted');

		//make sure temporary files are cleaned up
		register_shutdown_function(array('OC_Helper','cleanTmp'));

		//parse the given parameters
		self::$REQUESTEDAPP = (isset($_GET['app']) && trim($_GET['app']) != '' && !is_null($_GET['app'])?str_replace(array('\0', '/', '\\', '..'), '', strip_tags($_GET['app'])):OC_Config::getValue('defaultapp', 'files'));
		if(substr_count(self::$REQUESTEDAPP, '?') != 0) {
			$app = substr(self::$REQUESTEDAPP, 0, strpos(self::$REQUESTEDAPP, '?'));
			$param = substr($_GET['app'], strpos($_GET['app'], '?') + 1);
			parse_str($param, $get);
			$_GET = array_merge($_GET, $get);
			self::$REQUESTEDAPP = $app;
			$_GET['app'] = $app;
		}
		self::$REQUESTEDFILE = (isset($_GET['getfile'])?$_GET['getfile']:null);
		if(substr_count(self::$REQUESTEDFILE, '?') != 0) {
			$file = substr(self::$REQUESTEDFILE, 0, strpos(self::$REQUESTEDFILE, '?'));
			$param = substr(self::$REQUESTEDFILE, strpos(self::$REQUESTEDFILE, '?') + 1);
			parse_str($param, $get);
			$_GET = array_merge($_GET, $get);
			self::$REQUESTEDFILE = $file;
			$_GET['getfile'] = $file;
		}
		if(!is_null(self::$REQUESTEDFILE)) {
			$subdir = OC_App::getAppPath(OC::$REQUESTEDAPP) . '/' . self::$REQUESTEDFILE;
			$parent = OC_App::getAppPath(OC::$REQUESTEDAPP);
			if(!OC_Helper::issubdirectory($subdir, $parent)) {
				self::$REQUESTEDFILE = null;
				header('HTTP/1.0 404 Not Found');
				exit;
			}
		}
		
	}
	
}

function encrypt($sValue, $sSecretKey) {
	return rtrim(
        base64_encode(
            mcrypt_encrypt(
                MCRYPT_RIJNDAEL_256,
                $sSecretKey, $sValue, 
                MCRYPT_MODE_ECB, 
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256, 
                        MCRYPT_MODE_ECB
                    ), 
                    MCRYPT_RAND)
                )
            )
        ,"\0\3");
}

function decrypt($sValue, $sSecretKey) {
	return rtrim(
        mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256, 
            $sSecretKey, 
            base64_decode($sValue), 
            MCRYPT_MODE_ECB,
            mcrypt_create_iv(
                mcrypt_get_iv_size(
                    MCRYPT_RIJNDAEL_256,
                    MCRYPT_MODE_ECB
                ), 
                MCRYPT_RAND
            )
        )
    ,"\0\3");
}

// define runtime variables - unless this already has been done
if( !isset( $RUNTIME_NOAPPS )) {
	$RUNTIME_NOAPPS = false;
}

if(!function_exists('get_temp_dir')) {
	function get_temp_dir() {
		if( $temp=ini_get('upload_tmp_dir') )        return $temp;
		if( $temp=getenv('TMP') )        return $temp;
		if( $temp=getenv('TEMP') )        return $temp;
		if( $temp=getenv('TMPDIR') )    return $temp;
		$temp=tempnam(__FILE__, '');
		if (file_exists($temp)) {
			unlink($temp);
			return dirname($temp);
		}
		if( $temp=sys_get_temp_dir())    return $temp;

		return null;
	}
}
OC::init();