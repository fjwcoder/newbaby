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

namespace app\api\controller;

use app\common\controller\ControllerBase;

/**
 * newbaby 新生儿免疫平台 疫苗
 * 疫苗预约
 * 疫苗预约记录
 * 抗体检测记录
 */
class Vaccine extends ApiBase
{

    /**
     * =================== 新生儿疫苗系统 begin ====================
     */


    /**
     * 获取宝宝的接种提醒
     */
    public function getBabyVaccineRemind(){
        return $this->apiReturn($this->logicVaccine->getBabyVaccineRemind($this->param));
    }

    /**
     * 获取疫苗信息
     */
    public function getVaccineInfo(){
        return $this->apiReturn($this->logicVaccine->getVaccineInfoByWeek($this->param));
    }

    





    /**
     * =================== 新生儿疫苗系统 end ====================
     */


}
