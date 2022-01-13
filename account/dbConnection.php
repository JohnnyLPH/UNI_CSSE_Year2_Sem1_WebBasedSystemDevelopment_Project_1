<?php

/*
    the mysql table element
    user ID    - userID (int 11) (PRIMARY KEY AUTOMATIC)
    first name - f_name   (VARCHAR (50))
    last name  - l_name   (VARCHAR (50))
    email      - email    (VARCHAR (50))
    mobile num - mobile   (VARCHAR (50))
    password   - password (VARCHAR (50)) (in hash form)
    gender     - gender   (VARCHAR (6))
    state      - state    (VARCHAR (30))
*/
class NormalUser{
    const DB_HOST = "localhost";
    const DB_USERNAME = "p1-admin";
    const DB_PSD = "dummy123";
    const DB = "db75405";
    private $userID;
    private $db_connector;

    function __construct(){

        $this->db_connector = mysqli_connect(self::DB_HOST, self::DB_USERNAME, self::DB_PSD, self::DB);
        //connect to server AND database
        if(!$this->db_connector){
            echo 'Could not connect to server<br/>';
            trigger_error(mysqli_error(), E_USER_ERROR);
        }else{
           /* echo 'connection established<br/>'; */
        }
        
    }

    function execQuery($query, &$rs){
        $rs = mysqli_query($this->db_connector, $query);
       /*  echo '<br/>halo<br/>'; */
        if (!$rs) {
            echo "Could not execute query: $query";
            trigger_error(mysqli_error(), E_USER_ERROR); 
            return false;
        }else{
            /* echo "Query: $query executed<br/>"; */
            return true;
        }
    }

    //used for checking whether if that record had existed in database or not
    function isExistInDb($table, $key, $value){
        $key_escape = mysqli_real_escape_string($this->db_connector, $key);
        $query1 = "EXISTS(SELECT * from ".$table." WHERE ".$key." = '".$value."')";
        $query2 = "SELECT ".$query1;
        if(self::execQuery($query2, $rs)){
            $result = mysqli_fetch_assoc($rs);
            //if equal to 1, means there had record existed inside the table
            return ($result[$query1] == 1);
            
        }else{
            return false;
        }
    }

