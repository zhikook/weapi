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

class main extends AWS_CONTROLLER
{
	public function get_access_rule()
	{
		$rule_action['rule_type'] = "white"; //'black'黑名单,黑名单中的检查  'white'白名单,白名单以外的检查
		
		if ($this->user_info['permission']['visit_explore'] AND $this->user_info['permission']['visit_site'])
		{
			$rule_action['actions'][] = 'index';
		}
		
		return $rule_action;
	}
	
	public function index_action()
	{
		if (is_mobile() AND HTTP::get_cookie('_ignore_ua_check') != 'TRUE')
		{
			HTTP::redirect('/m/explore/' . $_GET['id']);
		}

		if ($this->user_id)
		{
			$this->crumb(AWS_APP::lang()->_t('方法'), '/wiki');
		}
		
		if ($_GET['category'])
		{
			if (is_numeric($_GET['category']))
			{
				$category_info = $this->model('system')->get_category_info($_GET['category']);
			}
			else
			{
				$category_info = $this->model('system')->get_category_info_by_url_token($_GET['category']);
			}
		}
		
		// 导航
		if (TPL::is_output('block/content_nav_menu.tpl.htm', 'wiki/index'))
		{
			TPL::assign('content_nav_menu', $this->model('menu')->get_nav_menu_list('wiki'));
		}
		
		//边栏可能感兴趣的人
		if (TPL::is_output('block/sidebar_recommend_users_topics.tpl.htm', 'wiki/index'))
		{
			TPL::assign('sidebar_recommend_users_topics', $this->model('module')->recommend_users_topics($this->user_id));
		}
		
		//边栏热门用户
		if (TPL::is_output('block/sidebar_hot_users.tpl.htm', 'wiki/index'))
		{
			TPL::assign('sidebar_hot_users', $this->model('module')->sidebar_hot_users($this->user_id, 5));
		}
		
		//边栏热门话题
		if (TPL::is_output('block/sidebar_hot_topics.tpl.htm', 'wiki/index'))
		{
			TPL::assign('sidebar_hot_topics', $this->model('module')->sidebar_hot_topics($category_info['id']));
		}
		
		//边栏专题
		if (TPL::is_output('block/sidebar_feature.tpl.htm', 'wiki/index'))
		{
			TPL::assign('feature_list', $this->model('module')->feature_list());
		}
		
		if ($category_info)
		{
			TPL::assign('category_info', $category_info);
			
			$this->crumb($category_info['title'], '/wiki/category-' . $category_info['id']);
			
			$meta_description = $category_info['title'];
			
			if ($category_info['description'])
			{
				$meta_description .= ' - ' . $category_info['description'];
			}
			
			TPL::set_meta('description', $meta_description);
		}
		
		$wiki_list = $this->model('index')->get_hot_wiki($_GET['page'], get_setting('contents_per_page'));
		
		$question_list = $this->model('index')->get_hot_question($_GET['page'], get_setting('contents_per_page'));

		$focus_wiki_list = $this->model('index')->get_focus_wiki();//获取焦点图
		
		
		TPL::assign('focus_wiki_list', $focus_wiki_list);
		
		/**
		 * test 
		 * add by david 
		 * 2014-08-01
		 */
		$wiki_test_snail_Image = $this->model('index')->get_Test_Image(15);		
		TPL::assign('wiki_test_snail_Image', $wiki_test_snail_Image);
		
		
		TPL::assign('wiki_list', $wiki_list);
		TPL::assign('question_list', $question_list);
		
		TPL::assign('wiki_list_bit', TPL::output('index/ajax/wiki.list', false));
		TPL::assign('question_list_bit', TPL::output('index/ajax/question.list', false));
		
		TPL::output('index/index');
	}
}