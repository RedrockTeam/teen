<?php
namespace Admin\Controller;
use Think\Controller;
class AddvoteController extends Controller {
	public function index(){
		$this->display();
	}
	public function addvote(){
		$id = I('post.pic_id');
		$vote = I('post.vote');
		if(is_null($id) && is_null($vote)){
			$this->error('信息不完整');
		}else{
			if(is_numeric($id) && is_numeric($vote)){
				$where = [
						'id' => $id,
					];
				if(M('image')->where($where)->select()){
					M('image')->where($where)->setInc('vote', $vote);
					$this->success('添加成功', U('Addvote/index'));
				}else{
					$this->error('不存在此人');
				}
			}else{
				$this->error('数据有误');
			}
		}
	}
}
