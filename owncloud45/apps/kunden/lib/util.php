<?php

class OC_Kunden_Util{
    
    
    
   
   
   public static function getNoticeSingleData($NOTIZID,$KUNDENID){
      
       $SQL="SELECT *  FROM *PREFIX*kunden_notizdaten WHERE id='".$NOTIZID."' AND kunden_id='".$KUNDENID."' AND user='".OCP\USER::getUser()."' ";
                $stmt = OCP\DB::prepare($SQL);
                 $result = $stmt->execute();
                 $row = $result->fetchRow();
                 
                 if(is_array($row)) return $row;
                 else return false;
   }
   
   public static function getKundenListe($KUNDENID=0){
       
       $stmt = OCP\DB::prepare( "SELECT * FROM *PREFIX*kunden_daten WHERE user='".OCP\USER::getUser()."' ORDER BY name ASC");
        $result = $stmt->execute();

        $kundenInfo ='';
        while( $row = $result->fetchRow()){
            $kundenInfo[] = $row;
            
        }
        $output='<ul style="margin-left:10px;" id="myScroller">';
         if(is_array($kundenInfo)){
           
            $aWV= self::getNoticeWVKunden();
            
            
           foreach($kundenInfo as $value){
            $addActiveClass='';
                if($value['id'] == $KUNDENID){
                    $addActiveClass='class="active"';
                }
                $WV='';
                
               if(isset($aWV[$value['id']]) && $aWV[$value['id']]) $WV='<sup class="redNotice" title="Heute Wiedervorlage">wv</sup>';
               
            $output.= '<li id="kunde_'.$value['id'].'" '.$addActiveClass.'><a href="javascript:getKunden(\''.$value['id'].'\');">'.$value['name'].'</a>'.$WV.'</li>';
            }        
         }else{
              $output.= '<li id="kunde_0">Kein Kunde vorhanden</li>';
         }
          $output.='</ul><div id="vBar"><div id="vKnob"></div></div>';
         
         
         return $output;
       
   }
   
   public static function getKunde($KUNDENID){
        $stmt = OCP\DB::prepare( "SELECT name FROM *PREFIX*kunden_daten  WHERE id='".intval($KUNDENID)."' AND user='".OCP\USER::getUser()."' ");
        $result = $stmt->execute();
        $row = $result->fetchRow();
        
        if(is_array($row)) return $row;
                else return false;   
   }
    public static function getAjaxTemplate($template) {
    global $tpl_array, $baseurl;
    if ($tpl_array[$template] == '') {
        $tplstr = OC::$SERVERROOT."/apps/kunden/templates/" . $template;

    }
    $tplstr = OC::$SERVERROOT."/apps/kunden/templates/" . $template . ".htm";

    if (self::filecheck($tplstr)) {

    //$tplstr=PARSER_TPL($tplstr);

        $tpl_array[$template] = str_replace("\\", "\\\\", file_get_contents($tplstr));
        $tpl_array[$template] =  str_replace("\"", "\\\"", file_get_contents($tplstr));

    //  $tpl_array[$template] = $tplName . str_replace("\"", "\\\"", PARSER_TPL(file_get_contents($tplstr))) . $tplName;


    } else {
        echo '<b>Achtung:</b> Template Datei "<b>' . $tplstr . ' </b>" wurde nicht im Templatesordner gefunden! <br />';
    }
    return $tpl_array[$template];

}
    
  public static function generateSelectFieldArray($NAME,$WERT,$ARRAYDATA){
        

        if(isset($_POST[$NAME])){
           $aSelectValue[$NAME]=$_POST[$NAME];
        }


      $getArrayData=$ARRAYDATA;
      
      if(is_array($getArrayData)){
      $OUTPUT='<select name="'.$NAME.'" size="1">';
      foreach ($getArrayData as $KEY => $VALUE) {
            ($KEY == $WERT) ? ($selected = 'selected') : ($selected = '');
            $OUTPUT .= '<option value="' . $KEY . '" ' . $selected . '>' . $VALUE . '</option>';
        }
      $OUTPUT.='</select>';
      
       return $OUTPUT;
    }else return false;
    
  }
  
