<?php
namespace Usersystem;

class DataSource
{
   const USERTABLE = 'Users';
   private $conn;


    function __construct()
    {
		if(!isset($wehaveconn)){
        $this->conn = $this->getConnection();
		$wehaveconn = 1;
        }
	}

    public function getConnection()
    {
		$dbconf = parse_ini_file("../../private/db-config.ini");
        $conn = new \mysqli($dbconf['host'], $dbconf['dbuser'], $dbconf['dbpass'], $dbconf['dbname']);
        
        if (mysqli_connect_errno()) {
            trigger_error("Problem with connecting to database.");
        }
        
        $conn->set_charset("utf8");
        return $conn;
    }


    public function select($query, $paramType="", $paramArray=array())
    {
        $stmt = $this->conn->prepare($query);
        
        if(!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $resultset[] = $row;
            }
        }
        
        if (! empty($resultset)) {
            return $resultset;
        }
    }
    

    public function insert($query, $paramType, $paramArray)
    {
        $stmt = $this->conn->prepare($query);
        $this->bindQueryParams($stmt, $paramType, $paramArray);
        $stmt->execute();
        $insertId = $stmt->insert_id;
        return $insertId;
    }
	

	    public function del($query, $paramType, $paramArray)
    {
        $stmt = $this->conn->prepare($query);
		//$stmt->bind_param($paramType, $paramArray);
        $this->bindQueryParams($stmt, $paramType, $paramArray);
        $stmt->execute();
        $delId = $stmt->del_id;
        return $delId;
    }
	
    
    public function execute($query, $paramType="", $paramArray=array())
    {
        $stmt = $this->conn->prepare($query);
        
        if(!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType="", $paramArray=array());
        }
        $stmt->execute();
    }
    

    public function bindQueryParams($stmt, $paramType, $paramArray=array())
    {
        $paramValueReference[] = & $paramType;
        for ($i = 0; $i < count($paramArray); $i ++) {
            $paramValueReference[] = & $paramArray[$i];
        }
        call_user_func_array(array(
            $stmt,
            'bind_param'
        ), $paramValueReference);
    }
    
 
    public function numRows($query, $paramType="", $paramArray=array())
    {
        $stmt = $this->conn->prepare($query);
        
        if(!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        
        $stmt->execute();
        $stmt->store_result();
        $recordCount = $stmt->num_rows;
        return $recordCount;
    }
}