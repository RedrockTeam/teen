<?php
namespace Admin\Controller;
use Think\Controller;
class ShowController extends Controller {
	public function _before_index(){
        if(!session('manager')){
            echo"<script>
            alert('请先登录');
            window.location.href='".U('Index/index')."'</script>";
        }
    }
	public function index(){  
		$message = $this->get_info();
		$this->assign('message', $message);
        $this->display();
    }
    private function get_info(){
    	return M('chairman')->select();
    }
    public function delete_chirman(){
       if(session('manager')){
            $chair_id = I('get.id');    //获取需要删除的问题id
            if(M('chairman')->where("id=".$chair_id)->delete()){
                echo"<script>
                alert('删除成功');
                window.location.href='".U('Show/index')."'</script>";
            }else{
                echo"<script>
                alert('删除失败');
                window.location.href='".U('Show/index')."'</script>";
            }
        }else{
            echo"<script>
            alert('没有权限');
            window.location.href='".U('Show/index')."'</script>";
        }
    }
}