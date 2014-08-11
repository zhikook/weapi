<?php
/*
 * Created on 2014-8-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class WikiPage{
     
     private $pageId;
     private $pageNS;
     private $title;
     private $pagelanguage;
     private $touchedTime;
     private $readCount;
     
     private $imageArr;    
     private $thumbUrl;
          
     public function __construct($id){
         this.$pageId = $id;
     }
     
     //===========================================================
     
     /**
      * setFromJSON()
      * $json : json索引对象
      * 
      * return :
      */
     function setFromJSON($json){
         $jsonArray = json_decode($json, true);
         foreach($jsonArray as $key=>$value){
             $this->$key = $value;
         }
     }
     
     /**
      * setFromArray()
      * $array
      */
     function setFromArray($array){
         foreach($array as $key=>$value){
             $this->$key = $value;
         }
     }

     //===========================================================
     
     function setPageId($data){
         $pageId = $data;
     }
     
     function getPageNS($data){
         $pageNS = $data;
     }
     
     function setPageTouchedTime($data){
         $touchedTime = $data;
     }
     
     function setPageReadCount($data){
         $readCount = $data;
     }
     
     function setPageThumbUrl($data){
         $thumbUrl = $data;
     }
     
     function setImageArr($data){
         $imageArr = $data;
     }
     
     //===========================================================
     
     function getPageTouchedTime(){
         if($touchedTime){
             return this->$touchedTime;
         }
     }
     
     function getThumbUrl(){
         return this->$thumbUrl;
     }
     
     
     function getPageId(){
         return this->$pageId;
     }
     
     function getPageNS(){
         if($pageNS){
             return this->$pageNS;
         }
     }
     
     function getPageTouchedTime(){
         if($touchedTime){
             return this->$touchedTime;
         }
     }
     
     function getPageImages($title){
     	if(this->$imageArr){
     		this->setImageArr();
     	}
     	
        return this->$imageArr[$title];
     }
     
     //=========================================================
     
 }

class PageExtract{
	private $pageid;
    private $title;
    private $extract;  
       
    function __construct($id,$t) {
        this->$pageId = $id;
        this->$title = $t;
    }
        
    public function setExetract($txt){
        this->$extract = $txt;
    }
        
    public function getExetract(){
        return this->$extract;
    }        
}
?>
