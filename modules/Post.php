<?php
class Post{

    protected $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }
    
    public function postStudents(){
        //code for retrieving data on DB
        return "This is some student records added.";
    }
    public function postClasses(){
        //code for retrieving data on DB
        return "This is some classes records added.";
    }
    public function postFaculty(){
        //code for retrieving data on DB
        return "This is some faculty records added.";
    }

    public function postChefs($body){
        $values = [];
        $errmsg = "";
        $code = 0;

        foreach($body as $value){
            array_push($values, $value);
        }

        try{
            $sqlString = "INSERT INTO chefs_tbl(fname, lname, position, isdeleted) VALUES (?,?,?,?)";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);

            $code = 200;
            $data = null;

            return array("data"=>$data, "code"=>$code);
        }
        catch(\PDOException $e){
            $errmsg = $e->getMessage();
            $code = 400;
        }


        return array("errmsg"=>$errmsg, "code"=>$code);
    }
}
?>