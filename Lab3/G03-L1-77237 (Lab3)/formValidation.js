var failValidation;

function nameValidation(name, nameType) {
    var acceptValue = true;
    name.value = name.value.trim();
    var nameValue = name.value;
    // Empty name.
    // alert("hi");
    if (nameValue == "") {
        // alert("Provide your " + nameType + "!");
        failValidation += "-> Please enter your " + nameType + "!\n";
        acceptValue = false;
    }
    else {
        var splitName = nameValue.split(" ");

        for (i = 0; i < splitName.length; i++) {
            var allAlphabet = "abcdefghijklmnopqrstuvwxyz";

            // Checking the first character. (Uppercase or not)
            if (allAlphabet.toUpperCase().indexOf(splitName[i][0]) < 0) {
                failValidation += "-> " + "First character of *" + nameType + "* must be Uppercase, then followed by Lowercase!\n";
                acceptValue = false;
            }
            // Checking the remaining character(s). (Lowercase or not)
            else {
                for (j = 1; j < splitName[i].length; j++) {
                    if (allAlphabet.indexOf(splitName[i][j]) < 0) {
                        failValidation += "-> " + "The remaining characters after *" + nameType + "* should be Lowercase!\n";
                        acceptValue = false;
                        break;
                    }
                }
            }

            // Checking the length of characters.
            if (splitName[i].length < 2) {
                failValidation += "-> " + "Please enter at least TWO characters in *" + nameType + "* !";
                failValidation += " You're only allow to put a single space between the words~ \n";
                acceptValue = false;
                break;
            }

            if (!acceptValue) {
                break;
            }
        }

    }

    if (!acceptValue) {
        // Alert invalid name entered.
        name.style.borderColor = "red";
        return false;
    }

    // Valid name entered.
    name.style.borderColor = "black";
    return true;
}

function emailValidation(validateEmail) {
    var acceptValue = true;
    validateEmail.value = validateEmail.value.trim();
    var emailValue = validateEmail.value;
    var res = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    //alert("hi");
    // Validate email format.
    if (emailValue.indexOf('@') < 1 || emailValue.indexOf('@') != emailValue.lastIndexOf('@') || emailValue.indexOf(' ') > -1) {
        acceptValue = false;
    }

    else if (emailValue.indexOf('.') - emailValue.indexOf('@') < 2 || emailValue.lastIndexOf('.') == emailValue.length - 1) {
        acceptValue = false;
    }

    else {
        var dotTrack = emailValue.indexOf('.'), dotLast = emailValue.lastIndexOf('.');
        var vSlice = emailValue;

        while (dotTrack < dotLast) {
            if (dotLast - dotTrack < 2 || !dotTrack) {
                acceptValue = false;
                break;
            }
            vSlice = vSlice.slice(dotTrack + 1, vSlice.length);
            dotTrack = vSlice.indexOf('.');
            dotLast = vSlice.lastIndexOf('.');
        }
    }

    if (!acceptValue || !res.test(emailValue)) {
        // Alert invalid email entered.
        failValidation += "-> Please enter the correct Email format! Example: example@xxxx.com\n";
        validateEmail.style.borderColor = "red";
        return false;
    }

    // Valid email entered.
    validateEmail.style.borderColor = "black";
    return true;
}

function phoneNumberValidation(validateCode, validatePhoneNo) {
    var acceptValue = true;
    validateCode.value = validateCode.value.trim();
    validatePhoneNo.value = validatePhoneNo.value.trim();
    var codeValue = validateCode.value;
    var phoneValue = validatePhoneNo.value;

    // Check country code format.
    if (codeValue.length < 3 || codeValue.length > 4 || codeValue.indexOf("+") || codeValue.indexOf("+") != codeValue.lastIndexOf("+") || isNaN(codeValue.slice(1, codeValue.length))) {
        failValidation += "-> The correct Country Code format is +60 .\n";
        // validateCode.focus();
        validateCode.style.borderColor = "red";
        acceptValue = false;
    }
    else {
        validateCode.style.borderColor = "black";
    }

    // Only integers accepted.
    if (phoneValue.length < 9 || isNaN(phoneValue) || phoneValue.indexOf('.') > -1) {
        // alert("Mobile Number should contain 9 - 10 digits!");
        failValidation += "-> Phone Number must have 9 - 10 digits without space!\n";
        // validatePhoneNo.focus();
        validatePhoneNo.style.borderColor = "red";
        acceptValue = false;
    }
    // Phone is fine.
    else {
        validatePhoneNo.style.borderColor = "black";
    }

    return acceptValue;
}

