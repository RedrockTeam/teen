<?php
namespace Admin\Controller;
use Think\Controller;
class UploadpicController extends Controller {
    public function _before_index(){
        if(!session('manager')){
            echo"<script>
            alert('请先登录');
            window.location.href='".U('Index/index')."'</script>";
        }
    }
    
    public function index(){
        $this->display();
    }


    public function upload(){
        $stunum = I('post.stunum');
        $name = I('post.name');
        $sex = I('post.sex');
        if($sex == '1'){
            $sex = '女';
        }else if($sex == '0'){
            $sex = '男';
        }
        $file = substr(strrchr($_FILES['picture']['name'], '.'), 1);
        if($stunum && $name && $sex && $file){
            if($file == 'jpg' || $file == 'jpeg' || $file == 'png' || $file == 'gif'){
                $filename = $stunum.".".$file;
                $path1 = "Public/upimage/".$filename;
                move_uploaded_file($_FILES['picture']['tmp_name'] , $path1);
                $bigname = $stunum."_big.".$file;
                // $path2 = "Public/upimage/".$bigname;

                $imginfo = getImageSize($path1);
                $imgw = $imginfo [0];     
                $imgh = $imginfo [1];
                $image = new \Think\Image();
                $image->open($path1);
                $image->save('./Public/allimage/'.$bigname);
                $image->thumb(300, 300,\Think\Image::IMAGE_THUMB_SCALE)->save('./Public/allimage/'.$filename);
                // unlink($path1);
        
                $data = [
                    'pic' => $filename,
                    'big_pic' => $bigname,
                    'uid' => $stunum,
                    'vote' => 0,
                    'sex' => $sex,
                    'time' => date('Y-m-d H:i:s',time()),
                    'is_pass' => 0,
                ];
                M('image')->add($data);
                $data = [
                    'stu_name' => $name,
                    'password' => '',
                    'vote_day' => '',
                    'uid' => $stunum,
                    'sex' => $sex,
                    'has_upload' => 1,
                ];
                M('user')->add($data);
                echo "<script> alert('成功'); window.location.href='".U('Uploadpic/index')."'</script>"
            }else{
                echo"<script>
                alert('格式不对');
                window.location.href='".U('Uploadpic/index')."'</script>";
            }
        }else{
            echo"<script>
                alert('信息不完整');
                window.location.href='".U('Uploadpic/index')."'</script>";
        }
    }
}
    
    