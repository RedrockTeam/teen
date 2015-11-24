<?php
	namespace Mobile\Controller;
	use Think\Controller;
	class IndexController extends Controller {
	    

	    public function index(){
	    	$this->display();
	    }

	    public function detail() {
	    	$voiceId = I('get.id');
	    	$this->assign('voiceId', $voiceId);
	    	$this->display();
	    }

	    public function personal() {
	    	$this->display();
	    }
	}