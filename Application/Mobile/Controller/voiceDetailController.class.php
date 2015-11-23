<?php
namespace Mobile\Controller;
use Think\Controller;
class voiceDtailController extends Controller {
	public function _before_index(){
		if($session('username')){
			$this->error('请先登陆！');
		}
	}
	public function index(){
		$data = $this->get_voice_detail();
		$this->assign('info', $data);
		print_r($data);
		$this->display();
	}

	private function get_voice_detail(){
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
				if($value['userid'] == 'chairman'){
					$commentData[$key]['image'] = M('chairman')->field('picture')->where('chairname='.$value['username'])->select();
				}else{
					$commentData[$key]['image'] = 'public/chairone/man.jpg';
				}
			}
			$data['comment'] = $commentData;
			$data['voice'] = $voiceData[0];
			if($voiceData['posterid'] == 'chairman'){ 		//把主席号改成chairman
				$data['voice']['image'] = M('chairman')->field('picture')->where($where)->select();
			}else{
				$data['voice']['image'] = 'public/chairone/man.jpg';
			}
			return $data;
		}
	}
}
