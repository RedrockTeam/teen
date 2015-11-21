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
	public function add(){
		if(session('manager')){
			$picture = '__PUBLIC__/chairone'.$_FILES['myfile']['name'];
			move_uploaded_file($_FILES['myfile']['tmp_name'] , $picture);
			$where = array(
				'chairname' => I('get.username'),
				'password' => I('get.password'),
				'information' => I('get.info'),
				'sex' => I('get.sex'),
				'picture' => 
			);
		}
	}

}
