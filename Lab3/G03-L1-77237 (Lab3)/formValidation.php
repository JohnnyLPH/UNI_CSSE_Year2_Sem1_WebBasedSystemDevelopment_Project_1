<!DOCTYPE html>
<!--
    Lab 3:
    Name: Yuki Chung Pei Ying
    Matric Number: 77237
-->

<html>
    <head>
        <title>Lab 3: User Account Registration</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="icon" type="image/x-icon" href="favicon.png">
        <link rel="stylesheet" href="registrationStyle.css">
    </head>

    <body>
        <header>
            <h1>&#127808; Registration Form &#127808;</h1>
        </header>

        <main>
            <div class="reg-form-head">
                <h2>User Account Registration</h2>
            </div>

            <div class = "phpContainer">
                <?php
                    // Define variables and set to empty values.
                    $firstName = $lastName = $email = $code = $number = "";
                    $password = $confirmPassword = $gender = $state = $acceptTerm = "";

                   // Define error message variables and set to empty values.
                    $firstNameErr = $lastNameErr = $emailErr = $codeErr = $numberErr = "";
                    $password = $confirmPassword = $gender = $state = $acceptTerm = "";

                    function testInput($data) {
                        $data = trim($data);
                        $data = stripslashes($data);
                        $data = htmlspecialchars($data);
                        return $data;
                    }

                    function minMaxLength($data, $min, $max) {
                        if (strlen($data) < $min || strlen($data) > $max) {
                            return false;
                        }
                        return true;
                    }

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $acceptForm = true;
                        
                        // First name validation.
                        if (isset($_POST["firstName"]) && !empty($_POST["firstName"])) {
                            $firstName = testInput($_POST["firstName"]);

                            if (!minMaxLength($firstName, 2, 100)) {
                                $firstNameErr = "Your <b>*First Name*</b> must contain at least 2 characters! &#127875;";
                                $acceptForm = false;
                            }
                            else if (!preg_match("/^[A-Z]{1}[a-z]+(\s[A-Z]{1}[a-z]+)*$/",$firstName)) {
                                $firstNameErr = "The <b>first character</b> of your <b>*First Name*</b> should be <b>Uppercase</b> and ";
                                $firstNameErr .= "the <b>remaining characters</b> should be <b>Lowercase</b>! &#127875;";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $firstNameErr = "Give me your <b>*First Name*</b>. &#129499;";
                            $acceptForm = false;
                        }

                        // Last name validation.
                        if (isset($_POST["lastName"]) && !empty($_POST["lastName"])) {
                            $lastName = testInput($_POST["lastName"]);

                            if (!minMaxLength($lastName, 2, 100)) {
                                $lastNameErr = "Your <b>*Last Name*</b> must contain at least 2 characters!";
                                $acceptForm = false;
                            }
                            else if (!preg_match("/^[A-Z]{1}[a-z]+(\s[A-Z]{1}[a-z]+)*$/",$lastName)) {
                                $lastNameErr = "The <b>first character</b> of your <b>*Last Name*</b> should be <b>Uppercase</b> and ";
                                $lastNameErr .= "the <b>remaining characters</b> should be <b>Lowercase</b>! &#127875;";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $lastNameErr = "&#129503; Give me your <b>*Last Name*</b>.";
                            $acceptForm = false;
                        }

                        // Email validation.
                        if (isset($_POST["email"]) && !empty($_POST["email"])) {
                            $email = testInput($_POST["email"]);

                            // Validate email format.
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $emailErr = "&#128171; Enter the correct Email Format: example@xxxx.com, try again! &#128171;";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $emailErr = "&#128376; I want to know your <b>Email</b>. &#128376;";
                            $acceptForm = false;
                        }

                        // Country code validation.
                        if (isset($_POST["code"]) && !empty($_POST["code"])) {
                            $code = testInput($_POST["code"]);

                            // Country code format: +60
                            if (!preg_match("/^[+]{1}[0-9]{2,3}$/", $code)) {
                                $codeErr = "The correct Country Code Format is +60, do not add extra digits/words! &#127875;";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $codeErr = "&#129503; Tell me your <b>Country Code</b>.";
                            $acceptForm = false;
                        }

                        // Phone number validation.
                        if (isset($_POST["number"]) && !empty($_POST["number"])) {
                            $number = testInput($_POST["number"]);

                            // Only 9 to 10 digits.
                            if (!preg_match("/^[0-9]{9,10}$/", $number)) {
                                $numberErr = "We only accept the Mobile Number with 9 - 10 digits and no space, try again! &#129415;";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $numberErr = "Tell me your <b>Mobile Number</b>. &#129499;";
                            $acceptForm = false;
                        }

                        // Password validaton.
                        if (isset($_POST["password"]) && !empty($_POST["password"])) {
                            $password = testInput($_POST["password"]);

                            if (!minMaxLength($password, 6, 6)) {
                                $passwordErr = "The Password length is EXACTLY 6 characters! &#129415;";
                                $acceptForm = false;
                            }
                            // Format: 1 uppercase, 1 lowercase, 1 special char, 3 numbers, 0 space.
                            else if (!preg_match("/^(?=(?:.*[A-Z]){1})(?=(?:.*[a-z]){1})(?=(?:.*[ \t\n]){0})(?=(?:.*\d){3})(.{6})$/", $password)) {
                                $passwordErr = "&#128171; Follow the Password format PLEASE! &#128171;<br>";
                                $passwordErr .= "<b><i>[1 Uppercase, 1 Lowercase, 1 Special character, 3 Numbers, No Space]</i></b>";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $passwordErr = "Come on! Set a <b>Password</b>.";
                            $acceptForm = false;
                        }

                        // Reconfirm password validation.
                        if (isset($_POST["confirmPassword"]) && !empty($_POST["confirmPassword"])) {
                            $confirmPassword = testInput($_POST["confirmPassword"]);

                            // Re-entered password must be the same as first entered password.
                            if ($password != $confirmPassword) {
                                $confirmPasswordErr = "Your Re-entered Password is not the same! Please check again...&#128169;";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $confirmPasswordErr = "&#128376; Re-enter your <b>Password</b>! &#128376;";
                            $acceptForm = false;
                        }

                        // State validation.
                        if (isset($_POST["state"]) && !empty($_POST["state"]) && $_POST["state"] != "none") {
                            $state = testInput($_POST["state"]);
                        }
                        else {
                            $stateErr = "Where is your <b>State</b>? &#128064;";
                            $acceptForm = false;
                        }

                        // Accept term validation.
                        if (isset($_POST["accept-term"]) && !empty($_POST["accept-term"])) {
                            $acceptTerm = testInput($_POST["accept-term"]);
                        }
                        else {
                            $acceptTermErr = "&#128125; You must accept the <b>*Terms and Conditions*</b>. &#128125;";
                            $acceptForm = false;
                        }

                        // Form rejected.
                        if (!$acceptForm) {
                            print((!empty($firstNameErr)) ? "<span>$firstNameErr</span>": "");
                            print((!empty($lastNameErr)) ? "<span>$lastNameErr</span>": "");

                            print((!empty($emailErr)) ? "<span>$emailErr</span>": "");
                            
                            print((!empty($codeErr)) ? "<span>$codeErr</span>": "");
                            print((!empty($numberErr)) ? "<span>$numberErr</span>": "");

                            print((!empty($passwordErr)) ? "<span>$passwordErr</span>": "");
                            print((!empty($confirmPasswordErr)) ? "<span>$confirmPasswordErr</span>": "");

                            print((!empty($stateErr)) ? "<span>$stateErr</span>": "");

                            print((!empty($acceptTermErr)) ? "<span>$acceptTermErr</span>": "");

                            print("<span>Please make sure that all your details are filled! If not...\n");
                            print("Nothing will happen~ WaHaHaHa &#128123;</span>");
                            print("<span>");
                        }
                        else {
                            print("<span>Oh, my goodness!! You've successfully created an account. &#129497;</span>");
                            print(
                                "<span>Thank you for your registration with us!" .
                                " You can create another new account if you want. &#128123;</span>"
                            );
                            print("<span>");
                        }
                        
                        // print("<span><a href=\"./registration.html\">Click to Return.</a></span>");
                        print(
                            "&#127812; <i>Click here to go back your</i> &#127812;<br> <a href=\"./registration.html\">" .
                            "Registration Page</a></span>"
                        );
                    }
                ?>
            </div>
        </main>

        <footer>
            <p>Created by :<br>
                &#127810;<b><i>Yuki Chung Pei Ying (77237)</i></b>&#127810;
                <br>
                &#127794; &#129498; &#127794;
            </p>
        </footer>
    </body>
</html>