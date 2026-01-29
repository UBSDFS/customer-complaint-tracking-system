<!-- DB connection code goes here  for MySQL db in phpMyAdmin -->
<?php
class dataBase
{
    private $host = "localhost";
    private $db_name = ""; //database name
    private $username = ""; //database username
    private $password = ""; //database password
    public $conn; //connection variable

    public function __construct()
    {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Connection error: " . $this->conn->connect_error);
            }
            $this->conn->set_charset("utf8");
        } catch (Exception $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
    }
}
