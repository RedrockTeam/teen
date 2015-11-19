<?php
	namespace Mobile\Controller;
	use Think\Controller;
	class UserController extends Controller {
	    

	    public function userIndex(){
	    	
	    	$this->display('user');

	    }


	    public function userLogin() {
	    	if (IS_GET) {
	    		$this->display('login');
	    	} else {
	    		$this->ajaxReturn(array(
	    			'status' => 200,
	    			'data' => 'index.php?s=/Mobile'
	    		));
	    	}
	    }

	    public function question() {
	    	if (IS_GET) {
	    		$this->display('question');
	    	}
	    }

	}