<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Admin extends DOD_Controller{

	/**
	 * 载入后台主页 
	 */

	public function load_admin(){
		$this -> load -> view ('admin/admin');
	}

	/**
	 * 载入文章页面
	 */
	public function load_article(){
		$uid = $_SESSION['uid'];
		
		$this -> load -> library('pagination');
		$perPage = 3;

		$config['base_url'] = site_url('admin/load_article');
		$config['total_rows'] = count($this -> db->get_where('article',array('uid'=> $uid))->result_array());
		$config['per_page'] = $perPage;
		$config['uri_segment'] = 3;
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['full_tag_open'] = '<ul class="pagination" style="margin-left:40px">';
		$config['full_tag_close'] = '</ul>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li><a style="color:#000">';
		$config['cur_tag_close'] = '</a></li>';
		$config['first_link'] = '第一页';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = '最后一页';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		$this -> pagination -> initialize($config);
		$data ['links'] = $this -> pagination -> create_links();
		// p($data);die;
		$offset = $this -> uri ->segment(3);
		$this -> db -> limit($perPage,$offset);


		$this -> load -> model ('article_model','article');
		$data['article'] = $this -> article -> check_info($uid);
		
		$this -> load -> view('admin/article',$data);
	}


	/**
	 * 载入添加文章页
	 */
	public function edit_article(){
		$this -> load -> model ('cate_model','cate');
		$data['category'] = $this -> cate -> check();
		$this -> load -> helper('form');
		$this -> load -> view('admin/add_article',$data);
	}

	/**
	 * 验证文章输入
	 */
	public function check_article(){
		
		$this -> load -> model ('cate_model','cate');
		$data['category'] = $this -> cate -> check();
		$this -> load -> helper('form');
		$this -> load -> library('form_validation');
		$status = $this->form_validation->run('article');
		if(!$status){

			$this -> load -> view ('admin/add_article',$data);

		} else {
			$aid = $this -> input -> post('aid');
			
			if($aid)
			{
				$article =array(
					'title' => $this -> input -> post('title'),
					'info' => $this -> input -> post('info'),
					'cid' => $this -> input -> post('cid'),
					'text' => $this -> input -> post('text'),
					'time' => time()
					);

				$this -> load -> model ('article_model','article');
				$this -> article -> update_by_aid($aid,$article);
				success('admin/load_article','修改成功');

			} else {
				$article = array (
				'uid'=> $_SESSION['uid'],
				'title' => $this -> input -> post('title'),
				'info' => $this -> input -> post('info'),
				'cid' => $this -> input -> post('cid'),
				'text' => $this -> input -> post('text'),
				'time' => time()
				);
				$this -> load -> model ('article_model','article');
				$this -> article -> add_artile($article);
				success('admin/load_article','添加成功');
			}
		}
	}


	/**
	 * 载入修改页面
	 */
	public function change_article(){
		$this -> load -> helper('form');
		$aid = $this -> uri ->segment(3);
		$this -> load -> model('article_model','article');
		$this -> load -> model ('cate_model','cate');

		$uid = $this -> article -> check_author_by_aid($aid);
		if($_SESSION['uid']==$uid[0]['uid']){
			$data['category'] = $this -> cate -> check();
			$data['article'] = $this -> article ->check_by_aid($aid);
			$this -> load -> view ('admin/add_article',$data);
		} else {
			error('对不起，您没有权限登入此页面');
		}
		
	}

	/**
	 * 删除文章确认弹窗
	 */
	public function del_article(){
		$aid = $this -> uri -> segment (3);
		$url = $this -> uri ->segment(2);
		$Msg = '确认该文章么？\n若确认，则该文章下的所有评论都将被删除';
		confirm('admin/drop_article'.'/'.$aid,'admin/load_artilce',$Msg);
	}

	/**
	 * 删除文章动作
	 */
	public function drop_article(){
		$aid = $this -> uri -> segment(3);
		$this -> load -> model('article_model','article');
		$this -> article -> del_article($aid);
		success('admin/load_article','删除文章成功');
	}




	/**
	 * 载入后台评论页面
	 */
	public function load_comment(){
		$this -> load -> model ('comment_model','comment');
		$uid = $_SESSION['uid'];

		$this -> load -> library('pagination');
		$perPage = 3;

		$config['base_url'] = site_url('admin/load_comment');
		$config['total_rows'] = count($this -> db->get_where('comment',array('uid'=> $uid))->result_array());
		$config['per_page'] = $perPage;
		$config['uri_segment'] = 3;
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['full_tag_open'] = '<ul class="pagination" style="margin-left:40px">';
		$config['full_tag_close'] = '</ul>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li><a style="color:#000">';
		$config['cur_tag_close'] = '</a></li>';
		$config['first_link'] = '第一页';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = '最后一页';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';


		$this -> pagination -> initialize($config);
		$data ['links'] = $this -> pagination -> create_links();
		// p($data);die;
		$offset = $this -> uri ->segment(3);
		$this -> db -> limit($perPage,$offset);



		$data['comment']= $this -> comment -> check_by_uid($uid);
		$this -> load -> view('admin/comment',$data);
	}

	/**
	 * 删除评论动作
	 */
	public function del_comment(){
		$com_id =  $this -> uri -> segment(3);
		$this -> load -> model ('comment_model','comment');

		$uid = $this -> comment -> check_uid_by_com_id($com_id);
		if($_SESSION['uid']==$uid[0]['uid']){
			$this -> comment -> del_comment($com_id);
			success('admin/load_comment','删除评论成功');
		} else {
			error('对不起，您没有权限登入此页面');
		}
	}



	/**
	 * 载入个人信息页
	 */
	public function load_userinfo(){
		$uid = $_SESSION['uid'];
		$this -> load -> model ('login_model','login');
		$data['userinfo'] = $this -> login -> check_by_uid($uid);
		$this -> load -> view('admin/user',$data);
	}


	/**
	 * 载入修改个人信息页
	 */
	public function load_change_user(){
		$uid = $_SESSION['uid'];
		$this -> load -> helper('form');
		$this -> load -> model ('login_model','login');
		$data['userinfo'] = $this -> login -> check_by_uid($uid);
		$this -> load -> view('admin/change_user',$data);

	}

	/**
	 * 验证用户信息修改
	 */
	public function check_userinfo(){
		$this -> load -> library('form_validation');
		$status = $this->form_validation->run('userinfo');

		if($status){
			$uid = $_SESSION['uid'];
			$data = array(
				'uid' => $uid,
				'username' => $this -> input -> post('username'),
				'nickname' => $this -> input -> post('nickname'),
				'email' => $this -> input -> post('email')
				);
			p($_SESSION);
			$this -> load -> model ('login_model','login');
			$this -> login -> update_userinfo_by_uid($uid,$data);
			$_SESSION['nickname'] = $this -> input -> post('nickname');
			success('admin/load_userinfo','修改成功');
		} else {
			$this -> load -> view('admin/change_user');
		}
	}



	/**
	 * 载入密码修改页
	 */
	public function load_change_passwd(){
		$this -> load -> helper('form');
		$this -> load -> library('form_validation');
		$this -> load -> view('admin/change_passwd');
	}

	/**
	 * 验证新密码动作
	 */
	public function check_passwd(){
		$this -> load -> helper('form');
		$this -> load -> library('form_validation');
		$status = $this -> form_validation -> run('passwd');
		if($status){
			
			$uid = $_SESSION['uid'];
			$this -> load -> model('login_model','login');
			$old_passwd = $this -> login ->select_passwd_by_uid($uid);

			if(md5($this->input->post('old_passwd'))!==$old_passwd[0]['password']){
				error('原密码输入错误');die;
			}
			else{
				$data['password'] = md5 ($this -> input -> post('new_passwd2'));
				$this -> login -> update_passwd_by_uid($uid,$data);
				unset($_SESSION['nickname']);
				unset($_SESSION['uid']);
				unset($_SESSION['logtime']);
				success('login/load_login','密码修改成功');
			}
			
		} else {
			$this -> load -> view('admin/change_passwd');
		}
	}



























}