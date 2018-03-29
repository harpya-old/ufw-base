<?php
namespace xbrain\ufw;


abstract class BO {

    protected $__dao;
    protected $__data;
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
     * @param \xbrain\ufw\DAO $dao
     */
    public function setDAO($dao){
        $this->__dao = $dao;
        
    }
    
    
    /**
     * 
     * @return \xbrain\ufw\DAO
     */
    public function getDAO() {        
        return $this->__dao;
    }
    
    
    public abstract function getTableName();
    
    
    /**
     * 
     * @param \xbrain\ufw\DAO $dao
     */
    public function __construct($dao=false) {
        if ($dao) {
            $this->setDAO($dao);
        } else {
            $this->setDAO(\xbrain\ufw\Application::getInstance()->getDB());
        }        
    }
    
    
    public function haveUpdateField() {
        return false;
    }
    
    public function getSequenceName() {        
        return  $this->getTableName() .  '_id_seq';
    }



    public function getMapFields() {
        $map = array_flip(array_keys(get_object_vars($this)));
        return $map;
    }
    

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


    public function update($arr, $criteria=false) {
        $mapped = $this->map($this->getMapFields(), $arr);
        
        if ($this->haveUpdateField()) {
            $mapped[$this->haveUpdateField()] = 'now()';
        }
        
        $criteria = $this->getCriteria($criteria);
        
        return $this->getDAO()->update($this->getTableName(), $mapped, $criteria);        
    }
    
    
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
    
    
    public function delete($criteria=false) {

        $criteria = $this->getCriteria($criteria);
        
        return $this->getDAO()->delete($this->getTableName(), $criteria);
    }
    
    
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
        
        
        $this->__data = $this->getDAO()->select($sql, $whereParms);
        
        return $this->__data;
        
    }
    
}

    