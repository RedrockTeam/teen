<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {
	//学生和老师登录的方法
	public function login(){	
    	$username = I('post.username');
    	$password = I('post.password');
    	$user_message = $this->curl_api($username, $password); //获取用户信息
        //
    	if($user_message['status'] == 200){
    		session('username', $user_message['userInfo']['real_name']);	//姓名
    		session('stunum', $user_message['userInfo']['stu_num']);	//学号或者教职工号
            session('sex', $user_message['userInfo']['gender']);	//性别
            cookie('stunum',$user_message['userInfo']['stu_num']);
            session('touxiang', '/teen/Public/home/images/default.png');		//根据不同性别设置头像
            $conf = array(
                'username' => $user_message['userInfo']['real_name'],
                'stunum' => $user_message['userInfo']['stu_num'],
                'status' => '200',
                'message' => true,
            );
    	}else{
    		$conf = array(
                'status' => '400',
                'message' => false,
            );
    	}
        $this->ajaxReturn($conf);
    }

    //学生会主席的登陆方法
    public function chair_login(){      
    	$where = array(
    		'chairname' => I('post.chairname'),
    		'password' => I('post.password'),
    	);
    	$message = M('chairman')->where($where)->select();
    	if($message){
    		$data = array(
            	'status' => '200',
            	'message' => '登陆成功' 
        	);
            session('userType', 'chairman');			//区分字段
        	session('username', $message[0]['chairname']);	//主席名字
			session('stunum', $message[0]['id']);	//主席的id生成的5位随机数
        	session('sex', $message[0]['sex']);		//性别
        	session('touxiang', $message[0]['picture']);	//头像的地址
            cookie('stunum',$message[0]['id']);
    	}else{
    		$data = array(
            	'status' => '400',
            	'message' => '用户名或密码错误', 
        	);
    	}
        $this->ajaxReturn($data);
    }

    //用户和老师登陆用的接口
    private function curl_api($username, $password){		//登录验证接口
    	$post_configs = "user=".$username."&password=".$password;
    	$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, "http://hongyan.cqupt.edu.cn/RedCenter/Api/Handle/login");    
		curl_setopt($ch, CURLOPT_HEADER, 0);    //不取得返回头信息    
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_configs);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
		$info = curl_exec($ch);
		$res = json_decode($info, true);		//解析json数据为数组
		return $res;
    }


    // 获取主席自己的提问和@提问
    public function get_voice(){        
        $id = I('get.id');
        if(!$id){
            $data = $this->loadData($id);//根据是否有id判断是首次加载还是下拉加载
            $this->assign('data', $data);
            $this->display('personal');
        }else{
            $this->ajaxReturn($this->loadData($id));
        }
    }

    private function loadData($id){    //下拉加载问题
        if(session('userType') == 'chairman'){
            $data['be_question'] = $this->load_be_question($id);  //加载被提问数据
            $data['is_chairman'] = true;
        }else{
            $data['is_chairman'] = false;
        }
        $data['question'] = $this->load_question($id);  //加载提问数据
        return $data;
    }
    private function load_be_question($id){
        $user = session('stunum');
        $UserVote = M('user_vote');
        $chairman = M('chairman');
        $comment = M('comment');
        $Voice = M('voice');

        $map['gettername'] = session('stunum');
        if ($id) {
            $map['id'] = array('lt', $id);
        }
        $voices = $Voice->where($map)->order('time desc')->select();

        foreach ($voices as $index => $voice) {
            if (strlen($voices[$index]['posterid']) > 6) {
                $voices[$index]['touxiang'] = '/teen/Public/home/images/default.png';    
            } else {
                $voices[$index]['touxiang'] = $chairman->where("id = '{$voice['posterid']}'")->getField('picture');
            }

          
            // 是否点过赞
            if($UserVote->where("voiceid = '{$voice['id']}' AND user = '$user'")->find()){
                $voices[$index]['is_voted'] = 'true';
            }else{
                $voices[$index]['is_voted'] = 'false';
            }
            $comments = $comment->where("voiceid = '{$voice['id']}'")->select();
            if ($comments) {
                foreach ($comments as $_index => $_comment) {
                    if (strlen($_comment['userid']) > 6) {
                        $comments[$_index]['touxiang'] = '/teen/Public/home/images/default.png';
                    } else {
                        $comments[$_index]['touxiang'] = $chairman->where("id = '{$_comment['userid']}'")->getField('picture');
                    }
                }
            }
            $voices[$index]['comments'] = $comments;
        }
        return $voices;
    }
    private function load_question(){
        $user = session('stunum');
        $UserVote = M('user_vote');
        $chairman = M('chairman');
        $comment = M('comment');
        $Voice = M('voice');

        $map['posterid'] = session('stunum');
        if ($id) {
            $map['id'] = array('lt', $id);
        }
        
        $voices = $Voice->where($map)->order('time desc')->select();
        
        foreach ($voices as $index => $voice) {
            if (strlen($voices[$index]['posterid']) > 6) {
                $voices[$index]['touxiang'] = '/teen/Public/home/images/default.png';    
            } else {
                $voices[$index]['touxiang'] = $chairman->where("id = '{$voice['posterid']}'")->getField('picture');
            }

            if ($voice['gettername'] != 'public') {
                $voices[$index]['gettername'] = $chairman->where("id = '{$voice['gettername']}'")->getField('chairname');
            }
          
            // 是否点过赞
            if($UserVote->where("voiceid = '{$voice['id']}' AND user = '$user'")->find()){
                $voices[$index]['is_voted'] = 'true';
            }else{
                $voices[$index]['is_voted'] = 'false';
            }
            $comments = $comment->where("voiceid = '{$voice['id']}'")->select();
            if ($comments) {
                foreach ($comments as $_index => $_comment) {
                    if (strlen($_comment['userid']) > 6) {
                        $comments[$_index]['touxiang'] = '/teen/Public/home/images/default.png';
                    } else {
                        $comments[$_index]['touxiang'] = $chairman->where("id = '{$_comment['userid']}'")->getField('picture');
                    }
                }
            }
            $voices[$index]['comments'] = $comments;
        }
        return $voices;
    }

    public function logout() {
        session(null);
        cookie('stunum', null);
        redirect('../Index/index');
    }

}