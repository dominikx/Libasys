<?php
/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */
OC::$CLASSPATH['OC_Share_Backend_File'] = "files_sharing/lib/share/file.php";
OC::$CLASSPATH['OC_Share_Backend_Folder'] = 'files_sharing/lib/share/folder.php';
OC::$CLASSPATH['OC_Filestorage_Shared'] = "files_sharing/lib/sharedstorage.php";
OCP\Util::connectHook('OC_Filesystem', 'setup', 'OC_Filestorage_Shared', 'setup');
OCP\Share::registerBackend('file', 'OC_Share_Backend_File');
OCP\Share::registerBackend('folder', 'OC_Share_Backend_Folder', 'file');

$bReal=false;
if (isset($_GET['action']) && $_GET['action']=='real') {
	$bReal=true;
}

if (isset($_GET['file']) || isset($_GET['dir'])) {

	if (isset($_GET['dir'])) {
		$type = 'folder';
		$type1 = 'dir';
		$path = $_GET['dir'];
		if (strlen($path) > 1 and substr($path, -1, 1) === '/') {
			$path = substr($path, 0, -1);
		}
		$baseDir = $path;
		$dir = $baseDir;

	} else {
		$type = 'file';
		$type1 = 'file';
		$path = $_GET['file'];
		if (strlen($path) > 1 and substr($path, -1, 1) === '/') {
			$path = substr($path, 0, -1);
		}
	}

		$uidOwner = substr($path, 1, strpos($path, '/', 1) - 1);
		
		if (OCP\User::userExists($uidOwner)) {
			OC_Util::setupFS($uidOwner);
			$fileSource = OC_Filecache::getId($path, '');
			if ($fileSource != -1 && ($linkItem = OCP\Share::getItemSharedWithByLink($type, $fileSource, $uidOwner))) {
				$passwordProtect=false;
					
				$Param=OC_Preferences::getValue($uidOwner, 'files_sharing_widget', 'parameter','');	
				if($Param) $ObjParamter=json_decode($Param,true);
				else{
					$ObjParamter['maxpicsperpage']=10;
					$ObjParamter['imgheight']=150;
					$ObjParamter['width']=750;
					$ObjParamter['height']=550;
					$ObjParamter['watermark']=1;
					$ObjParamter['watermarktxt']='';
					$ObjParamter['title']='';
				}
				if(!isset($ObjParamter['watermarktxt'])) {
					$ObjParamter['watermark']=0;	
					$ObjParamter['watermarktxt']='';
				}
				if(!isset($ObjParamter['title'])) $ObjParamter['title']='';
				
				
					
				if (isset($linkItem['share_with'])) {
				// Check password
				$passwordProtect=true;		
				if (isset($_GET['password'])) {
						
					$password = $_GET['password'];
					$storedHash = $linkItem['share_with'];
					$forcePortable = (CRYPT_BLOWFISH != 1);
					$hasher = new PasswordHash(8, $forcePortable);
					if (!($hasher->CheckPassword($password.OC_Config::getValue('passwordsalt', ''), $storedHash))) {
						$passwordProtect=true;	
						
					} else {
						// Save item id in session for future requests
						$_SESSION['public_link_authenticated'] = $linkItem['id'];
						$passwordProtect=false;		
					}
				// Check if item id is set in session
				} else if (!isset($_SESSION['public_link_authenticated']) || $_SESSION['public_link_authenticated'] !== $linkItem['id']) {
					$passwordProtect=true;		
					
				}
				
				if(isset($_SESSION['public_link_authenticated']) && $_SESSION['public_link_authenticated']==$linkItem['id']){
					$passwordProtect=false;		
				}
			}
					
				

				$path = $linkItem['path'];
				
				
				if (isset($_GET['path'])) {
					$path .= $_GET['path'];
					$dir .= $_GET['path'];
				
			    }
				// Download the file
				if (isset($_GET['action']) && $_GET['action']=='norm') {
					if (isset($_GET['dir'])) {
					   if (isset($_GET['path']) && $_GET['path'] != '') {// download a file from a shared directory
							//OC_Files::get('', $path, $_SERVER['REQUEST_METHOD'] == 'HEAD' ? true : false);
							OC_Widget_Helper::makeNormPic($path,$ObjParamter['watermark'],$ObjParamter['watermarktxt']);
						}
					} 
			
				} else if (isset($_GET['action']) && $_GET['action']=='thumb') {
					
					if (isset($_GET['dir'])) {
						if (isset($_GET['path']) && $_GET['path'] != '') {
							OC_Widget_Helper::makeThumb($path,$ObjParamter['imgheight'],$ObjParamter['watermark'],$ObjParamter['watermarktxt']);
						}
					}
				}else{

				
				if (!$passwordProtect && OC_Filesystem::is_dir($path)) {

					$dataOutput = '<div id="ownWidget-slider">';
					$dataFolder='';
					//$dataOutput='<ul class="ownWidget-scrollMe">
					//			          <li class="ownWidget-row">';
					//$files = array();
					$rootLength = strlen($baseDir) + 1;
					$counter = 0;
					$maxNeben = $ObjParamter['maxpicsperpage'];
					if ($maxNeben)
						$maxNeben = ((int)$maxNeben - 1);
					if (!$maxNeben)	$maxNeben = 5;
					
					$mySecret=OCP\Config::getSystemValue('secretword');
					if($mySecret=='') $mySecret='mySecretWord';
					$thumbSize=$ObjParamter['imgheight'];
					
					//$aFilesArray=[];
					
					foreach (OC_Files::getDirectoryContent($path) as $i) {
						$i['date'] = OCP\Util::formatDate($i['mtime']);
						
						$i['directory'] = '/'.substr('/'.$uidOwner.'/files'.$i['directory'], $rootLength);
							if ($i['directory'] == '/') {
								$i['directory'] = '';
							}
							
						if ($i['type'] == 'file' && stristr($i['mimetype'], 'image')) {
							if ($counter == 0) {
								$dataOutput .= '<div>';
							}
							$fileinfo = pathinfo($i['name']);
							//$i['path']=$uidOwner.'/files'.$path;
							$i['basename'] = $fileinfo['filename'];
							$i['extension'] = isset($fileinfo['extension']) ? ('.' . $fileinfo['extension']) : '';
							
							

							$SHOWURL = OC_Widget_Helper::linkToWidget('norm') . '&path='.$i['directory'].'/' . $i['basename'] . $i['extension'].'&iToken='.rawurlencode($_GET['iToken']);

							$SHOWThumb = OC_Widget_Helper::linkToWidget('thumb') . '&path='.$i['directory'].'/' . $i['basename'] . $i['extension'].'&iToken='.rawurlencode($_GET['iToken']);

							$dataOutput .='<a rel="fancyArea" href="' . $SHOWURL . '" title="' . $i['basename'] . '"><img class="imgshow" src="' . $SHOWThumb . '" height="'.$thumbSize.'" style="height:'.$thumbSize.'px;"  /></a>';

							if ($counter == $maxNeben) {
								$dataOutput .= '</div>';
								$counter = -1;
							}
							
							$counter++;
						}
						
                        if ($i['type'] == 'dir'){
							    
							    $AlbumData=OC_Widget_Helper::getCountPicsDirectory($i['id']);
							    $ouputAlbumThumb='<div class="rotate"><div style="width:100px;height:75px;">&nbsp;</div ><span>'.$i['name'].' ('.$AlbumData['ANZAHLPICS'].')</span></div>';
								if($AlbumData['path']){
									$relPath=substr($AlbumData['path'], $rootLength);
									
									$ShowAlbumThumb = OC_Widget_Helper::linkToWidget('thumb') . '&path=/'.$relPath.'&iToken='.rawurlencode($_GET['iToken']);
									$ouputAlbumThumb='<div class="rotate"><img class="imgAlbumshow" src="' . $ShowAlbumThumb . '" width="100" style="width:100px;height:65px;"  /><br /> <span>'.$i['name'].' ('.$AlbumData['ANZAHLPICS'].')</span></div>';
								}
								
							   $dataFolder.= '<a class="loadAlbum" href="javascript:;" title="'.$i['directory'].'/'.$i['name'].'">'.$ouputAlbumThumb.'</a> ';
						}
						
						$i['permissions'] = OCP\Share::PERMISSION_READ;

					}
					if($dataFolder=='') $dataFolder='';
					else  $dataFolder='<br /><div id="albumPics">'.$dataFolder.'</div>';
                    
					// Make breadcrumb
					//$breadcrumb = array();
					$breadCrumbOutput='';
					$pathtohere = '';
					$count = 1;
					foreach (explode('/', $dir) as $i) {
						if ($i != '') {
							if ($i != $baseDir) {
								$pathtohere .= '/'.$i;
							}						
							if ( strlen($pathtohere) <  strlen($_GET['dir'])) {
								continue;
							}
							$outputDir=str_replace($_GET['dir'], "", $pathtohere, $count);
							$outputName=$i;
							if($outputDir=='') $outputName='Home';	
							
							$breadCrumbOutput.='<a class="loadAlbum" href="javascript:;" title="'.$outputDir.'">&raquo; '.$outputName.'</a> ';
							
						}
						
					}


					if (($counter - 1) != $maxNeben) {
						$dataOutput .= '</div>';
					}
					//$dataOutput.='</li></ul>';
					$dataOutput .= '</div>';
					
					if($bReal==false){
						$aBack = array('databack' => $dataOutput,'folder'=>$dataFolder,'nav'=>$breadCrumbOutput, 'success' => 1);
						$data = json_encode($aBack);
					    echo $_GET['jsonp_callback'] . '(' . $data . ');';
					}else{
						echo OC_Widget_Helper::loadTemplateReal($ObjParamter['width'],$ObjParamter['height'],$ObjParamter['title']);
						
					}
				}else{
					if($passwordProtect	){
						$dataFolder='';
						$breadCrumbOutput='Password';	
						$dataOutput='<div style="text-align:center;margin-top:40px;"><form id="loginForm" action=" " method="post">
													Password:
													<input type="password" name="password" id="password" value="" />
													<input type="submit" value="Go" id="iSubmit" />
										             </form>
										     </div>';		
					}
					
					if($bReal==false){
						$aBack = array('databack' => $dataOutput,'folder'=>$dataFolder,'nav'=>$breadCrumbOutput, 'success' => 1);
						$data = json_encode($aBack);
					    echo $_GET['jsonp_callback'] . '(' . $data . ');';
					}else{
						echo OC_Widget_Helper::loadTemplateReal($ObjParamter['width'],$ObjParamter['height'],$ObjParamter['title']);
						
					}
					
				}
				}
			} else {
				$dataFolder='';
				
				$breadCrumbOutput='Share Pics';		
				$dataOutput = '&raquo; No Shared Files available!';
				if($bReal==false){
						$aBack = array('databack' => $dataOutput,'folder'=>$dataFolder,'nav'=>$breadCrumbOutput, 'success' => 1);
						$data = json_encode($aBack);
					    echo $_GET['jsonp_callback'] . '(' . $data . ');';
					}else{
						echo OC_Widget_Helper::loadTemplateReal($ObjParamter['width'],$ObjParamter['height'],$ObjParamter['title']);
					}
			}
		}
	

}
