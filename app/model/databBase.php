<!-- DB connection code goes here  for MySQL db in phpMyAdmin -->
<?php
class dataBase
{
    private $host = "localhost";
    private $db_name = "complaint_system"; //database name
    private $username = "root"; //database username
    private $password = ""; //database password
    public $conn; //connection variable

    public function __construct()
    {
        $this->conn = null;
        try {
            // Connect without database
            $this->conn = new mysqli(
                $this->host, 
                $this->username,
                $this->password
                );

            if ($this->conn->connect_error) {
                throw new Exception("Connection error: " . $this->conn->connect_error);
            }

            // Create Database if it does not exist
            $this->conn->query(
                "CREATE DATABASE IF NOT EXISTS {$this->db_name}
                CHARACTER SET utf8"
                );

            // select DB
            $this->conn->select_db($this->db_name);

            // load DB
            $schemaPath = __DIR__ . '/../../database/database.sql';
            if (!file_exists($schemaPath)) {
                throw new Exception("database.sql not found");
            }

            $sql = file_get_contents($schemaPath);

            if (!$this->conn->multi_query($sql)) {
                throw new Exception("Schema execution failed: " . $this->conn->error);
            }

            // Clear multi_query results
            while ($this->conn->more_results()) {
                $this->conn->next_result();
            }

            $this->conn->set_charset("utf8");
        } catch (Exception $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
    }
}
