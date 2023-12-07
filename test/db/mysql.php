<?php
include 'config.php';
class DB_MySQLi {
    protected $connection;

    public function __construct() {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_SCHEMA, 3306);
        $this->connection->set_charset('utf8');
    }

    public function query($query){
        $this->connection->query($query);
    }
}
