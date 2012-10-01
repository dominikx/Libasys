
<div id="controls">

<a href="javascript:showKundenList();" id="showKundenList" style="float:left;padding:3px 6px;" class="button isOpen">-</a>
<form id="newKunde" name="newKunde" method="post" action=" ">
<input type="hidden" name="ajaxaction" value="3" />	
<input type="text" name="newCustomer" id="newCustomer" placeholder="Neuer Kunde" class="" />
<a href="javascript:newKunde();" class="button">anlegen</a>
</form>

</div>
<div id="leftcontent" class="leftcontent">

<?php





if(isset($_['kunden_id'])) {
  
    $kundenId=intval( $_['kunden_id']);
  
    
     $SQL="SELECT COUNT(id) AS COUNTNOTICE FROM *PREFIX*kunden_notizdaten WHERE kunden_id='".$kundenId."'";
          $stmt = OCP\DB::prepare($SQL);
          $result = $stmt->execute();
          $countInfo = $result->fetchRow();
          $COUNTNOTICE='';
          if($countInfo['COUNTNOTICE']>0) $COUNTNOTICE='<sup class="redNotice" title="Notizen">'.$countInfo['COUNTNOTICE'].'</sup>';
          
    $SQL="SELECT * FROM *PREFIX*kunden_daten  WHERE id='".$kundenId."' ";
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
         $moreInfoAnsprechpartner.='<span class="labelLeft">Geburtsdatum:</span> <label class="editField" name="sKundenGeb" title="Geburtstag">'.$mainInfo['geburtsdatum'].'</label><br />';
         
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
        
        $output['kunde_detail']='';
        
        $isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
        $isiPhone = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPhone');
        $defaultWidth='44';
        if($isiPad || $isiPhone){
             $defaultWidth='96';
        }
        
       eval ("\$output['kunde_detail'].= \"" . OC_Kunden_Util::getAjaxTemplate("body_kunden_detail", "") . "\";");
        
}else{
            
        $outputNotice=OC_Kunden_Util::getOutputNotizen();
        $outputTermine='';
       if(OCP\App::checkAppEnabled('calendar')) $outputTermine=OC_Kunden_Util::getWeekCalenderList();
     
    
        $output['kunde_detail']='<ul id="myScrollerKunden"><li><br /><div class="kundenBody"><a href="javascript:;" style="float:left;font-size:0.1px;"  id="showNotice">+</a> <h3>Aufgaben mit Wiedervorlage Heute</h3><br /><div id="iNotiz">'.$outputNotice.'</div></div><br class="clearing" /><br /><div class="kundenBody">'.$outputTermine.'</div><br /><br /></li></ul>';

   
}
    $kundenId=0;
    if(isset($_['kunden_id'])) $kundenId= $_['kunden_id'];
    $OUTPUT['LIST']=OC_Kunden_Util::getKundenListe($kundenId);

     print $OUTPUT['LIST'];
      

?>
</div>
<div id="rightcontent" class="rightcontent">
 <?php print $output['kunde_detail']; ?>
 
 <div id="vBarRight"><div id="vKnobRight"></div></div>
</div>
