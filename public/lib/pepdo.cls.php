<?php
/*
 * Created on 2014-12-10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace PHPEMSPRO;

class pepdo
{
 	private $queryid = 0;
	private $linkid = 0;
	private $log = config::dblog;
	private $_mostlimits = 512;
	static $instance;
	public $parm;

	public function __construct($parm)
    {
        $this->parm = $parm;
        $this->connect(config::db[$parm]);
    }

     /**
      * @param string $parm
      * @return static
      */
	static function getInstance($parm = 'default')
	{
        if(!isset(self::$instance[$parm]))
        {
            self::$instance[$parm] = new static($parm);//new self();
        }
        return self::$instance[$parm];
	}


	private function _makeDefaultInsertArgs($tables,$args)
	{
		$newargs = array();
		if(!is_array($tables))
		{
			$tables = array($tables);
		}
		foreach($tables as $table)
		{
			$sql = "SHOW FULL COLUMNS FROM  `".config::db[$this->parm]['prefix'].$table."`";
			$r = $this->fetchAll(array('sql' => $sql));
			foreach($r as $p)
			{
				if($p['Extra'] != 'auto_increment')
				{
					if($args[$p['Field']])$newargs[$p['Field']] = $args[$p['Field']];
					else
					{
						if(array_key_exists($p['Field'],$args))
						{
							$newargs[$p['Field']] = self::_setDefaultInsetValue($p['Type']);
						}
						else
							$newargs[$p['Field']] = self::_setDefaultInsetValue($p['Type'],$p['Default']);
					}
				}
			}
		}
		return $newargs;
	}

	private function _makeDefaultUpdateArgs($tables,$args)
	{
		$newargs = array();
		if(!is_array($tables))
		{
			$tables = array($tables);
		}
		foreach($tables as $table)
		{
			$sql = "SHOW FULL COLUMNS FROM  `".config::db[$this->parm]['prefix'].$table."`";
			$r = $this->fetchAll(array('sql' => $sql));
			foreach($r as $p)
			{
				if($p['Extra'] != 'auto_increment')
				{
					if(array_key_exists($p['Field'],$args))
					{
						if($args[$p['Field']])$newargs[$p['Field']] = $args[$p['Field']];
						else
							$newargs[$p['Field']] = self::_setDefaultInsetValue($p['Type']);
					}
				}
			}
		}
		return $newargs;
	}

	static function _setDefaultInsetValue($type,$def = false)
	{
		$type = explode('(',$type);
		$type = $type[0];
		switch($type)
		{
			case 'char':
			case 'varchar':
			case 'tinytext':
			case 'longtext':
			case 'mediumtext':
			case 'text':
				if($def)return (string)$def;
				else
					return '';
				break;

			case 'int':
				if($def)return intval($def);
				else
					return 0;
				break;

			default:
				if($def)return floatval($def);
				else
					return 0;
				break;
		}
	}

	public function makeSelect($args,$tablepre = NULL)
	{
		if($tablepre === NULL)
		{
			$tb_pre = config::db[$this->parm]['prefix'];
		}
		else $tb_pre = $tablepre;
		//if($args[0] === false)$args[0] = '*';
		if(!$args[0])$args[0] = '*';
		$db_fields = is_array($args[0])?implode(',',$args[0]):$args[0];
		$tables = $args[1];
		if(is_array($tables))
		{
			$db_tables = array();
			foreach($tables as $p)
			{
				$db_tables[] = "{$tb_pre}{$p} AS `{$p}`";
			}
			$db_tables = implode(',',$db_tables);
		}
		else
		{
			$db_tables = $tb_pre.$tables;
		}
		$query = $args[2];
		if(!is_array($query) || empty($query))$db_query = 1;
		else
		{
			$q = array();
			$v = array();
			foreach($query as $key => $p)
			{
				if(isset($p[2]))
				{
					if(is_array($p[3]))
					{
						$i = 0;
						$tkey = array();
						foreach($p[3] as $tp)
						{
							$tkey[] = ':'.$p[2].'_'.$i;
							$v[$p[2].'_'.$i] = $tp;
							$i++;
						}
						$p[1] = str_replace(':'.$p[2],implode(',',$tkey),$p[1]);
					}
					else
						$v[$p[2]] = $p[3];
				}
				if($key)
				{
					$q[] = $p[0].' '.$p[1].' ';
				}
				else
				{
					$q[] = $p[1].' ';
				}
			}
			$db_query = ' '.implode(' ',$q);
		}
		if(isset($args[3]))
			$db_groups = is_array($args[3])?implode(',',$args[3]):$args[3];
		else
			$db_groups = '';
		if(isset($args[4]))
			$db_orders = is_array($args[4])?implode(',',$args[4]):$args[4];
		else
			$db_orders = '';
		if(isset($args[5]))
			$db_limits = is_array($args[5])?implode(',',$args[5]):$args[5];
		else
			$db_limits = '';
		if($db_limits == false && $db_limits !== false)$db_limits = $this->_mostlimits;
		$db_groups = $db_groups?' GROUP BY '.$db_groups:'';
		$db_orders = $db_orders?' ORDER BY '.$db_orders:'';
		if($db_limits === false)
			$sql = 'SELECT '.$db_fields.' FROM '.$db_tables.' WHERE '.$db_query.$db_groups.$db_orders;
		else
			$sql = 'SELECT '.$db_fields.' FROM '.$db_tables.' WHERE '.$db_query.$db_groups.$db_orders.' LIMIT '.$db_limits;
		return array('sql' => $sql, 'v' => $v);
	}

	/**
	 * [ select => * , array()
	 *   table => [
	 *      ['name' => 'table1','as' => 'tableas'],
	 *      ['name' => 'table2','type' => 'join/left join/right join','on' => [],'as' => 'tableas']
	 *      ]
	 *   query => []
	 *   groupby => []
	 *   having => []
	 *   orderby => []
	 *   limit => []
	 * ]
	 **/

	function makeSelectPro($args,$tablepre = NULL)
	{
		if($tablepre === NULL)$tb_pre = config::db[$this->parm]['prefix'];
		else $tb_pre = $tablepre;
		//if($args[0] === false)$args[0] = '*';
		if(!$args['select'])$args['select'] = '*';
		$db_fields = is_array($args['select'])?implode(',',$args['select']):$args['select'];
		$tables = $args['table'];
		if(is_array($tables))
		{
			$db_tables = '';
			foreach($tables as $key => $p)
			{
				if($p['as'])
				{
					$tbas = $p['as'];
				}
				else
				{
					$tbas = $p['name'];
				}
				if($key)
				{
					if(!$p['type'])$p['type'] = 'JOIN';
					$db_tables .= " {$p['type']} {$tb_pre}{$p['name']} AS `{$tbas}`";
					if($p['on'])
					{
						foreach($p['on'] as $nkey => $onp)
						{
							if($nkey)
							{
								$db_tables .= " {$onp[0]} {$onp[1]} ";
							}
							else
							{
								$db_tables .= " ON {$onp[1]} ";
							}
						}
					}
				}
				else
				{
					$db_tables .= "{$tb_pre}{$p['name']} AS `{$tbas}`";
				}
			}
		}
		else
		{
			$db_tables = $tb_pre.$tables;
		}
		$query = $args['query'];
		if(!is_array($query) || empty($query))
		{
			$db_query = 1;
		}
		else
		{
			$q = array();
			$v = array();
			foreach($query as $key => $p)
			{
				if(isset($p[2]))
				{
					if(is_array($p[3]))
					{
						$i = 0;
						$tkey = array();
						foreach($p[3] as $tp)
						{
							$tkey[] = ':'.$p[2].'_'.$i;
							$v[$p[2].'_'.$i] = $tp;
							$i++;
						}
						$p[1] = str_replace(':'.$p[2],implode(',',$tkey),$p[1]);
					}
					else
						$v[$p[2]] = $p[3];
				}
				if($key)
				{
					$q[] = $p[0].' '.$p[1].' ';
				}
				else
				{
					$q[] = $p[1].' ';
				}
			}
			$db_query = ' '.implode(' ',$q);
		}
		if(isset($args['groupby']))
			$db_groups = is_array($args['groupby'])?implode(',',$args['groupby']):$args['groupby'];
		else
			$db_groups = '';
		if(isset($args['having']))
		{
			$db_havings = '';
			if(is_array($args['having']))
			{
				foreach($args['having'] as $hkey => $hav)
				{
					if($hkey)
					{
						$db_havings .= " {$hav[0]} {$hav[1]} ";
					}
					else
					{
						$db_havings .= " {$hav[1]} ";
					}
				}
			}
			else
			{
				$db_havings = $args['having'];
			}
		}
		else
			$db_havings = '';
		if(isset($args['orderby']))
			$db_orders = is_array($args['having'])?implode(',',$args['orderby']):$args['orderby'];
		else
			$db_orders = '';
		if(isset($args['limit']))
			$db_limits = is_array($args['limit'])?implode(',',$args['limit']):$args['limit'];
		else
			$db_limits = '';
		if($db_limits == false && $db_limits !== false)$db_limits = $this->_mostlimits;
		$db_groups = $db_groups?' GROUP BY '.$db_groups:'';
		$db_havings = $db_havings?' HAVING '.$db_havings:'';
		$db_orders = $db_orders?' ORDER BY '.$db_orders:'';
		if($db_limits === false)
			$sql = 'SELECT '.$db_fields.' FROM '.$db_tables.' WHERE '.$db_query.$db_groups.$db_havings.$db_orders;
		else
			$sql = 'SELECT '.$db_fields.' FROM '.$db_tables.' WHERE '.$db_query.$db_groups.$db_havings.$db_orders.' LIMIT '.$db_limits;
		return array('sql' => $sql, 'v' => $v);
	}

	function mergeSql($primary,$key,$slaver)
	{
		if(!$primary['sql'])return ;
		if(!$slaver['sql'])return ;
		if(!$key)return ;
		$sql = array();
		if(!$primary['v'])$primary['v'] = [];
		if(!$slaver['v'])$slaver['v'] = [];
		$sql['v'] = array_merge($primary,$slaver);
		$sql['sql'] = str_replace($key,$slaver['sql'],$primary['sql']);
		return $sql;
	}

	//生成update sql
	function makeUpdate($args,$verify = 1,$tablepre = NULL)
	{
		if(!is_array($args))return false;
		if($tablepre === NULL)$tb_pre = config::db[$this->parm]['prefix'];
		else $tb_pre = $tablepre;
		$tables = $args[0];
		if($verify)
		{
			$args[1] = self::_makeDefaultUpdateArgs($tables,$args[1]);
		}
		if(is_array($tables))
		{
			$db_tables = array();
			foreach($tables as $p)
			{
				$db_tables[] = "{$tb_pre}{$p} AS `{$p}`";
			}
			$db_tables = implode(',',$db_tables);
		}
		else
		{
			$db_tables = $tb_pre.$tables;
		}
		$v = array();

		$pars = $args[1];
		if(!is_array($pars))return false;
		$parsql = array();
		foreach($pars as $key => $value)
		{
			$parsql[] = $key.' = '.':'.$key;
			if(is_array($value))$value = json_encode($value);
			$v[$key] = $value;
		}
		$parsql = implode(',',$parsql);

		$query = $args[2];
		if(!is_array($query) || empty($query))$db_query = 1;
		else
		{
			$q = array();
			foreach($query as $key => $p)
			{
				if($key)
				{
					$q[] = $p[0].' '.$p[1].' ';
					if(isset($p[2]))
						$v[$p[2]] = $p[3];
				}
				else
				{
					$q[] = $p[1].' ';
					if(isset($p[2]))
						$v[$p[2]] = $p[3];
				}
			}
			$db_query = ' '.implode(' ',$q);
		}
		if(isset($args[3]))
			$db_groups = is_array($args[3])?implode(',',$args[3]):$args[3];
		else
			$db_groups = '';
		if(isset($args[4]))
			$db_orders = is_array($args[4])?implode(',',$args[4]):$args[4];
		else
			$db_orders = '';
		if(isset($args[5]))
			$db_limits = is_array($args[5])?implode(',',$args[5]):$args[5];
		else
			$db_limits = '';
		if($db_limits == false && $db_limits !== false)$db_limits = $this->_mostlimits;
		$db_groups = $db_groups?' GROUP BY '.$db_groups:'';
		$db_orders = $db_orders?' ORDER BY '.$db_orders:'';
		$sql = 'UPDATE '.$db_tables.' SET '.$parsql.' WHERE '.$db_query.$db_groups.$db_orders.' LIMIT '.$db_limits;
		return array('sql' => $sql, 'v' => $v);
	}

	//生成delete sql
	function makeDelete($args,$tablepre = NULL)
	{
		if(!is_array($args))return false;
		if($tablepre === NULL)$tb_pre = config::db[$this->parm]['prefix'];
		else $tb_pre = $tablepre;
		$tables = $args[0];
		if(is_array($tables))
		{
			$db_tables = array();
			foreach($tables as $p)
			{
				$db_tables[] = "{$tb_pre}{$p} AS `{$p}`";
			}
			$db_tables = implode(',',$db_tables);
		}
		else
		{
			$db_tables = $tb_pre.$tables;
		}
		$query = $args[1];
		if(!is_array($query))$db_query = 1;
		else
		{
			$q = array();
			$v = array();
			foreach($query as $p)
			{
				$q[] = $p[0].' '.$p[1].' ';
				if(isset($p[2]))
					$v[$p[2]] = $p[3];
			}
			$db_query = '1 '.implode(' ',$q);
		}
		if(isset($args[2]))
		{
			$db_groups = is_array($args[2])?implode(',',$args[2]):$args[2];
		}
		else
		{
			$db_groups = '';
		}
		if(isset($args[3]))
		{
			$db_orders = is_array($args[3])?implode(',',$args[3]):$args[3];
		}
		else
		{
			$db_orders = '';
		}
		if(isset($args[4]))
		{
			$db_limits = is_array($args[4])?implode(',',$args[4]):$args[4];
		}
		else
		{
			$db_limits = '';
		}
		if($db_limits)$db_limits = ' LIMIT '.$db_limits;
		$db_groups = $db_groups?' GROUP BY '.$db_groups:'';
		$db_orders = $db_orders?' ORDER BY '.$db_orders:'';
		if(is_array($tables))
		{
			$sql = 'DELETE '.$db_tables.' FROM '.$db_tables.' WHERE '.$db_query.$db_groups.$db_orders.$db_limits;
		}
		else
		{
			$sql = 'DELETE FROM '.$db_tables.' WHERE '.$db_query.$db_groups.$db_orders.$db_limits;
		}
		return array('sql' => $sql, 'v' => $v);
	}

	/**
	 * 生成insert sql
	 * $args = array('user',array('username' => 'ttt1','useremail' => 'ttt11@166.com','userpassword' => '122s5a1s4sdfs5as5ax4a5sd5s','usergroupid' => '8'));
	 * $data = self::makeInsert($args);
	 */

	function makeInsert($args,$dim = 0,$verify = 1,$tb_pre = NULL)
	{
		if($tb_pre === NULL)$tb_pre = config::db[$this->parm]['prefix'];
		else $tb_pre = '';
		$tables = $args[0];
		if($verify)
		{
			$args[1] = self::_makeDefaultInsertArgs($tables,$args[1]);
		}
		if(is_array($tables))
		{
			$db_tables = array();
			foreach($tables as $p)
			{
				$db_tables[] = "{$tb_pre}{$p} AS `{$p}`";
			}
			$db_tables = implode(',',$db_tables);
		}
		else
			$db_tables = $tb_pre.$tables;
		$v = array();
		if($dim == 0)
		{
			$query = $args[1];

			foreach($args[1] as $key => $value)
			{
				if(is_array($value))
					$value = json_encode($value);
				$v[$key] = $value;
			}
		}
		else
		{
			$query = current($args[1]);
			foreach($args[1] as $pkey => $p)
			{
				$tn = array();
				foreach($p as $key => $value)
				{
					if(is_array($value))
						$value = json_encode($value);
					$tn[$key] = $value;
				}
				$v[] = $tn;
			}
		}
		if(!is_array($query))return false;
		$db_field = array();
		$db_value = array();
		foreach($query as $key => $value)
		{
			$db_field[] = $key;
			$db_value[] = ':'.$key;
		}
		$sql = 'INSERT INTO '.$db_tables.' ('.implode(',',$db_field).') VALUES ('.implode(',',$db_value).')';
		return array('sql' => $sql, 'v' => $v,'dim' => $dim);
	}

    private function _log($sql,$query)
    {
    	if($this->log)
    	{
    		$fp = fopen('public/data/error.log','a');
			fputs($fp,print_r($sql,true).print_r($query->errorInfo(),true));
			fclose($fp);
    	}
    }

    public function connect($setting = config::db['default'])
    {
    	$dsn="mysql:host={$setting['host']};dbname={$setting['name']};";
    	$this->linkid = new \PDO($dsn,$setting['user'],$setting['pass']);
    	$this->linkid->query("set names utf8");
    }

    public function commit()
    {
    	if(!$this->linkid)$this->connect($this->parm);
    	$this->linkid->commit();
    }

    public function beginTransaction()
    {
    	if(!$this->linkid)$this->connect($this->parm);
    	$this->linkid->beginTransaction();
    }

    public function rollback()
    {
    	if(!$this->linkid)$this->connect($this->parm);
    	$this->linkid->rollback();
    }

    public function fetchAll($sql,$index = false,$unserialize = false)
    {
    	if(!is_array($sql))return false;
    	if(!$this->linkid)$this->connect($this->parm);
    	$query = $this->linkid->prepare($sql['sql']);
    	$rs = $query->execute($sql['v']);
    	$this->_log($sql,$query);
		if ($rs) {
			$query->setFetchMode(\PDO::FETCH_ASSOC);
			$r = array();
			while($tmp = $query->fetch())
			{
				if($index)
				{
					$r[$tmp[$index]] = $tmp;
				}
				else
				$r[] = $tmp;
			}
			return $r;
		}
		else
		return false;
    }

    public function fetch($sql,$unserialize = false)
    {
    	if(!is_array($sql))return false;
    	if(!$this->linkid)$this->connect($this->parm);
    	$query = $this->linkid->prepare($sql['sql']);
    	$rs = $query->execute($sql['v']);
    	$this->_log($sql,$query);
    	if ($rs) {
			$query->setFetchMode(\PDO::FETCH_ASSOC);
			$tmp = $query->fetch();
			return $tmp;
		}
		else
		return false;
    }

    public function query($sql)
    {
    	if(!$sql)return false;
    	if(!$this->linkid)$this->connect($this->parm);
    	return $this->linkid->query($sql);
    }

    public function exec($sql)
    {
    	$this->affectedRows = 0;
    	if(!is_array($sql))return false;
    	if(!$this->linkid)$this->connect($this->parm);
    	if($sql['dim'])
    	return $this->dimexec($sql);
    	else
    	$query = $this->linkid->prepare($sql['sql']);
    	$rs = $query->execute($sql['v']);
		$this->_log($sql,$query);
		$this->affectedRows = $rs;
    	return $rs;
    }

    public function dimexec($sql)
    {
    	if(!is_array($sql))return false;
    	if(!$this->linkid)$this->connect($this->parm);
    	$query = $this->linkid->prepare($sql['sql']);
    	foreach($sql['v'] as $p)
    	$rs = $query->execute($p);
    	return $rs;
    }

    public function lastInsertId()
    {
    	return $this->linkid->lastInsertId();
    }

    private function _fixtables($tables,$prefix = null)
	{
        if(!$prefix)
		{
            $prefix = config::db[$this->parm]['prefix'];
		}
		if(is_array($tables))
        {
            $table = array();
            foreach($tables as $p)
			{
                if(is_array($p))
				{
					$table[] = $prefix.$p['name'];
				}
                else
				{
					$table[] = $prefix.$p;
				}
			}
			return $table;
        }
        else
		{
			return $prefix.$tables;
		}
	}

    public function getElement($args)
	{
        $data = array($args['select'],$args['table'],$args['query'],$args['groupby'],$args['orderby'],1);
        $sql = $this->makeSelect($data);
        $r = $this->fetch($sql);

		return database\model\database::decodeDbDataAuto('default',$this->_fixtables($args['table']),$r);
	}

     public function getElements($args)
     {
         $data = array($args['select'],$args['table'],$args['query'],$args['groupby'],$args['orderby'],$args['limit']);
         $sql = $this->makeSelect($data);
         $rs = $this->fetchAll($sql,$args['index']);
         $intros = database\model\database::getFieldsIntros('default',$this->_fixtables($args['table']));
         foreach($rs as $key => $p)
		 {
             $rs[$key] = database\model\database::decodeDbData($intros,$p);
		 }
         return $rs;
     }

	public function getProElement($args)
	{
		$data = array($args['select'],$args['table'],$args['query'],$args['groupby'],$args['having'],$args['orderby'],1);
		$sql = $this->makeSelectPro($data);
		$r = $this->fetch($sql);

		return database\model\database::decodeDbDataAuto('default',$this->_fixtables($args['table']),$r);
	}

	public function getProElements($args)
	{
		$data = array($args['select'],$args['table'],$args['query'],$args['groupby'],$args['having'],$args['orderby'],$args['limit']);
		$sql = $this->makeSelectPro($data);
		$rs = $this->fetchAll($sql,$args['index']);
		$intros = database\model\database::getFieldsIntros('default',$this->_fixtables($args['table']));
		foreach($rs as $key => $p)
		{
			$rs[$key] = database\model\database::decodeDbData($intros,$p);
		}
		return $rs;
	}

    public function insertElement($args = [])
	{
		if(empty($args))return false;
		$args['query'] = database\model\database::encodeDbData($this->parm,$this->_fixtables($args['table']),$args['query']);
		$data = array($args['table'],$args['query']);
        $intro = database\model\database::getTableIntro('default',$this->_fixtables($args['table']));
        if($intro['dbsynch'])
        {
            foreach(config::db as $dbid => $db)
            {
                $sql = self::getInstance($dbid)->makeInsert($data);
                self::getInstance($dbid)->exec($sql);
            }
        }
        else
        {
            $sql = $this->makeInsert($data);
            $this->exec($sql);
        }
		return $this->lastInsertId();
	}

    public function listElements($page,$number = 20,$args = [])
	{
		if(!is_array($args))return false;
		$page = $page > 0?$page:1;
		$args['limit'] = array(intval($page-1)*$number,$number);
		if(!$args['select'])$args['select'] = '*';
		if(is_array($args['select']))$args['select'] = implode(',',$args['select']);
		$args['select'] = 'SQL_CALC_FOUND_ROWS '. $args['select'];
		$r = array();
		$data = array($args['select'],$args['table'],$args['query'],$args['groupby'],$args['orderby'],$args['limit']);
		$sql = $this->makeSelect($data);
		$rs = $this->fetchAll($sql,$args['index']);
		$total = $this->query('SELECT FOUND_ROWS()')->fetchColumn();
		$intros = database\model\database::getFieldsIntros('default',$this->_fixtables($args['table']));
		foreach($rs as $key => $p)
		{
			$rs[$key] = database\model\database::decodeDbData($intros,$p);
		}
		$r['data'] = $rs;
		$pages = pg::outPage(pg::getPagesNumber($total,$number),$page);
		$r['pages'] = $pages;
		$r['number'] = $total;
		return $r;
	}

	public function delElement($args = [])
	{
		if(empty($args))return false;
		$data = array($args['table'],$args['query'],$args['orderby'],$args['limit']);
		$intro = database\model\database::getTableIntro('default',$this->_fixtables($args['table']));
		if($intro['dbsynch'])
		{
			foreach(config::db as $dbid => $db)
			{
                $sql = self::getInstance($dbid)->makeDelete($data);
                $rs = self::getInstance($dbid)->exec($sql);
			}
			return $rs;
		}
		else
		{
			$sql = $this->makeDelete($data);
            return $this->exec($sql);
        }
	}

	public function updateElement($args = [])
	{
		if(empty($args))return false;
		$args['query'] = database\model\database::encodeDbData($this->parm,$this->_fixtables($args['table']),$args['query']);
        $args['value'] = database\model\database::encodeDbData($this->parm,$this->_fixtables($args['table']),$args['value']);
        $data = array($args['table'],$args['value'],$args['query'],$args['limit']);
        $intro = database\model\database::getTableIntro('default',$args['table']);
        if($intro['dbsynch'])
        {
            foreach(config::db as $dbid => $db)
            {
                $sql = self::getInstance($dbid)->makeUpdate($data);
                $rs = self::getInstance($dbid)->exec($sql);
            }
            return $rs;
        }
        else
        {
            $sql = $this->makeUpdate($data);
            return $this->exec($sql);
        }
	}

	public function affectedRows()
	{
		return $this->affectedRows;
	}
 }
?>
