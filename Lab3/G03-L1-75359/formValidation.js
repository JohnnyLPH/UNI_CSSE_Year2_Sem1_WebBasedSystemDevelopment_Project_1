var firstName = document.getElementById("first-name");
var lastName = document.getElementById("last-name")

var email = document.getElementById("email");

var countryCode = document.getElementById("country-code");
var phoneNo = document.getElementById("phone-no");

var password = document.getElementById("password");
var reconfirmPassword = document.getElementById("reconfirm-password");

var state = document.getElementById("state")

var acceptTerm = document.getElementById("accept-term");

var validateFailWarning;


function validateName(name, nameType) {
    var accept = true;
    name.value = name.value.trim();
    var nameV = name.value;
    // Empty name.
    if (nameV == "") {
        // alert("Provide your " + nameType + "!");
        validateFailWarning += "-Provide your " + nameType + "!\n";
        accept = false;
    }
    else {
        var splitName = nameV.split(" ");

        for (i = 0; i < splitName.length; i++) {
            // Check number of characters.
            if (splitName[i].length < 2) {
                // alert(nameType + " must have at least 2 characters!");
                validateFailWarning += "-" + nameType + " must have at least 2 characters per word!";
                validateFailWarning += " Only 1 space allowed in-between words.\n";
                accept = false;
                break;
            }

            // var nameRegex = /[A-Z][a-z]+/g;
            // var result = nameRegex.test(splitName);
            // validateFailWarning += result;
            // validateFailWarning += "\n";
            
            var allLetter = "abcdefghijklmnopqrstuvwxyz";
            // Check first character is uppercase or not.
            if (allLetter.toUpperCase().indexOf(splitName[i][0]) < 0) {
                // alert(nameType + " should start with Uppercase Character follow by Lowercase Characters!");
                validateFailWarning += "-" + nameType;
                validateFailWarning += " should have word with 1 Uppercase Character followed by Lowercase Characters!\n";
                accept = false;
            }
            // Check remaining character is lowercase or not.
            else {
                for (j = 1; j < splitName[i].length; j++) {
                    if (allLetter.indexOf(splitName[i][j]) < 0) {
                        // alert(nameType + " should end with Lowercase Characters! Hint: " + splitName[i][j]);
                        validateFailWarning += "-" + nameType + " should end with Lowercase Characters! Hint: '";
                        validateFailWarning += splitName[i][j] + "'\n";
                        accept = false;
                        break;
                    }
                }
            }

            if (!accept) {
                break;
            }
        }

    }

    if (!accept) {
        // name.focus();
        name.style.borderColor = "red";
        return false;
    }

    // Name is fine.
    name.style.borderColor = "black";
    return true;
}

function validateEmail(checkEmail) {
    var accept = true;
    checkEmail.value = checkEmail.value.trim();
    var emailV = checkEmail.value;
    // Check email format.
    // Only 1 '@' accepted, and there must be character before it.
    // No space allowed.
    if (emailV.indexOf('@') < 1 || emailV.indexOf('@') != emailV.lastIndexOf('@') || emailV.indexOf(' ') > -1) {
        accept = false;
    }
    // '@' must come before '.' with at least one character in-between.
    // '.' cannot be the last character.
    else if (emailV.indexOf('.') - emailV.indexOf('@') < 2 || emailV.lastIndexOf('.') == emailV.length - 1) {
        accept = false;
    }
    //  2 '.' must have at least one character in-between.
    else {
        var dotTrack = emailV.indexOf('.'), dotLast = emailV.lastIndexOf('.');
        var vSlice = emailV;

        while (dotTrack < dotLast) {
            // If dotLast - dotTrack < 2, there's no character between dotTrack and dotLast.
            // If dotTrack = 0, there's no character between current and previous dotTrack.
            if (dotLast - dotTrack < 2 || !dotTrack) {
                accept = false;
                break;
            }
            vSlice = vSlice.slice(dotTrack + 1, vSlice.length);
            dotTrack = vSlice.indexOf('.');
            dotLast = vSlice.lastIndexOf('.');
        }
    }
    // Additional check using Regular Expression.
    var res = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

    if (!accept || !res.test(emailV)) {
        // alert("Follow the correct Email Format: abc@xxx.xxx");
        validateFailWarning += "-Follow the correct Email Format: abc@xxx.xxx\n";
        // checkEmail.focus();
        checkEmail.style.borderColor = "red";
        return false;
    }
    // Email is fine.
    checkEmail.style.borderColor = "black";
    return true;
}

