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
         $action = "logout";
         $login_vars['lgname'] = $user.getUserName();  
         $jsonResult= $jsonParser->execute($action,$login_vars)
         return $jsonResult;  
     }
     
     function checkToken($user){
         $action = "login";
         $login_vars['lgname'] = $user.getUserName();
         
         if($psw){
             $login_vars['lgpassword'] = $psw;
         }else{
             $login_vars['lgtoken'] = $user.getUserToken();
         }
         $jsonResult = $jsonParser->execute($action,$login_vars);
         
         foreach($jsonResult as $key=>$value){
             $jsonResult['result'] => $value;
             $jsonResult['lguserid']  => $value;
             $jsonResult['lgusername'] => $value;
             $jsonResult['lgtoken']  =>$value;
             
             $jsonResult['cookieprefix']  => $value;
             $jsonResult['sessionid'] => $value;
         }
    
         $user->setFromArray($jsonResult);
         
         if($jsonResult['result']=="success"){
             return true;
         }else{
             return false;
         }
         
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
     
     /**
      * 创建用户
      */
     function createAccount($user) {
         $action = "createaccount";
         $jsonData;
         
         $login_vars['lgname'] = $user.getUserName();
         $login_vars['email'] = $user.getUserEmail();
         $login_vars['realname'] = $user.getUserRealName();
         
         $jsonData  = $jsonParser->execute($action,$limit);

         foreach($result as key : value){
             $result['token']= $value;
             $result['userid']= $value;
             $result['username']= $value;
             $result['result']= $value;
         }
         if($result['result']){
             $user->setUserToken($result['token']);
             $user->setUserId($result['userid']);
             return true;
         }else{
             return false;
         }
     
     }
     //======================================================================
     
     /**
      * Page
      **/
     function getPage($titles,$prop){
         $action = "query";
         $jsonData;
         
         if($titles){
             $jsonData = $jsonParser->execute($action,$titles);
             if($prop){
                $prop ="info";
                $jsonData = $jsonParser->execute($action,$titles,$prop);     
             }
         }
         
         if($jsonData){
             if(is_array($jsonData){
                $pages = $mapper->map($jsonData,,new ArrayObject(),'WikiPage');
                return $pages;
             }else{
                $page = $mapper->map($json, new WikiPage());
                return $page;
             }
         }else{
             return false;
         }
     }
     
     /**
      * PageList
      **/
     function getPageList($limit){
         $action = "query"
         $pages;
                
         $jsonData = $jsonParser->execute($action,$limit);
         if($jsonData){
                $pages=$mapper->mapArray($jsonData,new ArrayObject(),'WikiPage');
         }
         return $pages;
     }
     
    /**
     * getPagesExtracts()
     * $exchars:截取字数
     * return: pages数组
     */
                
     function getPagesExtracts($exchars,$titles){
        $action = "query";
        $prop = "extracts"
        $extracts;
        $jsonData = $jsonParser->execute($action,$exchars,$titles,$prop);    
        if($jsonData){
            $extracts=$mapper->mapArray($jsonData,new ArrayObject(),'WikiPage');
        }
        
        return $pages;￼
                
     }
        
    //=================================================================================
                
     /**
      * Images
      **/
     function setImages($pages,$titles){
         $action = "query";
         $prop = "imageinfo";
         $images;
         $jsonData = $jsonParser->execute($action,$titles,$prop);
         if($jsonData){
            $result=$mapper->mapArray($jsonData,new ArrayObject(),'WikiPage');
         }
                
         $result->pageid;
         $pages[]->setImages($result->$images);
         
         return $pages;
     }
    
     
    function setThumbnail($pages){
        $thumbnail;
        
        $images = $pages->getPageImage();
        
        $action = "query";
        $prop = "pageimages";
        $titles = $pages->getTitle();
                
        $jsonData = $jsonParser->execute($action,$titles,$prop);
        if($jsonData){
            $thumbnail=$mapper->mapArray($jsonData,new ArrayObject(),'Thumbnail');
        }

        $image->setThumbnail();  
    }
     /**
      * Upload File
      */
     function uploadFile($file){
        return false;
     }
 	
}
?>
