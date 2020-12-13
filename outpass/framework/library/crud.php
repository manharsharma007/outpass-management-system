<?php
/**
 * Abstract class which has helper functions to get data from the database
 */
class crud
{
    /**
     * The current table name
     *
     * @var boolean
     */
    private $tableName = false;
    private $_connection;

    /**
     * Constructor for the database class to inject the table name
     *
     * @param String $tableName - The current table name
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
        $this->_connection = Database::connect();
    }

    public function get_connection()
    {
        return $this->_connection;
    }
    
    public function changeTable($tableName)
    {
        $this->tableName = $tableName;
    }
    /**
     * Insert data into the current data
     *
     * @param  array  $data - Data to enter into the database table
     *
     * @return InsertQuery Object
     */
    public function insert(array $data)
    {
        try
        {
             $sql = sprintf("INSERT INTO %s ( `%s` ) %sVALUES ( :%s );",
                        $this->tableName,
                        implode("`, `", array_keys($data)), 
                        PHP_EOL, 
                        implode(", :", array_keys($data))
                    );
                    
            $stmt = $this->_connection->prepare($sql);
            foreach ($data as $field => $value) {
                $stmt->bindValue(":$field", $value, PDO::PARAM_STR);
            }
            $stmt->execute();
            return $this->_connection->lastInsertId();
        }
        catch(PDOException $e)
        {
            $existingkey = "Integrity constraint violation: 1062 Duplicate entry";
            if (strpos($e->getMessage(), $existingkey) !== FALSE) {

                throw new Exception("Integrity constraint violation: 1062 Duplicate entry");
            }
            
            else
                throw new Exception("Error Processing Request. Error -> ".$e->getMessage());
            
            return false;
        }
    }

    /**
     * Get all from the selected table
     *
     * @param  String $orderBy - Order by column name
     *
     * @return Table result
     */
    public function get_all( $orderBy = NULL, $limit = false)
    {
        try
        {

            $sql = 'SELECT * FROM `'.$this->tableName.'`';

            if(!empty($orderBy))
            {
                $sql .= ' ORDER BY ' . $orderBy;
            }
            if($limit !== false)
                  $sql .= ' limit '.$limit;

            $stmt = $this->_connection->prepare($sql);
            $stmt->execute();
            $all = $stmt->fetchAll(PDO::FETCH_CLASS);

            return $all;
        }

        catch(PDOException $e)
        {
            throw new Exception("Error Processing Request. Error -> ".$e->getMessage());
        }
    }

    public function get_specific_till( array $orderBy, array $condition = [], $limit = 20)
    {
        try
        {

            $sql = 'SELECT * FROM `'.$this->tableName .'`';
            
            if(!empty($condition))
              $sql .= ' where '.$condition['key'].'=\''.$condition['value'].'\'';
              
            if(!empty($orderBy))
            {
               $sql .= ' ORDER BY ' . $orderBy['param'].' '.$orderBy['type'];
            }

            $sql .= ' limit '.$limit;
            
            $stmt = $this->_connection->prepare($sql);
            $stmt->execute();
            $all = $stmt->fetchAll(PDO::FETCH_CLASS);

            return $all;
        }


        catch(PDOException $e)
        {
            throw new Exception("Error Processing Request. Error -> ".$e->getMessage());
        }
    }




    public function get_col($col, array $conditionValue, $condition = '=', $returnSingleRow = FALSE, array $orderBy = [])
    {

        try
        {
            $sql = 'SELECT * FROM `'.$this->tableName.'` WHERE ';

            $conditionCounter = 1;
            foreach ($conditionValue as $field => $value)
            {
                if($conditionCounter > 1)
                {
                    $sql .= ' AND ';
                }

                switch(strtolower($condition))
                {
                    case 'in':
                        if(!is_array($value))
                        {
                            throw new Exception("Values for IN query must be an array.", 1);
                        }

                        $sql .= sprintf('`%s` IN (%s)', $field, implode(',', '\''.$value.'\''));
                        break;

                    default:
                        $sql .= sprintf('`'.$field.'` '.$condition.' %s', '\''.$value.'\'');
                        break;
                }

                $conditionCounter++;
            }
            if(!empty($orderBy))
                $sql .= ' order by '.$orderBy['param'].' '.$orderBy['type'];
  
            $stmt = $this->_connection->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_CLASS);

            // As this will always return an array of results if you only want to return one record make $returnSingleRow TRUE
            if(count($result) == 1 && $returnSingleRow)
            {
                $result = $result[0];
            }

            return $result;
        }
        
