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

/**
 * add by fjw in 19.7.2
 * 主要是添加新生儿信息
 */
namespace app\api\controller;

use app\common\controller\ControllerBase;

/**
 * newbaby 新生儿免疫平台 baby
 * 
 * 
 * 预定义保单信息列表
 * 预定义保单信息添加
 * 预定义保单信息详情
 * 预定义保单信息修改
 * 
 * 
 * 获取baby列表
 * 添加baby信息
 * 获取baby信息
 * 修改baby信息
 * 删除baby信息（未开发）
 */
class Baby extends ApiBase
{


    /**
     * =================== 新生儿疫苗系统 begin ====================
     */

    /**
     * create by fjw in 19.7.2
     * baby列表
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     */
    public function getBabyList(){

        return $this->apiReturn($this->logicBaby->getBabyList($this->param));

    }

    /**
     * create by fjw in 19.7.2
     * 获取baby信息 
     * 
     */

    public function getBabyInfo(){

        return $this->apiReturn($this->logicBaby->getBabyInfo($this->param)); 

    }

    /**
     * create by fjw in 19.7.2
     * 编辑baby信息
     * @param user_id: 用户id
     */
    public function editBabyInfo(){

        return $this->apiReturn($this->logicBaby->editBabyInfo($this->param));

    }

    /**
     * create by fjw in 19.7.2
     */
    public function editParentsInfo(){
        return $this->apiReturn($this->logicBaby->editParentsInfo($this->param));
    }

    /**
     * create by fjw in 19.7.2
     */
    public function editGuardianInfo(){
        return $this->apiReturn($this->logicBaby->editGuardianInfo($this->param));
    }

    




    /**
     * =================== 新生儿疫苗系统 end ====================
     */




    /**
     * create by fjw in 19.3.26
     * 预定义保单信息列表
     */
    public function getDefInsuranceInfoList(){

        return $this->apiReturn($this->logicBaby->getDefInsuranceInfoList($this->param));

    }

    /**
     * create by fjw in 19.3.26
     * 预定义保单信息添加
     */
    public function addDefInsuranceInfo(){

        return $this->apiReturn($this->logicBaby->addDefInsuranceInfo($this->param));

    }

    /**
     * create by fjw in 19.3.26
     * 预定义保单信息详情
     */
    public function getDefInsuranceInfo(){

        return $this->apiReturn($this->logicBaby->getDefInsuranceInfo($this->param));

    }

    /**
     * create by fjw in 19.3.26
     * 预定义保单信息修改
     */
    public function editDefInsuranceInfo(){
        return $this->apiReturn($this->logicBaby->editDefInsuranceInfo($this->param));

    }

/**  =================以下为宝宝信息========================    */



    

    

    /**
     * create by fjw in 19.3.18
     * 修改/编辑baby信息
     */
    // public function editBabyInfo(){

    //     return $this->apiReturn($this->logicBaby->editBabyInfo($this->param)); 

    // }

    /**
     * create by fjw in 19.3.18
     * 删除baby信息
     * @param user_id: 用户id
     */
    public function delBabyInfo(){



    }


}
