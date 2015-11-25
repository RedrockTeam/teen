<?php
	namespace Mobile\Controller;
	use Think\Controller;
	class IndexController extends Controller {
	    sadsasa

	    public function index(){
	    	$chairman = M('chairman')->field('id, chairname')->select();
	        $voice = $this->load_home_data();
	        // var_dump($voice);
	    	$this->assign('voice', $voice);          //这里缺少登陆状态和前端渲染的数据
	        $this->assign('chairman', $chairman);
	    	$this->display();
	    }

	    //首页加载的方法
	    public function load_home_data(){
	        $id = I('get.id');                    //获取上一次查询的终点id
	        $data = $this->home_load_data($id);
	        if($id){
	            $this->ajaxReturn($data, 'json');
	        }else{
	            return $data;
	        }
	    }


	    //首页的数据加载的方法
		public function home_load_data($id){		//默认的下拉次数为0即首页加载
			$voice = M('voice');
			if($id){
				$where = array(
					'id' => array('gt', $id)
				);
				return $voice->where($where)->order('time desc')->limit(5)->select();	//下拉加载
			}else{
				return $voice->limit(5)->order('time desc')->select();		//首页加载
			}
		}

	    public function detail() {
	    	$voiceId = I('get.id');
	    	$this->assign('voiceId', $voiceId);
	    	$this->display();
	    }
	}