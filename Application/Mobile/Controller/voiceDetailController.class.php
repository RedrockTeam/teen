<?php
namespace Mobile\Controller;
use Think\Controller;
class VoiceDtailController extends Controller {
	public function _before_index(){
		if(session('username')){
			$this->error('请先登陆！');
		}
	}
	public function index(){
		
	}

	
}
