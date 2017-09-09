<?php


class Mysql
{
    /**
     * PDO实例
     * @var PDO
     */
    protected $DB;
    /**
     * PDO准备语句
     * @var PDOStatement
     */
    protected $Stmt;
    /**
     * 最后的SQL语句
     * @var string
     */
    protected $Sql;
    /**
     * 配置信息 $config=array('dsn'=-->xxx,'name'=>xxx,'password'=>xxx,'option'=>xxx)
    * @var array
    */
    protected $Config;

    /**
    * 构造函数
    * @param array $config
    */
    public function __construct($config)
    {
        $this->Config = $config;
    }

    /**
    * 连接数据库
    * @return void
    */
    public function connect()
    {
        $this->DB = new PDO($this->Config['dsn'], $this->Config['name'], $this->Config['password']);
        //默认把结果序列化成stdClass
        $this->DB->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        //自己写代码捕获Exception
        $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }



    /**
    * 断开连接
    * @return void
    */
    public function disConnect()
    {
        $this->DB = null;
        $this->Stmt = null;
    }

    /**
    * 执行sql，返回新加入的id
    * @param string $statement
    * @return string
    */
    public function exec($statement)
    {
        if ($this->DB->exec($statement)) {
        $this->Sql = $statement;
        return $this->lastId();
        }
        $this->errorMessage();
    }

    /**
    * 查询sql
    * @param string $statement
    * @return Mysql
    */
    public function query($statement)
    {
        $res = $this->DB->query($statement);
        if ($res) {
            $this->Stmt = $res;
            $this->Sql = $statement;
            return $this;
        }
        $this->errorMessage();
    }

    /**
    * 序列化一次数据
    * @return mixed
    */
    public function fetchOne()
    {
        return $this->Stmt->fetch();
    }

    /**
    * 序列化所有数据
    * @return array
    */
    public function fetchAll()
    {
        return $this->Stmt->fetchAll();
    }

    /**
    * 最后添加的id
    * @return string
    */
    public function lastId()
    {
        return $this->DB->lastInsertId();
    }

    /**
    * 影响的行数
    * @return int
    */
    public function affectRows()
    {
        return $this->Stmt->rowCount();
    }

    /**
    * 预备语句
    * @param string $statement
    * @return Mysql
    */
    public function prepare($statement)
    {
        $res = $this->DB->prepare($statement);
        if ($res) {
            $this->Stmt = $res;
            $this->Sql = $statement;
            return $this;
        }
        $this->errorMessage();
    }

    /**
    * 绑定数据
    * @param array $array
    * @return Mysql
    */
    public function bindArray($array)
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                //array的有效结构 array('value'=>xxx,'type'=>PDO::PARAM_XXX)
                $this->Stmt->bindValue($k, $v['value'], $v['type']);
            } else {
                $this->Stmt->bindValue($k, $v, PDO::PARAM_STR);
            }
        }
        return $this;
    }

    /**
    * 执行预备语句
    * @return bool
    */
    public function execute()
    {
        if ($this->Stmt->execute()) {
            return true;
        }
        $this->errorMessage();
    }

    /**
    * 开启事务
    * @return bool
    */
    public function beginTransaction()
    {
        return $this->DB->beginTransaction();
    }

    /**
    * 执行事务
    * @return bool
    */
    public function commitTransaction()
    {
        return $this->DB->commit();
    }

    /**
    * 回滚事务
    * @return bool
    */
    public function rollbackTransaction()
    {
        return $this->DB->rollBack();
    }

    /**
    * 抛出错误
    * @throws Error
    * @return void
    */
    public function errorMessage()
    {
        $msg = $this->DB->errorInfo();
        throw new Error('数据库错误：' . $msg[2]);
    }

    //---------------------
    /**
    * 单例实例
    * @var Mysql
    */
    protected static $_instance;

    /**
    * 默认数据库
    * @static
    * @param array $config
    * @return Mysql
    */
    public static function instance($config)
    {
        if (!self::$_instance instanceof Mysql) {
            self::$_instance = new Mysql($config);
            self::$_instance->connect();
        }
        return self::$_instance;
    }

    //----------------------

    /**
    * 获取PDO支持的数据库
    * @static
    * @return array
    */
    public static function getSupportDriver(){
        return PDO::getAvailableDrivers();
    }
    /**
    * 获取数据库的版本信息
    * @return array
    */
    public function getDriverVersion(){
        $name = $this->DB->getAttribute(PDO::ATTR_DRIVER_NAME);
        return array($name=>$this->DB->getAttribute(PDO::ATTR_CLIENT_VERSION));
    }

}