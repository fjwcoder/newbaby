<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | fjwcoder<fjwcoder@gmail.com>                           |
// +---------------------------------------------------------------------+
// | Repository |   |
// +---------------------------------------------------------------------+

namespace app\api\model;
use think\Db;

/**
 * add by fjw in 19.7.2
 * 新生儿疫苗平台
 */
class Baby extends ApiBase
{
    
    /**
     * 检查参数信息
     */
    public function checkBabyInfo($param){
        if(empty($param['baby_sex'])) return ['status'=>false, 'msg'=>'baby性别信息错误'];
        if(empty($param['date_of_birth'])) return ['status'=>false, 'msg'=>'出生日期信息错误'];
        if(empty($param['place_of_birth'])) return ['status'=>false, 'msg'=>'出生地点信息错误'];
        if(empty($param['pregnant_week'])) return ['status'=>false, 'msg'=>'孕周信息错误'];
        if(empty($param['health_status'])) return ['status'=>false, 'msg'=>'健康状况信息错误'];
        if(empty($param['baby_weight'])) return ['status'=>false, 'msg'=>'出生体重信息错误'];
        if(empty($param['baby_height'])) return ['status'=>false, 'msg'=>'出生身高信息错误'];
        if(empty($param['birth_place_type'])) return ['status'=>false, 'msg'=>'出生地类型信息错误'];
        if(empty($param['name_of_facility'])) return ['status'=>false, 'msg'=>'出生机构信息错误'];
        return ['status'=>true];
    }
    /**
     * 检查参数信息
     */
    public function checkParentsInfo($param){
        if(empty($param['mother_name'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['mother_age'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['mother_nationality'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['mother_nation'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['mother_id_no'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        
        if(empty($param['father_name'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['father_age'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['father_nationality'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['father_nation'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['father_id_no'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        return ['status'=>true];
    }
    /**
     * 检查参数信息
     */
    public function checkGuardianInfo($param){
        if(empty($param['guardian_name'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['guardian_mobile'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['backup_mobile'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['relationship_to_baby'])) return ['status'=>false, 'msg'=>'参数信息错误'];

        if(empty($param['province'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['city'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['area'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        if(empty($param['address'])) return ['status'=>false, 'msg'=>'参数信息错误'];
        return ['status'=>true];
    }

    /**
     * 根据出生日期计算体检时间表和疫苗时间表
     */
    public function calDateByBirthday($birthday){
        // 儿保体检
        // $kids_check_info = $this->modelArticle->getList(['category_id'=>$this->check_category_id, 'status'=>1], 'name, week', 'id', false);
        $vaccine_check_info = $this->modelVaccine->getList(['status'=>1], 'ym_name as name, week', 'week', false);
        $begin_time = strtotime($birthday);
        $week_second = 604800; // 每周的秒数
        // $kids_check_list = '';
        $vaccine_check_list = '';
        // foreach($kids_check_info as $k=>$v){
        //     $time = $begin_time + $week_second * $v['week'];
        //     $kids_check_list .= date('Y-m-d', $time).';';
        // }
        foreach($vaccine_check_info as $k=>$v){
            $time = $begin_time + $week_second * $v['week'];
            $vaccine_check_list .= date('Y-m-d', $time).';';
        }
        
        // return ['kids_check_list'=>$kids_check_list, 'vaccine_check_list'=>$vaccine_check_list];
        return ['vaccine_check_info'=>$vaccine_check_info, 'vaccine_check_list'=>$vaccine_check_list];
    }


    /**
     * 验证信息
     * 如果存在
     */
    public function validateBabyInfo($user = [], $param = []){
        return ['status'=>true];
        $user_where = ['uid'=>$user['uid'], 'unionid'=>$user['unionid']];
        // dump($user_where); die;
        
        return ['status'=>true];
    }

}
