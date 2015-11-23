<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->flash();
        $this->display();
    }
    private function flash(){           //首页的数据渲染
        $chairman = M('chairman')->field('id, chairname')->select();
        $voice = $this->load_home_data();
        $this->assgin('voice', $voice);          
        $this->assgin('chairman', $chairman);
    }

    public function login(){	//普通用户和登录的方法
    	$username = I('post.username');
    	$password = I('post.password');
        //这里还需要处理老师和学生的区分逻辑
    	$user_message = $this->curl_api($username, $password); //获取用户信息
        //
    	if($user_message['status'] == 200){
    		session('username', $user_message['userInfo']['real_name']);
    		session('stunum', $user_message['userInfo']['stu_num']);
            session('sex', $user_message['userInfo']['gender']);
            if($user_message['userInfo']['gender'] == "男"){
                session('touxiang', '__PUBLIC__/chairone/boy.jpg');
            }else{
                session('touxiang', '__PUBLIC__/chairone/girl.jpg');
            }
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
    			'time' => time(), //获取当前时间戳
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
        $id = I('get.id');                    //获取上一次查询的终点id
        $data = D('voice')->home_load_data($id);
        if($id){
            $this->ajaxReturn($data, 'json');
        }else{
            return $data;
        }
    }

    /*提问的方法
        加一变量type区分公开提问还是@提问
        在数据库里面只将getername设置为public就好
    */
        //http://localhost/teen/?m=Home&c=Index&a=commit_voice&type=public&title=hehe&content=hehe 
    public function commit_voice(){
        if(!session('username')){return;} 
        $config = array(
            'postername' => session('username').session('stunum'),
            'gettername' => I('post.type'),
            'title' => I('post.title'),
            'question' => I('post.content')
        );      //设置需要插入数据库的参数数组
        if($config['postername'] && $config['gettername'] && $config['title'] && $config['question']){
            $config['time'] = time();
            if(M('voice')->add($config)){
                $config['touxiang'] = session('touxiang');   //将头像地址添加进返回参数
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


}