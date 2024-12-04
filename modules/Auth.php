<?php
class Authentication{

    protected $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }
    
    public function isAuthorized(){
        //compare request token to db token
        $headers = getallheaders();
        return $headers['Authorization'] === $this->getToken(); //to test the authorization
    }

    private function getToken(){
        //retrieve token from db
        $headers = getallheaders();

        $sqlString = "SELECT token FROM accounts_tbl WHERE username=?";
        $stmt = $this->pdo->prepare($sqlString);
        $stmt->execute([$headers['X-Auth-User']]);
        $result = $stmt->fetchAll()[0];
        return $result['token'];
    }

    private function generateHeader(){
        $header = [
            "typ"=> "JWT",
            "alg"=> "HS256",
            "app"=> "ChefHub", //put your own app
            "dev"=> "Mac and Richard"
        ];
        return base64_encode(json_encode($header));
    }

    private function generatePayload($id, $username){ 
        $payload = [    //put any variable you want
            "uid" => $id,
            "uc" => $username,
            "email" => "202310883@gordoncollege.edu.ph", 
            "date" => date_create(),
            "exp" => date("Y-m-d H:i:s")
        ];
        return base64_encode(json_encode($payload));
    }

    private function generateToken($id, $username){
        $header = $this->generateHeader();
        $payload = $this->generatePayload($id, $username);
        $signature = hash_hmac("sha256", "$header.$payload", TOKEN_KEY);
        return "$header.$payload." . base64_encode($signature);
    }

    private function isSamePassword($inputPassword, $existingHash){
        $hash = crypt($inputPassword, $existingHash);
        return $hash === $existingHash;
    }

    private function encryptPassword($password){
        //converts plaintext to gibberish
        //abc -> 2141kjasndkgad,adgnkal
        $hashFormat = "$2y$10$"; //blowfish
        $saltLength = 22;
        $salt = $this->generateSalt($saltLength);
        return crypt($password, $hashFormat . $salt);
    }

    private function generateSalt($Length){
        //random character generator
        //hash
        $urs = md5(uniqid(mt_rand(), true));
        $b64String = base64_encode($urs); //converts hash to base 64 format
        $mb64String = str_replace("+", ".", $b64String); //because base64 generates + we replace it with "." It will create error if there's "+" symbol
        return substr($mb64String, 0, $Length);
    }

    public function saveToken($token, $username){

        $errmsg = "";
        $code = 0;

        try{
            $sqlString = "UPDATE accounts_tbl SET token=? WHERE username = ?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([$token, $username]);

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

    public function login($body){
        $username = $body->username;
        $password = $body->password;

        $code = 0;
        $payload = "";
        $remarks = "";
        $message = "";

        try{
            $sqlString = "SELECT chefsid, username, password, token FROM accounts_tbl WHERE username=?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute([$username]);

            if($stmt->rowCount() > 0){
                $result = $stmt->fetchAll()[0]; //implementing check password
                if($this->isSamePassword($password, $result['password'])){
                    $code = 200;
                    $remarks = "Success";
                    $message = "Logged In Successfully!";

                    $token = $this->generateToken($result['chefsid'], $result['username']);
                    $token_array = explode('.', $token);
                    $this->saveToken($token_array[2], $result['username']);

                    $payload = array("chefsid"=>$result['chefsid'], "username"=>$result['username'], "token"=>$token_array[2]);
                }
                else{
                    $code = 401;
                    $payload = null;
                    $remarks = "failed";
                    $message = "Incorrect Password.";
                }
            }
            else{
                $code = 401;
                $payload = null;
                $remarks = "failed";
                $message = "Username does not exist.";
            }
        }
        catch(\PDOException $e){
            $MESSAGE = $e->getMessage();
            $remarks = "failed";
            $code = 400;
        }
        return array("payload"=>$payload, "remarks"=>$remarks, "message"=>$message, "code"=>$code);
    }

    public function addAccount($body){
        $values = [];
        $errmsg = "";
        $code = 0;


        $body->password =  $this->encryptPassword($body->password);

        foreach($body as $value){
            array_push($values, $value);
        }

        try{
            $sqlString = "INSERT INTO accounts_tbl(chefsid, username, password) VALUES (?,?,?)";
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