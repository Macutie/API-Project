<?php
class Get{

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
       $sqlString = "SELECT * FROM chefs_tbl"; //select table from where you want to retrieve data.
        if($id != null){
            $sqlString .= " WHERE id=" . $id; 
        }

       $data = array();
       $errmsg = "";
       $code = 0;

       try{
        if($result = $this->pdo->query($sqlString)->fetchAll()){ //fetch all records to database from table. If the value is returned the if condition will be executed. All records will be stored in $result.
            foreach($result as $record){ //looping all values inside result and all records will be pushed inside array_push
                array_push($data, $record); //every record or iteration will be read inside the data array. Stored inside the data array.
            }
            $result = null;
            $code = 200;
            return array("code"=>$code, "data"=>$data); 
        }
        else{
            $errmsg = "No data found";
            $code = 404;
        }
       }
       catch(\PDOException $e){ //if there is an error in the query
        $errmsg = $e->getMessage(); //store the error message
        $code = 403; //store the error code
       }

       return array("code"=>$code, "errmsg"=>$errmsg); //responsible for returning error and successful record.
    }
}
?>