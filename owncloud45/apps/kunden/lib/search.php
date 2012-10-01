<?php
class OC_Search_Provider_Kunden extends OC_Search_Provider{
    function search($query){
            
            
        $kundenArray = OC_Kunden::getKunden();
        
        
        if(count($kundenArray)==0 || !OCP\App::isEnabled('kunden')) {
            return array();
        }
        $results=array();
      
        foreach($kundenArray as $kundenInfo){
  
                if(substr_count(strtolower($kundenInfo['name']), strtolower($query)) > 0) {
                    $link = OCP\Util::linkTo('kunden', 'index.php').'&kundenId='.urlencode($kundenInfo['id']);
                    $results[]=new OC_Search_Result($kundenInfo['name'], '', $link, 'Kunden ');//$name,$text,$link,$type
                }
           
        }
        return $results;
    }
}

class OC_Kunden{
    
    
    
   function getKunden(){
       
       $stmt = OCP\DB::prepare( "SELECT id,name FROM *PREFIX*kunden_daten WHERE user='".OCP\USER::getUser()."'   ORDER BY name ASC");
        $result = $stmt->execute();

        $kundenInfo = array();
        while( $row = $result->fetchRow()){
            $kundenInfo[] = $row;
            
        }
        
        return $kundenInfo;
       
   }
    
    
}