    //if register success, return true
    //else return false
    function insertNewRecord($post_fname, $post_lname, $post_email, $post_mobile, $post_password, $post_gender, $post_state){
        $f_name_escape = mysqli_real_escape_string($this->db_connector, $post_fname);
        $l_name_escape = mysqli_real_escape_string($this->db_connector, $post_lname);
        $email_escape = mysqli_real_escape_string($this->db_connector, $post_email);
        $mobile_escape = mysqli_real_escape_string($this->db_connector, $post_mobile);
        $password_escape = mysqli_real_escape_string($this->db_connector, $post_password);
        $gender_escape = mysqli_real_escape_string($this->db_connector, $post_gender);
        $state_escape = mysqli_real_escape_string($this->db_connector, $post_state);

        //generate hash password
        $hash_password_generated = password_hash($password_escape, PASSWORD_DEFAULT);
        
        //check whether if the email had registered
        //$query1 = "SELECT EXISTS(SELECT * from normalUser WHERE email = 'email_escape')";
        if(!self::isExistInDb("normalUser", "email", $post_email)){
            //if no, then insert new record to db
            $query = "INSERT INTO normalUser (f_name, l_name, email, mobile, password, gender, state) VALUES (
                '$f_name_escape', '$l_name_escape', '$email_escape', '$mobile_escape', 
                '$hash_password_generated', '$gender_escape', '$state_escape')";
            //if mysqli_query return null
            
            return (mysqli_query($this->db_connector, $query) != false);
                
        }else{
            return false;
        }
        
    }

    //return true, if login success
    //else return false
    function login($post_email, $post_password){
        $query = "SELECT * FROM normalUser";
        $email_login= $password_login = "";
        
        $email_login = mysqli_real_escape_string($this->db_connector, $post_email);
        $password_login = mysqli_real_escape_string($this->db_connector ,$post_password);

        $password_extracted = $userID_extracted = $email_extracted = "";

        // Prepare a select statement
        $sql = "SELECT userID, email, password FROM normalUser WHERE email = ?";
        
        if($stmt = mysqli_prepare($this->db_connector, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email_login;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if email exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $userID_extracted, $email_extracted, $password_extracted);
                    if(mysqli_stmt_fetch($stmt)){
                        /* echo '<br/>'.password_hash($password_login, PASSWORD_DEFAULT).'<br/>'.strlen(password_hash($password_login, PASSWORD_DEFAULT)).'<br/>';
                        echo $password_login; */
                        if(password_verify($password_login, $password_extracted)){
                            // Password is correct, so start a new session
                           // Store data in session variables
                           if(isset($_SESSION["loggedIn"])){
                               unset($_SESSION["loggedIn"]);
                           }
                           if(isset($_SESSION["userID"])){
                               unset($_SESSION["userID"]);
                           }
                           if(isset($_SESSION["loggedInTime"])){
                               unset($_SESSION["loggedInTime"]);
                           }
                            $_SESSION["loggedIn"] = true;
                            $_SESSION["userID"] = $userID_extracted;
                            $_SESSION["loggedInTime"] = date('Y-m-d H:i:s');
                            //pre-set logout datetime, if user directly close the window,
                            //php perform operation to myql would not be handled
                            return true;
                            
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid password.";
                            echo '<br/>'.$login_err;
                            return false;
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username.";
                    echo '<br/>'.$login_err;
                    return false;
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
                return false;
            }
        }
        echo '<br/>Fail to login';

        return false;
    }

    
    

    //update one user information
    //$userID == userID
    //$key == the data column that you want to updated/modified
    //$value == value that you want to added to that column 
    function updateUserRecordByID($table, $userID ,$key, $value){
        if(self::isExistInDb($table, "userID", $userID)){
            $query = "UPDATE ".$table." SET ".$key."='".$value."' WHERE userID=".$userID;
            return execQuery($query, $rss) == true;
        }else{
            return false;
        }
    }

    function updateUserRecordByEmail($table, $userEmail ,$key, $value){
        if(!self::isExistInDb($table, "email", $userEmail)){
            $query = "UPDATE ".$table." SET ".$key."='".$value."' WHERE userID=".$userID;
            return execQuery($query, $rss) == true;
        }else{
            return false;
        }
    }

    function updateUserPasswordByEmail($table, $userEmail , $value){
        
        if(self::isExistInDb("normalUser", "email", $userEmail)){
            
            // Prepare a select statement
            $password_update = mysqli_real_escape_string($this->db_connector ,$value);
            $hash_password_generated = password_hash($password_update, PASSWORD_DEFAULT);
            $email_update = mysqli_real_escape_string($this->db_connector ,$userEmail);
            
            $sql = "UPDATE normalUser SET password= ? WHERE email = ?";

            if($stmt = mysqli_prepare($this->db_connector, $sql)){  
                
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ss", $hash_password_generated,$email_update);
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    
                    // Store result
                   return true;
                } 

            }
        } 
        
        return false;  
    }

    //Check password with userID
    function authenticatePassword($userID, $passwordTest){ 

        if(self::isExistInDb("normalUser", "userID", $userID)){
            // Prepare a select statement
            $sql = "SELECT userID, password FROM normalUser WHERE userID = ?";
            $userID_login = mysqli_real_escape_string($this->db_connector, $userID);
            $password_login = mysqli_real_escape_string($this->db_connector ,$passwordTest);

            if($stmt = mysqli_prepare($this->db_connector, $sql)){  
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "i", $userID_login);
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Store result
                    mysqli_stmt_store_result($stmt);
                    // Check if email exists, if yes then verify password
                    if(mysqli_stmt_num_rows($stmt) == 1){                    
                        // Bind result variables
                        mysqli_stmt_bind_result($stmt, $userID_extracted, $password_extracted);
                        if(mysqli_stmt_fetch($stmt)){
                            //if password is true
                            return password_verify($password_login, $password_extracted);
                        }
                    }
                } 

            }
        } 
        return false;  
    }

    function deleteUserRecord($userID){
        if(self::isExistInDb("normalUser", "userID", $userID)){
            $query1 = "DELETE from normalUser where userID='".$userID."'";
            return execQuery($query1, $rss);
        }

        return true;
    }

    function readUserRecordByID($userID, $array_user){
        if(self::isExistInDb("normalUser", "userID", $userID)){
            $query = "SELECT * FROM normalUser WHERE userID=".$userID; 
            if(self::execQuery($query, $rs)){
                $array_user = [];
                foreach($rs as $x){
                    array_push($array_user, $x);
                }

                return true;
            }
        }

        return false;
    }

    function readUserRecordByEmail($userEmail, &$array_user){

        if(self::isExistInDb("normalUser", "email", $userEmail)){
            $query = "SELECT * FROM normalUser WHERE email='".$userEmail."'"; 
            if(self::execQuery($query, $rs)){
                $array_user = [];
                foreach($rs as $x){
                    //array_push($array_user, $x);  <- used for 2-dimensional array
                    $array_user = $x;   //<- used for 1-dimensional array
                }
                
                return true;
            }
        }
        echo '<br/>no connect';
        return false;
    }

    //$array_row is a two-dimensional array, it store all row record from normalUser table
    //for exp:
    //      $array_row[0] == normalUser record with userID == 1,
    //      $array_row[1] == normalUser record with userID == 2...    
    //$array_th == storing table header (column name),
    //for exp:  
    //      $array_th[0] == f_name 
    //      $array_th[1] == l_name....
    //      ......
    //      $array_th[n] == state
    //The storing order follows the normalUser table

    function readAllUserRecord($array_th, $array_row){
        $query = "SELECT * FROM userNormal";
        if(self::execQuery($query, $rs)){
            $array1 = [];
            for($i=0;$i<mysqli_num_fields($rs);$i++){
                $array1[] = mysqli_fetch_field($rs)->name;
                
            }
            
            $array2 = [];
            while ($row = mysqli_fetch_row($rs)) {
                $array_temp = [];
                foreach($row as $x){
                    array_push($array_temp, $x);
                }
                array_push($array2, $array_temp);
            
            }
            return true;
        }
        return false;
    }
}    
?>