<?php

namespace app\common\service\wechat\driver\wxgzh;
use think\Db;
class Wechat{

    public $wechat_config = array(
        // appid
        'appid' =>'',
        'appsecret' =>'',
        'original_id'=>'', // 
        'token'=>''
    );
    
    private $siteroot = 'http://xiaoai.fjwcoder.com/index.php/';

    
    

    public function __construct($param = []){
        $this->wechat_config['appid'] = $param['appid'];
        $this->wechat_config['appsecret'] = $param['appsecret'];
        $this->wechat_config['original_id'] = $param['original_id'];
        $this->wechat_config['token'] = $param['token'];

    }

    public function index(){
        if(!isset($_GET['echostr'])){
			$this -> responseMsg();
		}else{
			$this -> valid();//验证key
		}
    }
    /**
     * ########################################################################
     *  信息验证模块 create by fjw in 18.5.30
     * ########################################################################
     */
    public function valid()
    {
        $echoStr = $_GET['echostr'];
        if($this->checkSignature()){//调用验证签名checkSignature函数
        	echo $echoStr;
        	exit;
        }
    }

    private function checkSignature()
	{
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
		$tmpArr = array($this->wechat_config['token'], $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
    }


    /**
     * ########################################################################
     *  响应公众号事件/信息 create by fjw in 18.5.30
     * ########################################################################
     */
    public function responseMsg()
	{
        $postStr = file_get_contents('php://input');
        // file_put_contents('responsemsg.txt', $postStr);
        // $postStr = '<xml><ToUserName><![CDATA[gh_93c427bbf52e]]></ToUserName>
        //     <FromUserName><![CDATA[o20RC1RcDMBYPdwPkfP9dCXkJz0g]]></FromUserName>
        //     <CreateTime>1557732229</CreateTime>
        //     <MsgType><![CDATA[event]]></MsgType>
        //     <Event><![CDATA[subscribe]]></Event>
        //     <EventKey><![CDATA[]]></EventKey>
        //     </xml>';
		if (!empty($postStr))
		{
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$RX_TYPE = trim($postObj -> MsgType);
			switch($RX_TYPE)
			{
                case 'event':
                    $resultStr = $this -> handleEvent($postObj);
				break;
				case 'text':
					$resultStr = $this -> handleText($postObj);
				break;
				default:
					$resultStr = 'Unknow msg type: '.$RX_TYPE;
				break;
			}
			echo $resultStr;
		}else{
			echo "no user's post data";
		}
    }
    

/**
     * ########################################################################
     *  响应公众号 “事件消息” 方法 create by fjw in 18.5.30
     * ########################################################################
     */
    public function handleEvent($object){

        $openid = strval($object->FromUserName);
        // $registerObj = new Regist();
        $access_token = $this->access_token();
        $content = "";
        switch ($object->Event){
            case "subscribe": // ok by fjw in 18.6.2
                $wx_user_info_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
                $response = httpsGet($wx_user_info_url); 
                // $response = '{"subscribe":1,"openid":"o1rxE5miEDCbnSGIEKGemHBIdz0c","nickname":"9:30","sex":1,"language":"zh_CN","city":"青岛","province":"山东","country":"中国","headimgurl":"http:\/\/thirdwx.qlogo.cn\/mmopen\/Q3auHgzwzM5yKoMCXfVbnccyEJk4Picg8UzR8cjRz7ojKpdkccJ56icoRosSqxWOe9QIIGB8Mh3Fiaz4SNicuKzsKA\/132","subscribe_time":1562205597,"unionid":"oPC1q6Aul52AWBud40-bxcoEhdRQ","remark":"","groupid":0,"tagid_list":[],"subscribe_scene":"ADD_SCENE_QR_CODE","qr_scene":0,"qr_scene_str":""}';
                $user_info = json_decode($response, true);
                if($user_info){
                    $subscribe = $this->wxSubscribe($user_info);
                    $content .= $subscribe['msg'];

                }

            break;
            case 'unsubscribe': // 2018.9.13 增加 取消关注 by fjw
                Db::name('wx_user') -> where(['wx_openid'=>$openid]) -> update(['wx_subscribe'=>0]);
            break;
            case "CLICK":
                switch($object->EventKey){
                    case "xiaoaiyouxuan": // 我的推广， 完成 in 18.6.26
                        $content .= '提供宝宝的日常用品和营养品';
                    break;
                    case 'erbaotijian': // 每日签到, 完成 in 18.6.26
                        $content .= '宝宝儿保的体检提醒和预约';
                    break;
                    case 'yimiaobaoxian': // 每日签到, 完成 in 18.6.26
                        $content .= '宝宝接种疫苗的保险';
                    break;
                    case 'yimiaojiezhong': // 每日签到, 完成 in 18.6.26
                        $content .= '宝宝疫苗的接种提醒和预约';
                    break;
                    case 'baobaoxinxi': // 每日签到, 完成 in 18.6.26
                        $content .= '能够记录宝宝的各项信息';
                    break;
                    case 'jiezhongxinxi': // 每日签到, 完成 in 18.6.26
                        $content .= '记录宝宝疫苗的各项接种信息';
                    break;
                    case 'murujiance': // 每日签到, 完成 in 18.6.26
                        $content .= '检测母乳成分的各项营养指标';
                    break;
                    case 'kangtijiance': // 每日签到, 完成 in 18.6.26
                        $content .= '检测宝宝疫苗接种后的抗体情况';
                    break;
                    case 'tianshifuwu': // 每日签到, 完成 in 18.6.26
                        $content .= '能够提供客服性质的功能，解决宝宝成长期间的各项问题';
                    break;
                    default: 
                        $content .= " unknown ";
                    break;
                }
            break;
            case "VIEW":
                $content .= "跳转链接 ".$object->EventKey;
            break;
            case "SCAN": 
                $content .= "扫描场景 ";//.$object->EventKey;
            break;
            case "LOCATION":
                $content .= "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
            break;
            case "scancode_waitmsg":
                $content .= 'scancode_waitmsg';
            break;
            case "scancode_push": // 收货可以用这个，或者直接微信扫码
                $content .= "扫码推事件";
            break;
            case "pic_sysphoto":
                $content .= "系统拍照";
            break;
            case "pic_weixin":
                $content .= "相册发图：数量 ".$object->SendPicsInfo->Count;
            break;
            case "pic_photo_or_album":
                $content .= "拍照或者相册：数量 ".$object->SendPicsInfo->Count;
            break;
            case "location_select":
                $content .= "发送位置：标签 ".$object->SendLocationInfo->Label;
            break;
            default:
                $content .= "receive a new event: ".$object->Event;
            break;
        }

        $result = $this->transmitText($object, $content);
        return $result;
    }

    public function access_token(){

        $access_token = Db::name('wx_config') -> where(['name'=>'wx_access_token']) -> find();
        $access_token = json_decode($access_token['value'], true);
        if($access_token['end_time'] > time()){
            return $access_token['access_token'];
        }else{
            $request_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->wechat_config['appid'].'&secret='.$this->wechat_config['appsecret'];
            $response = httpsGet($request_url);
            $response = json_decode($response, true);
            $data = ['access_token'=>$response['access_token'], 'end_time'=>time()+7100];
            Db::name('wx_config') -> where(['name'=>'wx_access_token']) -> update(['value'=>json_encode($data)]);
            return $data['access_token'];
        }
        
    }

    public function wxSubscribe($param = []){
        // 1. 检查是否存在
        $user = Db::name('wx_user') -> where(['unionid'=>$param['unionid']]) -> find();
        $data = [
            'nickname'=>$param['nickname'],
            'sex'=>$param['sex'],
            'headimgurl'=>$param['headimgurl'],
            'unionid'=>isset($param['unionid'])?$param['unionid']:'',
            'wx_subscribe'=>$param['subscribe'],
            'wx_openid'=>$param['openid'],
            'wx_language'=>$param['language'],
            'city'=>$param['city'],
            'province'=>$param['province'],
            'country'=>$param['country'],

            'wx_groupid'=>$param['groupid'],
            'wx_tagid_list'=>$param['tagid_list']
        ];
        if($user){
            $data['wx_subscribe_time'] = $param['subscribe_time'];
            $result = Db::name('wx_user') -> where(['unionid'=>$param['unionid']]) -> update($data);
            return ['status'=>true, 'code'=>2, 'msg'=>'欢迎回来~'];
        }else{

            $data['wx_subscribe_time'] = $param['subscribe_time'];
            $data['wx_subscribe_scene'] = $param['subscribe_scene'];
            $data['wx_qr_scene'] = $param['qr_scene'];
            $data['wx_qr_scene_str'] = $param['qr_scene_str'];

            $result = Db::name('wx_user')  -> insert($data);
            if($result){
                $lastID = Db::name('wx_user') ->getLastInsID();
                $result = Db::name('wx_user') -> where(['unionid'=>$param['unionid']]) -> update(['user_id'=>$lastID]);
                return ['status'=>true, 'code'=>1, 'msg'=>'欢迎来到新生儿免疫平台'];
            }else{
                return ['status'=>false, 'msg'=>'获取信息失败，请重新关注公众号~'];
            }
            
        }

        
        
    }

    /**
     * ########################################################################
     *  回复文本消息 create by fjw in 18.5.30
     * ########################################################################
     */
    private function transmitText($object, $content)
    {
        if (!isset($content) || empty($content)){
            return "";
        }
        
        $xmlTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
			       </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);

        return $result;
    }

