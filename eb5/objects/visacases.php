<?php


class visacases
{
    private $conn;
    private $table_name = 'eb5table';

    public $id;
    public $case_id;
    public $case_status;
    public $update_date;
    public $approve_date;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read(){

        $query  =   "SELECT * FROM ". $this->table_name. " order by id desc";

        $stmt   = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
    public function findbyid($var) {
        $query = "SELECT * FROM ". $this->table_name. " where case_id=:caseid";

        $stmt   = $this->conn->prepare($query);
        $this->case_id = htmlspecialchars(strip_tags($var));
        $stmt->bindParam(":caseid", $this->case_id);
        $stmt->execute();

        return $stmt;
    }
    public function findbystatus($var) {
        $query = "SELECT * FROM ". $this->table_name. " where case_status=:casestatus";

        $stmt   = $this->conn->prepare($query);
        $this->case_status = htmlspecialchars(strip_tags($var));
        $stmt->bindParam(":casestatus", $this->case_status);
        $stmt->execute();

        return $stmt;
    }

}