function validatePhoneNo(checkCode, checkPhone) {
    var accept = true;
    checkCode.value = checkCode.value.trim();
    checkPhone.value = checkPhone.value.trim();
    var codeV = checkCode.value;
    var phoneV = checkPhone.value;

    // Check country code format.
    if (codeV.length < 3 || codeV.length > 4 || codeV.indexOf("+") || codeV.indexOf("+") != codeV.lastIndexOf("+") || isNaN(codeV.slice(1, codeV.length))) {
        validateFailWarning += "-Country Code Format: +60\n";
        // checkCode.focus();
        checkCode.style.borderColor = "red";
        accept = false;
    }
    else {
        checkCode.style.borderColor = "black";
    }

    // Only integers accepted.
    if (phoneV.length < 9 || isNaN(phoneV) || phoneV.indexOf('.') > -1) {
        // alert("Mobile Number should contain 9 - 10 digits!");
        validateFailWarning += "-Mobile Number should contain 9 - 10 digits with no space!\n";
        // checkPhone.focus();
        checkPhone.style.borderColor = "red";
        accept = false;
    }
    // Phone is fine.
    else {
        checkPhone.style.borderColor = "black";
    }

    return accept;
}

function validatePassword(checkPass, confirmPass) {
    var accept = true;
    var passV = checkPass.value, confirmV = confirmPass.value;

    var lowChar = "abcdefghijklmnopqrstuvwxyz";
    var digit = "1234567890";
    var noChar = " \t";

    var lowCount = 0, upCount = 0, spCount = 0, digitCount = 0, spaceCount = 0;
    // Password length is not 6.
    if (passV.length != 6) {
        // alert("Password length must be 6 characters!");
        validateFailWarning += "-Password length must be 6 characters!\n";
        accept = false;
    }
    // 1 uppercase, 1 lowercase, 1 special character, 3 numbers, no space.
    else {
        for (i = 0; i < passV.length; i++) {
            // Check uppercase.
            if (lowChar.toUpperCase().indexOf(passV[i]) > -1) {
                upCount++;
                continue;
            }
            // Check lowercase.
            else if (lowChar.indexOf(passV[i]) > -1) {
                lowCount++;
                continue;
            }
            // Check number.
            else if (digit.indexOf(passV[i]) > -1) {
                digitCount++;
                continue;
            }
            // Check special character.
            else if (noChar.indexOf(passV[i]) < 0) {
                spCount++;
                continue;
            }
            // Space
            spaceCount++;
        }

        // Check count.
        if (lowCount != 1 || upCount != 1 || spCount != 1 || digitCount != 3) {
            // alert("Password Format: 1 uppercase(" + upCount + "), 1 lowercase(" + lowCount + "), 1 special character(" + spCount + "), 3 numbers(" + digitCount + "), 0 space(" + spaceCount + ").");
            validateFailWarning += "-Password Format: 1 uppercase(" + upCount + "), 1 lowercase(" + lowCount;
            validateFailWarning += "), 1 special character(" + spCount + "), 3 numbers(" + digitCount;
            validateFailWarning += "), 0 space(" + spaceCount + ").\n";
            accept = false;
        }
    }

    // Reset border color of password reconfirmation first.
    // confirmPass.style.borderColor = "black";

    if (!accept) {
        // checkPass.focus();
        checkPass.style.borderColor = "red";
        confirmPass.style.borderColor = "red";
        confirmPass.value = "";
        confirmPass.blur();
        return false;
    }
    // Password is fine.
    checkPass.style.borderColor = "black";
    
    if (confirmV != passV) {
        // alert("Re-entered Password must be the same!")
        validateFailWarning += "-Re-entered Password must be the same!\n";
        // confirmPass.focus();
        confirmPass.style.borderColor = "red";
        return false;
    }
    // Re-entered password is the same.
    confirmPass.style.borderColor = "black";
    return true;
}

function validateState(checkState) {
    if (checkState.value == "none") {
        checkState.style.borderColor = "red";
        validateFailWarning += "-Select your State!\n";
        return false;
    }
    checkState.style.borderColor = "black";
    return true;
}

function validateAcceptTerm(checkAccept) {
    if (!checkAccept.checked) {
        validateFailWarning += "-You must Accept the Terms and Conditions for Registration!\n";
        return false;
    }
    return true;
}

// Validate all inputs in the form.
function validateForm() {
    // if (!confirm("Submit Data for Registration?\nMake sure All Data are Correct!")) {
    //     return false;
    // }

    validateFailWarning = "";
    var acceptForm = true;

    if (!validateName(firstName, "First Name")) {
        acceptForm = false;
    }

    if (!validateName(lastName, "Last Name")) {
        acceptForm = false;
    }
    
    if (!validateEmail(email)) {
        acceptForm = false;
    }
    
    if (!validatePhoneNo(countryCode, phoneNo)) {
        acceptForm = false;
    }
    
    if (!validatePassword(password, reconfirmPassword)) {
        acceptForm = false;
    }
    
    if (!validateState(state)) {
        acceptForm = false;
    }

    if (!validateAcceptTerm(acceptTerm)) {
        acceptForm = false;
    }
    
    // For testing only.
    if (acceptForm) {
        alert("Success Registration!");
        return true;
    }

    alert(validateFailWarning);
    return false;
}
