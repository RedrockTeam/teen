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
						$_data = M('chairman')->field('picture')->where($where)->select();
						$commentData[$key]['image'] = $_data[0]['picture'];
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
					$_data = M('chairman')->field('picture')->where($where)->select();
					$data['voice']['image'] = $_data[0]['picture'];
				}else{
					$data['voice']['image'] = 'public/chairone/man.jpg';
				}
				//将照片地址添加到结果数组里
				$where = array(
					'voiceid' => $id,
					'user' => session('stunum'),
				);
				//查看是否已经对该问题点过赞
				if(M('user_vote')->where($where)->select()){
					$data['is_voted'] = true;
				}else{
					$data['is_voted'] = false;
				}
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
	    	$data = $this->get_voice();
	    	var_dump($data);
	    	$this->assign('data', $data);
	    	$this->display();
	    }


	    public function get_voice(){        //获取学生主席的@提问
	        $id = I('get.id');
	        if(!$id){
	            $id = 0;
	            return $this->loadData($id);                //根据是否有id判断是首次加载还是下拉加载
	        }else{
	            $this->ajaxReturn($this->loadData($id));
	        }
	    }
	    private function loadData($id){    //下拉加载问题
	        $data['be_question'] = $this->load_be_question($id);  //加载被提问数据
	        $data['question'] = $this->load_question($id);  //加载提问数据
	        return $data;
	    }
	    private function load_be_question($id = 0){     
	        $where = array(
	            'gettername' => session('username'),
	            'id' => array('gt', $id)
	        );
	        $res = M('voice')->where($where)->limit(5)->select();
	        return $res;
	    }
	    private function load_question($id = 0){
	        $where = array(
	            'posterid' => session('stunum'),    //这里是主席的id
	            'id' => array('gt', $id])
	        );
	        $res = M('voice')->where($where)->limit(5)->select();
	        return $res;
	    }


	    public function delete_vioce(){
	        if(!$session('userType')){
	            $data = array(
	                'status' => 403,
	                'message' => '没有权限'
	            );
	        }else{
	            $id = I('get.id');
	            $where = array(
	                'id' => $id,
	            );
	            M('voice')->where($where)->delete();
	            $where = array(
	                'voice_id' => $id, 
	            );
	            M('vote')->where($where)->delete();
	            M('comment')->where($where)->delete();
	            $data = array(
	                'status' => '200', 
	                'message' => '删除成功'
	            );
	        }
	        $this->ajaxReturn($data, 'json');
	    }

	}