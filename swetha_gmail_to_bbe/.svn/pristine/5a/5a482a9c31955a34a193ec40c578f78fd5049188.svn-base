<?php

abstract class Bugs_Common_Model
{
	/**
     *
     * @var Zend_Db_Table
     */
	protected $__dbTable;
	protected $__dbTable_name;
	
	// Log info
	protected $__LOG_is_active = false;
	protected $__LOG_actions = array();
	
	
	public function __construct($data = null)
	{
		if(is_array($data)){
			$this->populate($data);
		}elseif(is_numeric($data)){
			$this->find($data);
		}elseif(is_string($data)){
			$this->find($data);
		}
	}
	
	public function __set($key, $value)
    {
        $method = 'set' . $key;
        $class_name = get_class($this);

    	if(method_exists($this, $method)){
    		$this->$method($value);
    	}
    	elseif(property_exists($class_name, $key)){
    		$this->$key = $value;
    	}
    	else{
    		//throw new Exception("Property '{$key}' not defined for '{$class_name}' class!");
    	}
    }
 
    public function __get($key)
    {
    	$method = 'set' . $key;
        $class_name = get_class($this);

    	if(method_exists($this, $method)){
    		return $this->$method();
    	}
    	elseif(property_exists($class_name, $key)){
    		return $this->$key;
    	}
    	else{
    		throw new Exception("Property '{$key}' not defined for '{$class_name}' class!");
    	}
    }
	
	public function __toArray()
	{
		$data = array();
		
		foreach($this as $key => $value){
			// Excludes $__dbTable_name and $__dbTable;
			if(substr($key, 0,2) != "__"){
				$data[$key] = $value;
			}
		}
		
		return $data;
	}
	
	public function __toStdObject()
	{
		$stdObject = new stdClass();
		
		foreach($this as $key => $value){
			// Excludes $__dbTable_name and $__dbTable;
			if(substr($key, 0,2) != "__"){
				$stdObject->$key = $value;
			}
		}
		
		return $stdObject;
	}
	
	public function populate($data)
	{
		$class_name = get_class($this);
		foreach($data as $key => $value){
			if(property_exists($class_name, $key)){
				$this->$key = $value;
			}
		}
		
		return $this;
	}
	
    public function getDbTable()
    {
        if (null === $this->__dbTable) {
			$this->__dbTable = new $this->__dbTable_name;
        }
        return $this->__dbTable;
    }
    
    protected function _getPrimaryKey()
    {
    	return current($this->getDbTable()->info("primary"));
    }
    
 
    public function save()
    {
        $data = $this->__toArray();
        $primary_key_name = $this->_getPrimaryKey();
        if ($this->$primary_key_name) {
        	$rowset = $this->getDbTable()->find($this->$primary_key_name);
        	if($rowset->count()){
        		$this->update($data, $rowset->current()->toArray());
        	}else{
        		// Insert using a forced primary key value
        		$this->insert($data);
        	}
        }else{
        	// Insert using an auto-increment primary key value
        	unset($data[$primary_key_name]);
	        $this->insert($data);
        }
        
        return $this;
    }
    
    protected function update(array $data, array $old_data = array())
    {
    	$primary_key_name = $this->_getPrimaryKey();
    	if(property_exists($this, "updated_date")){
        	$data["updated_date"] = gmdate("Y-m-d H:i:s");
        }
    	if(property_exists($this, "updated_by")){
        	$data["updated_by"] = (Zend_Auth::getInstance()->getInstance()->hasIdentity()) ? Zend_Auth::getInstance()->getIdentity()->user_id : null;
        }
        
		$this->getDbTable()->update($data, array("{$primary_key_name} = ?" => $this->$primary_key_name));
		
		$this->find($this->$primary_key_name);
		
    	if($this->__LOG_is_active && method_exists(get_class($this), "log_update")){
    		$this->log_update($old_data);
    	}
    	
    	return $this;
    }
    
    protected function insert(array $data)
    {
    	if(property_exists($this, "created_date") && !isset($data["created_date"])){
        	$data["created_date"] = gmdate("Y-m-d H:i:s");
        }
    	if(property_exists($this, "created_by")){
    		if(is_null($this->created_by))
    		{
    			$identity = Zend_Auth::getInstance()->getIdentity();
    			if($identity)
    			{
	        		$data["created_by"] = Zend_Auth::getInstance()->getIdentity()->user_id;
    			}
    			else
    			{
    				$data["created_by"] = 0;
    			}
    		}
        }
        
    	$model_id = $this->getDbTable()->insert($data);
    	
    	$this->find($model_id);
    	
    	if($this->__LOG_is_active && method_exists(get_class($this), "log_insert")){
    		$this->log_insert();
    	}
    	
    	return $this;
    }
 
    public function find($model_id, $is_find_deleted = false)
    {
    	$this->reset();
    	
    	if($model_id){
	        $result = $this->getDbTable()->find($model_id);
	        if (count($result)) {
		        $row = $result->current();
		        
		        if($is_find_deleted || (!isset($this->is_deleted)) || (isset($this->is_deleted) && !$this->is_deleted)){
		        	$this->populate($row);
		        }
	        }
    	}

    	return $this;
    }
    
    public function reset()
    {
    	foreach($this as $key => $value){
    		$class_name = get_class($this);
    		$empty_obj = new $class_name();
			// Excludes $__dbTable_name and $__dbTable;
			if(substr($key, 0,2) != "__"){
				$this->$key = $empty_obj->$key;
			}
		}
    }
    
    /**
     * @desc Will only soft delete if set to true
     */
    public function delete($using_is_deleted_field = false)
    {
    	$primary_key_name = $this->_getPrimaryKey();
    	
    	$this->find($this->$primary_key_name);
    	
    	if(!$using_is_deleted_field){
    		$this->__dbTable->delete(array("{$primary_key_name} = ?"=>$this->$primary_key_name));
    	}else{
    		$data = array("is_deleted"=>1);
	    	if(property_exists($this, "deleted_date")){
	        	$data["deleted_date"] = gmdate("Y-m-d H:i:s");
	        }
	    	if(property_exists($this, "deleted_by")){
	        	$data["deleted_by"] = Zend_Auth::getInstance()->getIdentity()->user_id;
	        }
    		$this->__dbTable->update($data, array("{$primary_key_name} = ?"=>$this->$primary_key_name));
    	}
    	
		if($this->__LOG_is_active && method_exists(get_class($this), "log_delete")){
    		$this->log_delete();
    	}
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        
        $model = get_class($this);
        
        foreach ($resultSet as $row) {
            $entry = new $model();
            $entry->populate($row);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function is_found()
    {
    	$primary_key = $this->_getPrimaryKey();
    	
    	if($this->$primary_key){
    		return true;
    	}else{
    		return false;
    	}
    }
    
}