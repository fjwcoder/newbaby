<?php

namespace app\admin\logic;

/**
 * WxUser逻辑
 */
class WxUser extends AdminBase
{
    
    /**
     * 微信用户列表
     */
    public function getWxUserList($where = [])
    {
        $paginate = 20;
        return $this->modelWxUser->getList($where, true, '', $paginate);
    }


    /**
     * 获取微信用户列表搜索条件
     */
    public function getWxUserWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['nickname'] = ['like', '%'.$data['search_data'].'%'];
        
        return $where;
    }

    /**
     * 查看微信用户详情
     */
    public function getWxUserInfo($id)
    {
        return $this->modelWxUser->getInfo(['id' => $id]);
    }
    
    /**
     * 微信用户删除
     */
    // public function wxUserDel($where = [])
    // {
    //     $url = url('wxUserList');

    //     $result = $this->modelWxUser->deleteInfo($where);
                
    //     $result && action_log('删除', '删除微信用户，where：' . http_build_query($where));
        
    //     return $result ? [RESULT_SUCCESS, '微信用户删除成功', $url] : [RESULT_ERROR, $this->modelWxUser->getError(), $url];
    // }
 
}
