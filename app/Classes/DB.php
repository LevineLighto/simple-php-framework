<?php

namespace App\Classes;

use App\Classes\Config\Database;
use DateTime;
use Exception;
use mysqli;

class DB {
    protected mysqli $dbConnection;
    protected $statement;
    protected $result;

    protected string $tablename;
    protected string $conditions;
    protected array $conditionValues;

    protected string $query;

    protected int $insertId;

    protected bool $closable;
    
    public function __construct()
    {
        $default    = Database::get('default');

        if(!$default) {
            throw new Exception("Database config is not properly set.");
        }

        $this->conditions   = 'WHERE';
        $this->closable     = true;

        $this->setConnection($default);
    }

    /**
     * Atur koneksi mysqli
     */
    private function setConnection($connection) {
        $host       = $connection['hostname'];
        $username   = $connection['username'];
        $password   = $connection['password'];
        $database   = $connection['database'];
        $port       = null;

        if(!empty($connection['port'])) {
            $port = $connection['port'];
        }

        $this->dbConnection = new mysqli($host, $username, $password, $database, $port);

        if($this->dbConnection->errno) {
            $error_no   = $this->dbConnection->errno;
            $message    = $this->dbConnection->error;
            $this->dbConnection->close();

            throw new Exception("Error connecting to database: ({$error_no}) {$message}");
        }
    }

    /**
     * Ubah koneksi
     * @return \App\Classes\DB
     */
    public function connection($name) {
        $connection = Database::get($name);

        if(!$connection) {
            throw new Exception("Database name cannot be found.");
        }

        $this->dbConnection->close();

        $this->setConnection($connection);

        return $this;
    }

    /**
     * Select table
     * @return \App\Classes\DB
     */
    public function table($name) {
        $this->tablename = $name;

        return $this;
    }
    
    /**
     * Prepare sql query
     * @return \App\Classes\DB
     */
    public function prepare($statement, $data = []) {
        $datacount = count($data);

        $this->query = $statement;

        $this->statement = $this->dbConnection->prepare($statement);
        if(!$this->statement) {
            $error      = $this->dbConnection->errno;
            $message    = $this->dbConnection->error;
            throw new Exception("Could not prepare query (Error: {$error} - {$message})\n{$statement}\n");
        }
        if($datacount) {
            $type = '';
            for($i = 0; $i < $datacount; $i++) {
                $type .= 's';
            }

            $this->statement->bind_param($type, ...$data);
        }
        return $this;
    }

    /**
     * Add condition
     * @return \App\Classes\DB
     */
    public function where(string $column, string $operator, $value) {
        if($value instanceof DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        }

        if($this->conditions != 'WHERE') {
            $this->conditions .= ' AND ';
        } else {
            $this->conditions .= ' ';
        }

        $this->conditions .= "{$column} {$operator} ?";
        $this->conditionValues[] = $value;

        return $this;
    }

    /**
     * Save prepared sql query
     * @return boolean
     */

    public function save() {
        if(empty($this->statement)) {
            return false;
        }

        if($this->statement->execute()) {
            if($this->dbConnection->insert_id) {
                $this->insertId = $this->dbConnection->insert_id;
            }

            $this->close();
            return true;
        }

        if($this->statement->errno) {
            $errors = json_encode($this->statement->error_list, JSON_PRETTY_PRINT);
            $errorCode = $this->statement->errno;
            $error  = $this->statement->error;

            $this->close();

            throw new Exception("DB Error (Error code: {$errorCode}) {$error}\nQuery:{$this->query} \n{$errors}");
        }
        
        $this->close();
        return false;
    }

    /**
     * Get data
     * @return array
     */

    public function get() {
        if(empty($this->statement)) {
            return false;
        }

        $data = [];
        $this->statement->execute();
        $this->result = $this->statement->get_result();

        while ($row = $this->result->fetch_object()) {
            $data[] = $row;
        }
        $this->close();

        return $data;
    }

    /**
     * Get first data
     * @return \stdClass
     */

    public function first() {
        if(empty($this->statement)) {
            return false;
        }

        $this->statement->execute();
        $this->result = $this->statement->get_result();
        $output = $this->result->fetch_object();

        $this->close();

        return $output;
    }
    
    /**
     * Get rows found
     * @return int
     */
    public function count() {
        if(empty($this->statement)) {
            throw new Exception('Query is not prepared');
        }

        $this->statement->execute();
        $this->result = $this->statement->get_result();

        $count = $this->result->num_rows;

        $this->close();

        return $count;
    }

