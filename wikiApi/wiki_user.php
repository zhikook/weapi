<?php
/*
 * Created on 2014-8-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class WikiUser{
     
     private $userName;
     private $userId;
     private $userPassword;
     private $userToken;
     private $sessionid;
     
     private $sessionid;
     
     //��������芥��
     function __construct($userName,$userToken){
         //
         return this;
     }
     
     //===============================================================
     
     /**
      * 璁剧疆user���灞���у�笺��
      */
     function setFromJSON($json){
         $jsonArray = json_decode($json, true);
         foreach($jsonArray as $key=>$value){
             $this->$key = $value;
         }
     }
     
     /**
      * 璁剧疆user���灞���у�笺��
      */
     function setFromArray($arr){         
         foreach($jsonArray as $key=>$value){
             $this->$key = $value;
         }
     }
     
     //===============================================================
     
     
     /**
      * ��婚��
      **/
     public function login(){
         
     }
     
     /**
      * 妫���ョ�婚��
      */
     public function checkToken(){
         
     }
     
     /**
      * ���寤虹�ㄦ��
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
         this->$userName = $data ;
     }
     
     function setUserId($data){
         this->$userId = $data ;
     }
     
     function setUserToken($data){
         this->$userToken  = $data ;
     }
     
     
 }       
?>
