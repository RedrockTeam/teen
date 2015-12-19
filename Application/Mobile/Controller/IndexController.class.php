<?php

    namespace Mobile\Controller;
    use Think\Controller;
    class IndexController extends Controller {
        
        public function index(){
            $this->flash();
            $this->assign('title', '青年之声首页');
            $this->assign('headerTitle', '最新提问');
            $this->display();
        }

        private function flash(){           //首页的数据渲染
            if (session('userType')) {
                $stunum = session('stunum');
                $chairman = M('chairman')->field('id, chairname, picture')->where("id != '$stunum'")->select();    
            } else {
                $chairman = M('chairman')->field('id, chairname, picture')->select();
            }
            $voice = $this->load_home_data();
        	$this->assign('voice', $voice); //这里缺少登陆状态和前端渲染的数据
            $this->assign('chairman', $chairman);
        }


        //首页加载的方法
        public function load_home_data(){
            $id = I('post.id');                    //获取上一次查询的终点id
            $data = $this->home_load_data($id);
            if($id){
                $this->ajaxReturn($data, 'json');
            }else{
                return $data;
            }
        }

        private function home_load_data($id){        //默认的下拉次数为0即首页加载
            $voice = M('voice');
            if($id){
                $map['id'] = array('lt', $id);
                return $voice->where($map)->order('time desc')->limit(5)->select();   //下拉加载
            }else{
                return $voice->order('time desc')->limit(10)->select();      //首页加载
            }
        }
    }
