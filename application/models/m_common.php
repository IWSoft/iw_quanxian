<?php

class m_common extends CI_Model
{
    public $db;
    public $type;
    private $subclass;
    private $tablename;

    /**
     * common constructor.
     */
    public function __construct($params="")
    {
        $this->subclass = get_called_class();//被调用的类名>=PHP5.3
        if(substr(strtolower($this->subclass),0,2)=="m_"){
            $this->tablename = substr(strtolower($this->subclass),2,strlen($this->subclass)-2);
        }
        else{
            $this->tablename = strtolower($this->subclass);
        }

        //$params['type'] 用于处理多数据库 confit/config.inc.php
        $this->type = (isset($params['type']) && $params['type']) ? $params['type'] : 'default';
        parent::__construct();
        $this->db = $this->load->database($this->type, true);
    }


    //插入一条数据
    function insert_one($table, $data)
    {
        $this->db->insert($table, $data);
        $arr = array(
            'line' => $this->db->affected_rows(),//影响的行数
            'insert_id' => $this->db->insert_id(),
            'sql' => $this->db->last_query()
        );
        return $arr["insert_id"];
    }

    //查询1条数据，返回结果
    function query_one($sql)
    {
        return $this->db->query($sql)->row_array();
    }

    //返回单 表
    function getmodel($table, $id)
    {
        return $this->db->query("select * from $table where id=" . $id)->row_array();
    }

    //查询list data
    function querylist($sql)
    {

        $result = array();
        $query = $this->db->query($sql);
        if ($query) {
            foreach ($query->result_array() as $row) {
                $result[] = $row;
            }
        }

        return $result;
    }

    //查询返回的结果
    function query_count($sql)
    {
        $query = $this->db->query($sql);
        $num_array = $query->result_array();
        $num = 0;
        if (isset($num_array[0]) && !empty($num_array[0])) {
            foreach ($num_array[0] as $k => $v) {
                $num = $v;
                break;
            }
        }
        return $num;

    }

    //删除数据
    function del_data($sql)
    {

        $query = $this->db->query($sql);
        return $this->db->affected_rows(); //返回影响的行数

    }

    //修改数据
    function update_data($sql)
    {

        $query = $this->db->query($sql);
        return $this->db->affected_rows(); //返回影响的行数
    }

    //修改数据
    function update_data2($table, $data, $where)
    {

        //$query = $this->db->update_string($table,$data,$where);
        $err = 0;
        $this->db->update($table, $data, $where) or $err = 1;
        $arr = array(
            'line' => ($this->db->affected_rows() >= 0 && $err == 0) ? 1 : 0,//影响的行数
            'insert_id' => $this->db->insert_id(),
            'sql' => $this->db->last_query()
        );
        //echo $this->db->last_query();
        //return $this->db->affected_rows(); //返回影响的行数
        return $arr["line"];
    }

    function create_table($table, $fields)
    {
        $this->load->dbutil();
        $this->load->dbforge();
        $this->dbforge->add_field($fields);
        //可选的第二个参数如果被设置为TRUE，那么，表的定义中就会添加 "IF NOT EXISTS" 子句
        $this->dbforge->create_table($table, TRUE);
    }


    function create_cols($table, $fields)
    {
        /*
        $fields = array(
                'blog_id' => array(
                        'type' => 'INT',
                        'constraint' => 5,
                        'unsigned' => TRUE,
                        'auto_increment' => TRUE
                ),
                'blog_title' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '100',
                ),
                'blog_author' => array(
                        'type' =>'VARCHAR',
                        'constraint' => '100',
                        'default' => 'King of Town',
                ),
                'blog_description' => array(
                        'type' => 'TEXT',
                        'null' => TRUE,
                ),
        );
        */
        $this->load->dbutil();
        $this->load->dbforge();
        $this->dbforge->add_column($table, $fields);
    }

    /*
     *获取表字段
     */
    function get_fields($table)
    {
        $fields = $this->db->list_fields($table);
        return $fields;
    }


