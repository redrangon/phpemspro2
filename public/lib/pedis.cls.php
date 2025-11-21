<?php
/*
 * redis类，初次使用，错误较多，如有建议欢迎加QQ探讨
 */
namespace PHPEMSPRO;
class pedis
{
    public static $instance = array();
    public $parm;
    private $redis;

    /**
     * @param string $parm
     * @return static
     */
    static function getInstance($parm = 'default')
    {
        if(self::$instance[$parm] == NULL)
        {
            self::$instance[$parm] = new static($parm);//new self();
        }
        return self::$instance[$parm];
    }

    public function __construct($parm)
    {
        $this->parm = $parm;
        $this->redis = new \Redis();
        $this->redis->connect(config::redis[$parm]['host']);
        if(config::redis[$parm]['pass'])
        {
            $this->redis->auth(config::redis[$parm]['pass']);
        }
        $this->redis->select(intval(config::redis[$parm]['name']));
    }

    public function setStringData($key,$value,$expire = 0)
    {
        $this->redis->set($key,$value);
        if($expire)
        {
            $this->redis->expire($key,$expire);
        }
    }

    public function delData($key)
    {
        $this->redis->del($key);
    }

    public function getStringData($key)
    {
        if($this->redis->ttl($key) > 0)
        {
            return $this->redis->get($key);
        }
        else
        {
            return false;
        }
    }

    public function setHashData($key,$field,$value,$expire = 0)
    {
        $this->redis->hset($key,$field,$value);
        if($expire)
        {
            $this->redis->expire($key,$expire);
        }
    }

    public function getHashData($key,$field)
    {
        if($this->redis->ttl($key) > 0)
        {
            return $this->redis->hget($key,$field);
        }
        elseif($this->redis->ttl($key) == -1)
        {
            return $this->redis->hget($key,$field);
        }
        else
        {
            return false;
        }
    }

    public function getHashKeys($key)
    {
        return $this->redis->hkeys($key);
    }

    public function delHashData($key,$field = null)
    {
        if($field)
        {
            return $this->redis->hdel($key,$field);
        }
        else
        {
            return $this->redis->del($key);
        }
    }

    public function searchKeys($key, $wildcard = "*")
    {
        $iterator = null;
        $pattern = $key.$wildcard;
        // 务必设置，如果没扫描到，继续扫描，而不是返回空，否则while直接退出，遍历就会不准确
        $this->redis->setOption(\Redis::OPT_SCAN,\Redis::SCAN_RETRY);
        $arr = [];
        while(true) {
            $keys = $this->redis->scan($iterator, $pattern);
            if ($keys === false) {
                break;//迭代结束，未找到匹配pattern的key
            }
            if(isset($keys) && !empty($keys))
            {
                foreach ($keys as $key)
                {
                    array_push($arr, $key);
                }
            }
        }
        return $arr;
    }
}
?>