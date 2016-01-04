<?php
namespace Admin\Controller;
use Think\Controller;
class AddxuebaController extends Controller {
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
            $picture = './Public/chairone/'.$_FILES['pic']['name'];
            if(move_uploaded_file($_FILES['pic']['tmp_name'] , $picture)){
                echo "ok";
            }
            $data = array(
                'id' => rand(1000, 5000),
                'name' => I('post.username'),
                'password' => I('post.password'),
                'sex' => I('post.sex'),
                'info' => I('post.info'),
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