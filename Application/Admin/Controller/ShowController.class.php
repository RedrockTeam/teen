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
}