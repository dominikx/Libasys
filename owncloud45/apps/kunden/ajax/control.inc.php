<?php
//header("Content-Type: text/xhtml; charset=iso-8859-1");
//include ("../lib/configuration.inc.php");
OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('kunden');

if (isset ($_POST['ajaxaction']))
  $AJAX_ACTION = intval($_POST['ajaxaction']);

switch($AJAX_ACTION){
	
	case 1:
		
		if(isset($_POST['kundenId'])) $kundenId=intval($_POST['kundenId']);
	
				
		  $SQL="SELECT COUNT(id) AS COUNTNOTICE FROM *PREFIX*kunden_notizdaten WHERE kunden_id='".$kundenId."' AND user='".OCP\USER::getUser()."'";
          $stmt = OCP\DB::prepare($SQL);
          $result = $stmt->execute();
          $countInfo = $result->fetchRow();
          $COUNTNOTICE='';
          if($countInfo['COUNTNOTICE']>0) $COUNTNOTICE='<sup class="redNotice" title="Notizen">'.$countInfo['COUNTNOTICE'].'</sup>';
                        
		$SQL="SELECT * FROM *PREFIX*kunden_daten  WHERE id='".$kundenId."' AND user='".OCP\USER::getUser()."'";
		$stmt = OCP\DB::prepare($SQL);
		$result = $stmt->execute();
		$mainInfo = $result->fetchRow();
		
		$kundeId = $mainInfo['id'];
		
		$mainName = $mainInfo['name'];
		$mainVorname = $mainInfo['vorname'];
		$mainAnsprechpartner = $mainInfo['ansprechpartner'];
		$mainKundenummer = htmlentities($mainInfo['kundennummer']);
		
		$mainPlz = $mainInfo['plz'];
		$mainStrasse = $mainInfo['strasse'];
		$mainOrt = $mainInfo['ort'];
		
		$mainTel = htmlentities($mainInfo['telefon']);
		$mainFax = htmlentities($mainInfo['fax']);
		$mainHandy = htmlentities($mainInfo['handy']);
		$mainEmail = $mainInfo['email'];
		
		$foundCust = $mainInfo['foundCustomer'];
		
		$CustomerOrdner=stripslashes($mainInfo['kunden_ordner']);
		
		$SHOWDOCLINK='';
		
		if($CustomerOrdner!='') $SHOWDOCLINK='<a href="?app=files&dir=clientsync/Kunden/'.$kundeId.'-'.$CustomerOrdner.'"  class="button">Dokumente</a>';
		
		 $moreInfoAnsprechpartner='<span class="labelLeft">Funktion:</span> <label class="editField" name="sKundenFunktion" title="Funktion">'.$mainInfo['funktion'].'</label><br />';
		 $moreInfoAnsprechpartner.='<span class="labelLeft">Geburtsdatum: </span><label class="editField" name="sKundenGeb" title="Geburtstag">'.$mainInfo['geburtsdatum'].'</label><br />';
		 
		 $getKundenStatus=OC_Kunden_Util::getArrays(2);
		
			$get_kStatus_select='';
		if (is_array($getKundenStatus)) {
			$get_kStatus_select='<select id="sKundenStatus_Sel" name="sKundenStatus_Sel" size="1" style="float:right; display:none;">';
			foreach ($getKundenStatus as $key=> $value) {
				($key == $mainInfo['kundenstatus']) ? $selected = 'selected' : $selected = '';
				$get_kStatus_select .= '<option value="' . $key . '" ' . $selected . '>' . htmlentities($value) . '</option>';

			}
			$get_kStatus_select.='</select>';
		}
		
		 $kundenStatus='<label class="editField readOnly" name="sKundenStatus">'.$getKundenStatus[$mainInfo['kundenstatus']].'</label> '.$get_kStatus_select;
		
		
		
		 $getKundenArt=OC_Kunden_Util::getArrays(3);
		 
		 $get_kArt_select='';
		if (is_array($getKundenArt)) {
			$get_kArt_select='<select id="sKundenArt_Sel" name="sKundenArt_Sel" size="1" style=" float:right; display:none;">';
			
			foreach ($getKundenArt as $key=> $value) {
				($key == $mainInfo['kundeart']) ? $selected = 'selected' : $selected = '';
				$get_kArt_select .= '<option value="' . $key . '" ' . $selected . '>' . htmlentities($value) . '</option>';

			}
			$get_kArt_select.='</select>';
		}
		
		
		$kundeArt='<label class="editField readOnly" name="sKundenArt">'.$getKundenArt[$mainInfo['kundeart']].'</label> '.$get_kArt_select;
		$output='';
       
         $isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
        $isiPhone = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPhone');
        $defaultWidth='44';
        if($isiPad || $isiPhone){
             $defaultWidth='96';
        }
		eval ("\$output.= \"" . OC_Kunden_Util::getAjaxTemplate("body_kunden_detail", "") . "\";");
		$output.=' <div id="vBarRight"><div id="vKnobRight"></div></div>';
        
		print $output;
		
		break;
		
		case 2:
			
			
			if(intval($_POST['kundenid'])>0){
			    
 			     $getKundenStatus=OC_Kunden_Util::getArrays(4);
			     $getKundenArt=OC_Kunden_Util::getArrays(5);
			
			/**
			 * 
				vorname='" . addslashes($_POST['vorname']) . "',
			 * @var unknown_type
			 */
			
			
			
			$SQL = "UPDATE *PREFIX*kunden_daten SET
										name='" . addslashes($_POST['sKundenName']) . "',										
										strasse='" . addslashes($_POST['sKundenStreet']) . "',
										plz='" . addslashes($_POST['sKundenPlz']) . "',
										ort='" . addslashes($_POST['sKundenOrt']) . "',
										ansprechpartner='" . addslashes($_POST['sKundenPartner']) . "',
										telefon='" . addslashes($_POST['sKundenTel']) . "',
										handy='" . addslashes($_POST['sKundenHdy']) . "',
										fax='" . addslashes($_POST['sKundenFax']) . "',
										kundeart='" . $getKundenArt[$_POST['sKundenArt']] . "',
										kundenstatus='" . $getKundenStatus[$_POST['sKundenStatus']] . "',
										kundennummer='" . addslashes($_POST['sKundenNr']) . "',
										funktion='" . addslashes($_POST['sKundenFunktion']) . "',
										foundCustomer='" . addslashes($_POST['sKundenFound']) . "',
										geburtsdatum='" . addslashes($_POST['sKundenGeb']) . "',
										email='" . addslashes($_POST['sKundenEmail']) . "',
										kunden_ordner='".addslashes($_POST['sKundenOrdner'])."'
							            WHERE id='" . intval($_POST['kundenid']) . "' AND user='".OCP\USER::getUser()."' ";
			
				$stmt = OCP\DB::prepare($SQL);
		        $result = $stmt->execute();
		        
			if($_POST['sKundenOrdner']!=''){
                  
                $dir='/clientsync/Kunden/'.intval($_POST['kundenid']).'-'.strtolower($_POST['sKundenOrdner']);
						
				if(!OC_Filesystem::is_dir($dir)){
					OC_Filesystem::mkdir($dir);
					OC_Filesystem::mkdir($dir.'/Auftraege');
					OC_Filesystem::mkdir($dir.'/Angebote');
					OC_Filesystem::mkdir($dir.'/Administration');
					OC_Filesystem::mkdir($dir.'/Projekte');
					OC_Filesystem::mkdir($dir.'/Schriftverkehr');
				}
		}
				
			}
			
			break;
			case 3:
				if($_POST['newCustomer']!=''){
					$SQL="INSERT INTO  *PREFIX*kunden_daten SET  name='" . addslashes($_POST['newCustomer']) . "', user='".OCP\USER::getUser()."' ";
					$stmt = OCP\DB::prepare($SQL);
		             $result = $stmt->execute();
						
				}
				
				
				break;
				
			case 4:
				
				
				if(intval($_POST['kundenId'])>0){
					$SQL="DELETE FROM  *PREFIX*kunden_daten WHERE id='" . intval($_POST['kundenId']) . "' AND user='".OCP\USER::getUser()."' ";
					$stmt = OCP\DB::prepare($SQL);
		            $result = $stmt->execute();
				}
				
   
                $OUTPUT['LIST']=OC_Kunden_Util::getKundenListe();
                
                print  $OUTPUT['LIST'];
				
					
				break;
             
            case 5:
                      $outputNotice=OC_Kunden_Util::getOutputNotizen();
                      $output='<ul id="myScrollerKunden"><li><br /><div class="kundenBody"><a href="javascript:;" style="float:left;font-size:0.1px;"  id="showNotice">+</a> <h3>Aufgaben mit Wiedervorlage</h3><br /><div id="iNotiz">'.$outputNotice.'</div></div></li></ul>';
                     print $output;
                break; 
              
                
            case 6:
                 
                 if(isset($_POST['nId'])) $noticeId=intval($_POST['nId']);
                 if(isset($_POST['noticeFlag'])) {
                         $isFlag=0;
                         if($_POST['noticeFlag']=='true'){
                             $isFlag=1;
                         }
                     
                 }
                 $SQL="UPDATE *PREFIX*kunden_notizdaten SET                              
                                           notice_flag='".$isFlag."'
                                          WHERE id='".$noticeId."' AND user='".OCP\USER::getUser()."'
                                          ";
                                    $stmt = OCP\DB::prepare($SQL);
                                    $result = $stmt->execute();
                break;
                
            case 7:
                  
                 $kundenId=0;
                 
                   if(isset($_POST['kId'])) $kundenId=intval($_POST['kId']);
                
                  $noticeId='';
                  if(isset($_POST['notice_id'])) $noticeId=intval($_POST['notice_id']);
                  
                  if(isset($_POST['hiddenfield']) && $_POST['hiddenfield']=='newitNotice' && $noticeId==''){
                
                    $time_wv='';
                    if($_POST['sWV']!=''){
                        
                        $wvdate=explode('.', $_POST['sWV'], 3);
                        $time_wv = mktime(0, 0, 0, $wvdate[1], $wvdate[0], $wvdate[2]);
                    }
                    $tempCal=explode('_',$_POST['read_worker']);
                        
                        $SQL="INSERT INTO  *PREFIX*kunden_notizdaten SET
                               kunden_id='".$kundenId."',                              
                               text='".addslashes($_POST['noticetxt'])."',
                               generate_time='".time()."',
                               user='".OCP\USER::getUser()."', 
                               worker='".addslashes($tempCal[1])."' ,                             
                               timestamp='".$time_wv."',
                               cal_id='".$tempCal[0]."' 
                              
                              ";
                        $stmt = OCP\DB::prepare($SQL);
                        $result = $stmt->execute();
                        
                        if(isset($_POST['toTask']) && $_POST['toTask']==true){
                           
                            $cid = $tempCal[0];
                           $kundenInfo=OC_Kunden_Util::getKunde($kundenId);
                            $request = array();
                            $request['summary'] = addslashes($kundenInfo['name'].': '.$_POST['noticetxt']);
                            $request["categories"] = 'Kunden';
                            $request['priority'] = '1';
                            $request['percent_complete'] = null;
                            $request['completed'] = null;
                            $request['location'] = null;
                            $request['due'] = null;
                            $request['description'] = null;
                            $vcalendar = OC_Task_App::createVCalendarFromRequest($request);
                            $id = OC_Calendar_Object::add($cid, $vcalendar->serialize());
                        }
                       
                        
                    }
                  
                  if(isset($_POST['hiddenfield']) && $_POST['hiddenfield']=='newitNotice' && $noticeId>0){
                    $time_wv='';
                    if($_POST['sWV']!=''){
                        
                        $wvdate=explode('.', $_POST['sWV'], 3);
                        $time_wv = mktime(0, 0, 0, $wvdate[1], $wvdate[0], $wvdate[2]);
                    }
                         $tempCal=explode('_',$_POST['read_worker']);
                         
                        $SQL="UPDATE *PREFIX*kunden_notizdaten SET                         
                              
                               user='".OCP\USER::getUser()."', 
                               text='".addslashes($_POST['noticetxt'])."',
                               worker='".addslashes($tempCal[1])."' ,                             
                               timestamp='".$time_wv."',
                               cal_id='".$tempCal[0]."' 
                              WHERE id='".$noticeId."' AND  kunden_id='".$kundenId."' AND user='".OCP\USER::getUser()."'
                              ";
                        $stmt = OCP\DB::prepare($SQL);
                        $result = $stmt->execute();
                        
                       if(isset($_POST['toTask']) && $_POST['toTask']==true){
                           
                            $cid = $tempCal[0];
                           $kundenInfo=OC_Kunden_Util::getKunde($kundenId);
                            $request = array();
                            $request['summary'] = addslashes($kundenInfo['name'].': '.$_POST['noticetxt']);
                            $request["categories"] = 'Kunden';
                            $request['priority'] = '1';
                            $request['percent_complete'] = null;
                            $request['completed'] = null;
                            $request['location'] = null;
                            $request['due'] = null;
                            $request['description'] = null;
                            $vcalendar = OC_Task_App::createVCalendarFromRequest($request);
                            $id = OC_Calendar_Object::add($cid, $vcalendar->serialize());
                        }
                        
                    }
                  
               $getWorkerArray=OC_Kunden_Util::getArrays(7);
               $getNoticeStatusArray=OC_Kunden_Util::getArrays(6);
            
                 if($noticeId>0){
                    $getEditData=OC_Kunden_Util::getNoticeSingleData($noticeId,$kundenId);
                    
                    $aktiveWorker=OC_Kunden_Util::generateSelectFieldArray('read_worker',$getEditData['cal_id'].'_'.$getEditData['worker'],$getWorkerArray);
                   
                    
                    $_POST['noticetxt']=stripslashes($getEditData['text']);
                    $_POST['sWV']='';
                    
                    if($getEditData['timestamp']>0) $_POST['sWV']=date("d.m.Y",$getEditData['timestamp']);
                    }else{
                        if(!isset($_POST['read_worker'])) $_POST['read_worker']='';
                        if(!isset($_POST['noticetxt'])) $_POST['noticetxt']='';
                        if(!isset($_POST['sWV'])) $_POST['sWV']='';
                        if(!isset($_POST['read_rights'])) $_POST['read_rights']='';
                         
                        $aktiveWorker=OC_Kunden_Util::generateSelectFieldArray('read_worker',$_POST['read_worker'],$getWorkerArray);
                      
                       
                        $_POST['noticetxt']=stripslashes($_POST['noticetxt']);
                    }
                
                
                   $output='<br />
                            <form name="noticeForm" id="noticeForm" action=" ">
                            <input type="hidden" name="ajaxaction" value="7" />
                            <input type="hidden" name="hiddenfield" value="" />
                             <input type="hidden" name="kId" id="refKundenId" value="'.$kundenId.'" />
                            <input type="hidden" name="notice_id" value="'.$noticeId.'" />
                            <ul class="pageitem">
                            <li class="textboxAdmin" style="padding:5px;">
                            
                             <textarea name="noticetxt" class="textClass pflicht" style="width:96%;height:100px;">'.$_POST['noticetxt'].'</textarea>
                            <br />
                            <span class="labelClass">Zuweisung</span>'.$aktiveWorker.'
                            <br class="clearing"  />
                           
                            <span class="labelClass">Wiedervorlage am</span> <input type="text" name="sWV" id="sWV" class="textField"  size="10" value="'.$_POST['sWV'].'" />
                            <br class="clearing"  />
                            <span class="labelClass">Zu Aufgaben hinzuf&uumlgen</span><input type="checkbox" name="toTask" /> ja
                            <br class="clearing"  />
                            <br />
                            <a class="button" style="float:right;" href="javascript:;" onclick="SubmitForm(\'newitNotice\',\'noticeForm\',\'innerContentNotiz\');">ERSTELLEN</a>
                            <br />
                        
                            </form><br />                            
                            ';
                            
                            print $output;
                break;
                
                case 8:
                    
                    if(isset($_POST['kundenId'])) $kundenId=intval($_POST['kundenId']);
                    
                          
                    $output=OC_Kunden_Util::getOutputNotizen($kundenId);
                    
                    
                    print '<div class="kundenBody">'.$output.'</div><br class="clearing"><br />';
                    break;
                    
				case 9:
					//als Kontakt hinzufuegen
					if(isset($_POST['kundenId'])) $kundenId=intval($_POST['kundenId']);
		
						$SQL="SELECT * FROM *PREFIX*kunden_daten  WHERE id='".$kundenId."' AND user='".OCP\USER::getUser()."' ";
						$stmt = OCP\DB::prepare($SQL);
						$result = $stmt->execute();
						$contactInfo = $result->fetchRow();
						
						
						 if(isset($_POST['hiddenfield']) && $_POST['hiddenfield']=='addContact'){
						
    						$vcard = new OC_VObject('VCARD');
    						$vcard->setUID();
    						
    						$vcard->setString('FN',$contactInfo['name']);
    						$vcard->setString('N',$contactInfo['name']);
    						$vcard->setString('ADR;TYPE=WORK',';;'.$contactInfo['strasse'].';'.$contactInfo['ort'].';;'.$contactInfo['plz'].';Germany');
    						$vcard->setString('EMAIL;TYPE=PREF',$contactInfo['email']);
    						$vcard->setString('TEL',$contactInfo['telefon']);
    						//13 = Adressbuch Kunden
    						OC_Contacts_VCard::add($_POST['adressbook_id'],$vcard, null, true);
                         }
                         
                         if(!isset($_POST['adressbook_id'])) $_POST['adressbook_id']='';
                         
                           $getAdressbookArray=OC_Kunden_Util::getArrays(8);
                           $adressBook=OC_Kunden_Util::generateSelectFieldArray('adressbook_id',$_POST['adressbook_id'],$getAdressbookArray);
					    $output='
                            <form name="contactForm" id="contactForm" action=" ">
                            <input type="hidden" name="ajaxaction" value="9" />
                            <input type="hidden" name="hiddenfield" value="" />
                              <input type="hidden" name="kundenId"  value="'.$kundenId.'" />
                              <span style="font-weight:bold; font-size:14px;line-height:24px;">'.$contactInfo['name'].'</span><br /><br />
                              <span class="labelClass">Adressbuch ausw&auml;hlen:</span>'.$adressBook.'
                            <br class="clearing"  /><br />
                            <span class="labelClass" style="text-align:center;">
                             <a class="button" style="float:none;" href="javascript:;" onclick="SubmitForm(\'addContact\',\'contactForm\',\'innerContentContact\');">hinzuf&uuml;gen</a>
                              </span>
                          
                            </form><br />                            
                            ';
                           print $output;
					break;
					
			
			
			case 11:
				
				$output='<form id="newKunde" name="newKunde" method="post" action=" ">
				<input type="hidden" name="ajaxaction" value="3" />	
				<input type="text" name="newCustomer" id="newCustomer" placeholder="Neuer Kunde" class="" />
				<a href="javascript:newKunde();" class="button">anlegen</a>
				</form>';
				print $output;
			break;
			
			
				
}



?>