function passwordValidation(validatePassword, confirmPassword) {
    var acceptValue = true;
    var passwordValue = validatePassword.value, confirmV = confirmPassword.value;

    var lowercaseC = "abcdefghijklmnopqrstuvwxyz";
    var digit = "1234567890";
    var noneC = " \t";

    var lowerCount = 0, upperCount = 0, digitCount = 0, specialCount = 0, spaceCount = 0;

    if (passwordValue.length != 6) {
        failValidation += "-> Please ensure that your Password length is 6 characters!\n";
        acceptValue = false;
    }
    else {
        for (i = 0; i < passwordValue.length; i++) {
            // Check uppercase.
            if (lowercaseC.toUpperCase().indexOf(passwordValue[i]) > -1) {
                upperCount++;
                continue;
            }
            // Check lowercase.
            else if (lowercaseC.indexOf(passwordValue[i]) > -1) {
                lowerCount++;
                continue;
            }
            // Check number.
            else if (digit.indexOf(passwordValue[i]) > -1) {
                digitCount++;
                continue;
            }
            // Check special character.
            else if (noneC.indexOf(passwordValue[i]) < 0) {
                specialCount++;
                continue;
            }
            // Space
            spaceCount++;
        }

        // Check count.
        if (lowerCount != 1 || upperCount != 1 || specialCount != 1 || digitCount != 3) {
            failValidation += "-> Please enter password using the correct format: 1 uppercase, 1 lowercase";
            failValidation += ", 1 special character, 3 numbers and no space.\n";
            acceptValue = false;
        }
    }

    if (!acceptValue) {
        validatePassword.style.borderColor = "red";
        confirmPassword.style.borderColor = "red";
        confirmPassword.value = "";
        confirmPassword.blur();
        return false;
    }
    // Valid password entered.
    validatePassword.style.borderColor = "black";
    
    if (confirmV != passwordValue) {
        failValidation += "-> Please re-enter the SAME Password!\n";
        confirmPassword.style.borderColor = "red";
        return false;
    }
    // Re-entered password is the same.
    confirmPassword.style.borderColor = "black";
    return true;
}

function stateValidation(validateState) {
    if (validateState.value == "none") {
        validateState.style.borderColor = "red";
        failValidation += "-> Please select your State!\n";
        return false;
    }
    validateState.style.borderColor = "black";
    return true;
}

function acceptTermValidation(validateTerm) {
    if (!validateTerm.checked) {
        failValidation += "-> Please accept the Terms and Conditions!\n";
        return false;
    }
    return true;
}

function formValidation() {
    failValidation = "";
    var acceptForm = true;

    if (!nameValidation(document.getElementsByClassName("firstName")[0], "First Name")) {
        acceptForm = false;
    }

    if (!nameValidation(document.getElementsByClassName("lastName")[0], "Last Name")) {
        acceptForm = false;
    }
    
    if (!emailValidation(document.getElementsByClassName("email")[0])) {
        acceptForm = false;
    }

    if (!phoneNumberValidation(document.getElementsByClassName("code")[0], document.getElementsByClassName("number")[0])) {
        acceptForm = false;
    }

    if (!passwordValidation(document.getElementsByClassName("password")[0], document.getElementsByClassName("confirmPassword")[0])) {
        acceptForm = false;
    }

    if (!stateValidation(document.getElementsByClassName("state")[0])) {
        acceptForm = false;
    }

    if (!acceptTermValidation(document.getElementsByClassName("accept-term-checkbox")[0])) {
        acceptForm = false;
    }

    if (acceptForm) {
        alert("Congrats! Your account has registered successfully.");
        return true;
    }

    alert(failValidation);
    return false;
}