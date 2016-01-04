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

	/* 
		傻逼代码，毫无维护性，作者表示，还是不要写备注了。。。
	*/


	public function add(){
		if(session('manager')){
			$picture = './Public/chairone/'.$_FILES['pic']['name'];

			//move_uploaded_file($_FILES['pic']['tmp_name'] , $picture); 
			$data = array(
				'id' => rand(10000, 50000),
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
	private function upload(){
		$upload = new \Think\Upload();// 实例化上传类
    	$upload->maxSize   =     3145728 ;// 设置附件上传大小
    	$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    	$upload->rootPath  =     './Public/'; // 设置附件上传根目录
    	$upload->savePath  =     'chairone'; // 设置附件上传（子）目录
    	// 上传文件 
    	$info   =   $upload->upload();
    	if(!$info) {// 上传错误提示错误信息
    	    $this->error($upload->getError());
    	}else{// 上传成功
    	    $this->success('上传成功！');
    	}


	}
}



