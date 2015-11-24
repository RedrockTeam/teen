<?php
namespace Home\Controller;
use Think\Controller;
class voiceDtailController extends Controller {
	public function _before_index(){
		if(session('username')){
			$this->error('请先登陆！');
		}
	}
	public function index(){
		$data = $this->get_voice_detail();
		dump($data);
		$this->assign('info', $data);
		$this->display();
	}

	public function get_voice_detail(){
		$id = I('get.id');
		if(!$id){
			$data = array(
				'status' => 300,
				'message' => '无效id'
			);
			$this->ajaxReturn($data, 'json');
		}else{
			$where = array(
				'voice.id' => $id,
			);
			$commentData = M('voice')->field('comment.time, comment.username, comment.comment, comment.userid')->join("comment ON voice.id = comment.voiceid")->where($where)->select();
			$voiceData = M('voice')->where('id='.$id)->select();
			foreach ($commentData as $key => $value) {
				if(strlen($value['userid']) == 5){
					$where = array(
						'id' => $value['userid']
					);
					$commentData[$key]['image'] = M('chairman')->field('picture')->where($where)->select()[0]['picture'];
				}else{
					$commentData[$key]['image'] = 'public/chairone/man.jpg';
				}
			}

			//分别获取评论和问题
			$data['comment'] = $commentData;
			$data['voice'] = $voiceData[0];
			//将结果负值给data数组

			if(strlen($voiceData[0]['posterid']) == 5){ 
				$where = array(
					'id' => $voiceData[0]['posterid']
				);
				$data['voice']['image'] = M('chairman')->field('picture')->where($where)->select()[0]['picture'];
			}else{
				$data['voice']['image'] = 'public/chairone/man.jpg';
			}
			//将照片地址添加到结果数组里
			$where = array(
				'voiceid' => $id,
				'user' => session('stunum'),
			);
			if(M('user_vote')->where($where)->select()){
				$data['is_voted'] = true;
			}else{
				$data['is_voted'] = false;
			}
			//查看是否已经对该问题点过赞
			dump($data);
			return $data;
		}
	}
}
