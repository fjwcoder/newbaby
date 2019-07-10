<?php

namespace app\admin\logic;

/**
 * Baby逻辑
 */
class Baby extends AdminBase
{
    

    /**
     * baby列表
     */
    public function getBabyList($where = [] )
    {
        $paginate = 20;
        return $this->modelBaby->getList($where, true, '', $paginate);
    }

    
    /**
     * 获取baby列表搜索条件
     */
    public function getBabyWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['full_name_of_baby|mother_name|father_name'] = ['like', '%'.$data['search_data'].'%'];
        
        return $where;
    }

    /**
     * 查看baby详情
     */
    public function getBabyInfo($id)
    {
        return $this->modelBaby->getInfo(['id' => $id]);
    }


    /**
     * 删除宝宝
     */
    // public function babyDel($where = [])
    // {
    //     $url = url('babyList');
        
    //     $result = $this->modelBaby->deleteInfo($where);
                
    //     $result && action_log('删除', '删除宝宝，where：' . http_build_query($where));
        
    //     return $result ? [RESULT_SUCCESS, '宝宝删除成功', $url] : [RESULT_ERROR, $this->modelBaby->getError(), $url];

    // }

    
 
}
