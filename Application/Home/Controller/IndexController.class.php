<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	//$this->assgin('voice', $voice);          //这里缺少登陆状态和前端渲染的数据
        $this->display();
    }

    public function login(){	//登录的方法
    	$username = I('get.username');
    	$password = I('get.password');
    	$user_message = $this->curl_api($username, $password); //获取用户信息
    	if($user_message['status'] == 200){
    		session('username', $user_message['userInfo']['real_name']);
    		session('stunum', $user_message['userInfo']['stu_num']);
            session('sex', $user_message['userInfo']['gender']);
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

    //用户评论的方法
    public function commit_comment(){			//评论方法
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
    			'comment' => $comment,
    			'time' => date('y-m-d h:m:s', time() + 8 * 3600), //获取当前时间
    		);
    		M('comment')->add($data);	
            $config = array(
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

    //首页加载的方法
    public function load_home_data(){
        $id = I('get.id');                      //获取上一次查询的终点id
        $data = D('voice')->home_load_data($id);
        $this->ajaxReturn($data, 'json');
    }

    /*提问的方法
        加一变量type区分公开提问还是@提问
        在数据库里面只将getername设置为public就好
    */
    public function commit_voice(){
        $config = array(
            'postername' => session('username'),
            'gettername' => I('get.gettername'),
            'title' => I('get.title'),
            'question' => I('get.question')
        );      //设置需要插入数据库的参数数组
        if($config['postername'] && $config['gettername'] && $config['title'] && $config['question']){
            $config['time'] = date('y-m-d h:m:s', time() + 8 * 3600);
            var_dump(M('voice')->add($config));
            $info = array(
                'status' => 200,
                'message' => 'ok',
            );
        }else{
            $info = array(
                'status' => 400,
                'message' => '数据不全',
            );
        }
        $this->ajaxReturn($info, 'json');
    }




}