    /**
     * ########################################################################
     *  发送模板消息 create by fjw in 18.5.30
     * ########################################################################
     */
    public function templateRedirectUrl($remind){
        $redirect_url = $this->siteroot;

        $yunqijiance_url = $redirect_url.'wechat/login?c=pregnant&a=check';
        $erbaotijian_url = $redirect_url.'wechat/login?c=kids&a=kidslist';
        $jiezhongtixing_url = $redirect_url.'wechat/login?c=kids&a=kidslist';
        $murujiance_url = $redirect_url.'wechat/login?c=pregnant&a=breastmilkcheck';

        switch($remind){
            case 'pregnant':
                return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($yunqijiance_url).'&response_type=code&scope=snsapi_base&state=1#wechat_redirect'; //$redirect_url.'pregnant/check',
            break;
            case 'kids':
                return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($erbaotijian_url).'&response_type=code&scope=snsapi_base&state=1#wechat_redirect'; //$redirect_url.'pregnant/check',
            break;
            case 'vaccine':
                return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($jiezhongtixing_url).'&response_type=code&scope=snsapi_base&state=1#wechat_redirect'; //$redirect_url.'pregnant/check',
            break;
            default: return '';
        }
    }
    // public function sendTemplateMsg(){
    //     // 诊疗计划提醒
    //     $template_id = 'OPENTM201438585';
    //     // http请求方式: POST
    //     $send_url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->access_token();
    //     return ['template_id'=>$template_id, 'send_url'=>$send_url];

        
        
