<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//+-------------------------------
//+ 系统菜单模型类文件:system_menu_model.php;
//+-------------------------------

class Message_model extends CI_Model {


    /**
    * 定义数据库表名
    */
    private $_messageTable = MESSAGE;
    private $_userTable = USER;



    /**
    * 构造函数
    */
    public function __construct(){
        parent::__construct();
        $this->_messageTable = $this->db->dbprefix(MESSAGE);
        $this->_userTable = $this->db->dbprefix(USER);

    }


    public function insert($info){

        $res = $this->db->insert($this->_messageTable,$info);
        if($res){
            return $this->db->insert_id();
        }else{
            return false;
        }
    }


    /**
     * 更新用户
     */
    public function update($where,$info){
        if(empty($where)){
            return false;
        }
        if(!empty($where['idIn'])){
            $this->db->where_in('id',$where['idIn']);
            unset($where['idIn']);
        }

        $res = $this->db->update($this->_messageTable,$info,$where);
//        echo $this->db->last_query();
        return $res;
    }

    /**
     * 更新用户
     */
    public function updateByUid($uid,$info){
        if(empty($uid)){
            return false;
        }
        return $this->db->update($this->_messageTable,$info,array('uid'=>$uid));
    }

    public function getInfoById($id){
        $field = "*";
        $sql = "SELECT $field FROM {$this->_messageTable} WHERE id={$id}";
        $query = $this->db->query($sql);
        $res =  $query->row_array();
        return $res;
    }


    /**
     * 获取我的消息列表
     */
    public function getList($data,$offset,$num,$order=''){
        $field = "*";
        $where = " WHERE type not in (5,6) AND is_del = 0 ";

        if(is_array($data) && !empty($data)){
            foreach($data AS $key => $val){
                if(!in_array($key,array('page','limit','order'))){
                    $where .= " AND `" . $key . "`='" . $val . "'";
                }
            }
        }


        if(!empty($order)){
            $order_str = $order;
        }else{
            $order_str = ' ORDER BY add_time desc '; //默认时间倒叙
        }


        $result = array();
        $sql = "SELECT {$field} FROM {$this->_messageTable} {$where} {$order_str} LIMIT {$offset}, {$num};";
        $query = $this->db->query($sql);
        if($query !== FALSE){
            $_list = $query->result_array();
            if(!empty($_list) && is_array($_list)){
                //加载user_model
                $CI = &get_instance();
                $CI->load->model('user_model');
                $CI->load->model('charity_model');
                $CI->load->model('business_model');


                foreach($_list as $k=>&$v){
                    switch($v['type']){
                        case 2:  //点赞
                            //查询头像 todo;
                            $res_uid = $CI->user_model->getInfoByUid($v['s_uid']);
                            $v['logo'] = !empty($res_uid['avatar'])? $res_uid['avatar'] : '';
                            $v['click_url'] = '';
                            $v['click_msg'] = '';
                            $v['content'] = str_replace('{name}',$res_uid['name'],$v['content']);
                            break;
                        case 3:  //慈善项目
                            //查询企业logo todo;
                            $res_charity = $CI->charity_model->getRow(array('id'=>$v['s_uid']));
                            $v['click_msg'] = '';
                            if(!empty($res_charity['b_id'])){
                                $res_business = $CI->business_model->getRow(array('id'=>$res_charity['b_id']));
                                if(!empty($res_business) && $res_business['status'] == 0){  //已下架的不能进入项目详情
                                    $v['click_msg'] = '项目已下架'; //todo
                                }
                            }

                            $v['logo'] = !empty($res_business['logo']) ? OSS_URL.$res_business['logo'] : '';
                            $v['click_url'] = M_URL.'/charity/charity_view?id='.$v['s_uid'];
                            $v['content'] = str_replace('{charity_name}',$res_charity['name'],$v['content']);
                            break;
                        case 4: //系统消息
                            //默认头像 益起 todo;
                            $v['logo'] = YI_LOGO_URL;
                            $v['click_url'] = $v['link'];
                            $v['click_msg'] = '';
                            break;
                        case 11 ://邀请加入跑团
                        case 13 ://转让给你做团长
                            $CI->load->model('team_model');

                            $v['click_url'] = 'yiplayapp://team/'.$v['team_id'];

                            $res_team = $CI->team_model->getRow(array('id'=>$v['team_id']));
                            $v['click_msg'] = '';
                            if(!empty($res_team) && $res_team['status'] == 1){  //已下架的不能进入项目详情
                                $v['click_msg'] = '跑团已解散'; //todo
                            }
                            if($v['type'] == 11){
                                //查询头像 todo;
                                $res_uid = $CI->user_model->getInfoByUid($v['s_uid']);
                                $v['logo'] = !empty($res_uid['avatar'])? $res_uid['avatar'] : '';
                                $v['content'] = str_replace('{name}',$res_uid['name'],$v['content']);
                            }else{
                                $v['logo'] = !empty($res_team['logo'])? $res_team['logo'].'?x-oss-process=image/resize,w_150' : '';
                            }

                            $v['content'] = str_replace('{team}',$res_team['name'],$v['content']);
                            break;
                        case 12 : //被移除跑团
                        case 14 : //跑团解散
                            $CI->load->model('team_model');
                            //查询头像 todo;
                            $v['click_url'] = '';  //不可点击
                            $v['click_msg'] = '';
                            $res_team = $CI->team_model->getRow(array('id'=>$v['team_id']));
                            $v['logo'] = !empty($res_team['logo'])? $res_team['logo'].'?x-oss-process=image/resize,w_150' : '';
                            $v['content'] = str_replace('{team}',$res_team['name'],$v['content']);
                            break;

                    }
                    unset($v['link']);
                    unset($v['is_del']);
                    unset($v['open_id']);
                }

                unset($v);
            }

            $result['list'] = $_list;

            $cntQuery = $this->db->query("SELECT COUNT(*) AS cnt FROM {$this->_messageTable} {$where};");
            $cntRow = $cntQuery->row_array();
            $result['cnt'] = $cntRow['cnt'];
            return $result;
        }else{
            return FALSE;
        }
    }



