<?php

namespace app\admin\controller;

/**
 * InjectRecord控制器
 */
class InjectRecord extends AdminBase
{

    /**
     * 接种疫苗注入记录列表
     */
    public function injectRecordList()
    {

    	$where = [];
    	
    	$where = $this->logicInjectRecord->getInjectRecordWhere($this->param);

    	$where[DATA_STATUS_NAME] = DATA_NORMAL;

    	$this->assign('list',$this->logicInjectRecord->getInjectRecordList($where));

    	return $this->fetch('inject_record_list');
    }

    /**
     * 查看接种疫苗注入详情
     */
    public function getInjectRecordInfo($id = 0)
    {
        $this->assign('info',$this->logicInjectRecord->getInjectRecordInfo($id));

        return $this->fetch('inject_record_info');
    }

    /**
     * 接种疫苗记录删除
     */
    // public function injectRecordDel($id = 0)
    // {
    // 	return $this->jump($this->logicInjectRecord->injectRecordDel(['id' => $id]));

    // }


}
