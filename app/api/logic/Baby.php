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

/**
 * baby 模块
 * 
 * 预定义保单信息列表
 * 预定义保单信息添加
 * 预定义保单信息详情
 * 预定义保单信息修改
 * 
 * 
 * 
 * 添加宝宝信息
 * 更新宝宝信息
 * 修改宝宝信息
 * 获取宝宝详情
 */
class Baby extends ApiBase
{

    /**
     * =================== 新生儿疫苗系统 begin ====================
     */

     /**
     * create by fjw in 19.7.2
     * 获取baby列表
     */
    public function getBabyList($data = []){

        $decoded_user_token = $data['decoded_user_token'];

        $where = ['uid'=>$decoded_user_token->user_id, 'status'=>1];

        $field = '
                id as baby_id, uid, unionid, birth_certificate_no, vaccine_certificate_no,
                full_name_of_baby, baby_sex, date_of_birth
            ';
        return $this->modelBaby->getList($where, $field, 'id', false);
    }

    /**
     * create by fjw in 19.7.2
     * 获取baby详情
     * 获取父母详情
     * 获取监护人信息
     */
    public function getBabyInfo($param = []){

        $decoded_user_token = $param['decoded_user_token'];

        $where = ['uid'=>$decoded_user_token->user_id, 'id'=>intval($param['baby_id']), 'status'=>1];

        return $this->separateAddress($this->modelBaby->getInfo($where));

    }

    


    /**
     * create by fjw in 19.3.18
     * 添加/修改baby信息
     */
    public function editBabyInfo($param = []){
        //@param $baby_id : 如果存在baby_id，则是修改
        $baby_id = isset($param['baby_id'])?$param['baby_id']:0;
        $decoded_user_token = $param['decoded_user_token'];
        // dump($decoded_user_token); die;
        // 1. 查询用户信息
        $user = $this->modelWxUser->getInfo([
            'id'=>$decoded_user_token->user_id,
            'user_id'=>$decoded_user_token->user_id,
        ]);
        $user_data = [
            'uid'=>$user['user_id'], 'wx_openid'=>$user['wx_openid'], 
            'app_openid'=>$user['app_openid'], 'unionid'=>$user['unionid']
        ];

        // 1. 验证信息
        $check = $this->modelBaby->checkBabyInfo($param);
        if($check['status'] == false) return [API_CODE_NAME => 41002, API_MSG_NAME => $check['msg']];
        
        // 2. 拼凑数据
        $calDateByBirthday = isset($param['date_of_birth'])?$this->modelBaby->calDateByBirthday($param['date_of_birth']):['vaccine_check_list'=>''];
        $baby_data = [
            'birth_certificate_no'=>isset($param['birth_certificate_no'])?$param['birth_certificate_no']:'',
            'vaccine_certificate_no'=>isset($param['vaccine_certificate_no'])?$param['vaccine_certificate_no']:'',
            'full_name_of_baby'=>isset($param['full_name_of_baby'])?$param['full_name_of_baby']:'',
            'baby_sex'=>intval($param['baby_sex']),
            'date_of_birth'=>$param['date_of_birth'],
            'place_of_birth'=>$param['place_of_birth'],
            'pregnant_week'=>floatval($param['pregnant_week']),
            'health_status'=>intval($param['health_status']),
            'baby_weight'=>floatval($param['baby_weight']),
            'baby_height'=>floatval($param['baby_height']),
            'birth_place_type'=>intval($param['birth_place_type']),
            'name_of_facility'=>$param['name_of_facility'],
            'vaccine_check_list'=>$calDateByBirthday['vaccine_check_list'] // 疫苗体检的提醒列表
        ];
        
        $data = array_merge($user_data, $baby_data);
        // dump($data); die;
        if($baby_id == 0){ // 新增
            // // 2. 校验数据
            // $validate = $this->modelBaby->validateBabyInfo($user_data, $param);
            // if($validate['status'] == false) return [API_CODE_NAME => 41003, API_MSG_NAME => $validate['msg']];
            if($this->modelBaby->setInfo($data)){
                $baby_id = $this->modelBaby->getLastInsID();
                $babyInfo = $this->separateAddress($this->modelBaby->getInfo(['id'=>$baby_id]));
                return ['babyInfo'=>$babyInfo];
            }else{
                return [API_CODE_NAME => 41004, API_MSG_NAME => '添加宝宝信息失败'];
            }
        }else{
            // // 2. 校验数据
            // $validate = $this->modelBaby->validateBabyInfo($user_data, $param);
            // if($validate['status'] == false) return [API_CODE_NAME => 41003, API_MSG_NAME => $validate['msg']];
            if($this->modelBaby->updateInfo(['id'=>$baby_id, 'uid'=>$decoded_user_token->user_id], $data)){
                $babyInfo = $this->separateAddress($this->modelBaby->getInfo(['id'=>$baby_id]));
                return ['babyInfo'=>$babyInfo];
            }else{
                return [API_CODE_NAME => 41004, API_MSG_NAME => '更新宝宝信息失败'];
            }
           

        }
        
    }

