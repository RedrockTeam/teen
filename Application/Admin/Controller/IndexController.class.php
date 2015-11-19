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
        if($manager == 'hongyanstaff' && $password == 'hongyanstaff'){
            session('manager', '红岩网校管理者');
            $this->ajaxReturn("true");
        }
    }
    public function logout(){
        session('manager', null);
        echo"<script>window.location.href='".U('Index/index')."'</script>";
    }
}