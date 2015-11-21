<?php 
namespace Admin\Model;
use Think\Model;
class ImageModel extends Model{
	public function getNopass(){
		$M = M('Image');
		$nopassers = $M->where('is_pass=0')->select();
		return $nopassers;
	}

	public function getpic(){
		$M = M('Image');
		$data = $M->select();
		return $data;
	}

	public function addpic($data){
		$M = M('Image');
		$has_upload = array();
		foreach ($data[0] as $d) {
			$where['uid'] = $d['uid'];
			$res = $M->where($where)->find();
			if(!$res){
				$save['pic'] = $d['pic'];
				$save['uid'] = $d['uid'];
				$save['time'] = date('Y-m-d H:i:s',time());
				$save['is_pass'] = 1;
				$M->add($data);
			}
		}
		foreach ($data[1] as $d) {
			$where['uid'] = $d['uid'];
			$res = $M->where($where)->find();
			if(!$res){
				$save['pic'] = $d['pic'];
				$save['uid'] = $d['uid'];
				$save['time'] = date('Y-m-d H:i:s',time());
				$save['is_pass'] = 2;
				$M->add($data);
			}
		}
	}

	public function getdata(){
		$M = M('Image');
		$data['all'] = $M->count('id');
		$data['wait'] = $M->where('is_pass=0')->count('id');
		$data['fail'] = $M->where('is_pass=1')->count('id');
		return $data;
	}

	public function showpic(){
			/*if(I('get.limit') == '最新')
	        	session('order','time desc');
		    if(I('get.limit') == '人气')
		        session('order','vote desc');
		    if(I('get.limit') == '综合')
		       	session('order',null);
			if(I('get.sex') == '妹子') 
		        session('sex','女');
		    if(I('get.sex') == '汉子') 
		        session('sex','男');
		    if(I('get.sex') == '全部') 
		        session('sex',null);  
		    
			if(I('session.sex')!=null)   
				$data['sex'] = I('session.sex');
		    $order = I('session.order');
		    if(I('get.search')!=null){
		    	$data = null;
		    	$order = null;
		    	$data['uid'] = I('get.search');
		    }*/
			$begin = I('post.btn') ? (I('post.btn')-1)*12 : 0 ;
	        $res = M('Image')->where($data)->order('id')->limit($begin,12)->select();
	        return $res;	
	}

	public function showpage(){
		$count = M('Image')->count('id');
		$page = ceil($count/12);
		return $page;
	}
} 
			