    /**
     * create by fjw in 19.3.18
     * 添加/修改baby的父母信息
     */
    public function editParentsInfo($param = []){
        //@param $baby_id : 如果存在baby_id，则是修改
        $baby_id = (isset($param['baby_id']) && $param['baby_id'] > 0)?$param['baby_id']:0;
        if($baby_id == 0) return [API_CODE_NAME => 41002, API_MSG_NAME => '获取baby信息失败'];
        $decoded_user_token = $param['decoded_user_token'];

        // 1. 验证信息
        $check = $this->modelBaby->checkParentsInfo($param);
        if($check['status'] == false) return [API_CODE_NAME => 41002, API_MSG_NAME => $check['msg']];
        
        // 2. 拼凑数据
        $parent_data = [
            'mother_name'=>$param['mother_name'],
            'mother_age'=>intval($param['mother_age']),
            'mother_nationality'=>$param['mother_nationality'],
            'mother_nation'=>$param['mother_nation'],
            'mother_id_no'=>$param['mother_id_no'],

            'father_name'=>$param['father_name'],
            'father_age'=>intval($param['father_age']),
            'father_nationality'=>$param['father_nationality'],
            'father_nation'=>$param['father_nation'],
            'father_id_no'=>$param['father_id_no'],

        ];

        if($this->modelBaby->updateInfo(['id'=>$baby_id, 'uid'=>$decoded_user_token->user_id], $parent_data)){
            $babyInfo = $this->separateAddress($this->modelBaby->getInfo(['id'=>$baby_id]));
                return ['babyInfo'=>$babyInfo];
        }else{
            return [API_CODE_NAME => 41004, API_MSG_NAME => '保存父母信息失败'];
        }

    }

    /**
     * create by fjw in 19.3.18
     * 添加/修改baby的父母信息
     */
    public function editGuardianInfo($param = []){
        //@param $baby_id : 如果存在baby_id，则是修改
        $baby_id = (isset($param['baby_id']) && $param['baby_id'] > 0)?$param['baby_id']:0;
        if($baby_id == 0) return [API_CODE_NAME => 41002, API_MSG_NAME => '获取baby信息失败'];
        $decoded_user_token = $param['decoded_user_token'];

        // 1. 验证信息
        $check = $this->modelBaby->checkGuardianInfo($param);
        if($check['status'] == false) return [API_CODE_NAME => 41002, API_MSG_NAME => $check['msg']];
        
        // 2. 拼凑数据
        $guardain_data = [
            'guardian_name'=>$param['guardian_name'],
            'guardian_mobile'=>intval($param['guardian_mobile']),
            'backup_mobile'=>$param['backup_mobile'],
            'relationship_to_baby'=>intval($param['relationship_to_baby']), // 监护人与宝宝关系 1：父子；2：母子；3：父女；4：母女
            'address'=>$param['province'].';'.$param['city'].';'.$param['area'].';'.$param['address'],

        ];

        if($this->modelBaby->updateInfo(['id'=>$baby_id, 'uid'=>$decoded_user_token->user_id], $guardain_data)){

            $babyInfo = $this->separateAddress($this->modelBaby->getInfo(['id'=>$baby_id]));
            return ['babyInfo'=>$babyInfo];
        }else{
            return [API_CODE_NAME => 41004, API_MSG_NAME => '保存监护人信息失败'];
        }

    }


    /**
     * add by fjw in 19.7.4
     * 处理address
     */
    public function separateAddress($babyInfo){

        $address = explode(';', $babyInfo['address']);
        $babyInfo['province'] = isset($address[0])?$address[0]:'';
        $babyInfo['city'] = isset($address[1])?$address[1]:'';
        $babyInfo['area'] = isset($address[2])?$address[2]:'';
        $babyInfo['address'] = isset($address[3])?$address[3]:'';

        return $babyInfo;
    }

    



