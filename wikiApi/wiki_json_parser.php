<?php
/*
 * Created on 2014-8-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class WikiJsonParser{
     
     private static $wikiRoot = '';
     private static $apiUrl = '';
     private static $default_format = 'json';
     
     public function __construct(){
         $this->wikiRoot = 'http://10.171.26.240/wiki';
         $this->apiUrl = $this->wikiRoot.'/api.php?'.$this->default_format;
     }
         
     /**
      *
      */
     private function execute($action,$prop,$list,$iip){
         if($action){
             $fullUrl = $this->apiUrl.$action;
         }else if($prop){
             $fullUrl = $this->apiUrl.parseVars($prop);
         }else if($list){
             $fullUrl = $this->apiUrl.parseVars($list);
         }else if($meta){
             $fullUrl = $this->apiUrl.parseVars($meta);
         }
         
         if(!$jsonRepo){
             $jsonRepo =  file_get_contents($fullUrl);
             $jsonRepo->setHeader('.........');
         }
         
         return $jsonRepo;
     } 
     
     
     function parseVars($reqVars) {
         $reqStr = '';
         foreach($reqVars as $key=>$var) {
             $reqStr .= '&'.$key.'='.$var;
         }
         return $reqStr;
     }
 }
?>
