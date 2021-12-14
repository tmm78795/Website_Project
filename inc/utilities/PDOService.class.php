<?php

    class PDOService {
    
        private $_host = DB_HOST;
        private $_user = DB_USER;
        private $_pass = DB_PASS;
        private $_db = DB_NAME;
        
        private $_error;
        private $_pstmt;
        private $_dbh;
        private $_className;

        public function PDOService(string $className) {

            $this->_className = $className;

            $dsn = "mysql:host=".$this->_host.";dbname=".$this->_db;

            $options =  array (PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

            //
            try {

                $this->_dbh = new PDO($dsn, $this->_user, $this->_pass, $options);

            }
            catch(PDOException $p){
                $this->_error = $p->getMessage();
                echo $this->_error;
            }
        }

        public function query(string $sql)  {
        //Create a prepared statement from the SQL query
        //Store the new prepared statement object in $this->_pstmt;

        $this->_pstmt = $this->_dbh->prepare($sql);
    }   
     //Bind (bind parameters for the prepared statement)
     public function bind($param, $value, $type=null)   {

        //If the type was set to null
        if (is_null($type)) {
            //Automatically set it.
            switch (true)   {
                //If It is an integer
                case is_int($value):
                    $type = PDO::PARAM_INT;
                break;
                //If a boolean do this
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                break;
                //If a null value do this
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                break;
                //Otherwise do this.
                default:
                    $type = PDO::PARAM_STR;
                break;
            }
        }

        //Bind the parameter to the statement
        $this->_pstmt->bindValue($param, $value, $type);
    }

    //Execute
    public function execute()   {
        $this->_pstmt->execute();
    }

    //Return multiple records
    public function resultSet() {
        return $this->_pstmt->fetchAll(PDO::FETCH_CLASS, $this->_className);
    }

    //Return when expecting one object back.
    public function singleResult() {
        $this->_pstmt->setFetchMode(PDO::FETCH_CLASS, $this->_className);
        return $this->_pstmt->fetch(PDO::FETCH_CLASS);
    }

    // return the primary key that was generated by the system
    public function lastInsertedId() {
        return $this->_dbh->lastInsertId();

    }


    }



?>