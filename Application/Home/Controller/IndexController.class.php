<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    
    public function index(){
        $this->flash();
        $this->display();
    }

    private function flash(){           //首页的数据渲染
        $chairman = M('chairman')->field('id, chairname')->select();
        $voice = $this->load_home_data();
    	$this->assign('voice', $voice); //这里缺少登陆状态和前端渲染的数据
        $this->assign('chairman', $chairman);
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

    private function home_load_data($id){        //默认的下拉次数为0即首页加载
        $voice = M('voice');
        $comment = M('comment');
        $chairman = M('chairman');
        if($id){
            $where = array(
                'id' => array('gt' => $id)
            );
            $voices = $voice->where($where)->order('time desc')->limit(5)->select();   //下拉加载
            foreach ($voices as $index => $voice) {
                if (strlen($voices[$index]['posterid']) > 3) {
                    $voices[$index]['face'] = '/teen/Public/home/images/default.png';    
                } else {
                    $voices[$index]['face'] = $chairman->where("id = '{$voice['posterid']}'")->getField('picture');
                }
                $comments = $comment->where("voiceid = '{$voice['id']}'")->select();
                if ($comments) {
                    foreach ($comments as $_index => $_comment) {
                        if (strlen($_comment['userid']) > 3) {
                            $comments[$_index]['face'] = '/teen/Public/home/images/default.png';
                        } else {
                            $comments[$_index]['face'] = $chairman->where("id = '{$_comment['userid']}'")->getField('picture');
                        }
                    }
                }
                $voices[$index]['comments'] = $comments;
            }
            return $voices;
        }else{
            $voices = $voice->order('time desc')->limit(5)->select();   //下拉加载
            foreach ($voices as $index => $voice) {
                if (strlen($voices[$index]['posterid']) > 3) {
                    $voices[$index]['face'] = '/teen/Public/home/images/default.png';    
                } else {
                    $voices[$index]['face'] = $chairman->where("id = '{$voice['posterid']}'")->getField('picture');
                }
                $comments = $comment->where("voiceid = '{$voice['id']}'")->select();
                if ($comments) {
                    foreach ($comments as $_index => $_comment) {
                        if (strlen($_comment['userid']) > 3) {
                            $comments[$_index]['face'] = '/teen/Public/home/images/default.png';
                        } else {
                            $comments[$_index]['face'] = $chairman->where("id = '{$_comment['userid']}'")->getField('picture');
                        }
                    }
                }
                $voices[$index]['comments'] = $comments;
            }
            return $voices;      //首页加载
        }
    }
}