  public static function cutstring($str, $width=15) {
    // $str=replacehtmlentities($str);
    
    if ($width <= 0) $width = 15;
    $tmpLength=strlen($str);
    
    if ($tmpLength > $width){
        $str = substr($str, 0, $width)."...";
    }
    return $str;
    
}
    
private  static function filecheck($file) {
    if (!($fp = @ fopen($file, 'r'))) {
        return false;
    } else {
        fclose($fp);
        return true;
    }
}
  
  public static function getNoticeWVKunden(){
         $timenow = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
          $SQLMORE="timestamp > 0 AND timestamp='".$timenow."' ";
          
            $SQL="SELECT * FROM *PREFIX*kunden_notizdaten WHERE ".$SQLMORE." AND user='".OCP\USER::getUser()."' GROUP BY kunden_id ORDER BY id DESC";
                      $stmt = OCP\DB::prepare($SQL);
                        $result = $stmt->execute();
                
                        $infoDaten_cache = '';
                        while( $row = $result->fetchRow()){
                            $infoDaten_cache[$row['kunden_id']] = 1;
                            
                        } 
                if(is_array($infoDaten_cache)) return $infoDaten_cache;
                else return false;   
  }
  
  public static function getOutputNotizen($KUNDENID=0){
     
     
     $SQLMORE='';
     $SHOWLINK='';
     $SWITCHNAME='';
     if($KUNDENID>0){
         $SQLWHERE="kn.kunden_id='".$KUNDENID."' ";
         $SHOWLINK='<a href="javascript:newNotice(\''.$KUNDENID.'\');" class="button">+</a>';
         $SWITCHNAME='Wiedervorl.';
     }
      if($KUNDENID==0){
               $timenow = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
          $SQLWHERE="kn.timestamp > 0 AND kn.timestamp='".$timenow."' ";
          $SWITCHNAME='Kunde';
      }
      
      $SQL="SELECT kn.generate_time,kn.id,kn.kunden_id,kn.timestamp,kn.notice_flag, kn.text,kn.worker,kn.user,kd.name,cc.calendarcolor FROM *PREFIX*kunden_notizdaten kn  
                 LEFT JOIN  *PREFIX*kunden_daten kd ON kn.kunden_id=kd.id
                 LEFT JOIN *PREFIX*calendar_calendars cc ON kn.cal_id=cc.id
                  WHERE ".$SQLWHERE." AND kn.user='".OCP\USER::getUser()."'
                   ORDER BY id DESC";
                      $stmt = OCP\DB::prepare($SQL);
                        $result = $stmt->execute();
                
                        $infoDaten_cache = array();
                        while( $row = $result->fetchRow()){
                            $infoDaten_cache[] = $row;
                            
                        } 
                        
                        
       $output='<table cellpadding="0" cellspacing="0" align="center" width="98%" style="margin:0;margin-left:auto;margin-right:auto;">
                                            <tr>
                                                <td class="tbHeads" style="width:10%;">&nbsp;'.$SHOWLINK.' Erstellt</td>
                                                <td class="tbHeads" style="width:10%;">&nbsp;Ersteller</td>
                                                <td class="tbHeads" style="width:50%;" nowrap>&nbsp;Notiz</td>
                                                <td class="tbHeads" style="width:10%;">&nbsp;'.$SWITCHNAME.'</td>
                                                <td class="tbHeads" style="width:10%;">&nbsp;zugew. an</td>
                                                <td class="tbHeads" style="width:5%;">&nbsp;erledigt</td>
                                                </tr>' ;  
                                                
                     if(is_array($infoDaten_cache)){

                        $counter=0;
            
            
                        foreach($infoDaten_cache as $noticeInfo){
                            $checked='';
            
                            $sNoticeDate='';
                           
                             $sNoticeWVDate='';
                          
                            if($KUNDENID==0){
                                if($noticeInfo['generate_time']>0) $sNoticeDate=date("d.m.Y",$noticeInfo['generate_time']);
                                  $sNoticeWVDate='<a href="javascript:getKunden(\''.$noticeInfo['kunden_id'].'\');">'.OC_Kunden_Util::cutstring($noticeInfo['name'],15).'</a>';
                            }else{
                                 if($noticeInfo['generate_time']>0) $sNoticeDate='<a href="javascript:editNotice(\''.$noticeInfo['id'].'\',\''.$noticeInfo['kunden_id'].'\');">'.date("d.m.Y",$noticeInfo['generate_time']).'</a>';
                                if($noticeInfo['timestamp']>0) $sNoticeWVDate=date("d.m.Y",$noticeInfo['timestamp']);
                            }
                            $addStyle='';
                            if($noticeInfo['notice_flag']) {
                                $checked='checked';
                                $addStyle='text-decoration:line-through;';
                            }
            
                             $output.='<tr  id="tr_'.$noticeInfo['id'].'" style="background:'.$noticeInfo['calendarcolor'].';color:#313131;">
                                    <td class="tbRow" style="'.$addStyle.'">'.$sNoticeDate.'</td>
                                    <td class="tbRow" style="'.$addStyle.'">'.$noticeInfo['user'].'</td>
                                    <td class="tbRow" style="line-height:20px;'.$addStyle.'" nowrap>'.OC_Kunden_Util::cutstring(strip_tags($noticeInfo['text']),50).'</td>
                                    <td class="tbRow" style="'.$addStyle.'">'.$sNoticeWVDate.'</td>
                                    <td class="tbRow" style="'.$addStyle.'">'.$noticeInfo['worker'].'</td>
                                    <td class="tbRow"><input type="checkbox" onclick="checkNotice(\''.$noticeInfo['id'].'\');" id="chk_'.$noticeInfo['id'].'"  '.$checked.' /></td>
                                    </tr>';
            
                                $counter++;
                        }
                         $output.='</table>';
                    }else{
                        $output.='<tr><td class="tbRow" colspan="6">&raquo; Keine Notizen vorhanden</td></tr></table>';
                    }                              
                  
      return $output;
      
  }

public static function getWeekCalenderList(){
    
     
      $iTagAkt=(date("N",time())) -1;
      
           $iBackCalc=($iTagAkt*24*3600);
           $iForCalc=((6-$iTagAkt)*24*3600);
           
           
           $start=date('Y-m-d 00:00:00',(time()-$iBackCalc));
           $end=date('Y-m-d 23:59:59',(time()+$iForCalc));
   
   
         $SQL="SELECT c.id, c.userid,c.uri,c.calendarcolor,oc.startdate,oc.enddate,oc.summary,oc.repeating,oc.calendardata,oc.uri AS ICS
                    FROM *PREFIX*calendar_calendars c
                    LEFT JOIN *PREFIX*calendar_objects oc ON c.id = oc.calendarid
                    LEFT JOIN *PREFIX*share s ON c.id=s.item_source
                    WHERE (c.userid = '".OCP\USER::getUser()."' OR (s.share_with='".OCP\USER::getUser()."' AND s.item_type='calendar'))
                    AND oc.`objecttype` = 'VEVENT'
                    AND ((oc.startdate>='".$start."' AND oc.enddate<='".$end."' AND oc.repeating = '0') OR (oc.enddate>='".$start."' AND oc.startdate<='".$end."' AND oc.repeating = '0') OR (oc.startdate <= '".$end."' AND oc.repeating = '1')) 
                    ORDER BY oc.startdate ASC
                    ";
                    
                    $stmt = OCP\DB::prepare($SQL);
                    $result = $stmt->execute();
            
                    $infoDaten_cache = array();
                    while( $row = $result->fetchRow()){
                        $infoDaten_cache[] = $row;
                        
                    } 
                    $saveTermine='';
                     $tz=OC_Calendar_App::getTimezone();
                    if(is_array($infoDaten_cache)){
                        foreach($infoDaten_cache as $terminInfo){
                                $object = OC_VObject::parse($terminInfo['calendardata']);
                                 $vevent = $object->VEVENT;  
                                 $start_dt = new DateTime($vevent->DTSTART, new DateTimeZone($tz));
                                 $CALSTARTDATE=$start_dt->format('d.m.Y');
                                 $end_dt = new DateTime($vevent->DTEND, new DateTimeZone($tz));
                                 $CALENDDATE=$end_dt->format('d.m.Y');
                                $bWrite=true;
                                  $STARTDATETMP=date_parse($vevent->DTSTART);
                                  $STARTDATEMK=mktime(0,0,0,$STARTDATETMP['month'],$STARTDATETMP['day'],$STARTDATETMP['year']);  
                                  
                                   if(isset( $vevent->RRULE)){
                                    
                                       $temp=explode('UNTIL=',$vevent->RRULE);
                                      
                                       $until_dt = new DateTime( $temp[1], new DateTimeZone($tz));
                                        $CALUNTILDATE=$until_dt->format('Y-m-d H:i:s');
                                       if($CALUNTILDATE<$start) $bWrite=false;
                                   }
                                     
                                  if($bWrite)
                                  $saveTermine[]=array(
                                      'startdate'=>$CALSTARTDATE,
                                      'starthour'=>$start_dt->format('H:i'),
                                      'enddate'=>$CALENDDATE,
                                      'endhour'=>$end_dt->format('H:i'),
                                      'calcolor'=>$terminInfo['calendarcolor'],
                                      'summary'=>$terminInfo['summary'],
                                      'repeating'=>$terminInfo['repeating'],
                                      'starttimestamp'=>$STARTDATEMK
                                  );      
                                
                            
                        }
                    }
                    
                  $getWeekDayArray=array('0'=>'Montag','1'=>'Dienstag','2'=>'Mittwoch','3'=>'Donnerstag','4'=>'Freitag','5'=>'Samstag','6'=>'Sonntag');  
                 
                     
                     $OUTPUT['WEEK']='<ul id="weeks">';
                    
                    $firstDay=time()-$iBackCalc;
                    $iKalenderWoche=date("W",time()+(24*3600));
                    
                    for($i=0; $i<7; $i++){
                           
                          if($i==0) $calc=$firstDay;
                          else $calc=$firstDay+($i*24*3600);
                            
                            $datum=date("d.m.Y",$calc);
                            
                            if($iTagAkt==$i) $OUTPUT['WEEK'].='<li class="today">'.$getWeekDayArray[$i].', '.$datum;
                            else $OUTPUT['WEEK'].='<li>'.$getWeekDayArray[$i].', '.$datum;
                          
                           if(is_array($saveTermine)){
                           foreach($saveTermine as $termininfo){
                                //Termin nur an diesem Tag mit Stunden
                                $addStyle='';
                                if($termininfo['repeating']==0){
                                       if($datum==$termininfo['startdate']  && $datum==$termininfo['enddate']) {
                                           $hourBetween=$termininfo['starthour'].' - '.$termininfo['endhour'];
                                           if($termininfo['calcolor']!='') $addStyle='style="background:'.$termininfo['calcolor'].';"';
                                           $OUTPUT['WEEK'].='<span class="weekCalRow"><span class="calRound" '.$addStyle.'>&nbsp;</span><span style="width:120px;display:block;float:left;text-align:center;">'.$hourBetween.'</span> '.$termininfo['summary'].'</span>';
                                       }
                                       elseif($datum==$termininfo['startdate']  && $termininfo['enddate'] > $datum) {
                                           if($termininfo['calcolor']!='') $addStyle='style="background:'.$termininfo['calcolor'].';"';    
                                           $OUTPUT['WEEK'].='<span class="weekCalRow"><span class="calRound" '.$addStyle.'></span><span style="width:120px;display:block;float:left;text-align:center;">Ganzt&auml;gig</span> '.$termininfo['summary'].'</span>';
                                       }
                                       elseif($termininfo['startdate']<$datum && $termininfo['enddate']>$datum) {
                                            if($termininfo['calcolor']!='') $addStyle='style="background:'.$termininfo['calcolor'].';"';     
                                           $OUTPUT['WEEK'].='<span class="weekCalRow"><span class="calRound" '.$addStyle.'></span><span style="width:120px;display:block;float:left;text-align:center;">Ganzt&auml;gig</span> '.$termininfo['summary'].'</span>';
                                       }
                               }

                               if($termininfo['repeating']==1){
                                       //Samstag
                                    $iTagWeek=date("N",$termininfo['starttimestamp']);
                                    if(($i+1)==$iTagWeek){
                                           if($termininfo['calcolor']!='') $addStyle='style="background:'.$termininfo['calcolor'].';"';    
                                           $OUTPUT['WEEK'].='<span class="weekCalRow"><span class="calRound" '.$addStyle.'></span><span style="width:120px;display:block;float:left;text-align:center;">Ganzt&auml;gig</span> '.$termininfo['summary'].'</span>';
                                    }
                                  
                               }
                               
                           }
                           }
                       $OUTPUT['WEEK'].='</li>';
                    }
                     $OUTPUT['WEEK'].='</ul>';
                    
                    
        $output='<h3>Termine diese Woche KW: '.$iKalenderWoche.'</h3>'.$OUTPUT['WEEK'];
     
         
        //   if(is_array($infoDaten_cache)){
                   
             //  $output.=$OUTPUT['WEEK'];
           /*
            $tz=OC_Calendar_App::getTimezone();
           
             foreach($infoDaten_cache as $termininfo){
                           
                $object = OC_VObject::parse($termininfo['calendardata']);
                $vevent = $object->VEVENT;     
              //print $vevent->DTSTART.':'.$vevent->DTEND.'<br />';
                   
                $STARTDATETMP=date_parse($vevent->DTSTART);
             
                $STARTDATE=mktime(0,0,0,$STARTDATETMP['month'],$STARTDATETMP['day'],$STARTDATETMP['year']);
                
                $ENDDATETMP=date_parse($vevent->DTEND);
                $ENDDATE=mktime(0,0,0,$ENDDATETMP['month'],$ENDDATETMP['day'],$ENDDATETMP['year']);
                $DATE='';
                $GANZTAG=($ENDDATE-(24*3600));
                 $start_dt = new DateTime($vevent->DTSTART, new DateTimeZone($tz));
                 $end_dt = new DateTime($vevent->DTEND, new DateTimeZone($tz));
                 
                 if($STARTDATE==$ENDDATE){                     
                        
                         $DATE=$start_dt->format('D, d.m.Y H:i').' - '.$end_dt->format('H:i');
                 }
                 
                 if($STARTDATE==$GANZTAG){
                      $DATE='Ganzt&auml;gig: '.$start_dt->format('D, d.m.Y');
                 }
                $output.='<li>'.$DATE.' '.$termininfo['summary'].'<li />';
             }
             
              $output.='</ul>';*/
      //    }
         
        return $output;
        
}
  
