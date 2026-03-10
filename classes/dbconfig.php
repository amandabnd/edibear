<?php
class Database{ 
    //private $host = "localhost";
    //private $db_name = "traveylo_edibear";
    //private $username = "root";
    //private $password = "";
    
        private $host = "localhost";
    private $db_name = "traveylo_edibear";
    private $username = "root";
    private $password = "root123";
    
    public $conn;
	
    public function dbConnection(){
        $this->conn = null;    
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
        }catch(PDOException $exception){
            $errorMsg = "Connection error: " . $exception->getMessage();
            echo $errorMsg;
        }
        return $this->conn;
    }
}
?>