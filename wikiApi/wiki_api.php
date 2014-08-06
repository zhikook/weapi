<?php
/*
 * Created on 2014-8-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class WikiApi{
     
     private static $userArray;
     
     private $jsonParser ;
     
     public function __construct(){
         
     }
          
     /**
      * User
      **/
     
     function login($user){
         
         return $token; 
     }
     
     function unlogin($user){
         
         return true;  
     }
     
     function checkToken(){
         
         return true;
     }
     
     function getUserList(){
         
         return $userArray;
     }
     
     
     /**
      * Page
      **/
     function getPage(){
         $jsonData = JsonResponse->getResultOfPage($title);
         
         $wikipage = new WikiPage(title);
         $wikipage->setPageId($jsonData[id]);
         $wikipage->setPageId($jsonData[id]);
         $wikipage->setPageId($jsonData[id]);
         $wikipage->setPageId($jsonData[id]);
         
         return $page;
     }
     
     function getPageList(){
         return $pages;
     }
     
     /**
      * Images
      **/
     function getImageInfo(){
         //add url...
         
         return $image;
     }
     
     function getImageList(){
         return $imagelist;
     }
     
     /**
      * Upload File
      */
     function uploadFile($file){
         
     }
 	
 }
?>
