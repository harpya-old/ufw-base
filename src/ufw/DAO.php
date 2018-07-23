<?php
namespace harpya\ufw;

/**
 * @author Eduardo Luz <eduluz@harpya.net>
 * @package ufw
 */
class DAO extends \PDO {
    
    

    public function __construct( $dsn,  $username = null,  $password = null, $options = null) {
        
        $this->dsn = $dsn;
        parent::__construct($dsn, $username, $password, $options);
        
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        
    }
    
    

    /**
     * 
     * @param string $table
     * @param array $values
     * @param string $sequenceName
     */
    public function insert($table,$values,$sequenceName=null) {
        $sql = "INSERT INTO $table ";

        $fieldNames = [];
        $fieldValues = [];
        $fieldOccureences = [];
        
        foreach ($values as $k => $v) {
            $fieldNames[] = $k;
            $fieldValues[] = $v;
            $fieldOccureences[] = '?';
        }
        
        $sql .= '( ' . join(',', $fieldNames).') values ('.join(',',$fieldOccureences).')';
        
        $this->select($sql, $fieldValues, false);
        
        if ($sequenceName) {        
            return $this->lastInsertId($sequenceName);
        };
    }
    
    
    public function update($table, $values, $criteria=false) {
         $sql = "UPDATE $table SET ";

        $fieldValues = [];
        $changes = [];

        foreach ($values as $k => $v) {
            
            $changes[] = " $k = ? ";
            $fieldValues[] = $v;
        }
        
        $sql .=  join(',', $changes);
        
        
        
        if ($criteria !== false) {
            
            if (is_array($criteria)) {
                $conditions = [];
                foreach ($criteria as $k => $v) {
                    $conditions[] = " $k = '$v' ";
                }
                $sql .= 'WHERE '.join(' AND ',$conditions);
            } else {
                $sql .= " WHERE $criteria ";
            }
        }
                    
        $this->select($sql, $fieldValues, false);
        
        return $this->errorCode();
        
        
    }
    
    
    
    public function delete($table, $criteria) {
         $sql = "DELETE FROM $table  ";
        
        if ($criteria) {
            
            if (is_array($criteria)) {
                $conditions = [];
                foreach ($criteria as $k => $v) {
                    $conditions[] = " $k = '$v' ";
                }
                $sql .= 'WHERE '.join(' AND ',$conditions);
            } else {
                $sql .= " WHERE $criteria ";
            }
        }
        
        return $this->exec($sql);
        
    }
    
    
    
    public function getOne($table, $where, $parms=[]) {
        $sql = "SELECT * FROM $table WHERE $where ";
        return $this->select($sql, $parms);
    }
    
    
    /**
     * 
     * @param type $sql
     * @param type $parms
     * @param boolean $fetch
     * @return \PDOStatement | array
     * @throws \PDOException
     */
    public function select($sql, $parms=[], $fetch=true) {
        $this->sql = $sql;
        $sth = $this->prepare($sql);        
        
        try {
            if (empty($parms)) {
                $sth->execute();
            } else {
                $sth->execute($parms);
            }
            if ($fetch) {
                return $sth->fetchAll(\PDO::FETCH_ASSOC);
            } else {
              return $sth;  
            }
        } catch (\PDOException $ex) {
            throw $ex;
        }

    }
    
    
    /**
     * 
     * @param type $sql
     * @param type $parms
     * @return array
     */
    public function selectOne($sql, $parms=[]) {
        
        if (!is_array($parms)) {
            $parms = [$parms];
        }
        
        $arr = $this->select($sql, $parms, true);

        if (!empty($arr)) {
            $reg = reset($arr);
        } else {
            $reg = false;
        }
        return $reg;
    }    
    
    
    
    
}

