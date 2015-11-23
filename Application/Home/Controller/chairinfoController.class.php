<?php
namespace Home\Controller;
use Think\Controller;
class chairinfoController extends Controller {
	public function _before_index(){
		if(!$session('chairname')){			//只有登陆了的用户才能查看问题详情
			$this->ajaxReturn('hehe');
		}
	}
}
