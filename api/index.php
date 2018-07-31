<?php
require 'config.php';
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->post('/register','register'); /* User Register  */


$app->run();



/* ### User registration ### */
function register() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $email=$data->email;
    $phone=$data->phone;
    $name=$data->name;
    
    try {
        
        $name_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $name);
        $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
        
        
        
        if (strlen(trim($name))>0 && strlen(trim($phone))>0 && strlen(trim($email))>0 && $email_check>0 && $name_check>0)
        {
            
            $db = getDB();
            $userData = '';
            $sql = "SELECT user_id FROM users WHERE name=:name or email=:email";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("name", $name,PDO::PARAM_STR);
            $stmt->bindParam("email", $email,PDO::PARAM_STR);
            $stmt->execute();
            $mainCount=$stmt->rowCount();
            $created=time();
            if($mainCount==0)
            {
                
                /*Inserting user values*/
                $sql1="INSERT INTO users(name,email,phone)VALUES(:name,:email,:phone)";
                $stmt1 = $db->prepare($sql1);
                $stmt1->bindParam("name", $name,PDO::PARAM_STR);
                $stmt1->bindParam("email", $email,PDO::PARAM_STR);
                $stmt1->bindParam("phone", $phone,PDO::PARAM_STR);
                $stmt1->execute();
                
                $userData=internalUserDetails($email);
                
            }
            
            $db = null;
         

            if($userData){
               $userData = json_encode($userData);
                echo '{"userData": ' .$userData . '}';
            } else {
               echo '{"error":{"text":"Enter valid data"}}';
            }

           
        }
        else{
            echo '{"error":{"text":"Enter valid data"}}';
        }
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

?>
