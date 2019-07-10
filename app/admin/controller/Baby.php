<?php

namespace app\admin\controller;

/**
 * Baby控制器
 */
class Baby extends AdminBase
{

    /**
     * 宝宝信息列表
     */
    public function babyList()
    {

    	$where = [];

    	$where = $this->logicBaby->getBabyWhere($this->param);

		$where[DATA_STATUS_NAME] = DATA_NORMAL;

    	$this->assign('list',$this->logicBaby->getBabyList($where));

    	return $this->fetch('baby_list');
    }

    /**
     * 查看宝宝详情
     */
    public function getBabyInfo($id = 0)
    {
        $this->assign('info',$this->logicBaby->getBabyInfo($id));

        return $this->fetch('baby_info');
    }

    /**
     * 删除宝宝信息
     */
    // public function babyDel($id = 0)
    // {
    // 	return $this->jump($this->logicBaby->babyDel(['id' => $id]));
    // }

}
