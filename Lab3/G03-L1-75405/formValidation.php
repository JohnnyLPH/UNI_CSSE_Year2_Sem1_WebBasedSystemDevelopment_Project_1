<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="mystyle.css">
    <style>
        form div input[type="text"]{
            width: 200px;
            outline: none;
            border-radius: 5px;
            background-color: beige;
        }

        form div.go_back_class a{
            text-decoration: none;
            color: white;
            padding: 16px 50px;
            border-radius: 10px;
            background-color: #293241;
            text-align: center;
            float: right;
            display:inline-block;
        }
    </style>
</head>
<body>
    <?php
        //check available function
        function isEmptyOrTrimEmpty($elementId){
            return ((isset($_POST[$elementId]))?(empty($_POST[$elementId]) || empty(trim($_POST[$elementId]))):true);
        }
        //declare variable
        $user_fname = $user_lname = $user_email = $user_mobile = "";
        $user_psd = $user_cpsd = $user_gender = $user_state = "";

        $user_fname_alert_string = $user_lname_alert_string = $user_email_alert_string = $user_mobile_alert_string = '';
        $user_password_alert_string = $user_cpassword_alert_string = $user_gender_alert_string = $user_state_alert_string = '';
        $user_provision_alert_string = '';

        $user_provision_checked = '';
        $isValidate = false;
        $validateNumber = 0;
        //form validation
        //validation for fname
        if(isset($_POST['Submit'])){
            if(isEmptyOrTrimEmpty('user_fname')){
                $user_fname_alert_string = "Your input cannot be empty or contain space character !";
            }else{
                $user_fname = $_POST['user_fname'];
                if(!preg_match("/^[A-Z][a-z]{0}/", $user_fname)  ||  preg_match("/\s/", $user_fname)){
                    $user_fname_alert_string = "Your name input must be the first character uppercase and followed by lowercase without any space character!";
                }else{
                    $validateNumber++;
                }
            }
            
            //validation for lname
            if(isEmptyOrTrimEmpty('user_lname')){
                $user_lname_alert_string = "Your input cannot be empty or contain space character !";
            }else{
                $user_lname = $_POST['user_lname'];
                if(!preg_match("/^[A-Z][a-z]{0}/", $user_fname)  ||  preg_match("/\s/", $user_lname)){
                    $user_lname_alert_string = "Your name input must be the first character uppercase and followed by lowercase without any space character!";
                }else{
                    $validateNumber++;
                }
            }
            
            //validation for email
            if(isEmptyOrTrimEmpty('user_email')){
                $user_email_alert_string = "Your input cannot be empty or contain space character !";
            }else{
                $user_email = $_POST['user_email'];
                if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                    $user_email_alert_string = "Invalid email format !";
                }else{
                    $validateNumber++;
                }
            }
            //validation for mobile
            if(isEmptyOrTrimEmpty('user_mobile')){
                $user_mobile_alert_string = "Your input cannot be empty or contain space character !";
            }else{
                $user_mobile = $_POST['user_mobile'];
                if(!preg_match('/^[1-9][0-9]{7,9}+$/', $user_mobile)  ||  strlen($user_mobile) < 8  ||  strlen($user_mobile) > 10){
                    $user_mobile_alert_string = "Your input must have at least 8 digits and not more than 10 digits character !";
                }else{
                    $validateNumber++;
                }
            }
            //validation for password
            if(isEmptyOrTrimEmpty('user_password')){
                $user_password_alert_string = "Your input cannot be empty or contain space character !";
            }else{
                $user_psd = $_POST['user_password'];
                if(!preg_match("#.*^(?=.{6})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\S).*$#", $user_psd)){
                    $user_password_alert_string = "Your 6 alphanumeric password must have at least 1 uppercase, 1 lowercase, 1 special character and no space character";
                }else{
                    $validateNumber++;
                }
            }
            //validation for cpassword
            if(isEmptyOrTrimEmpty('user_cpassword')){
                $user_cpassword_alert_string = "Your input cannot be empty or contain space character !";
            }else{
                $user_cpsd = $_POST['user_cpassword'];
                if($user_cpsd != $user_psd){
                    $user_cpassword_alert_string = "Your confirm password is not correct !";
                }else{
                    $validateNumber++;
                }
            }
            //validation for gender
            if(isEmptyOrTrimEmpty('user_gender')){
                $user_gender_alert_string = "Please choose your gender !";
            }else{
                $user_gender = $_POST['user_gender'];
                $validateNumber++;
            }
            //validation for state
            if(isEmptyOrTrimEmpty('user_state')){
                $user_state_alert_string = "Please choose your staying state !";
            }else{
                $user_state = $_POST['user_state'];
                $validateNumber++;
            }
            //validation for provision
            if(isEmptyOrTrimEmpty('user_provision')){
                $user_provision_alert_string = "You must accept the provision before registration !";
            }else{
                $user_provision_checked = "checked";
                $validateNumber++;
            }
            
            $isValidate = ($validateNumber == 9)?true:false;
        
        }
        
    ?>  
    <!-- display data -->
    <main>
        <div>
            <h1>Display Input in Server Side</h1>
        </div>
        
        <section>
            <form action="success.php">
                <div>   
                    <label for="user_fname"><p>First Name</p><img src="source/icon/Normal/id-card.png" alt="icon" id="fname_img"></label>
                    <!--  <label for="user_fname" id="user_fname_icon"></label> -->
                    <input type="text" name="user_fname"  disabled value= <?php echo $user_fname ?>>
                    <p><?php echo $user_fname_alert_string ?></p>
                </div>
                <div>
                    <label for="user_lname"><p>Last Name</p><img src="source/icon/Normal/id-card.png" alt="icon" id="lname_img"></label>
                    <input type="text" name="user_lname" disabled value= "<?php echo $user_lname ?>">
                    <p><?php echo $user_lname_alert_string ?></p>
                </div>
                <div>
                    <label for="user_email"><p>Email</p><img src="source/icon/Normal/email.png" alt="icon" id="email_img"></label>
                    <input type="text" name="user_email" disabled value= <?php echo $user_email ?>>
                    <p><?php echo $user_email_alert_string ?></p>
                </div>
                <div>
                    <label for="user_mobile"><p>Mobile</p><img src="source/icon//Normal/smartphone.png" alt="icon" id="mobile_img"></label>
                    <input type="text" id="country_code" style= "width:50px" disabled value="+60">
                    <input type="text" name="user_mobile" style="width: 140px" disabled value= "<?php echo $user_mobile ?>">
                    <p><?php echo $user_mobile_alert_string ?></p>
                </div>
                <div>
                    <label for="user_password"><p>Password</p><img src="source/icon/Normal/password.png" alt="icon" id="password_img"></label>
                    <input type="text" name="user_password" disabled value= "<?php echo $user_psd ?>">
                    <p><?php echo $user_password_alert_string ?></p>
                </div>
                <div>
                    <label for="user_cpassword"><p>Confirm Password</p><img src="source/icon/Normal/confirm_password.png" alt="icon" id="cpassword_img"></label>
                    <input type="text" name="user_cpassword" disabled value= "<?php echo $user_cpsd ?>">
                    <p><?php echo $user_cpassword_alert_string ?></p>
                </div>
                <div>
                    <label for="user_gender"><p>Gender</p><img src="source/icon/Normal/gender.png" alt="icon" id="gender_img"></label>
                    <input type="text" name="user_gender" disabled value= "<?php echo $user_gender?>">
                    <p><?php echo $user_gender_alert_string ?></p>
                </div>
                <div>
                    <label for="user_state"><p>State</p><img src="source/icon/Normal/region.png" alt="icon" id="state_img"></label>
                    <input type="text" name="user_state" disabled value = "<?php echo $user_state ?>">
                    <p><?php echo $user_state_alert_string ?></p>
                </div>
                <div>
                    <div><input type="checkbox" name="user_provision" <?php echo $user_provision_checked ?> disabled/><span>I accept the above Terms and Conditions</span></div>
                    <p><?php echo $user_provision_alert_string ?></p>
                </div>
                
                <div class="go_back_class">
                    <?php if($isValidate) echo "<input type=\"submit\" value=\"Submit\" style=\"display:inline-block\">" ?>
                    <a href="registration.html">Go back</a>
                </div>
            </form>
        </section>
    </main>


</body>
</html>

