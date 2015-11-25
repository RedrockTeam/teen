<?php
namespace Home\Controller;
use Think\Controller;
class chairinfoController extends Controller {

	public function chair_login(){      //学生会主席的登陆方法
        if(I('get.type') != 'chairman'){
            $data = array(
                'status' => '403',
                'message' => '参数不正确' 
            );
        }else{
        	$where = array(
        		'chairname' => I('get.chairname'),
        		'password' => I('get.password'),
        	);
        	$message = M('chairman')->where($where)->select();
        	if($message){
        		$data = array(
                	'status' => '200',
                	'message' => '登陆成功' 
            	);
                session('userType', 'chairman');
            	session('username', $message[0]['chairname']);
    			session('stunum', $message[0]['id']);
            	session('sex', $message[0]['sex']);
            	session('touxiang', $message[0]['picture']);
        	}else{
        		$data = array(
                	'status' => '400',
                	'message' => '用户名或密码错误', 
            	);
        	}
        }
        $this->ajaxReturn($data);
    }

    /*  
    获取学生会主席个人信息页面的被提问还有
    自己的提问栏目，分为首次加载和下拉加载
    每次取五条数据，首次加载为模板渲染，其
    余为ajax接口
    */

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
            'id' => ['gt', $id],
        );
        $res = M('voice')->where($where)->limit(5)->select();
        return $res;
    }
    private function load_question($id = 0){
        $where = array(
            'posterid' => session('stunum'),    //这里是主席的id
            'id' => ['gt', $id],
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
