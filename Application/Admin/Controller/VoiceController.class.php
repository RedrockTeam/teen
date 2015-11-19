<?php
namespace Admin\Controller;
use Think\Controller;
class VoiceController extends Controller {
    public function _before_index(){
        if(!session('manager')){
            echo"<script>
            alert('请先登录');
            window.location.href='".U('Index/index')."'</script>";
        }
    }

    //够着方法，渲染模板
    public function index(){  
        $config = $this->load_voice();
        $this->assign('voice', $config);
        $this->display();
    }

    //获取所有提问的方法
    private function load_voice(){        //获取所有的提问消息
        $all_voice = M('voice')->order('time desc')->select();
        return $all_voice;
    }

    //管理员删除方法
    public function delete_voice(){
        if(session('manager')){
            $voice_id = I('get.id');    //获取需要删除的问题id
            if(M('voice')->where("id=$voice_id")->delete()){
                echo"<script>
                alert('删除成功');
                window.location.href='".U('Voice/index')."'</script>";
            }else{
                echo"<script>
                alert('删除失败');
                window.location.href='".U('Voice/index')."'</script>";
            }
        }else{
            echo"<script>
            alert('没有权限');
            window.location.href='".U('Index/index')."'</script>";
        }
    }
}