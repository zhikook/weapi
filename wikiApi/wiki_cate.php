<?php
/*
 * Created on 2014-8-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
    
class Category{
    private $cateId;
    private $cateName; 
    
    //===========================================================
    
    function setFromJSON($json){
        $jsonArray = json_decode($json, true);
        foreach($jsonArray as $key=>$value){
            $this->$key = $value;
        }
    }
    
    //===========================================================
    
    function getCateId(){
        return this->$cateId;
    }
    
    function getCateName(){
        return this->$cateName;
    }
    
    //===========================================================
    
    function setCateId(){
        this->$cateId => $data;
    }
    
    function setCateName(){
        this->$cateName => $data;
    }
    
    
}
    
    
?>