    /**
     * Eksekusi statemen
     */
    public function execute() {
        if(empty($this->statement)) {
            throw new Exception('Query is not prepared');
        }

        $this->statement->execute();

        $this->close();
    }

    /**
     * Insert data
     * @return boolean
     */
    public function insert(array $data) {
        if(!isAssoc($data)) {
            throw new Exception("Data invalid");
        }

        if(empty($this->tablename)) {
            throw new Exception("Table is not set");
        }

        $keys           = [];
        $values         = [];
        $placeholder    = [];

        foreach($data as $key => $value) {
            $keys[] = $key;
            if($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }
            $values[] = $value;
            $placeholder[] = '?';
        }

        $keys           = implode(',', $keys);
        $placeholder    = implode(',', $placeholder);

        $query = "INSERT INTO {$this->tablename} ({$keys}) VALUES({$placeholder})";

        return $this->prepare($query, $values)->save();
    }

    /**
     * Insert dan dapatkan id item terbaru
     * @return int
     */
    public function insertGetId(array $data) {
        if(!$this->insert($data)) {
            return 0;
        }

        return $this->insertId;
    }

    /**
     * Update data
     * @return boolean
     */
    public function update(array $data) {
        if(!isAssoc($data)) {
            throw new Exception("Data invalid");
        }

        if(empty($this->tablename)) {
            throw new Exception("Table is not set");
        }

        $keys           = [];
        $values         = [];

        foreach($data as $key => $value) {
            $keys[] = "{$key} = ?";
            
            if($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }
            $values[] = $value;
        }

        $keys           = implode(',', $keys);

        $query = "UPDATE {$this->tablename} SET {$keys}";

        if(!empty($this->conditionValues)) {
            $query .= " {$this->conditions}";
            $values = array_merge($values, $this->conditionValues);
        }

        return $this->prepare($query, $values)->save();
    }

    /**
     * Insert or Update data
     * @return boolean
     */
    public function insertOrUpdate(array $keys, array $values = []) {
        if(!isAssoc($keys)) {
            throw new Exception("Data invalid");
        }

        if(empty($this->tablename)) {
            throw new Exception("Table is not set");
        }

        $conditionKeys      = '';
        $conditionValues    = [];

        foreach($keys as $key => $value) {
            if(!empty($conditionKeys)) {
                $conditionKeys .= ' AND ';
            } else {
                $conditionKeys = 'WHERE ';
            }
            $conditionKeys  .= "{$key} = ?";

            if($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }
            $conditionValues[] = $value;
        }

        $this->closable = false;

        $query = "SELECT id FROM {$this->tablename} {$conditionKeys} LIMIT 1";
        $entry = $this->prepare($query, $conditionValues)->first();

        $data = array_merge($keys, $values);

        $this->closable = true;

        if(empty($entry)) {
            return $this->insertGetId($data);
        } 

        return $this->where('id', '=', $entry->id)
                ->update($data);
    }

    /**
     * First or Insert data
     * @return stdClass
     */
    public function firstOrInsert(array $values) {
        if(empty($this->tablename)) {
            throw new Exception("Table is not set");
        }

        $conditionKeys      = '';
        $conditionValues    = [];

        foreach($values as $key => $value) {
            if(!empty($conditionKeys)) {
                $conditionKeys .= ' AND ';
            } else {
                $conditionKeys = 'WHERE ';
            }
            $conditionKeys  .= "{$key} = ?";

            if($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }
            $conditionValues[] = $value;
        }

        $this->closable = false;

        $query = "SELECT * FROM {$this->tablename} {$conditionKeys} LIMIT 1";
        $entry = $this->prepare($query, $conditionValues)->first();

        
        if(!empty($entry)) {
            $this->closable = true;
            $this->close();
            return $entry;
        }
        
        $id = $this->insertGetId($values);

        $this->closable = true;

        $query = "SELECT * FROM {$this->tablename} WHERE id = ? LIMIT 1";
        $entry = $this->prepare($query, [$id])->first();

        return $entry;
    }

    /**
     * Close connection
     */

    public function close() {
        if(!empty($this->statement)) {
            $this->statement->close();
            unset($this->statement);
        }

        if(!empty($this->result)) {
            $this->result->close();
            unset($this->result);
        }

        if(!$this->closable) {
            return;
        }

        return $this->dbConnection->close();
    }
}