<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | fjwcoder<fjwcoder@gmail.com>                           |
// +---------------------------------------------------------------------+
// | Repository |                   |
// +---------------------------------------------------------------------+

namespace app\api\logic;

use app\api\error\Common as CommonError;


class Inject extends ApiBase
{
    
    /**
     * =================== 新生儿疫苗系统 begin ====================
     */

    protected $inject_body_part = [
        1=>'左上臂',
        2=>'右上臂',
        3=>'口服',
        4=>'左臀部', 
        5=>'右臀部',
        6=>'左大腿',
        7=>'右大腿',
    ];

     /**
     * create by fjw in 19.7.2
     * 获取baby列表
     */
    public function getBabyInjectList($param = []){

        //@param $baby_id : 如果存在baby_id，则是修改
        $baby_id = (isset($param['baby_id']) && $param['baby_id'] > 0)?$param['baby_id']:0;
        if($baby_id == 0) return [API_CODE_NAME => 41002, API_MSG_NAME => '获取baby信息失败'];

        $decoded_user_token = $param['decoded_user_token'];

        // 1. 查询baby信息
        $baby_info = $this->logicBaby->getBabyInfo($param);
        // dump($baby_info); die;
        // 2. 查询接种列表
        $where = ['uid'=>$decoded_user_token->user_id, 'status'=>1];
        $field = '
            id as inject_id, uid, baby_id, vaccine_name,
            inject_date
        ';
        $inject_list = $this->modelInjectRecord->getList($where, $field, 'id desc', false);
        $return = ['baby_info'=>$baby_info, 'inject_list'=>$inject_list];
        $remind = $this->logicVaccine->getBabyVaccineRemind($param);
        
        if(is_array($remind)){
            $return = array_merge($return, $remind);
        }
        return $return;
    }

    /**
     * create by fjw in 19.7.2
     */
    public function editBabyInjectInfo($param = []){
        //@param $baby_id : 如果存在baby_id，则是修改
        $baby_id = (isset($param['baby_id']) && $param['baby_id'] > 0)?$param['baby_id']:0;
        if($baby_id == 0) return [API_CODE_NAME => 41002, API_MSG_NAME => '获取baby信息失败'];

        $inject_id = isset($param['inject_id'])?$param['inject_id']:0;

        $decoded_user_token = $param['decoded_user_token'];
        
        $vaccine_factory = isset($param['vaccine_factory'])?$param['vaccine_factory']:'';
        $vaccine_branch_no = isset($param['vaccine_branch_no'])?$param['vaccine_branch_no']:'';

        $data = [
            'uid'=>$decoded_user_token->user_id,
            'baby_id'=>intval($baby_id),
            'vaccine_name'=>$param['vaccine_name'],
            'vaccine_info'=>$vaccine_factory.'-'.$vaccine_branch_no,
            'inject_body_part'=>intval($param['inject_body_part']), //$this->inject_body_part[$param['inject_body_part']],
            'inject_date'=>isset($param['inject_date'])?$param['inject_date']:'', //date('Y-m-d', time()),
            'reaction'=>$param['reaction'],
        ];
// dump($data); die;
        if($inject_id == 0){ // 新增
            if($this->modelInjectRecord->setInfo($data)){
                return true;
            }else{
                return [API_CODE_NAME => 41004, API_MSG_NAME => '添加接种信息失败'];
            }
        }else{ // 修改
            if($this->modelInjectRecord->updateInfo(['id'=>$inject_id, 'uid'=>$decoded_user_token->user_id], $data)){
                return true;
            }else{
                return [API_CODE_NAME => 41004, API_MSG_NAME => '更新接种信息失败'];
            }
        }
        
    }

    /**
     * create by fjw in 19.7.3
     */
    public function getBabyInjectInfo($param = []){
        //@param $baby_id : 如果存在baby_id，则是修改
        $baby_id = (isset($param['baby_id']) && $param['baby_id'] > 0)?$param['baby_id']:0;
        if($baby_id == 0) return [API_CODE_NAME => 41002, API_MSG_NAME => '获取baby信息失败'];

        $inject_id = (isset($param['inject_id']) && $param['inject_id'] > 0)?$param['inject_id']:0;
        if($inject_id == 0) return [API_CODE_NAME => 41002, API_MSG_NAME => '获取接种信息失败'];

        $decoded_user_token = $param['decoded_user_token'];

        $where = [
            'id'=>intval($inject_id), 
            'uid'=>$decoded_user_token->user_id, 
            'baby_id'=>$baby_id
        ];
// dump($where); die;
        $info = $this->modelInjectRecord->getInfo($where);
        $vaccine_info = explode('-', $info['vaccine_info']);
        $info['vaccine_factory'] = $vaccine_info[0];
        $info['vaccine_branch_no'] = $vaccine_info[1];
        // dump($info); die;
        return $info;

    }






    



    /**
     * =================== 新生儿疫苗系统 end ====================
     */





  




    
}
