<?php
/*
 * Created on 2014-8-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
    
class PageImage{
    private $pageid;
    private $imageUrl;
    private $imageRedirectUrl;
    
    private $title;
    private $width;
    private $height;
    
    function __construct($title){
        this->$title;
    }
    
    //===========================================================
    
    function setFromJSON($json){
        $jsonArray = json_decode($json, true);
        foreach($jsonArray as $key=>$value){
            $this->$key = $value;
        }
    }
    
    //===========================================================
    
    function getPageId(){
        return this->$pageid;
    }
    
    function getImageTitle(){
        return this->$title;
    }
    
    function getImageWidth(){
        return this->$width;
    }
    
    function getImageHeight(){
        return this->$height;
    }
    
    function getImageUrl(){
        return this->$imageUrl;
    }
    
    function getImageRedirectUrl(){
        return this->$imageRedirectUrl;
    }
    
    //===========================================================
    
    function setPageId($data){
        this->$pageid => $data;
    }
    
    function setImageWidth($data){
        this->$width => $data;
    }
    
    function getImageHeight($data){
        this->$height => $data;
    }
    
    function getImageUrl($data){
        this->$imageUrl => $data;
    }
    
    function getImageRedirectUrl($data){
        this->$imageRedirectUrl => $data;
    }
    
    public function toHtml(){
        
    }
}
?>
