<?php
	namespace Mobile\Controller;
	use Think\Controller;
	class IndexController extends Controller {
	    

	    public function index(){
	    	
	    	$chairman = M('chairman')->field('id', 'chairname')->select();
	        $voice = R('Home/load_home_data');
	    	$this->assgin('voice', $voice);          //这里缺少登陆状态和前端渲染的数据
	        $this->assgin('chairman', $chairman);
	    	$this->display();

	    }

	}