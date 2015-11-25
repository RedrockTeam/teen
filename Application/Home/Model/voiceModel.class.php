<?php
namespace Home\Model;
use Think\Model;
class voiceModel extends Model {
	//首页的数据加载的方法
	public function home_load_data($id){		//默认的下拉次数为0即首页加载
		$voice = M('voice');
		if($id){
			$where = array(
				'id' => ['gt', $id],
			);
			return $voice->where($where)->order('time desc')->limit(5)->select();	//下拉加载
		}else{
			return $voice->order('time desc')->limit(5)->select();		//首页加载
		}
	}
}
