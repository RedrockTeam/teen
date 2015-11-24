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
			print_r($data);
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

	   
	}