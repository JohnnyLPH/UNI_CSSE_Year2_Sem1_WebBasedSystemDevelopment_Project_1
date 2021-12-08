<!DOCTYPE html>
<html lang="en">
    <head>
        <title>User Account Registration</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">

        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="https://avatars.githubusercontent.com/u/70358914?v=4">

        <!-- <script type="text/javascript" src="formValidation.js" defer></script> -->
    </head>
    
    <body>
        <header>
            <h1>REGISTRATION FORM</h1>
        </header>
        
        <main>
            <h2>User Account Registration</h2>
            <div class="err-container">
                <?php
                    // Define variables with empty values.
                    $firstName = $lastName = $email = $countryCode = $phoneNo = "";
                    $password = $reconfirmPassword = $gender = $state = $acceptTerm = "";

                    // Error message variables.
                    $firstNameErr = $lastNameErr = $emailErr = $countryCodeErr = $phoneNoErr = "";
                    $passwordErr = $reconfirmPasswordErr = $genderErr = $stateErr = $acceptTermErr = "";

                    function testInput($data) {
                        // Remove extra spaces.
                        $data = trim($data);
                        // Remove backslashes.
                        $data = stripslashes($data);
                        // Converts special characters to HTML entities.
                        $data = htmlspecialchars($data);
                        return $data;
                    }

                    function minMaxLength($data, $min, $max) {
                        if (strlen($data) < $min || strlen($data) > $max) {
                            return false;
                        }
                        return true;
                    }

                    // Post method used.
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $acceptForm = true;
                        // Regular Expression guide: https://www.w3schools.com/php/php_ref_regex.asp
                        
                        // Check if each value is submitted in $_POST array.
                        // Check first name.
                        if (isset($_POST["first-name"]) && !empty($_POST["first-name"])) {
                            $firstName = testInput($_POST["first-name"]);

                            if (!minMaxLength($firstName, 2, 100)) {
                                $firstNameErr = "First Name must have at least 2 characters per word!";
                                $acceptForm = false;
                            }
                            // Each word separated by 1 space and has 1 Uppercase and at least 1 Lowercase Characters.
                            else if (!preg_match("/^[A-Z]{1}[a-z]+(\s[A-Z]{1}[a-z]+)*$/",$firstName)) {
                                $firstNameErr = "First Name should have word with 1 Uppercase Character followed by ";
                                $firstNameErr .= "Lowercase Characters and only 1 space allowed in-between words!";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $firstNameErr = "Provide your First Name.";
                            $acceptForm = false;
                        }

                        // Check last name.
                        if (isset($_POST["last-name"]) && !empty($_POST["last-name"])) {
                            $lastName = testInput($_POST["last-name"]);

                            if (!minMaxLength($lastName, 2, 100)) {
                                $lastNameErr = "Last Name must have at least 2 characters per word!";
                                $acceptForm = false;
                            }
                            // Each word separated by 1 space and has 1 Uppercase and at least 1 Lowercase Characters.
                            else if (!preg_match("/^[A-Z]{1}[a-z]+(\s[A-Z]{1}[a-z]+)*$/",$lastName)) {
                                $lastNameErr = "Last Name should have word with 1 Uppercase Character followed by ";
                                $lastNameErr .= "Lowercase Characters and only 1 space allowed in-between words!";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $lastNameErr = "Provide your Last Name.";
                            $acceptForm = false;
                        }

                        // Check email.
                        if (isset($_POST["email"]) && !empty($_POST["email"])) {
                            $email = testInput($_POST["email"]);

                            // Check email format.
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $emailErr = "Follow the correct Email Format: abc@xxx.xxx";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $emailErr = "Provide your Email.";
                            $acceptForm = false;
                        }

                        // Check country code.
                        if (isset($_POST["country-code"]) && !empty($_POST["country-code"])) {
                            $countryCode = testInput($_POST["country-code"]);

                            // Country code format: +60
                            if (!preg_match("/^[+]{1}[0-9]{2,3}$/", $countryCode)) {
                                $countryCodeErr = "Follow the correct Country Code Format: +60";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $countryCodeErr = "Provide your Country Code.";
                            $acceptForm = false;
                        }

                        // Check phone no.
                        if (isset($_POST["phone-no"]) && !empty($_POST["phone-no"])) {
                            $phoneNo = testInput($_POST["phone-no"]);

                            // Only 9 to 10 digits.
                            if (!preg_match("/^[0-9]{9,10}$/", $phoneNo)) {
                                $phoneNoErr = "Mobile Number should contain 9 - 10 digits with no space!";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $phoneNoErr = "Provide your Mobile Number.";
                            $acceptForm = false;
                        }

                        // Check password.
                        if (isset($_POST["password"]) && !empty($_POST["password"])) {
                            $password = testInput($_POST["password"]);

                            if (!minMaxLength($password, 6, 6)) {
                                $passwordErr = "Password length must be 6 characters!";
                                $acceptForm = false;
                            }
                            // Format: 1 uppercase, 1 lowercase, 1 special char, 3 numbers, 0 space.
                            else if (!preg_match("/^(?=(?:.*[A-Z]){1})(?=(?:.*[a-z]){1})(?=(?:.*[ \t\n]){0})(?=(?:.*\d){3})(.{6})$/", $password)) {
                                $passwordErr = "Password Format: 1 uppercase, 1 lowercase, 1 special character";
                                $passwordErr .= ", 3 numbers, 0 space!";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $passwordErr = "Set a Password.";
                            $acceptForm = false;
                        }

                        // Check reconfirm password.
                        if (isset($_POST["reconfirm-password"]) && !empty($_POST["reconfirm-password"])) {
                            $reconfirmPassword = testInput($_POST["reconfirm-password"]);

                            // Re-entered password must be the same as first entered password.
                            if ($password != $reconfirmPassword) {
                                $reconfirmPasswordErr = "Re-entered Password must be the same!";
                                $acceptForm = false;
                            }
                        }
                        else {
                            $reconfirmPasswordErr = "Re-enter your Password.";
                            $acceptForm = false;
                        }

                        // Check gender.
                        // if (isset($_POST["gender"]) && !empty($_POST["gender"])) {
                        //     $gender = testInput($_POST["gender"]);
                        // }
                        // else {
                        //     $genderErr = "Select your Gender.";
                        //     $acceptForm = false;
                        // }

                        // Check state.
                        if (isset($_POST["state"]) && !empty($_POST["state"]) && $_POST["state"] != "none") {
                            $state = testInput($_POST["state"]);
                        }
                        else {
                            $stateErr = "Select your State.";
                            $acceptForm = false;
                        }

                        // Check accept term.
                        if (isset($_POST["accept-term"]) && !empty($_POST["accept-term"])) {
                            $acceptTerm = testInput($_POST["accept-term"]);
                        }
                        else {
                            $acceptTermErr = "You must accept the Terms and Conditions.";
                            $acceptForm = false;
                        }
                        // print("Run here");

                        // Form not accepted.
                        if (!$acceptForm) {
                            print((!empty($firstNameErr)) ? "<span>$firstNameErr</span>": "");
                            print((!empty($lastNameErr)) ? "<span>$lastNameErr</span>": "");

                            print((!empty($emailErr)) ? "<span>$emailErr</span>": "");
                            
                            print((!empty($countryCodeErr)) ? "<span>$countryCodeErr</span>": "");
                            print((!empty($phoneNoErr)) ? "<span>$phoneNoErr</span>": "");

                            print((!empty($passwordErr)) ? "<span>$passwordErr</span>": "");
                            print((!empty($reconfirmPasswordErr)) ? "<span>$reconfirmPasswordErr</span>": "");

                            // print((!empty($genderErr)) ? "<span>$genderErr</span>": "");
                            
                            print((!empty($stateErr)) ? "<span>$stateErr</span>": "");

                            print((!empty($acceptTermErr)) ? "<span>$acceptTermErr</span>": "");

                            print("<span>Make sure everything is filled correctly before submitting!</span>");
                            print("<span>");
                        }
                        else {
                            print("<span class=\"success\">Your Account is successfully Registered!</span>");
                            print(
                                "<span class=\"success\">Thank you for your submission!" .
                                " You can now close this webpage or if you want to register another Account," .
                                " you can return to the Registration Page.</span>"
                            );
                            print("<span class=\"success\">");
                        }
                        
                        // print("<span><a href=\"./registration.html\">Click to Return.</a></span>");
                        print(
                            "Click -> <a href=\"./registration.html\">" .
                            " Return to Registration Page</a>.</span>"
                        );
                    }
                ?>
            </div>
        </main>

        <footer>
            <p>
                Created by<br>
                <a href="https://github.com/JohnnyLPH" target="_blank">
                    Lau Pikk Heang
                </a>
            </p>
        </footer>
    </body>
</html>
