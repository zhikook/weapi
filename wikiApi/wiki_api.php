<?php

require_once __DIR__ . '/JsonMapper.php';
require_once __DIR__ . '/wiki_cate.php'; 
require_once __DIR__ . '/wiki_page.php';  
require_once __DIR__ . '/wiki_user.php'; 
    
/*
 * Created on 2014-8-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class WikiApi{
     private $jsonParser ;
     private $mapper ；
     
     public function __construct($user){
        $mapper = new JsonMapper();
     }
     
     /**
      * User
      **/  
     function login($user){
         $action = "login";
         $login_vars['lgname'] = $user.getUserName();
         
         if($psw){
             $login_vars['lgpassword'] = $psw;
         }else{
             $login_vars['lgtoken'] = $user.getUserToken();
         }
         $jsonResult = $jsonParser->execute($action,$login_vars);
         return $jsonResult; 
     }
     
     function unlogin($user){
         $action = "unlogin";
         $login_vars['lgname'] = $user.getUserName();  
         $jsonResult= $jsonParser->execute($action,$login_vars)
         return $jsonResult;  
     }
     
     function checkToken($user){
         
     }
     
     function getUserList($limit){
         
         $users;
         $jsonData;
         
         $action = 'query';
         
         if($limit){
            $jsonData  = $jsonParser->execute($action,$limit);
         }else{
            $jsonData = $jsonParser->execute($action);
         }
         
         //map to array object
         $users=$mapper->mapArray($jsonData,new ArrayObject(),'WikiUser');
         
         return $userArray;
         
     }
     
     //======================================================================
     
     /**
      * PageList
      **/
     function getPageList($limit){
         $pages;
         $jsonData = $jsonParser->execute($action,$limit);
         $pages=$mapper->mapArray($jsonData,new ArrayObject(),'WikiPage');

         return $pages;
     }
     
     
     /**
      * Page
      **/
     function getPage($title){
         $page = new WikiPage($title);
         $jsonData = $jsonParser->execute($action,$title);
         $page = $mapper->map($json, new WikiPage());
         return $page;
     }
     
     /**
      * Images
      **/
     function getImageInfo($title){
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
