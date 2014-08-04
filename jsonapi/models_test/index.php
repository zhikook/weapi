<?php
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

	public function get_focus_wiki() {
		$this->set_prefix(AWS_APP::config()->get('database')->wikiprefix);//设置为wiki的表前缀
		$sql = "select t1.il_from,
		t1.il_to,
		t2.page_title,
		(SELECT page_id FROM ".$this->get_table('page')." WHERE page_title=t1.il_to) AS img_page_id 
		from ".$this->get_table('imagelinks'). " t1 
		left join ".$this->get_table('page'). " t2 
		on t1.il_from = t2.page_id 
		group by t1.il_to order by t1.il_from limit 0,6";
		$posts_index = $this->query_all($sql);
		$this->set_prefix('');
		
		require ROOT_PATH.'jsonApi.php';
		$imgWiki = new jsonApi();
		$reqArr = array();
		$reqArr['action'] = 'query';
		$reqArr['prop'] = 'imageinfo';
		$reqArr['iiprop'] = 'timestamp|user|url';

		foreach ($posts_index as $key=>$posts) {
			$reqArr['titles'] = 'Image:'.$posts['il_to'];
			$imgurl = $imgWiki->getImg($reqArr, $posts['img_page_id']);
			$posts_index[$key]['img_url'] = $imgurl;
		}
		return $posts_index;
	}
}
?>