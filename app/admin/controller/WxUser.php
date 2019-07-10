<?php

namespace app\admin\controller;

/**
 * WxUser控制器
 */
class WxUser extends AdminBase
{
    /**
     * 微信用户列表
     */
    public function wxUserList()
    {
    	$where = [];
    	
    	$where = $this->logicWxUser->getWxUserWhere($this->param);

    	$where[DATA_STATUS_NAME] = DATA_NORMAL;

        $this->assign('list', $this->logicWxUser->getWxUserList($where));
        
        return $this->fetch('wxuser_list');
    }

    /**
     * 查看接种疫苗注入详情
     */
    public function getWxUserInfo($id = 0)
    {
        $this->assign('info',$this->logicWxUser->getWxUserInfo($id));

        return $this->fetch('wxuser_info');
    }


    /**
     * 微信用户删除
     */
    // public function wxUserDel($id = 0)
    // {
    //     return $this->jump($this->logicWxUser->wxUserDel(['id' => $id]));
    // }

}
