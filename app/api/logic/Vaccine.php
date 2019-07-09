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

class Vaccine extends ApiBase
{

    /**
     * =================== 新生儿疫苗系统 begin ====================
     */

    /**
     * 获取宝宝的接种提醒
     */
    public function getBabyVaccineRemind($param = []){

        $baby_id = (isset($param['baby_id']) && $param['baby_id'] > 0)?$param['baby_id']:0;
        if($baby_id == 0) return [API_CODE_NAME => 41002, API_MSG_NAME => '获取baby信息失败'];
        
        $decoded_user_token = $param['decoded_user_token'];

        $babyinfo = $this->modelBaby->getInfo(['id'=>$baby_id, 'uid'=>$decoded_user_token->user_id]);
        if(empty($babyinfo) || !isset($babyinfo['vaccine_check_list'])) return [API_CODE_NAME => 41002, API_MSG_NAME => '获取baby信息失败'];

        $vaccine_check = $this->modelBaby->calDateByBirthday($babyinfo['date_of_birth']);
        $vaccine_check_list = array_filter(explode(';', $vaccine_check['vaccine_check_list']));

        $vaccine_check_info = [];
        foreach($vaccine_check['vaccine_check_info'] as $k=>$v){
            $temp = $v->getData();
            $vaccine_check_info[$vaccine_check_list[$k]] = strval($temp['week']);
        }

        return ['remind_days'=>array_keys($vaccine_check_info), 'vaccine_days'=>array_values($vaccine_check_info)];
    
    }

    /**
     * 根据周数，获取疫苗信息
     * 
     */
    public function getVaccineInfoByWeek($param = []){
        $week = isset($param['week'])?$param['week']:0;
        $vaccine = $this->modelVaccine->getList(['week'=>$week, 'status'=>1], 'ym_id, ym_name, week, ym_text, ym_yongtu', 'ym_id', false);
        // dump($vaccine); die;
        return $vaccine; 
    }



    /**
     * =================== 新生儿疫苗系统 end ====================
     */


    
}
