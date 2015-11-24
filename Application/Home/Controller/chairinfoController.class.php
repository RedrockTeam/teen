<?php
namespace Home\Controller;
use Think\Controller;
class chairinfoController extends Controller {

	public function chair_login(){      //学生会主席的登陆方法
        if(I('get.type') != 'chairman'){
            $data = array(
                'status' => '403',
                'message' => '参数不正确' 
            );
        }else{
        	$where = array(
        		'chairname' => I('get.chairname'),
        		'password' => I('get.password'),
        	);
        	$message = M('chairman')->where($where)->select();
        	if($message){
        		$data = array(
                	'status' => '200',
                	'message' => '登陆成功' 
            	);
            	session('username', $message['chairname']);
    			session('stunum', 'chairman');
            	session('sex', $message['sex']);
            	session('touxiang', $message['picture']);
        	}else{
        		$data = array(
                	'status' => '400',
                	'message' => '用户名或密码错误', 
            	);
        	}
        }
        $this->ajaxReturn($data);
    }
}
