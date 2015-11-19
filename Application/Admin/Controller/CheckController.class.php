<?php
namespace Admin\Controller;
use Think\Controller;
class CheckController extends Controller {
    public function _before_index(){
        if(!session('manager')){
            echo"<script>
            alert('请先登录');
            window.location.href='".U('Index/index')."'</script>";
        }
    }

    public function index(){
        $data = D('Image')->getNopass();
        $this->assign('noPassers', $data);
        $this->display();
    }
    
    public function pass(){
        $stunum = I('stunum');
        if($stunum){
            $save = [
                'is_pass' => 1, 
            ];
            $where = [
                'uid' => $stunum,
            ];
            if(M('image')->where($where)->save($save)){
                $this->ajaxReturn("ok");
            }
        }
    }

    public function delete(){
        $stunum = I('stunum');
        if($stunum){
            $where = [
                'uid' => $stunum,
            ];
            if(M('image')->where($where)->delete()){
                $this->ajaxReturn("ok");
            }
        }
    }

    public function change(){
        if(I('post.uid')!=null && I('post.state')!=null){
            $where['uid'] = I('post.uid');
            $data['is_pass'] = I('post.state');
        }
        M('Image')->where($where)->save($data);
    }

    public function get_pic(){
        $res = D('Image')->showpic();
        $this->ajaxReturn($res);
    }

    public function get_page(){
        $res = D('Image')->showpage();
        $this->ajaxReturn($res);
    }
}