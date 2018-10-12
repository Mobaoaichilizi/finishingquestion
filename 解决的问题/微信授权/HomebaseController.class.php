<?php
// +----------------------------------------------------------------------
// | APi公共文件
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 https://www.lovezhuan.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Liuguoqiang <415199201@qq.com>
// +----------------------------------------------------------------------
namespace Common\Controller;
use Common\Controller\BaseController;
class HomebaseController extends BaseController {
   public function __construct() {
		parent::__construct();
		$time=time();
		$this->assign("js_debug",APP_DEBUG?"?v=$time":"");
   }
   function _initialize(){
		 parent::_initialize();
		 $map    = array('status' => 1);
			$data   = M('Config')->where($map)->field('type,name,value')->select();
			$config = array();
			if($data && is_array($data)){	
				foreach ($data as $value) {
						$config[$value['name']] = $value['value'];
				}
			}
		 C($config); //添加配置
		 $host='http://'.$_SERVER['HTTP_HOST'];////////
		 $this->assign('host',$host);
		 
		Vendor('jssdk');
		$wx_jssdk_every = new \JSSDK(C('WX_APPID'),C('WX_APPSECRET'));
		$signPackage_every = $wx_jssdk_every->GetSignPackage();
		$this->assign('signPackage_every',$signPackage_every);
		 
		 if($_SESSION['wx_user']=='')
		 {
		 if(!isset($_GET['code']))
		 {
		 $customeUrl=$host.$_SERVER['REQUEST_URI'];
		 $scope='snsapi_userinfo';
		 $oauthUrl='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.C('WX_APPID').'&redirect_uri='.urlencode($customeUrl).'&response_type=code&scope='.$scope.'&state=oauth#wechat_redirect';
		 header('Location:'.$oauthUrl);
		 exit;
		 }
		 $customeUrl=$host.$_SERVER['REQUEST_URI'];
		 $rt=curlGet('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.C('WX_APPID').'&secret='.C('WX_APPSECRET').'&code='.$_GET['code'].'&grant_type=authorization_code');
		 $jsonrt=json_decode($rt,1);
		 $openid=$jsonrt['openid'];
		 $access_token=$jsonrt['access_token'];
		 $user_rt=curlGet('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
		 $user_jsonrt=json_decode($user_rt,1);
		 $member=M('member');
		 $count=$member->where("openid='".$user_jsonrt['openid']."'")->count();
		 if($count > 0)
		 {
			$result=$member->where("openid='".$user_jsonrt['openid']."'")->find();
			$_SESSION['wx_user']=$openid;
			$_SESSION['user_id']=$result['id'];
		 }else
		 {
			unset($data);
			$data['nickname']= $user_jsonrt['nickname'];
			$data['openid']= $user_jsonrt['openid'];
			$data['head_img']= $user_jsonrt['headimgurl'];
			$data['sex']= $user_jsonrt['sex'];
			$data['province']= $user_jsonrt['province'];
			$data['city']= $user_jsonrt['city'];
			$data['country']= $user_jsonrt['country'];
			$data['createtime']= time();
			$data['balance']=C('SCORE_REG');
			if($data['openid']!='')
			{
				
				
				$member_id=$member->add($data);
				bill_log($member_id,1,0,0,$data['balance'],'新用户首次授权登录');
				$get_openid=I('get.openid');
				if($get_openid!='')
				{
					$opend_id=$member->where("openid='".$get_openid."'")->find();
					if($opend_id)
					{
						$member->where("id=".$member_id)->save(array('prev_id'=>$opend_id['id']));
					}
				}
				$_SESSION['wx_user']=$openid;
				$_SESSION['user_id']=$member_id;
			}
		 } 
		 } 	 
	   }
}
?>