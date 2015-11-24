<?php
	namespace Home\Controller;
	use Think\Controller;
	class IndexController extends Controller {
	    public function index(){
	        $this->display();

	    }


	    public function commit_comment() {
	    	$this->ajaxReturn(array(
	    		'status' => 2020,
	    		'data' => array(
	    			"face" => '/teen/Public/mobile/images/wifi.jpg',
					"comment" => I('post.comment'),
					"username" => '呵呵呵呵',
					"time" => '2014-7-12 15:30'
	    		)
	    	));
	    }

	    public function vote() {
	    	$this->ajaxReturn(array(
	    		'status' => 0
	    	));
	    }
	}