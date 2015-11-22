<?php
	namespace Mobile\Controller;
	use Think\Controller;
	class IndexController extends Controller {
	    

	    public function index(){
	    	
	    	$chairman = M('chairman')->field('id, chairname')->select();
	        $voice = R('Home://Index/load_home_data');
	        var_dump($voice);
	    	// $this->assign('voice', $voice);          //这里缺少登陆状态和前端渲染的数据
	        // $this->assign('chairman', $chairman);
	    	$this->display();

	    }

	}