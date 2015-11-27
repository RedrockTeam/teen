<?php
namespace Home\Controller;
use Think\Controller;
class questionController extends Controller {
//问题详情页的渲染方法
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

/**
 * [get_voice_detail description]
 * @return [type] [description]
 */
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

	//提交评论的方法
    public function commit_comment(){			//评论方法
        if(!session('username')){return;}
        $comment = I('get.comment');
        $id = I('get.id');
    	if(!$username = session('username')){
    		$config = array(
    			'status' => 401,
    			'message' => '还未登录哦，亲！'
    		);
    	}
    	if($comment && $id){   //获取问题id，回复内容
    		$data = array(
    			'voiceid' => $id,
    			'username' => $username,
                'userid' => session('stunum'),
    			'comment' => $comment,
    			'time' => time(), //获取当前时间戳
    		);
    		M('comment')->add($data);
            $message = array(
                'comment' => $data['comment'],
                'time' => date('Y-m-d H:i:s', $data['time']),
                'username' => $data['username'],
                'touxiang' => session('touxiang'),
            );
            $config = array(
                'data' => $message,
                'status' => 200,
                'message' => 'ok'
            );
    	}else{
    		$config = array(
    			'status' => 400,
    			'message' => "请输入评论内容！"
    		);
    	}
        $this->ajaxReturn($config);
    }

    //提交问题的方法
    public function commit_voice(){
        if(!session('username')){return;} 
        $config = array(
            'postername' => session('username'),
            'posterid' => session('stunum'),
            'gettername' => I('post.type'),
            'title' => I('post.title'),
            'question' => I('post.content')
        );      //设置需要插入数据库的参数数组
        if($config['postername'] && $config['gettername'] && $config['title'] && $config['question']){
            $config['time'] = time();
            if(M('voice')->add($config)){
                $config['touxiang'] = session('touxiang');   //将头像地址添加进返回参数
                $where = array(
                    'title' => $config['title'],
                    'stunum' => $config['posterid']
                );
                $config['id'] = M('voice')->where($where)->field('id')->select();
                $info = array(
                    'status' => 200,
                    'message' => 'ok',
                    'data' => $config
                );
            }else{
                $info = array(
                'status' => 500,
                'message' => '服务器错误',
                );
            }
        }else{
            $info = array(
                'status' => 400,
                'message' => '数据不全',
            );
        }
        $this->ajaxReturn($info, 'json');
    }

    //点赞的接口
    public function vote(){      //点赞接口
        if(!session('username')){return;}
        $id = I('get.id');
        $where = array(
            'voiceid' => $id,
        );
        if(!M('user_vote')->where($where)->select()){
            //增加关联表的一条数据
            $config = array(
                'user' => session('stunum'), //这里可能是教师的职工号，也可能是主席学号
                'voiceid' => $id,
            );
            M('user_vote')->add($config);
            //修改vioce表的vote
            $where = array(
                'id' => $id,
            );
            M('voice')->where($where)->setInc('vote', 1);
            $data = array(
                'status' => 200,
                'message' => 'ok',
            );
        }else{
            $data = array(
                'status' => 304,
                'message' => '已经点过赞了哦亲!',
            );
        }
        $this->ajaxReturn($data, 'json');
    }

    //删除自己提问的问题
    public function delete_vioce(){
    	$stunum = session('stunum');
    	$where = array(
    		'id' => I('get.id'),  //问题的id
    	);
    	$voice_stunum = M('voice')->field('posterid')->where($where)->select();	//查看是否是本人提的问题
    	if($stunum == $voice_stunum){
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
    	}else{
    		$data = array(
            	'status' => '403', 
            	'message' => '没有权限'
        	);
    	}
    	$this->ajaxReturn($data, 'json');
    }
}