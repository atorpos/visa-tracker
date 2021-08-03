<?php


class visacasesupdate
{
    private $conn;
    private $table_name = 'eb5update';

    public $id;
    public $case_id;
    public $case_status;
    public $update_date;
    public $approve_date;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function read(){

        $query  =   "SELECT * FROM ". $this->table_name. " order by id desc";

        $stmt   = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}