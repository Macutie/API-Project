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
}
?>