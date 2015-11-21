<?php
namespace Admin\Controller;
use Think\Controller;
class AddchaironeController extends Controller {
	public function _before_index(){
        if(!session('manager')){
            echo"<script>
            alert('请先登录');
            window.location.href='".U('Index/index')."'</script>";
        }
    }

	public function index(){
		$this->display();
	} 

}
