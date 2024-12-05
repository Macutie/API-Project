<?php
include_once "Common.php";
class Get extends Common{

    protected $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
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