    // }

    

    /**
     * 网页授权 add by fjw in 19.5.20
     */
    public function webAuth($code){
        $web_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wechat_config['appid'].'&secret='.$this->wechat_config['appsecret'].'&code='.$code.'&grant_type=authorization_code';
        $open_res = httpsGet($web_url); //则本步骤中获取到网页授权access_token的同时，也获取到了openid，snsapi_base式的网页授权流程即到此为止。
        $open_arr = json_decode($open_res, true);
        return isset($open_arr['openid'])?$open_arr['openid']:'';
    
    }


    public function menuDIY(){
        $miniprogram_appid = 'wxb31a31b942906cc3';
        $out_redirect_url = 'http://xiaoai.fjwcoder.com/';
        $menu = [
            'button'=>[
                [
                    'name'=>'宝宝家园',
                    'sub_button'=>[
                        [
                            'type'=>'miniprogram',
                            "name"=>"宝宝建档",
                            "url"=>"http://mp.weixin.qq.com",
                            "appid"=>$miniprogram_appid,
                            "pagepath"=>"pages/new/index"
                        ],
                        [
                            "type"=>"miniprogram",
                            "name"=>"宝宝列表",
                            "url"=>"http://mp.weixin.qq.com",
                            "appid"=> $miniprogram_appid,
                            "pagepath"=>"pages/user/myBabyList"
                        ],
                    ]
                ],
                [
                    'type'=>'miniprogram',
                    'name'=>'接种提醒',
                    "url"=>"http://mp.weixin.qq.com",
                    "appid"=>$miniprogram_appid,
                    "pagepath"=>"pages/user/myBabyList"
                    
                ],
                [
                    'name'=>'妈咪助手',
                    'sub_button'=>[
                        [
                            'type'=>'view', 'name'=>'宝妈配方',
                            'url'=> $out_redirect_url.'article/category?cid=5',
                        ],
                        [
                            'type'=>'view', 'name'=>'天使配方',
                            'url'=> $out_redirect_url.'article/category?cid=1',
                        ],
                        [
                            'type'=>'view','name'=>'健康知识',
                            'url'=> $out_redirect_url.'article/category?cid=2',
                        ]
                    ]
                ]
                
            ]
        ];
        // dump(json_encode($menu, JSON_UNESCAPED_UNICODE)); die;
        $wx_menu_url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token();
        $response = httpsPost($wx_menu_url, json_encode($menu, JSON_UNESCAPED_UNICODE));
        dump($response); die;


    }

}

