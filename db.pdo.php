<?php
    class database extends PDO
    {
        public $isConnected;
        protected $datab;
        public function __construct($username, $password, $host, $dbname, $options=array()){
            $this->isConnected = true;
            if(!is_array($options)){$options=array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');}
            try { 
                $this->datab = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); 
                $this->datab->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
                $this->datab->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } 
            catch(PDOException $e) { 
                $this->isConnected = false;
                die( $e->getMessage() );
            }
        }
        public function disconnect(){
            $this->datab = null;
            $this->isConnected = false;
        }
        public function getRow($query, $params=array()){
            try{ 
                $stmt = $this->datab->prepare($query); 
                $stmt->execute($params);
                return $stmt->fetch();  
                }catch(PDOException $e){
                throw new Exception($e->getMessage());
            }
        }
        public function getRows($query, $params=array()){
            try{ 
                $stmt = $this->datab->prepare($query); 
                $stmt->execute($params);
                return $stmt->fetchAll();       
                }catch(PDOException $e){
                throw new Exception($e->getMessage());
            }       
        }
        public function insertRow($query, $params){
            try{ 
                $stmt = $this->datab->prepare($query); 
                $stmt->execute($params);
                }catch(PDOException $e){
                throw new Exception($e->getMessage());
            }           
        }
        public function updateRow($query, $params){
            return $this->insertRow($query, $params);
        }
        public function deleteRow($query, $params){
            return $this->insertRow($query, $params);
        }
        public function getNumber($query,$params){
            //mysql_num_rows
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        }
        public function getFetch($query,$params){
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
?>