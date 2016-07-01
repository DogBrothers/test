<?php


//数据库连接
class DB{
    static private $_instance;
    static private $_connectSource;
    private $_dbConfig;

    private function __construct()
    {

    }
    static public function getInstace(){
        if(!(self::$_instance instanceof self)){
            self::$_instance=new self();
        }
        return self::$_instance;
    }

    public function connect(){
        if(!self::$_connectSource){
            $mysqli= new mysqli(DB_HOST,DB_USER,DB_PASSWORD);
            if(self::$_connectSource){
                die("mysql connect_error".$mysqli->connect_error);
            }
            $mysqli->select_db(DB_NAME);
            $mysqli->set_charset("utf8");
            self::$_connectSource=$mysqli;
            return self::$_connectSource;
        }
        else{
            return self::$_connectSource;
        }
    }
    /*
     * 查询函数
     * @param $table 查询表
     * @param $table 查询条件数据键值与数值
     * return array 返回查询结果
     * */
    public function select($table,$arr){
        $str=implode(",",array_values($arr));
        $in = "(".substr($str,0,strlen($str)-1).")";
        $sql="select * from ".$table." where ".$arr[0]." in ".$in;
        $res=self::$_connectSource->query($sql);
        $i=0;
        $data=array();
        while($row=$res->fetch_array(MYSQLI_ASSOC)){
            $data[$i]=$row;
            $i++;
        }
        return $data;
    }
    /*
     * 执行一条查询语句
     * @param string $sql 查询sql语句
     * return array  返回查询结果
     * */
    public function query($sql){
        $res=self::$_connectSource->query($sql);
        $i=0;
        $data=array();
        while($row=$res->fetch_array(MYSQLI_ASSOC)){
            $data[$i]=$row;
            $i++;
        }
        return $data;
    }
    /*
     * 添加一条记录
     * @param string $table 添加记录的表
     * @param array $arr 添加记录的数组
     *  return id  返回添加记录的id
     * */
    public function insert($table, $arr){
        foreach ($arr as $key => $value) {
            $keyArr[] = "`" . $key . "`";
            $valueArr[] = "'" . $value . "'";
        }
        $key = implode(",", $keyArr);
        $value = implode(",", $valueArr);
        $sql = "insert into " . $table . "(" . $key . ") values (" . $value . ")";
        $res=self::$_connectSource->query($sql);
        return self::$_connectSource->insert_id;
    }
    /*
     * 修改一条记录
     * @param string $table 修改记录的表
     * @param array $arr 修改记录的数组
     * @param array $where 修改记录的条件
     * */
    public function update($table, $arr, $where){
        foreach ($arr as $key => $value) {
            $keyAndValueArr[] = "`" . $key . "`='" . $value . "'";
        }
        $keyAndValueArrs = implode(",", $keyAndValueArr);
        $sql = "update " . $table . " set " . $keyAndValueArrs . " where " . $where;

        self::$_connectSource->query($sql);
    }
    /*
     * 删除一条记录
     * @param string $table 删除记录的表
     * @param array $where 修改记录的条件
     * */
    public function del($table, $where){
        $sql = "delete from " . $table . " where " . $where;
        self::$_connectSource->query($sql);
    }
}
