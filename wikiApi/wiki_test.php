<?php
require_once __DIR__ . '/wiki_api.php';     
    
/*
 * Created on 2014-8-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
    
    //加载相关对象
    global $wikiApi;
    global $output;
    
    $wikiApi ＝ new wikiApi();
    
    //=================================================================
    public function get_page($title){
        
        $page = $wikiApi->getPage($title);      
        
    }
    
    public function get_recent_pages($limit){
        
        $page = $wikiApi->getPageList($limit);      
        
    }
    
    public function get_recent_pages($cates,$limit){
        
        $page = $wikiApi->getPageList($cates,$limit);      
        
    }
    
    public function get_page_image($title){
        $pageimage = $wikiApi->getPageImage($title); 
    }
    
    public function get_page_thumb($title){
        $pageimage = $wikiApi->getPageImage($title);         
    }
    
    //=================================================================
    
    public function get_user_list(){
        $userlist = $wikiApi->getUserList();
    }

 ?>