        catch(PDOException $e)
        {
            throw new Exception("Error Processing Request. Error -> ".$e->getMessage());
        }
    }

    /**
     * Get a value by a condition
     *
     * @param  Array $conditionValue - A key value pair of the conditions you want to search on
     * @param  String $condition - A string value for the condition of the query default to equals
     *
     * @return Table result
     */
    public function get_by(array $conditionValue, $condition = '=', $returnSingleRow = FALSE, array $orderBy = [])
    {

        try
        {
            $sql = 'SELECT * FROM `'.$this->tableName.'` WHERE ';

            $conditionCounter = 1;
            foreach ($conditionValue as $field => $value)
            {
                if($conditionCounter > 1)
                {
                    $sql .= ' AND ';
                }

                switch(strtolower($condition))
                {
                    case 'in':
                        if(!is_array($value))
                        {
                            throw new Exception("Values for IN query must be an array.", 1);
                        }

                        $sql .= sprintf('`%s` IN (%s)', $field, implode(',', '\''.$value.'\''));
                        break;

                    default:
                        $sql .= sprintf('`'.$field.'` '.$condition.' %s', '\''.$value.'\'');
                        break;
                }

                $conditionCounter++;
            }
            if(!empty($orderBy))
                $sql .= ' order by '.$orderBy['param'].' '.$orderBy['type'];
  
            $stmt = $this->_connection->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_CLASS);

            // As this will always return an array of results if you only want to return one record make $returnSingleRow TRUE
            if(count($result) == 1 && $returnSingleRow)
            {
                $result = $result[0];
            }

            return $result;
        }
        
        catch(PDOException $e)
        {
            throw new Exception("Error Processing Request. Error -> ".$e->getMessage());
        }
    }

    /**
     * Update a table record in the database
     *
     * @param  array  $data           - Array of data to be updated
     * @param  array  $conditionValue - Key value pair for the where clause of the query
     *
     * @return Updated object
     */
    public function update(array $data,array $conditions)
    {
        try
        {
            if(!empty($data) && is_array($data)){
                $colvalSet = '';
                $whereSql = '';
                $i = 0;
                
                foreach($data as $key=>$val){
                    $pre = ($i > 0)?', ':'';
                    $colvalSet .= $pre.$key."='".$val."'";
                    $i++;
                }
                if(!empty($conditions)&& is_array($conditions)){
                    $whereSql .= ' WHERE ';
                    $i = 0;
                    foreach($conditions as $key => $value){
                        $pre = ($i > 0)?' AND ':'';
                        $whereSql .= $pre.$key." = '".$value."'";
                        $i++;
                    }
                }
                $sql = "UPDATE ".$this->tableName." SET ".$colvalSet.$whereSql;
                $query = $this->_connection->prepare($sql);
                $update = $query->execute();
                return $update?$query->rowCount():false;
            }else{
                return false;
            }
        }


        catch(PDOException $e)
        {
            $existingkey = "Integrity constraint violation: 1062 Duplicate entry";
            if (strpos($e->getMessage(), $existingkey) !== FALSE) {

                throw new Exception("Integrity constraint violation: 1062 Duplicate entry");
            }
            
            else
                throw new Exception("Error Processing Request. Error -> ".$e->getMessage());
        }
    }
    
    /*
     * Delete data from the database
     * @param string name of the table
     * @param array where condition on deleting data
     */
    public function delete(array $conditions)
    {
        try
        {
            $whereSql = '';
            if(!empty($conditions)&& is_array($conditions)){
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach($conditions as $key => $value){
                    $pre = ($i > 0)?' AND ':'';
                    $whereSql .= $pre.$key." = '".$value."'";
                    $i++;
                }
            }
            $sql = "DELETE FROM ".$this->tableName.$whereSql;
            $delete = $this->_connection->exec($sql);
            return $delete?$delete:false;
        }
        

        catch(PDOException $e)
        {
            throw new Exception("Error Processing Request. Error -> ".$e->getMessage());
        }
    }



    public function exec_query($query, $markers = false) {
        $con = $this->_connection;
        $dbResult = $con->prepare($query);
        try {
                if($markers !== false) {
                    foreach($markers as $key=>$value)
                        $dbResult->bindValue($key,$value);
                }
            $dbResult->execute();
        return $dbResult;
        }
        catch(PDOException $e) {
            throw new PDOException("Error Processing Request. Error -> ".$e->getMessage());
            return false;            
        }
    }

}


