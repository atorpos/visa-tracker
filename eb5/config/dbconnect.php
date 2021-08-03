<?php


class dbconnect
{
    private $host = "testingdb.cnurlrknw76w.us-west-2.rds.amazonaws.com";
    private $db_name = "visa_container";
    private $username = "admin";
    private $password = "xepxub-danwat-2qAxfi";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}