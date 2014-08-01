<?php

require __DIR__ . '/wiki/includes/WebStart.php';
require __DIR__ . '/wiki/includes/weapi/api.php';
require __DIR__ . '/wiki/includes/weapi/wikifile.php';
require __DIR__ . '/wiki/includes/weapi/template_api.php';

/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2013 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|   
+---------------------------------------------------------------------------
*/


if (!defined('IN_ANWSION'))
{
	die;
}

class index_class extends AWS_MODEL
{
	public function set_posts_index($post_id, $post_type, $data = null)
	{
		if (!$data)
		{
			switch ($post_type)
			{
				case 'question':
					$result = $this->fetch_row('question', 'question_id = ' . intval($post_id));
				break;
				
				case 'article':
					$result = $this->fetch_row('article', 'id = ' . intval($post_id));
				break;
			}
			
			if (!$result)
			{
				return false;	
			}
		}
		else
		{
			$result = $data;
		}
			
		switch ($post_type)
		{
			case 'question':
				$data = array(
					'add_time' => $result['add_time'],
					'update_time' => $result['update_time'],
					'category_id' => $result['category_id'],
					'is_recommend' => $result['is_recommend'],
					'view_count' => $result['view_count'],
					'anonymous' => $result['anonymous'],
					'popular_value' => $result['popular_value'],
					'uid' => $result['published_uid'],
					'lock' => $result['lock'],
					'agree_count' => $result['agree_count'],
					'answer_count' => $result['answer_count']
				);
			break;
			
			case 'article':
				$data = array(
					'add_time' => $result['add_time'],
					'update_time' => $result['add_time'],
					'category_id' => $result['category_id'],
					'view_count' => $result['views'],
					'anonymous' => 0,
					'uid' => $result['uid'],
					'agree_count' => $result['votes'],
					'answer_count' => $result['comments'],
					'lock' => $result['lock'],
					'is_recommend' => $result['is_recommend'],
				);
			break;
		}
		
		if ($posts_index = $this->fetch_all('posts_index', "post_id = " . intval($post_id) . " AND post_type = '" . $this->quote($post_type) . "'"))
		{
			$post_index = end($posts_index);
			
			$this->update('posts_index', $data, 'id = ' . intval($post_index['id']));
			
			if (sizeof($posts_index) > 1)
			{
				$this->delete('posts_index', "post_id = " . intval($post_id) . " AND post_type = '" . $this->quote($post_type) . "' AND id != " . intval($post_index['id']));
			}
		}
		else
		{
			$data = array_merge($data, array(
				'post_id' => intval($post_id),
				'post_type' => $post_type
			));
			
			$this->remove_posts_index($post_id, $post_type);
			
			$this->insert('posts_index', $data);
		}
	}
	
	public function remove_posts_index($post_id, $post_type)
	{
		return $this->delete('posts_index', "post_id = " . intval($post_id) . " AND post_type = '" . $this->quote($post_type) . "'");
	}
	
	/*
	 * 
	 */
	public function get_hot_wiki($page, $per_page) {
	
		$this->set_prefix(AWS_APP::config()->get('database')->wikiprefix);//设置为wiki的表前缀
		$sql = "select t1.page_id,t1.page_title,t1.page_counter,date_format(t1.page_touched,'%Y-%m-%d') as page_touched,t2.rc_user,(select count(rc_id) as comments from ".$this->get_table('recentchanges'). " where rc_title = t1.page_title and rc_namespace =1) as comments from ".$this->get_table('page'). " t1 left join ".$this->get_table('recentchanges'). " t2 on t1.page_id = t2.rc_cur_id where t1.page_namespace= 0 and t2.rc_new=1 and t2.rc_user <>0 order by t1.page_id desc limit 0,10";
		$posts_index = $this->query_all($sql);
		
		$this->set_prefix('');
		
		foreach ($posts_index as $key => $data) {
			$posts_index[$key]['user_info'] = $this->model('account')->get_user_info_by_uid($data['rc_user']);
		}
		
		return $posts_index;
		
	}
	
	/**
	 * 
	 */
	public function get_hot_question($page, $per_page) {
	
		$sql = "select t1.question_id,t1.add_time,t1.question_content,t1.question_detail,t1.published_uid,t1.last_answer,t1.agree_count,t1.against_count,t2.user_name as answer_user_name from ".$this->get_table('question')." t1 left join ".$this->get_table('users')." t2 on t1.last_answer = t2.uid order by t1.question_id desc limit 0,10";
		$posts_index = $this->query_all($sql);
		
		$sql = "select count(question_id) as count from ".$this->get_table('question')." order by question_id desc limit 0,10";
		$_found_rows = $this->query_row($sql);
		$this->posts_list_total = $_found_rows['count'];
		
		foreach ($posts_index as $key => $data) {
			$posts_index[$key]['user_info'] = $this->model('account')->get_user_info_by_uid($data['published_uid']);
		}
		
		return $posts_index; 
		
	}
	
