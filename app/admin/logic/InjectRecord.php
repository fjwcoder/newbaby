<?php

namespace app\admin\logic;

/**
 * InjectRecord逻辑
 */
class InjectRecord extends AdminBase
{
    

    /**
     * 接种疫苗注入记录列表
     */
    public function getInjectRecordList($where = [])
    {
        $paginate = 20;
        return $this->modelInjectRecord->getList($where, true, 'inject_date desc', $paginate);
    }


    /**
     * 获取接种疫苗列表搜索条件
     */
    public function getInjectRecordWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['baby_id|vaccine_name'] = ['like', '%'.$data['search_data'].'%'];
        
        return $where;
    }

    /**
     * 查看接种疫苗详情
     */
    public function getInjectRecordInfo($id)
    {
        return $this->modelInjectRecord->getInfo(['id' => $id]);
    }

    /**
     * 接种疫苗记录删除
     */
    // public function injectRecordDel($where = [])
    // {
    //     $url = url('injectRecordList');
        
    //     $result = $this->modelInjectRecord->deleteInfo($where);
                
    //     $result && action_log('删除', '删除接种疫苗记录，where：' . http_build_query($where));
        
    //     return $result ? [RESULT_SUCCESS, '接种疫苗记录删除成功', $url] : [RESULT_ERROR, $this->modelInjectRecord->getError(), $url];

    // }
    
 
}