    /**
     * 加入黑名单
     * @param $ids  逗号隔开
     */
    public function deleteByIds($ids){

    }

    public function updateById($id,$info){
        return $this->db->update($this->_messageTable,$info,array('id'=>$id));
    }

    //获取用户信息getInfoByUid
    public function getInfoByUid($uid){
        $field = "*";
        $sql = "SELECT $field FROM {$this->_messageTable} WHERE uid={$uid}";
        $query = $this->db->query($sql);
        $res =  $query->row_array();
        return $res;
    }

    //获取用户信息getInfoByUid
    public function getInfoByMobile($mobile){
        $field = "*";
        $sql = "SELECT $field FROM {$this->_messageTable} WHERE mobile='{$mobile}'";
        $query = $this->db->query($sql);
        $res =  $query->row_array();
        return $res;
    }

    //获取用户信息getInfoByUid
    public function getInfoByName($name){
        $field = "*";
        $sql = "SELECT $field FROM {$this->_messageTable} WHERE name='{$name}'";
        $query = $this->db->query($sql);
        $res =  $query->row_array();
        return $res;
    }

    /**
     * 查出未查看的加我好友的，且未读的
     * @param $uid
     * @return mixed
     */
    public function isNoReadFriend($uid){
        $field = "*";
        $sql = "SELECT $field FROM {$this->_messageTable} WHERE type = 5 and is_new = 0 and uid='{$uid}' limit 0,1";
        $query = $this->db->query($sql);
        $res =  $query->row_array();
        return $res;
    }

    /**
     * 根据条件获取(type6 信息)，不分页
     * @param $data
     */
    public function getType6($data){
        $where = " WHERE 1=1 ";
        if(is_array($data) && !empty($data)){
            foreach($data AS $key => $val){
                $where .= " AND  m.`" .$key . "`='" . $val . "'";
            }
        }

        $sql = "SELECT u.mobile FROM {$this->_messageTable} as m left join {$this->_userTable} as u  on m.s_uid= u.uid {$where};";
        $query = $this->db->query($sql);
        $res = $query->result_array();
        return $res;
    }

    /**
     * 根据条件获取，不分页
     * @param $data
     */
    public function getAll($data){
        $where = " WHERE 1=1 ";
        if(is_array($data) && !empty($data)){
            foreach($data AS $key => $val){
                $where .= " AND  `" .$key . "`='" . $val . "'";
            }
        }

        $sql = "SELECT * FROM {$this->_messageTable} {$where};";
        $query = $this->db->query($sql);
        $res = $query->result_array();
        return $res;
    }

    /**
     * 根据条件获取，不分页
     * @param $data
     */
    public function getRow($data){
        $where = " WHERE 1=1 ";
        if(is_array($data) && !empty($data)){
            foreach($data AS $key => $val){
                $where .= " AND  `" .$key . "`='" . $val . "'";
            }
        }

        $sql = "SELECT * FROM {$this->_messageTable} {$where};";
        $query = $this->db->query($sql);
        $res = $query->row_array();
        return $res;
    }

    /**
     * 批量插入
     */
    public function insert_batch($data){
        $res = $this->db->insert_batch($this->_messageTable,$data);
        return $res;
    }
        $field = "*";
        $where = " WHERE type in(5,6) AND is_del = 0 ";

        if(is_array($data) && !empty($data)){
            foreach($data AS $key => $val){
                if(!in_array($val,array('page','limit','order'))){
                    $where .= " AND `" . $key . "`='" . $val . "'";
                }
            }
        }


        if(!empty($order)){
            $order_str = $order;
        }else{
            $order_str = ' ORDER BY add_time desc '; //默认时间倒叙
        }


        $result = array();
        $sql = "SELECT {$field} FROM {$this->_messageTable} {$where} {$order_str} LIMIT {$offset}, {$num};";
        $query = $this->db->query($sql);
        if($query !== FALSE){
            $final_arr = array();
            $_list = $query->result_array();
            if(!empty($_list) && is_array($_list)){
                //加载user_model
                $CI = &get_instance();
                $CI->load->model('user_model');

                foreach($_list as $k=>$v){
                    switch($v['type']){
                        case 5:  //
                        case 6:  //
                            //查询头像 todo;
                            $res_uid = $CI->user_model->getInfoByUid($v['s_uid']);
                            $final_arr[] = array(
                                'msg_id'=>$v['id'],
                                'uid'=> $res_uid['uid'],
                                'avatar'=>!empty($res_uid['avatar'])? $res_uid['avatar'] : '',
                                'name'=>$res_uid['name'],
                                'type'=>$v['type'],
                                'status'=>$v['status'],
                                's_uid'=>$v['s_uid'],
                                'content'=>$v['content']
                            );
                            break;

                    }
                }

            }
            unset($_list);
            $result['list'] = $final_arr;

            $cntQuery = $this->db->query("SELECT COUNT(*) AS cnt FROM {$this->_messageTable} {$where};");
            $cntRow = $cntQuery->row_array();
            $result['cnt'] = $cntRow['cnt'];
            return $result;
        }else{
            return FALSE;
        }
    }

}