    /**
     * =================== 新生儿疫苗系统 end ====================
     */





    /**
     * create by fjw in 19.3.26
     * 预定义保单信息列表
     */
    public function getDefInsuranceInfoList($param = []){

        $decoded_user_token = $param['decoded_user_token'];

        $where['user_id'] = $decoded_user_token->user_id;

        if(isset($param['baby_id']) && $param['baby_id'] > 0){
            $where['baby_id'] = $param['baby_id'];
        }

        return $this->modelDefineInsuranceInfo->getList($where, true, 'id', false);
    }

    /**
     * create by fjw in 19.3.26
     * 预定义保单信息添加
     */
    public function addDefInsuranceInfo($param = []){

        $decoded_user_token = $param['decoded_user_token'];

        $data = [
            'user_id'=>$decoded_user_token->user_id,
            'user_name'=>$param['user_name'],
            'user_id_card'=>$param['user_id_card'],
            'user_id_card_begintime'=>$param['user_id_card_begintime'],
            'user_id_card_endtime'=>$param['user_id_card_endtime'],
            'user_sex'=>intval($param['user_sex']),
            'user_age'=>intval($param['user_age']),
            'user_country'=>$param['user_country'],
            'user_address'=>$param['user_address'],
            'user_mobile'=>$param['user_mobile'],
            'relationship_to_baby'=>$param['relationship_to_baby'],
            
            'baby_id'=>$param['baby_id'],
            'baby_name'=>$param['baby_name'],
            'baby_id_card'=>$param['baby_id_card'],
            'baby_id_card_begintime'=>$param['baby_id_card_begintime'],
            'baby_id_card_endtime'=>$param['baby_id_card_endtime'],
            'baby_sex'=>intval($param['baby_sex']),
            'baby_age'=>intval($param['baby_age']),
            'baby_country'=>$param['baby_country'],
            'baby_address'=>$param['baby_address'],
            'relationship_to_user'=>$param['relationship_to_user'],
        ];
        // dump($data); die;
        return $this->modelDefineInsuranceInfo->setInfo($data);

    }

    /**
     * create by fjw in 19.3.26
     * 预定义保单信息详情
     */
    public function getDefInsuranceInfo($param = []){

        $decoded_user_token = $param['decoded_user_token'];
        $where['id'] = $param['def_id'];
        $where['user_id'] = $decoded_user_token->user_id;
        if(isset($param['baby_id'])){
            $where['baby_id'] = $param['baby_id'];
        }
        

        return $this->modelDefineInsuranceInfo->getInfo($where, true);
    }

    /**
     * create by fjw in 19.3.26
     * 预定义保单信息修改
     */
    public function editDefInsuranceInfo($param = []){

        $decoded_user_token = $param['decoded_user_token'];

        $where = ['id'=>$param['def_id'], 'user_id'=>$decoded_user_token->user_id,];
        if(isset($param['baby_id'])){
            $where['baby_id'] = $param['baby_id'];
        }

        $data = [
            // 'user_id'=>$decoded_user_token->user_id,
            'user_name'=>$param['user_name'],
            'user_id_card'=>$param['user_id_card'],
            'user_id_card_begintime'=>$param['user_id_card_begintime'],
            'user_id_card_endtime'=>$param['user_id_card_endtime'],
            'user_sex'=>$param['user_sex'],
            'user_age'=>$param['user_age'],
            'user_country'=>$param['user_country'],
            'user_address'=>$param['user_address'],
            'user_mobile'=>$param['user_mobile'],
            'relationship_to_baby'=>$param['relationship_to_baby'],
            
            // 'baby_id'=>$param['baby_id'],
            'baby_name'=>$param['baby_name'],
            'baby_id_card'=>$param['baby_id_card'],
            'baby_id_card_begintime'=>$param['baby_id_card_begintime'],
            'baby_id_card_endtime'=>$param['baby_id_card_endtime'],
            'baby_sex'=>$param['baby_sex'],
            'baby_age'=>$param['baby_age'],
            'baby_country'=>$param['baby_country'],
            'baby_address'=>$param['baby_address'],
            'relationship_to_user'=>$param['relationship_to_user'],
        ];

        return $this->modelDefineInsuranceInfo->updateInfo($where, $data);

    }





    
}
