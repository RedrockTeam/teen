<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    
    public function index(){
        $this->flash();
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
        // print_r($voice);
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
        $comment = M('comment');
        $chairman = M('chairman');
        $user = session('stunum');
        $UserVote = M('user_vote');
        if($id){
            $map['id'] = array('lt', $id);
            $voices = $voice->where($map)->order('time desc')->limit(5)->select();   //下拉加载
            foreach ($voices as $index => $voice) {
                if($UserVote->where("voiceid = '{$voice['id']}' AND user = '$user'")->find()){
                    $voices[$index]['is_voted'] = 'true';
                }else{
                    $voices[$index]['is_voted'] = 'false';
                }
                if ($voice['gettername'] != 'public') {
                    $voices[$index]['gettername'] = $chairman->where("id = '{$voice['gettername']}'")->getField('chairname');
                }
                if (strlen($voices[$index]['posterid']) > 6) {
                    $voices[$index]['touxiang'] = '/teen/Public/home/images/default.png';
                } else {
                    $voices[$index]['touxiang'] = $chairman->where("id = '{$voice['posterid']}'")->getField('picture');
                }
                $comments = $comment->where("voiceid = '{$voice['id']}'")->select();
                if ($comments) {
                    foreach ($comments as $_index => $_comment) {
                        if (strlen($_comment['userid']) > 6) {
                            $comments[$_index]['touxiang'] = '/teen/Public/home/images/default.png';
                        } else {
                            $comments[$_index]['touxiang'] = $chairman->where("id = '{$_comment['userid']}'")->getField('picture');
                        }
                    }
                }
                $voices[$index]['comments'] = $comments;
            }
            return $voices;
        }else{
            $voices = $voice->order('time desc')->limit(5)->select();   //下拉加载
            foreach ($voices as $index => $voice) {
                if (strlen($voices[$index]['posterid']) > 6) {
                    $voices[$index]['touxiang'] = '/teen/Public/home/images/default.png';    
                } else {
                    $voices[$index]['touxiang'] = $chairman->where("id = '{$voice['posterid']}'")->getField('picture');
                }

                // 
                if ($voice['gettername'] != 'public') {
                    $voices[$index]['gettername'] = $chairman->where("id = '{$voice['gettername']}'")->getField('chairname');
                }
                // 是否点过赞
                if($UserVote->where("voiceid = '{$voice['id']}' AND user = '$user'")->find()){
                    $voices[$index]['is_voted'] = 'true';
                }else{
                    $voices[$index]['is_voted'] = 'false';
                }
                $comments = $comment->where("voiceid = '{$voice['id']}'")->select();
                if ($comments) {
                    foreach ($comments as $_index => $_comment) {
                        if (strlen($_comment['userid']) > 6) {
                            $comments[$_index]['touxiang'] = '/teen/Public/home/images/default.png';
                        } else {
                            $comments[$_index]['touxiang'] = $chairman->where("id = '{$_comment['userid']}'")->getField('picture');
                        }
                    }
                }
                $voices[$index]['comments'] = $comments;
            }
            return $voices;      //首页加载
        }
    }
}

