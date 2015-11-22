<?php
	namespace Mobile\Controller;
	use Think\Controller;
	class IndexController extends Controller {
	    

	    public function index(){
	    	$chairman = M('chairman')->field('id, chairname')->select();
	        $voice = $this->load_home_data();
	    	$this->assgin('voice', $voice);          //这里缺少登陆状态和前端渲染的数据
	        $this->assgin('chairman', $chairman);
	    	$this->display();
	    }

	    //首页加载的方法
	    public function load_home_data(){
	        $id = I('get.id');                    //获取上一次查询的终点id
	        $data = M('voice')->home_load_data($id);
	        if($id){
	            $this->ajaxReturn($data, 'json');
	        }else{
	            return $data;
	        }
	    }

	}