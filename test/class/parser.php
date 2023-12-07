<?php
include 'db/mysql.php';

class Parser {

    protected $index;
    private $dirFileError;
    protected $db;

    public function __construct() {
        $this->index = 0;

        $dir = date('Ymd');
        $this->dirFileError = "./error/{$dir}/";
        if(!file_exists($this->dirFileError)){
            if (!mkdir($this->dirFileError, 0777, true)) {
                die('Не удалось создать директории.');
            }
        }
        $this->db = new \DB_MySQLi();

        $this->readFile();
    }

    public function readFile(){
        $file = fopen("test.txt", "r") or die("Не удалось открыть файл");

        while(!feof($file)){
            $str = fgets($file);
            $str = str_replace(PHP_EOL, '', $str);

            if(preg_match('/^\d+;{1}\d+;{1}.+$/', $str)){
                $this->insertStr($str);
            }else{
                $this->errorStr($str);
            }
        }

        fclose($file);
    }

    protected function insertStr($str){
        $str = htmlspecialchars_decode($str);
        $data = explode(';', $str);
        $orderDate = date("Y-m-d H:i:s");
        $comment = $this->addSlashes($data[2]);

        $query = "insert into orders(item_id, customer_id, comment, status, order_date)
                    values({$data[0]}, {$data[1]}, '{$comment}', 'new', '{$orderDate}')";
        $this->db->query($query);
    }

    protected function errorStr($str){
		if(file_exists("{$this->dirFileError}result_{$this->index}.txt")){
            $result = fopen("{$this->dirFileError}result_{$this->index}_1.txt", "a") or die("Не удалось создать файл");
        }else{
            $result = fopen("{$this->dirFileError}result_{$this->index}.txt", "a") or die("Не удалось создать файл");
        }

        $this->index++;
        if(fwrite($result, $str) == false){
            echo 'Ошибка записи строки';
        }
        fclose($result);
    }

    private function addSlashes($str) {
        if (!get_magic_quotes_gpc()) {
            $str = addslashes($str);
        }

        return $str;
    }
}