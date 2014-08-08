<?php
/*
 * Created on 2014-8-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class WikiUser{
     
     private  $userName;
     private $userId;
     private $userPassword;
     private $userToken;
     private $sessionid;
     
     $sessionid;
     
     //构造函数
     function __construct($userName,$userToken){
         //
         return this;
     }
     
     
     
     /**
      * 登陆
      **/
     public function login(){
         
     }
     
     /**
      * 检查登陆
      */
     public function checkToken(){
         
     }
     
     /**
      * 创建用户
      */
     private function createUser(){
         
     }
     
     //=========================================================
     
     function getUserName(){
         return $userName;
     }
    
     function getUserId(){
         return $userId;
     }
     
     function getUserToken(){
         return $userToken;
     }
     
     function setUserName($data){
         this->$userName => $data ;
     }
     
     function setUserId($data){
         this->$userId => $data ;
     }
     
     function setUserToken($data){
         this->$userToken => $data ;
     }
     
     /**
      * 设置user的属性值。
      */
     function setFromJSON($json){
         $jsonArray = json_decode($json, true);
         foreach($jsonArray as $key=>$value){
             $this->$key = $value;
         }
     }
     
     /**
      * 设置user的属性值。
      */
     function setFromArray($arr){         
         foreach($jsonArray as $key=>$value){
             $this->$key = $value;
         }
     }
     
         
?>
