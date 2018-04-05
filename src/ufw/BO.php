<?php
namespace harpya\ufw;

/**
 * Class Business Object base.
 * 
 * @author Eduardo Luz <eduardo@harpya.net>
 * @copyright (c) 2018, Harpya.net
 * @package harpya\ufw-base
 */
abstract class BO {

    protected $__dao;
    protected $__data = [];
    protected $id;
    
    /**
     * 
     * @return int
     */
    public function getID() {
        return $this->id;
    }
    
    /**
     * 
     * @param int $id
     * @return $this
     */
    public function setID($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * 
     * @param array $map
     * @param array $data
     * @return array
     */
    public function map($map=[], $data=[]) {
        $response = [];
        
        foreach ($map as $attrName => $attrConfig) {
            if (array_key_exists($attrName, $data)) {
                $response[$attrName] = $data[$attrName];
            }
        }
        
        return $response;
    } 
    
    /**
     * 
     * @param \harpya\ufw\DAO $dao
     */
    public function setDAO($dao){
        $this->__dao = $dao;
        
    }
    
    
    /**
     * 
     * @return \harpya\ufw\DAO
     */
    public function getDAO() {        
        return $this->__dao;
    }
    
    
    /**
     * @return string the table name
     */
    public abstract function getTableName();
    
    
    /**
     * 
     * @param \harpya\ufw\DAO $dao
     */
    public function __construct($dao=false) {
        if ($dao) {
            $this->setDAO($dao);
        } else {
            $this->setDAO(\harpya\ufw\Application::getInstance()->getDB());
        }        
    }
    
    /**
     * 
     * @return mixed
     */
    public function haveUpdateField() {
        return false;
    }
    
    
    /**
     * 
     * @return string
     */
    public function getSequenceName() {        
        return  $this->getTableName() .  '_id_seq';
    }



    /**
     * 
     * @return array
     */
    public function getMapFields() {
        $map = array_flip(array_keys(get_object_vars($this)));
        return $map;
    }
    

    /**
     * 
     * @param array $arr
     */
    public function insert($arr) {
        
        $mapped = $this->map($this->getMapFields(), $arr);
        if ($this->getSequenceName()) {
            
            if (array_key_exists('id', $mapped)) {
                $this->id = $mapped['id'];
                $this->getDAO()->insert($this->getTableName(), $mapped);
            } else {            
                $this->id = $this->getDAO()->insert($this->getTableName(), $mapped, $this->getSequenceName());
            }
        } else {
            $this->getDAO()->insert($this->getTableName(), $mapped);
            $this->id = null;
        }
        
    }


    /**
     * 
     * @param array $arr
     * @param mixed $criteria
     * @return mixed
     */
    public function update($arr, $criteria=false) {
        $mapped = $this->map($this->getMapFields(), $arr);
        
        if ($this->haveUpdateField()) {
            $mapped[$this->haveUpdateField()] = 'now()';
        }
        
        $criteria = $this->getCriteria($criteria);
        
        return $this->getDAO()->update($this->getTableName(), $mapped, $criteria);        
    }
    
    
    /**
     * 
     * @param mixed $criteria
     * @return mixed
     * @throws \Exception
     */
    protected function getCriteria($criteria=false) {
        if ($criteria === false) {
            if ($this->id) {
                $criteria = ['id' => $this->id];
            } else {
                throw new Exception("Update without criteria defined", 4);
            }
        }
        return $criteria;
    }
    
    
    /**
     * 
     * @param mixed $criteria
     * @return mixed
     */
    public function delete($criteria=false) {

        $criteria = $this->getCriteria($criteria);
        
        return $this->getDAO()->delete($this->getTableName(), $criteria);
    }
    
    
    /**
     * 
     * @param mixed $criteria
     * @return array
     */
    public function load($criteria=false) {
        $criteria = $this->getCriteria($criteria);
        
        $sql = "SELECT * FROM ". $this->getTableName();
        
        $where = '';
        $whereParms = [];
        if (is_array($criteria)) {
            $whereArgs = [];            
            foreach ($criteria as $attrName => $attrValue) {
                $whereArgs[] = " $attrName = ? ";
                $whereParms[] = $attrValue;
            }
            $where = join(" AND ", $whereArgs);
        } elseif (is_string($criteria)) {
            $where = $criteria;
        }
        
        
        
        $sql .= " WHERE $where";
        
        $record = $this->getDAO()->selectOne($sql, $whereParms);
        $this->bind($record);
        
        return $record;
        
    }
    
    
    /**
     * 
     * @param mixed $criteria
     * @param mixed $orderBy 
     * @param string $limit 
     * @return array
     */
    public function loadList($criteria=false, $orderBy=false, $limit=false) {
        $criteria = $this->getCriteria($criteria);
        
        $sql = "SELECT * FROM ". $this->getTableName();
        
        $where = '';
        $whereParms = [];
        if (is_array($criteria)) {
            $whereArgs = [];            
            foreach ($criteria as $attrName => $attrValue) {
                if (is_numeric($attrName)) {
                    $whereArgs[] = " $attrValue ";
                } else {
                    $whereArgs[] = " $attrName = ? ";
                    $whereParms[] = $attrValue;
                }
            }
            $where = join(" AND ", $whereArgs);
        } elseif (is_string($criteria)) {
            $where = $criteria;
        }
        
        
        
        $sql .= " WHERE $where";
        
        if ($orderBy) {
            if (is_array($orderBy)) {
                $sql .= " ORDER BY " . join(',',$orderBy);
            } elseif (is_scalar($orderBy)) {
                $sql .= " ORDER BY $orderBy ";
            }
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit ";
        }
        
        $list = $this->getDAO()->select($sql, $whereParms);
        
        return $list;
        
    }
    
    
    /**
     * 
     * @param array $arr
     */
    public function bind($arr) {
        $this->__data = $arr;
        
        $lsAttributes = get_object_vars($this);
        foreach ($lsAttributes as $k => $v) {
            if (Utils::get($k, $arr)) {
                $this->$k = Utils::get($k, $arr);
            }
        }
        
    }
    
    /**
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key,$default=false) {
        return Utils::get($key, $this->__data, $default);
    }
    
    /**
     * 
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $lsAttributes = get_object_vars($this);
        
        if (Utils::get($key, $lsAttributes)) {
            $this->$key = $value;
        }
        
        $this->__data[$key] = $value;
    }
    
    
    
}

    