<?php
/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */


class OC_Widget_Helper {

/**
	 * @brief Creates an absolute url for widget use
	 * @param string $service id
	 * @return string the url
	 *
	 * Returns a absolute url to the given service.
	 */
	public static function linkToWidget($service, $add_slash = false) {
		return OC_Helper::linkToAbsolute( '', 'widget.php') . '?action=' . $service . (($add_slash && $service[strlen($service)-1]!='/')?'/':'');
	}
   
   
   /**
	 * @make temp Thumbs
	 * @param string path
	 * @param int thumgheight
	 *@return Thumb
	 */
	 
   public static function makeThumb($path,$imgHeight=150) {
                $img = $path;
				
				
				$image = new \OC_Image();
				$image -> loadFromFile(OC_Filesystem::getLocalFile($img));
				if (!$image -> valid())
					return false;
				$image -> fixOrientation();
				$ret = $image -> preciseResize(floor(($imgHeight * $image -> width()) / $image -> height()), $imgHeight);
				if ($image) {
					OCP\Response::enableCaching(3600 * 24);
					// 24 hour
					$image -> show();
				}
   }
   
    /**
	 * @load Template for real view
	 * @param int width of the widget
	 * @param int height of the widget
	 * @return return the template
	 *
	 */
   
    public static function loadTemplateReal($WIDTH="770",$HEIGHT="570") {
    	
		$tpl="<!DOCTYPE html>\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"de-DE\" lang=\"de-DE\">\n<head>\n<title>Picture Widget</title>
		\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><meta content=\"yes\" name=\"apple-mobile-web-app-capable\" />
		\n<meta content=\"minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no\" name=\"viewport\" />
		\n<link href=\"".OC_HELPER::makeURLAbsolute(OC::$WEBROOT)."apps/files_sharing_widget/img/startup.png\" rel=\"apple-touch-startup-image\" />
        \n<link href=\"".OC_HELPER::makeURLAbsolute(OC::$WEBROOT)."apps/files_sharing_widget/img/homescreen.png\" rel=\"apple-touch-icon\" />
										\n<script>var ownWidgetOptions = {crypt:'".$_GET['iToken']."',path:'".OC_HELPER::makeURLAbsolute(OC::$WEBROOT)."',cssAddWidget:{'width':'".$WIDTH."','height':'".$HEIGHT."'}};</script>
										\n<script src=\"".OC_HELPER::makeURLAbsolute(OC::$WEBROOT)."widgetloader.php\" type=\"text/javascript\"></script>\n
									\n</head>
									\n<body class=\"widgetbg\">
										\n<div id=\"ownWidget-container\"></div>
									\n</body>
					\n</html>";
				
			return $tpl;		
		
	}
	
	 /**
	 * @encrypt string
	 * @param string Value to encrypt
	 * @param string Secret KEy
	 * @return enctrypted string
	 *
	 */
	
	public static function encrypt($sValue, $sSecretKey) {
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
	
	
	 /**
	 * @get Count Pics of Directory and one image for Preview 
	 * @param int ID of the Directory
	 * 
	 * @return the Count of images and one path to a pic
	 *
	 */
	public static function getCountPicsDirectory($ID){
		$SQL="SELECT path, COUNT(*) AS ANZAHLPICS FROM *PREFIX*fscache  WHERE parent='".intval($ID)."' AND mimepart='image' ORDER BY mtime DESC";
		//print $SQL;
		$stmt = OCP\DB::prepare($SQL);
		$result = $stmt -> execute();
		$sData=$result->fetchRow();
		if($sData['ANZAHLPICS']) return $sData;
		else return "0";
	}
	
	
	 /**
	 * @returns an Overview of all Shares
	 * 
	 * @param string Secret KEy
	 * @return array of all shares
	 *
	 */
   public static function getAllSharesUser($SECRET){
    
       //  USER = 0; GROUP = 1;LINK = 3;
       // link = parent.location.protocol+'//'+location.host+OC.linkTo('', 'public.php')+'?service=files&'+$('tr[data-id='+String(itemSource)+']').attr('data-type')+'='+file;
 		

         $SQL="SELECT s.id,s.share_with,s.file_target,.s.item_type,s.share_type,s.expiration,s.uid_owner,s.token, f.path FROM  *PREFIX*share s
         LEFT JOIN  *PREFIX*fscache f ON s.item_source=f.id 
         WHERE s.uid_owner='".\OC_User::getUser()."'  ";
         $stmt = \OCP\DB::prepare( $SQL);
        $result = $stmt->execute();
        $shareInfo ='';
        $output=false;
         while( $row = $result->fetchRow()){
            $shareInfo[] = $row;
            
        }
         if($shareInfo!=''){
              $tz=\OC_Calendar_App::getTimezone();
             foreach($shareInfo as $share){
                       $itemTypeChoose='file';
                     if($share['item_type']=='folder')  $itemTypeChoose='dir';
                      $expDate = new \DateTime($share['expiration'], new \DateTimeZone($tz));
                      $EXPDATE=$expDate->format('d.m.Y H:i');
                      
                  if($share['share_type']==0){
                        $output[]=array(
                        'id'=>$share['id'],
                        'shareType'=>'User',
                         'shareName'=>$share['share_with'],
                        'link'=>'#',
                        'name'=>$share['file_target'],
                        'date'=>'Gültig bis: '.$EXPDATE,
                        'iToken'=>''
                      );
                   }
                if($share['share_type']==1){
                        $output[]=array(
                         'id'=>$share['id'],
                        'shareType'=>'Gruppe',
                        'shareName'=>$share['share_with'],
                        'link'=>'#',
                        'name'=>$share['file_target'],
                        'date'=>'Gültig bis: '.$EXPDATE,
                        'iToken'=>''
                      );
                   }
                 if($share['share_type']==3){
                     $addPassImg='';
					 if($share['share_with']!='') $addPassImg=' [Password]';	
                     $output[]=array(
                      'id'=>$share['id'],
                     'shareType'=>'Link',
                     'shareName'=>'Guest'.$addPassImg,
                     'link'=>\OC_Helper::linkToPublic('files').'&t='.$share['token'],
                      'name'=>$share['file_target'],
                      'date'=>'Gültig bis: '.$EXPDATE,
                      'iToken'=>rawurlencode(self::encrypt($share['path'],$SECRET))
                      );
                 }
             }
         
         }
        return $output;
       
    }

      /**
	 * @delete Shares
	 * 
	 * @param int Id of the shared folder or file
	 * 
	 *
	 */
	 
	public static function delShare($ID) {
		$SQL = "DELETE FROM *PREFIX*share WHERE id='".intval($ID)."' LIMIT 1";
		$stmt = OCP\DB::prepare($SQL);
		$result = $stmt -> execute();

	}
	
}