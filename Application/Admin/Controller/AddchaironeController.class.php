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
			$picture = C('__PUBLIC__').'/chairone'.$_FILES['pic']['name'];
			var_dump($picture);
			//move_uploaded_file($_FILES['pic']['tmp_name'] , $picture);
			$data = array(
				'chairname' => I('post.username'),
				'password' => I('post.password'),
				'sex' => I('post.sex'),
				'information' => I('post.info'),
				'picture' => $picture
			);
			if(M('chairman')->add($data)){
				echo"<script>
     			alert('添加成功');
            	window.location.href='".U('Addchairone/index')."'</script>";
			}else{
				echo"<script>
     			alert('信息不全');
            	window.location.href='".U('Addchairone/index')."'</script>";
			}
		}else{
			echo"<script>
            alert('请先登录');
            window.location.href='".U('Index/index')."'</script>";
		}
	}

}
