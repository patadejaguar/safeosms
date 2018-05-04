<?php 
/*
* ClassName: PHP MySQL Importer v2.0.1
* PHP class for importing big SQL files into a MySql server. 
* Author: David Castillo - davcs86@gmail.com  
* Hire me on: https://www.freelancer.com/u/DrAKkareS.html
* Blog: http://blog.d-castillo.info/
*/

class MySQLImporter { 
    public $hadErrors = false;
    public $errors = array();
    private $conn = null;
     
    public function __construct($host, $user, $pass, $port = false) { 
        if ($port==false){
            $port = ini_get("mysqli.default_port");
        }
        $this->hadErrors = false;
        $this->errors = array();
        $this->conn = new mysqli($host, $user, $pass, "", $port);
        if ($this->conn->connect_error) {
            $this->addError("Connect Error (".$this->conn->connect_errno.") ".$this->conn->connect_error);
        }
    }

    private function addError($errorStr){
        $this->hadErrors = true;
        $this->errors[] = $errorStr;
    }

    public function doImport($sqlFile, $database = "", $createDB = false, $dropDB = false) {    
        if ($this->hadErrors == false) {
            //Drop database if it's required
            if ($dropDB && $database!=""){
                if (!$this->conn->query("DROP DATABASE IF EXISTS ".$database)){
                    $this->addError("Query error (".$this->conn->errno.") ".$this->conn->error);
                }
            }
            //Create the database if it's required
            if ($createDB && $database!=""){
                if (!$this->conn->query("CREATE DATABASE IF NOT EXISTS ".$database)){
                    $this->addError("Query error (".$this->conn->errno.") ".$this->conn->error);
                }
            }
            //Select the database if it's required
            if ($database!=""){
                if (!$this->conn->select_db($database)){
                    $this->addError("Query error (".$this->conn->errno.") ".$this->conn->error);
                }
            }
            if (is_file($sqlFile) && is_readable($sqlFile)){
                try {
                    $f = fopen($sqlFile,"r");
                    $sqlFile = fread($f, filesize($sqlFile));
                    // processing and parsing the content 
                    $sqlFile = str_replace("\r","\n",$sqlFile);
                    $lines = preg_split("/\n/", $sqlFile);
                    $queryStr = "";
                    foreach($lines as $line){
                        $lt_line = ltrim($line);
                        $t_queryStr = trim($queryStr);
                        if (1==preg_match("/^#|^\-\-/",$lt_line) && $t_queryStr == ""){
                            continue; // skip one-line comments
                        }
                        $queryStr .= $line."\n"; // append the line to the current query
                        $t_line = rtrim($lt_line);
                        if (1!==preg_match("/;$/",$t_line)){
                            continue; // skip incomplete statement
                        }
                        if (substr_count($queryStr,"/*")!=substr_count($queryStr,"*/")){
                            continue; // skip incomplete statement (hack for multiline comments)
                        }
                        $queryStr = trim($queryStr);
                        if (!$this->conn->query($queryStr)){
                            $this->addError("Query error (".$this->conn->errno.") ".$this->conn->error."\r\n\r\nOriginal Query:\r\n\r\n".$queryStr);
                        }
                        $queryStr="";
                    }
                } catch(Exception $error) {
                    $this->addError("File error: (".$error->getCode().") ".$error->getMessage());
                }
            } else {
                $this->addError("File error: '".$sqlFile."' is not a readable file.");
            }
        }
    }
}  
    
?> 
