<?php

// Init owncloud
require_once '../../lib/base.php';
OCP\JSON::callCheck();

// Check if we are a user
if( !OC_User::isLoggedIn() || !OC_Group::inGroup( OC_User::getUser(), 'admin' )) {
	OC_JSON::error(array("data" => array( "message" => $l->t("Authentication error") )));
	exit();
}

OCP\JSON::callCheck();

if(is_array($_POST)){
    $changed='';
    $depenceFileInaktivArray=array('files_sharing','files_archive','files_versions','files_pdfviewer','files_odfviewer','files_imageviewer','files_texteditor','files_videoviewer');
    $depenceFileAktivArray=array('files_sharing','files_archive','files_versions');
    
    foreach($_POST as $KEY => $VALUE){
      
      if(stristr($KEY,'old_')){
           $temp=explode('old_',$KEY);
           
            if((!isset($_POST[$temp[1]]) && $_POST[$KEY]=='on') || (isset($_POST[$temp[1]]) && $_POST[$KEY]=='')){
                  
                 $aTemp=explode('##',$temp[1]);
                   $SQL="DELETE FROM *PREFIX*groups_apps_access WHERE gid='".$aTemp[0]."' AND appid='".$aTemp[1]."' ";
                    $stmt = OCP\DB::prepare( $SQL);
                    $result = $stmt->execute();
                    
                     if(!isset($_POST[$temp[1]])) {
                            $baccess=0;
                            if($aTemp[1]=='files'){
                                  foreach($depenceFileInaktivArray as $depInfo){
                                        activityDB($aTemp[0],$depInfo,$baccess);  
                                        $changed.='Dependencie Gruppe '.$aTemp[0].': App '.$depInfo. "\n";
                                  }
                            }
                           if($aTemp[1]=='calendar'){
                                        activityDB($aTemp[0],'tasks',$baccess);  
                                          $changed.='Dependencie Gruppe '.$aTemp[0].": App Tasks \n";                                  
                            }
                     }
                     if(isset($_POST[$temp[1]])) {
                         $baccess=1;
                             if($aTemp[1]=='files'){
                                  foreach($depenceFileAktivArray as $depInfo){
                                         activityDB($aTemp[0],$depInfo,$baccess);  
                                         $changed.='Dependencie Gruppe '.$aTemp[0].': App '.$depInfo. "\n";
                                  }
                                  
                            }
                             
                              if($aTemp[1]=='tasks'){
                                      activityDB($aTemp[0],$depInfo,$baccess);  
                                      $changed.='Dependencie Gruppe '.$aTemp[0].": App calendar \n";
                                  
                            }
                      }
                     
                    $SQL1="INSERT INTO  *PREFIX*groups_apps_access SET gid='".$aTemp[0]."', appid='".$aTemp[1]."', access='".$baccess."' ";
                    $stmt = OCP\DB::prepare( $SQL1);
                    $result = $stmt->execute();
                  
                   $changed.='Gruppe '.$aTemp[0].': App '.$aTemp[1]."\n";
            }
      }
        
    
    }
}

OC_JSON::success(array("data" => array( "changed" => $changed )));

function activityDB($GID,$APPID,$BACCESS){
    
    $SQL="DELETE FROM *PREFIX*groups_apps_access WHERE gid='".$GID."' AND appid='".$APPID."' ";
     $stmt = OCP\DB::prepare( $SQL);
     $result = $stmt->execute();

   $SQL2="INSERT INTO  *PREFIX*groups_apps_access SET gid='".$GID."', appid='".$APPID."', access='".$BACCESS."' ";
    $stmt = OCP\DB::prepare( $SQL2);
    $result = $stmt->execute();
    
}
