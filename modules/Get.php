<?php
include_once "Common.php";
class Get extends Common{

    protected $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }
    
    public function getLogs($date){
        $filename = "./logs/". $date . ".log";
        // $file = file_get_contents("./logs/$filename"); // another version 
        // $logs = explode(PHP_EOL, $file); // another version

        $logs = array();
        try{
            $file = new SplFileObject($filename);
            while(!$file->eof()){
                array_push($logs, $file->fgets());
            }
            $remarks = "Success";
            $message = "Successfully retrieved logs.";
        }
        catch(Exception $e){
            $remarks = "Failed";
            $message = $e->getMessage();
        }

        return $this->generateResponse(array("logs"=>$logs), $remarks, $message, 200);
    }

    public function getStudents(){
        //code for retrieving data on DB
        return "This is some student records retrieved from db";
    }
    public function getClasses(){
        //code for retrieving data on DB
        return "This is some classes records retrieved from db";
    }
    public function getFaculty(){
        //code for retrieving data on DB
        return "This is some Faculty records retrieved from db";
    }
    public function getQuests(){
        //code for retrieving data on DB
        return "This is some Quests records retrieved from db";
    }

    public function getChefs($id = null){ //functions significant for your own database
       $condition = "isdeleted = 0";
        if($id != null){
            $condition .= " AND id=" . $id; 
        }

        $result = $this->getDataByTable('chefs_tbl', $condition, $this->pdo);
        if($result['code'] == 200){
            return $this->generateResponse($result['data'], "success", "Sucessfully retrieved records.", $result['code']);
        }
        else{
            return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
        }
    }

    public function getMenu($id = null){ //functions significant for your own database
        $condition = "isdeleted = 0"; //select table from where you want to retrieve data.
         if($id != null){
            $condition .= " AND id=" . $id; 
        }

        $result = $this->getDataByTable('menu_tbl', $condition, $this->pdo);
        if($result['code'] == 200){
            return $this->generateResponse($result['data'], "success", "Successfully retrieved records.", $result['code']);
        }
        else{
            return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
        }
    }
}
?>