  public static function getArrays($MODUS){
    $GetArray='';

        switch($MODUS){
           //Vorgang
            case 0:
               $GetArray= array (
                    '1'=>'Bericht',
                    '2'=>'Service-Auftrag',
                    '3'=>'Email',
                    '4'=>'Telefonat'
                 );
            break;

            //Prio
            case 1:
               $GetArray= array (
                    '1'=>'niedrig',
                    '2'=>'mittel',
                    '3'=>'hoch'
                 );
            break;
            //Kundenstatus
            case 2:
               $GetArray= array (
                    '1'=>'aktiv',
                    '0'=>'inaktiv',
                    '2'=>'aquise'
                 );
            break;

            case 3:
               $GetArray= array (
                    '2'=>'C-Kunde',
                    '0'=>'A-Kunde',
                    '1'=>'B-Kunde'
                 );
            break;
            case 4:
               $GetArray= array (
                    'aktiv'=>'1',
                    'inaktiv'=>'0',
                    'aquise'=>'2'
                 );
            break;

            case 5:
               $GetArray= array (
                    'C-Kunde'=>'2',
                    'A-Kunde'=>'0',
                    'B-Kunde'=>'1'
                 );
            break;
            
             case 6:
               $GetArray= array (
                    '0'=>'privat',
                    '1'=>'alle'
                 );
            break;
            
            case 7:
                      
                      $SQL="SELECT id,uri,displayname,calendarcolor FROM *PREFIX*calendar_calendars WHERE userid='".OCP\USER::getUser()."'  ORDER BY displayname ASC";
                      $stmt = OCP\DB::prepare($SQL);
                        $result = $stmt->execute();
                
                        $GetArray = '';
                        while( $row = $result->fetchRow()){
                            $GetArray[$row['id']."_".$row['uri']] = $row['displayname'];                            
                        } 
                
            break;
            case 8:
                      
                      $SQL="SELECT id,displayname FROM *PREFIX*contacts_addressbooks WHERE userid='".OCP\USER::getUser()."'  ORDER BY displayname ASC";
                      $stmt = OCP\DB::prepare($SQL);
                        $result = $stmt->execute();
                
                        $GetArray = '';
                        while( $row = $result->fetchRow()){
                            $GetArray[$row['id']] = $row['displayname'];                   
                        } 
                
            break;
            case 10:
               $GetArray= array (
                    '0'=>'Benutzer',
                    '1'=>'Admin'
                 );
            break;
            
            case 11:
               $GetArray= array (
                    'Benutzer'=>'0',
                    'Admin'=>'1'
                 );
            break;
        }

        if(is_array($GetArray)){
           return $GetArray;
        }else return false;
}
    
    
}
