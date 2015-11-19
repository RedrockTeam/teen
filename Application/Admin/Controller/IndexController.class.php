<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }

    public function login(){
        $manager = I('manager');
        $password = I('password');
        $where = [
            "manager" => $manager,
            "password" => $password, 
        ];
        if(M('manager')->where($where)->select()){
            session('manager', $manager);
            $this->ajaxReturn("true");
        }
    }
    public function logout(){
        session('manager', null);
        echo"<script>window.location.href='".U('Index/index')."'</script>";
    }
}