<?php
class Common {

    protected function logger($user, $method, $action){
        //write an activity inside a log file
        $filename = date("Y-m-d") . ".log";
        $datetime = date("Y-m-d H:i:s");
        $logMessage = "$datetime,$method,$user,$action" . PHP_EOL;
        file_put_contents("./logs/$filename", $logMessage, FILE_APPEND | LOCK_EX);
    }

    private function generateInsertString($tableName, $body){ //function to generate post sql string values
        $keys = array_keys($body);
        $fields = implode(",", $keys);
        $parameter_array = [];
        for($i = 0; $i < count($keys); $i++){
            $parameter_array[$i] = "?";
        }
        $parameters = implode(',', $parameter_array);
        $sql = "INSERT INTO $tableName($fields) VALUES ($parameters)";
        return $sql;
    }
    protected function getDataByTable($tableName, $condition, \PDO $pdo){
        $sqlString = "SELECT * FROM $tableName WHERE $condition"; //select table from where you want to retrieve data.

        $data = array();
        $errmsg = "";
        $code = 0;

        try{
            if($result = $pdo->query($sqlString)->fetchAll()){ //fetch all records to database from table. If the value is returned the if condition will be executed. All records will be stored in $result.
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

    protected function generateResponse($data, $remark, $message, $statusCode){
        $status = array(
            "remark" => $remark,
            "message" => $message
        );

        http_response_code($statusCode);

        return array(
            "payload" => $data,
            "status" => $status,
            "prepared_by" => "Mac and Richard",
            "data_generated" => date_create()
        );
    }

    public function postData($tableName, $body, \PDO $pdo){
        $values = [];
        $errmsg = "";
        $code = 0;

        foreach($body as $value){
            array_push($values, $value);
        }

        try{
            $sqlString = $this->generateInsertString($tableName, $body);
            $sql = $pdo->prepare($sqlString);
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