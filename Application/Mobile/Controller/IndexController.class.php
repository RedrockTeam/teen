<?php
	namespace Mobile\Controller;
	use Think\Controller;
	class IndexController extends Controller {

	    public function index(){
	    	$chairman = M('chairman')->field('id, chairname')->select();
	        $voice = $this->load_home_data();
	        // var_dump($voice);
	    	$this->assign('voice', $voice);          //这里缺少登陆状态和前端渲染的数据
	        $this->assign('chairman', $chairman);
	    	$this->display();
	    }

	    public function detail() {
	    	$id = I('get.id');
			$data = $this->get_voice_detail($id);
			$this->assign('info', $data);
			$this->display('detail');
	    }

	    private function get_voice_detail($id){
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

	    //首页加载的方法
	    public function load_home_data(){
	        $id = I('get.id');                    //获取上一次查询的终点id
	        $data = $this->home_load_data($id);
	        if($id){
	            $this->ajaxReturn($data, 'json');
	        }else{
	            return $data;
	        }
	    }


	    //首页的数据加载的方法
		public function home_load_data($id){		//默认的下拉次数为0即首页加载
			$voice = M('voice');
			if($id){
				$where = array(
					'id' => array('gt', $id)
				);
				return $voice->where($where)->order('time desc')->limit(5)->select();	//下拉加载
			}else{
				return $voice->limit(5)->order('time desc')->select();		//首页加载
			}
		}

	   
	    public function personal() {
	    	$this->display();
	    }
	}