    /**
     * 获取字段所有信息
     * @param $table
     * @return mixed
     */
    function get_fields_all($table)
    {
        $result = $this->db->query("SELECT 
							COLUMN_NAME,
							COLUMN_COMMENT							
						  FROM 
						  information_schema.COLUMNS WHERE TABLE_NAME='" . $table . "'");

        return $result->result_array();
    }


    /**
     * GET一个实体
     * @param $id
     * @param bool $isguid 是否GUID
     */
    public function get($id, $isguid = true)
    {
        return $this->query_one("select * from " . $this->tablename . " where " . ($isguid ? "guid" : "id") . "='" . $id."'");
    }

    /**
     * 通用添加实体 返回 新插入ID
     * @param $model
     *
     */
   public function add($model, $isguid = true)
    {
        $insert_id = $this->insert_one(
            $this->tablename, $model);
        if($isguid){
            return $model["guid"];
        }
        else{
            return $insert_id;
        }

    }

    /**
     * 通用更新实体
     * @param $model
     * @param bool $isguid $isguid 是否GUID
     * @return 修改行数
     */
   public  function update($model, $isguid = true)
    {
        return $this->update_data2(
            $this->tablename,
            $model,
            array(($isguid ? "guid" : "id") => $model[($isguid ? "guid" : "id")])
        );
    }

    public function get_list($where, $orderby="")
    {
        return $this->querylist(
            "select * from " . $this->tablename . " where $where ".($orderby!=""?("order by ".$orderby):"")
        );
    }

    /**
     * @param $id * 通用判断ID是否重复
     * @param int $guid 不等于空代表要查找记录新ID是否有重复
     * @return bool
     * 存在返回true
     */
    public function chk_id_exist($id,$guid=""){
        if($id>0){
            return $this->get_count("id='{$id}' and guid<>'$guid'");
        }
        else {
            return $this->get_count("id='{$id}'") > 0;
        }
    }

    /**
     * 通用判断某表的ID是否重复，如重复，使用新的ID，直到不重复
     * @param $id
     * @return int
     */
    public function get_id($id){
        $count = $this->get_count("id='{$id}'");
        while($count>0){
            $id = create_id();
            $count = $this->get_count("id='{$id}'");
        }
        return $id;
    }

    /**
     * 真删除数据
     * @param $where
     * @param bool $isguid
     * @return mixed 返回影响的行数
     */
    public function del($where, $isguid = false)
    {
        $sql = "delete from " . $this->tablename . " where " . $where;
        $num = $this->del_data($sql);
        return $num;
    }

    /**
     * 通用分页
     * @param int $pageindex
     * @param int $pagesize
     * @param string $where
     * @param string $orderby
     * @return array
     */
    public function get_list_pager($pageindex = 1, $pagesize = 10, $where = "", $orderby = "")
    {
        //$this->load->library("common_page");
        $page = $pageindex;//$this->input->get_post("per_page");
        if ($page <= 0) {
            $page = 1;
        }
        $limit = ($page - 1) * $pagesize;
        $limit .= ",{$pagesize}";
        if ($where != "") {
            $where = ' where ' . $where;
        }
        if ($orderby != "") {
            $orderby = " order by " . $orderby . " ";
        }
        $sql_count = "SELECT COUNT(*) AS tt FROM " . $this->tablename . $where;
        //echo $sql_count;
        $total = $this->query_count($sql_count);
        $page_string = "";//$this->common_page->page_string2($total, $pagesize, $page);
        $sql = "SELECT * FROM {$this->tablename} {$where}  {$orderby} limit {$limit}";
        //echo $sql;
        $list = $this->querylist($sql);
        $data = array(
            "pager" => "",
            "total"=>$total,
            "list" => $list
        );
        return $data;
    }


    public function get_count($where){
        $sql = "select count(1) as dd from  $this->tablename where ".$where;
        $total = $this->query_count($sql);
        return $total;
    }

}