	/***
	 * 
	 */
	public function get_focus_wiki() {
		$this->set_prefix(AWS_APP::config()->get('database')->wikiprefix);//设置为wiki的表前缀
		$sql = "select t1.il_from,
		t1.il_to,
		t2.page_title,
		t3.img_metadata,
		t3.img_sha1 
		from ".$this->get_table('imagelinks'). " t1 
		left join ".$this->get_table('page'). " t2 
		on t1.il_from = t2.page_id 
		left join ".$this->get_table('image'). " t3 
		on t1.il_to = t3.img_name 
		group by t1.il_to order by t1.il_from limit 0,6";
		$posts_index = $this->query_all($sql);
		$this->set_prefix('');

		return $posts_index;
	}
	
	public function get_Test_Image($id){
		$id = 15;
		$myImageRequest = ImageWikiRequest::newImageRequest( $id );
		if($myImageRequest->getDisplayedFile()){
			$mythumbnail = $myImageRequest->transformImage();
			return $mythumbnail->toHtml();
		}else{
			return false;
		}
	}
	
	public function get_posts_list_total()
	{
		return $this->posts_list_total;
	}
	
	public function get_posts_list_by_topic_ids($post_type, $topic_type, $topic_ids, $category_id = null, $answer_count = null, $order_by = 'post_id DESC', $is_recommend = false, $page = 1, $per_page = 10)
	{
		if (!is_array($topic_ids))
		{
			return false;
		}
		
		array_walk_recursive($topic_ids, 'intval_string');
		
		$result_cache_key = 'posts_list_by_topic_ids_' . implode('_', $topic_ids) . '_' . md5($answer_count . $category_id . $order_by . $is_recommend . $page . $per_page . $post_type . $topic_type);
		
		$found_rows_cache_key = 'posts_list_by_topic_ids_found_rows_' . implode('_', $topic_ids) . '_' . md5($answer_count . $category_id . $is_recommend . $per_page . $post_type . $topic_type);
			
		$where[] = 'topic_relation.topic_id IN(' . implode(',', $topic_ids) . ')';
			
		if ($answer_count !== null)
		{
			$where[] = "posts_index.answer_count = " . intval($answer_count);
		}
		
		if ($is_recommend)
		{
			$where[] = 'posts_index.is_recommend = 1';
		}
				
		if ($category_id)
		{
			$where[] = 'posts_index.category_id IN(' . implode(',', $this->model('system')->get_category_with_child_ids('question', $category_id)) . ')';
		}
		
		$on_query[] = 'posts_index.post_id = topic_relation.item_id';
		
		if ($post_type)
		{
			$on_query[] = "posts_index.post_type = '" . $this->quote($post_type) . "'";
		}
		
		if ($topic_type)
		{
			$on_query[] = "topic_relation.type = '" . $this->quote($topic_type) . "'";
		}
		
		if (!$found_rows = AWS_APP::cache()->get($found_rows_cache_key))
		{
			$_found_rows = $this->query_row('SELECT COUNT(DISTINCT posts_index.post_id) AS count FROM ' . $this->get_table('posts_index') . ' AS posts_index LEFT JOIN ' . $this->get_table('topic_relation') . " AS topic_relation ON " . implode(' AND ', $on_query) . " WHERE " . implode(' AND ', $where));
			
			$found_rows = $_found_rows['count'];
			
			AWS_APP::cache()->set($found_rows_cache_key, $found_rows, get_setting('cache_level_high'));
		}
		
		$this->posts_list_total = $found_rows;
		
		if (!$result = AWS_APP::cache()->get($result_cache_key))
		{
			$result = $this->query_all('SELECT posts_index.* FROM ' . $this->get_table('posts_index') . ' AS posts_index LEFT JOIN ' . $this->get_table('topic_relation') . " AS topic_relation ON " . implode(' AND ', $on_query) . " WHERE " . implode(' AND ', $where) . ' GROUP BY posts_index.post_id ORDER BY posts_index.' . $order_by, calc_page_limit($page, $per_page));
			
			AWS_APP::cache()->set($result_cache_key, $result, get_setting('cache_level_high'));
		}
		
		return $result;
	}
}

/**
 * 这是一个Hello World简单插件的实现
 *
 * @link    http://www.jb51.net/
 */
/**
 *需要注意的几个默认规则：
 *  1. 本插件类的文件名必须是action
 *  2. 插件类的名称必须是{插件名_actions}
 */
class DEMO_actions
{
  //解析函数的参数是pluginManager的引用
  function __construct(&$pluginManager)
  {
    //注册这个插件
    //第一个参数是钩子的名称
    //第二个参数是pluginManager的引用
    //第三个是插件所执行的方法
    $pluginManager->register('demo', $this, 'say_hello');
  }
 
  function say_hello()
  {
    echo 'Hello World';
  }
}
?>