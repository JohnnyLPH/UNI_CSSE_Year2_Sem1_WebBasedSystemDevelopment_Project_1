<?php

/*
    the mysql table element
    user ID      - id               (int 11) (PRIMARY KEY AUTOMATIC)
    first name   - firstName        (VARCHAR (100))
    last name    - lastName         (VARCHAR (100))
    email        - email            (VARCHAR (256))
    countryCode  - countryCode      (VARCHAR (4))
    mobile num   - phone            (VARCHAR (10))
    password     - password         (VARCHAR (256)) (in hash form)
    gender       - gender           (VARCHAR (1))
    state        - state            (VARCHAR (30))
    registerDate - registerDate     datetime
    dob          - dob              (date)
*/
class Members{
    const DB_HOST = "localhost";
    const DB_USERNAME = "id18274200_wbsd";
    const DB_PSD = "G03abc-abc03G";
    const DB = "id18274200_lingscars";
    private $userID;
    private $db_connector;

    function __construct(){

        $this->db_connector = mysqli_connect(self::DB_HOST, self::DB_USERNAME, self::DB_PSD, self::DB);
        //connect to server AND database
        if(!$this->db_connector){
            echo 'Could not connect to server<br/>';
            trigger_error(mysqli_error(), E_USER_ERROR);
        }else{
            mysqli_query($this->db_connector, 'SET GLOBAL time_zone = "+0:00"');
        }
        
    }

    function execQuery($query, &$rs){
        $rs = mysqli_query($this->db_connector, $query);
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
    function insertNewRecord($post_fname, $post_lname, $post_email, $post_mobile, $post_password, $post_gender, $post_state, $post_dob){
        $f_name_escape = mysqli_real_escape_string($this->db_connector, $post_fname);
        $l_name_escape = mysqli_real_escape_string($this->db_connector, $post_lname);
        $email_escape = mysqli_real_escape_string($this->db_connector, $post_email);
        $mobile_escape = mysqli_real_escape_string($this->db_connector, $post_mobile);
        $password_escape = mysqli_real_escape_string($this->db_connector, $post_password);
        $gender_escape = mysqli_real_escape_string($this->db_connector, $post_gender);
        $state_escape = mysqli_real_escape_string($this->db_connector, $post_state);
        $dob_escape = mysqli_real_escape_string($this->db_connector, $post_dob);

        //generate hash password
        $hash_password_generated = password_hash($password_escape, PASSWORD_DEFAULT);
        
        //check whether if the email had registered
        //$query1 = "SELECT EXISTS(SELECT * from members WHERE email = 'email_escape')";
        if(!self::isExistInDb("members", "email", $post_email)){
            //if no, then insert new record to db
            $query = "INSERT INTO members (firstName, lastName, email, phone, password, gender, state, dob) VALUES (
                '$f_name_escape', '$l_name_escape', '$email_escape', '$mobile_escape', 
                '$hash_password_generated', '$gender_escape', '$state_escape', '$dob_escape')";
            //if mysqli_query return null
            
            return (mysqli_query($this->db_connector, $query) != false);
                
        }else{
            return false;
        }
        
    }



