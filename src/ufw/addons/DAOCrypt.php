<?php

namespace harpya\ufw\addons;


trait DAOCrypt {

    
    protected $cryptoCredentials = [];
    
    
    
    /**
     * 
     * @param type $key
     * @param type $type
     */
    public function setCryptoCredentials($key, $type) {
        $this->cryptoCredentials['key'] = $key;
        $this->cryptoCredentials['type'] = $type;
        
    }
    
    
    /**
     * 
     * @param type $query
     * @param array $bindValues
     * @return type
     */
    public function select($query, array $bindValues = null) {
        if (is_array($query)) {
            $query = $this->preProcessSelectSQL($query);
        }
        return parent::select($query, $bindValues);
    }
    
    
    /**
     * 
     * @return type
     */
    protected function getCryptoKey() {
        return $this->cryptoCredentials['key']??false;
    }
    
    
    
    /**
     * 
     * @return type
     */
    protected function getCryptoType() {
        return $this->cryptoCredentials['type']??'aes';
    }
    
    
    /**
     * 
     * @param array $arrSql
     * @return string
     */
    protected function preProcessSelectSQL($arrSql) {
        $sql = 'SELECT ';
        
        $fields = $arrSql['fields']??false;
        if (is_array($fields)) {
            foreach ($fields as $k => $fieldName) {
                if (($fieldName[0] == '*') && strlen($fieldName)>1) {
                    $fieldName = substr($fieldName,1);
                    // TODO refactory use a configuration key to check if the DB supports encryption, and if yes, the command to do it
                    $fields[$k] = "convert_from(decrypt($fieldName,'".$this->getCryptoKey()."','".$this->getCryptoType()."'),'utf-8') as $fieldName";
                }
            }
            $sql .= join("\n,",$fields);
        } else {
            $sql .= " * ";
        }
        
        if (!getValue('from', $arrSql)) {
            throw new \Exception('Invalid SQL - missing FROM clause', 100);
        }
        
        
        $sql .= "\n FROM " .join(",\n", getValue('from', $arrSql));
        
        $where = getValue('where',$arrSql);
        if (is_array($where) && !empty($where)) {
            $sql .= "\n WHERE ".join("\n AND",$where);
        }
        
        
        
        $group = getValue('group',$arrSql);
        if (is_array($group) && !empty($group)) {
            $sql .= "\n  GROUP BY ".join(", ",$group);
        }
        
        
        $order = getValue('order',$arrSql);
        if (is_array($order) && !empty($order)) {
            $sql .= "\n ORDER BY ".join(", ",$order);
        }
        
        
        $sql .= "\n";

        return $sql;
    }
    
    
    /**
     * 
     * @param type $tableName
     * @param array $insertMappings
     * @return type
     */
    public function insert($tableName, array $insertMappings) {
        $arrKeyValues = [];
        foreach ($insertMappings as $k => $v) {
            
            if ($k[0] == '*') {
                $k = substr($k,1);
                $v = "﻿encrypt('".$v."','".$this->getCryptoKey()."','".$this->getCryptoType()."') ";
            }
            $arrKeyValues[$k] = $v;
        }
        return parent::insert($tableName, $arrKeyValues);
    }
    
    
    
    /**
     * 
     * @param type $tableName
     * @param array $updateMappings
     * @param array $whereMappings
     */
    public function update($tableName, array $updateMappings, array $whereMappings) {
        $arrKeyValues = [];
        foreach ($updateMappings as $k => $v) {
            
            if ($k[0] == '*') {
                $k = substr($k,1);
                $v = "﻿encrypt('".$v."','".$this->getCryptoKey()."','".$this->getCryptoType()."') ";
            }
            $arrKeyValues[$k] = $v;
        }
        parent::update($tableName, $arrKeyValues, $whereMappings);
    }
        
}