    //return true, if login success
    //else return false
    function login($post_email, $post_password){
        $query = "SELECT * FROM members";
        $email_login= $password_login = "";
        
        $email_login = mysqli_real_escape_string($this->db_connector, $post_email);
        $password_login = mysqli_real_escape_string($this->db_connector ,$post_password);

        $password_extracted = $userID_extracted = $email_extracted = "";

        // Prepare a select statement
        $sql = "SELECT id, firstName, email, password FROM members WHERE email = ?";
        
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
                    mysqli_stmt_bind_result($stmt, $userID_extracted, $firstName_extracted, $email_extracted, $password_extracted);
                    if(mysqli_stmt_fetch($stmt)){
                        /* echo '<br/>'.password_hash($password_login, PASSWORD_DEFAULT).'<br/>'.strlen(password_hash($password_login, PASSWORD_DEFAULT)).'<br/>';
                        echo $password_login; */
                        if(password_verify($password_login, $password_extracted)){
                            // Password is correct, so start a new session
                           // Store data in session variables
                          
                            $_SESSION["loggedIn"] = true;
                            $_SESSION["memberId"] = $userID_extracted;
                            $_SESSION["memberFirstName"] = $firstName_extracted;
                            $_SESSION["loggedInTime"] = date('Y-m-d H:i:s');
                            //pre-set logout datetime, if user directly close the window,
                            //php perform operation to myql would not be handled
                           //insert loggin time to members log
                            $temp_login_time = $_SESSION["loggedInTime"];
                            $minutes_to_add = 5;

                            $time = new DateTime();
                            $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

                            $temp_logout_time = $time->format('Y-m-d H:i:s');

                            $temp_duration = $time->format('U') - strtotime( $_SESSION["loggedInTime"]);
                            $query3 = '';
                            if(self::isExistInDb("memberlog", "memberId", $userID_extracted)){
                                $query3 = "UPDATE memberlog SET loginDate = '$temp_login_time', logoutDate='$temp_logout_time' , duration = '$temp_duration' WHERE memberId = '$userID_extracted'";
                            }else{
                                $query3 = "INSERT INTO memberlog (memberId, loginDate, logoutDate, duration) VALUE ('$userID_extracted', '$temp_login_time', '$temp_logout_time','$temp_duration')";
                            }
                           
                            if(self::execQuery($query3, $rs)){
                                /* echo 'insert into myUserLog login date time success<br/>'; */
                                return true;
                            }
                            
                        } else{
                            // Password is not valid, display a generic error message
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
    function updateLogoutDT(){
        $user_id_login = $_SESSION['memberId'];
        $login_dateTime_save = $_SESSION['loggedInTime'];
        /*echo $logout_dateTime_save.'<br/>'; */
        $minutes_to_add = 5;
        $time = new DateTime();
        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

        $logout_dateTime_save = $time->format('Y-m-d H:i:s');

        $duration_save = $time->format('U') - strtotime($login_dateTime_save);
        /* echo $duration_save; */
        $t_sql = "UPDATE memberlog SET logoutDate='$logout_dateTime_save' , duration = '$duration_save' WHERE memberId = '$user_id_login' AND loginDate = '$login_dateTime_save'";
        mysqli_query($this->db_connector,$t_sql);
    }

    function updateCurrentLogoutDT(){
        $user_id_login = $_SESSION['memberId'];
        $login_dateTime_save = $_SESSION['loggedInTime'];
        /*echo $logout_dateTime_save.'<br/>'; */
        $time = new DateTime();

        $logout_dateTime_save = $time->format('Y-m-d H:i:s');

        $duration_save = $time->format('U') - strtotime($login_dateTime_save);
        /* echo $duration_save; */
        $t_sql = "UPDATE memberlog SET logoutDate='$logout_dateTime_save' , duration = '$duration_save' WHERE memberId = '$user_id_login' AND loginDate = '$login_dateTime_save'";
        mysqli_query($this->db_connector,$t_sql);
    }
    
    

    //update one user information
    //$userID == userID
    //$key == the data column that you want to updated/modified
    //$value == value that you want to added to that column 
    function updateUserRecordByID($table, $userID ,$key, $value){
        if(self::isExistInDb($table, "id", $userID)){
            $query = "UPDATE ".$table." SET ".$key."='".$value."' WHERE id=".$userID;
            return execQuery($query, $rss) == true;
        }else{
            return false;
        }
    }

    function updateUserRecordByEmail($table, $userEmail ,$key, $value){
        if(!self::isExistInDb($table, "email", $userEmail)){
            $query = "UPDATE ".$table." SET ".$key."='".$value."' WHERE email=".$userEmail;
            return execQuery($query, $rss) == true;
        }else{
            return false;
        }
    }


    function updateUserPasswordByEmail($table, $userEmail , $value){
        
        if(self::isExistInDb("members", "email", $userEmail)){
            
            // Prepare a select statement
            $password_update = mysqli_real_escape_string($this->db_connector ,$value);
            $hash_password_generated = password_hash($password_update, PASSWORD_DEFAULT);
            $email_update = mysqli_real_escape_string($this->db_connector ,$userEmail);
            
            $sql = "UPDATE members SET password= ? WHERE email = ?";

            if($stmt = mysqli_prepare($this->db_connector, $sql)){  
                
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ss", $hash_password_generated, $email_update);
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    
                    // Store result
                   return true;
                } 

            }
        } 
        
        return false;  
    }

    function updateExistedRecord($post_fname, $post_lname, $post_email, $post_mobile,  $post_gender, $post_state, $post_dob){
        $f_name_escape = mysqli_real_escape_string($this->db_connector, $post_fname);
        $l_name_escape = mysqli_real_escape_string($this->db_connector, $post_lname);
        $email_escape = mysqli_real_escape_string($this->db_connector, $post_email);
        $mobile_escape = mysqli_real_escape_string($this->db_connector, $post_mobile);
        $gender_escape = mysqli_real_escape_string($this->db_connector, $post_gender);
        $state_escape = mysqli_real_escape_string($this->db_connector, $post_state);
        $dob_escape = mysqli_real_escape_string($this->db_connector, $post_dob);

        $sql = "UPDATE members SET firstName = '$f_name_escape', lastName = '$l_name_escape', phone = '$mobile_escape', gender = '$gender_escape', state ='$state_escape', dob = '$dob_escape'  WHERE email = '$email_escape'";
        if(self::execQuery($sql, $rs)){
            return true;
        }else{
            return false;
        }
    }

    //Check password with userID
    function authenticatePassword($userID, $passwordTest){ 

        if(self::isExistInDb("members", "id", $userID)){
            // Prepare a select statement
            $sql = "SELECT id, password FROM members WHERE id = ?";
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
        if(self::isExistInDb("members", "id", $userID)){
            $query1 = "DELETE from members where id='".$userID."'";
            return execQuery($query1, $rss);
        }

        return true;
    }

    function readUserRecordByID($userID, &$array_user){
        if(self::isExistInDb("members", "id", $userID)){
            $query = "SELECT * FROM members WHERE id=".$userID; 
            if(self::execQuery($query, $rs)){
                $array_user = [];
                foreach($rs as $x){
                    $array_user = $x;
                }

                return true;
            }
        }

        return false;
    }

    function readUserRecordByEmail($userEmail, &$array_user){

        if(self::isExistInDb("members", "email", $userEmail)){
            $query = "SELECT * FROM members WHERE email='".$userEmail."'"; 
            if(self::execQuery($query, $rs)){
                $array_user = [];
                foreach($rs as $x){
                    //array_push($array_user, $x);  <- used for 2-dimensional array
                    $array_user = $x;   //<- used for 1-dimensional array
                }
                
                return true;
            }
        }
        
        return false;
    }

    //$array_row is a two-dimensional array, it store all row record from members table
    //for exp:
    //      $array_row[0] == members record with userID == 1,
    //      $array_row[1] == members record with userID == 2...    
    //$array_th == storing table header (column name),
    //for exp:  
    //      $array_th[0] == f_name 
    //      $array_th[1] == l_name....
    //      ......
    //      $array_th[n] == state
    //The storing order follows the members table

    function readAllUserRecord($array_th, $array_row){
        $query = "SELECT * FROM members";
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