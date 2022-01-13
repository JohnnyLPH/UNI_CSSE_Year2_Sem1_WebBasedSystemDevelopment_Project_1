var warningMsg;
var passValidate;
var res

function checkStringInput(inputToCheck, minLength = null, maxLength = null, compareInput = null, regexTest = null, clearValue = false) {
    var acceptInput = true;
    
    inputToCheck.value = inputToCheck.value.trim();
    var inputV = inputToCheck.value;

    // Empty input.
    if (inputV == "") {
        acceptInput = false;
    }
    else {
        // Check max length.
        if (maxLength != null && inputV.length > maxLength) {
            acceptInput = false;
        }
        // Check min length.
        else if (minLength != null && inputV.length < minLength) {
            acceptInput = false;
        }
        // Check regex.
        else if (regexTest != null && !regexTest.test(inputV)) {
            acceptInput = false;
        }
        // Compare input.
        else if (compareInput != null && inputV != compareInput) {
            acceptInput = false;
        }
    }

    if (!acceptInput) {
        inputToCheck.style.borderColor = "red";
        inputToCheck.blur();

        if (clearValue) {
            inputToCheck.value = "";
        }
    }
    else {
        inputToCheck.style.borderColor = "";
    }
    return acceptInput;
}

function checkNumberInput(inputToCheck, minV = null, maxV = null, clearValue = false) {
    var acceptInput = true;
    
    inputToCheck.value = inputToCheck.value.trim();
    var inputV = inputToCheck.value;

    // Not a number.
    if (inputV.length < 1 || isNaN(inputV)) {
        acceptInput = false;
    }
    else {
        // Check max value.
        if (maxV != null && inputV > maxV) {
            acceptInput = false;
        }
        // Check min value.
        else if (minV != null && inputV < minV) {
            acceptInput = false;
        }
    }

    if (!acceptInput) {
        inputToCheck.style.borderColor = "red";
        inputToCheck.blur();

        if (clearValue) {
            inputToCheck.value = "";
        }
    }
    else {
        inputToCheck.style.borderColor = "";
    }
    return acceptInput;
}

function checkUploadFile(inputToCheck, minSize = null, maxSize = null, acceptedType = null, clearValue = false) {
    var acceptInput = true;
    
    var inputV = inputToCheck.value;

    // No file.
    if (!inputV) {
        acceptInput = false;
    }
    else {
        var fileType = inputToCheck.files[0].type.split('/').pop().toLowerCase();
        if (acceptedType != null && !acceptedType.includes(fileType)) {
            acceptInput = false;
        }
        // Check max size.
        if (maxSize != null && inputToCheck.files[0].size > maxSize) {
            acceptInput = false;
        }
        // Check min size.
        else if (minSize != null && inputToCheck.files[0].size < minSize) {
            acceptInput = false;
        }
    }

    if (!acceptInput) {
        inputToCheck.style.borderColor = "red";
        inputToCheck.blur();

        if (clearValue) {
            inputToCheck.value = "";
        }
    }
    else {
        inputToCheck.style.borderColor = "";
    }
    return acceptInput;
}

// For Admin Login.
function adminLoginValidation(emptyMsg = true, showAlert = true) {
    if (emptyMsg) {
        warningMsg = "";
    }
    passValidate = true;

    res = /^[A-Za-z0-9]{1}[A-Za-z0-9]+(\s[A-Za-z0-9]{1}[A-Za-z0-9]+)*$/;

    // Check admin name.
    if (!checkStringInput(document.getElementById("admin-name"), 3, 128, null, res)) {
        warningMsg += "* Enter Valid Admin Name!\n";
        passValidate = false;
    }

    res = /^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/;

    // Check admin password.
    if (!checkStringInput(document.getElementById("admin-password"), 6, 256, null, res, true)) {
        warningMsg += "* Enter Valid Admin Password!\n";
        passValidate = false;
    }

    if (!passValidate && showAlert) {
        alert(warningMsg);
    }

    return passValidate;
}

// For Manage Admin: Add Admin.
function addAdminValidation(emptyMsg = true, showAlert = true) {
    if (emptyMsg) {
        warningMsg = "";
    }
    passValidate = true;

    res = /^[A-Za-z0-9]{1}[A-Za-z0-9]+(\s[A-Za-z0-9]{1}[A-Za-z0-9]+)*$/;

    // Check admin name.
    if (!checkStringInput(document.getElementById("admin-name"), 3, 128, null, res)) {
        warningMsg += "* Enter Valid Admin Name (3 - 128 Char; Alphabets & Digits; Min 2 Char per Word; Allow 1 Space Between)!\n";
        passValidate = false;
    }

    res = /^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/;

    // Check admin password.
    if (!checkStringInput(document.getElementById("admin-password"), 6, 256, null, res, true)) {
        warningMsg += "* Enter Valid Admin Password (1 Special Char, 1 Upper, 1 Lower, 3 Digits; 6 - 256 Char; Space Ignored at Start & End)!\n";
        passValidate = false;
    }

    // Check reentered admin password.
    if (
        !checkStringInput(document.getElementById("admin-password2"), 6, 256, document.getElementById("admin-password").value, res, true)
    ) {
        warningMsg += "* Reenter Same & Valid Admin Password!\n";
        passValidate = false;
    }
    
    if (!passValidate && showAlert) {
        alert(warningMsg);
    }

    return passValidate;
}

// For Manage Admin: Edit Admin.
function editAdminValidation(emptyMsg = true, showAlert = true) {
    if (emptyMsg) {
        warningMsg = "";
    }
    passValidate = true;

    res = /^[A-Za-z0-9]{1}[A-Za-z0-9]+(\s[A-Za-z0-9]{1}[A-Za-z0-9]+)*$/;

    // Check admin name.
    if (!checkStringInput(document.getElementById("new-admin-name"), 3, 128, null, res)) {
        warningMsg += "* Enter Valid Admin Name (3 - 128 Char; Alphabets & Digits; Min 2 Char per Word; Allow 1 Space Between)!\n";
        passValidate = false;
    }

    res = /^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/;

    // Check new password if provided.
    if (document.getElementById("new-admin-password").value.length > 0 || document.getElementById("new-admin-password2").value.length > 0) {
        // Check admin password.
        if (!checkStringInput(document.getElementById("new-admin-password"), 6, 256, null, res, true)) {
            warningMsg += "* Reenter Valid New Password (1 Special Char, 1 Upper, 1 Lower, 3 Digits; 6 - 256 Char; Space Ignored at Start & End) or Leave Empty!\n";
            passValidate = false;
        }
    
        // Check reentered admin password.
        if (
            !checkStringInput(document.getElementById("new-admin-password2"), 6, 256, document.getElementById("new-admin-password").value, res, true)
        ) {
            warningMsg += "* Reenter Same & Valid New Password or Leave Empty!\n";
            passValidate = false;
        }
    }
    else {
        document.getElementById("new-admin-password").style.borderColor = "black";
        document.getElementById("new-admin-password2").style.borderColor = "black";
    }
    
    // Check old password if required.
    if (document.getElementById("old-admin-password") != null && !checkStringInput(document.getElementById("old-admin-password"), 6, 256, null, res, true)) {
        warningMsg += "* Enter Valid Old Password to Save Changes!\n";
        passValidate = false;
    }

    if (!passValidate && showAlert) {
        alert(warningMsg);
    }

    return passValidate;
}

// For Manage Admin/Member/Vehicle: Delete Admin/Member/Car.
function adminDeleteValidation(emptyMsg = true, showAlert = true) {
    if (emptyMsg) {
        warningMsg = "";
    }
    passValidate = true;

    res = /^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/;

    // Check old password if required, for Delete Admin only.
    if (document.getElementById("old-admin-password") != null && !checkStringInput(document.getElementById("old-admin-password"), 6, 256, null, res, true)) {
        warningMsg += "* Enter Valid Password for Admin which will be Deleted!\n";
        passValidate = false;
    }

    // Check current admin password if provided.
    if (!checkStringInput(document.getElementById("current-admin-password"), 6, 256, null, res, true)) {
        warningMsg += "* Enter Your Password (Must be Valid) to Confirm Delete!\n";
        passValidate = false;
    }
    
    if (!passValidate && showAlert) {
        alert(warningMsg);
    }

    return passValidate;
}

// For Manage Admin/Member/Vehicle: Delete Admin/Member/Car.
function adminDeleteValidation(emptyMsg = true, showAlert = true) {
    if (emptyMsg) {
        warningMsg = "";
    }
    passValidate = true;

    res = /^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/;

    // Check old password if required, for Delete Admin only.
    if (document.getElementById("old-admin-password") != null && !checkStringInput(document.getElementById("old-admin-password"), 6, 256, null, res, true)) {
        warningMsg += "* Enter Valid Password for Admin which will be Deleted!\n";
        passValidate = false;
    }

    // Check current admin password if provided.
    if (!checkStringInput(document.getElementById("current-admin-password"), 6, 256, null, res, true)) {
        warningMsg += "* Enter Your Password (Must be Valid) to Confirm Delete!\n";
        passValidate = false;
    }
    
    if (!passValidate && showAlert) {
        alert(warningMsg);
    }

    return passValidate;
}

// For Manage Member/Vehicle/Order/Transaction: Delete Admin/Member/Car.
function searchWordValidation(emptyMsg = true, showAlert = true) {
    if (emptyMsg) {
        warningMsg = "";
    }
    passValidate = true;

    // Check word to search.
    if (!checkStringInput(document.getElementById("word-to-search"), 1, 100)) {
        warningMsg += "* To Search, Enter Min 1, Max 100 Char!\n";
        passValidate = false;
    }
    
    if (!passValidate && showAlert) {
        alert(warningMsg);
    }

    return passValidate;
}

// For Manage Vehicle: Add Car.
function addCarValidation(emptyMsg = true, showAlert = true) {
    if (emptyMsg) {
        warningMsg = "";
    }
    passValidate = true;

    res = /^[A-Z]{1}[A-Za-z]*([-\s]{1}[A-Z]{1}[A-Za-z]*)*$/;

    // Check car brand.
    if (!checkStringInput(document.getElementById("car-brand"), 3, 100, null, res)) {
        warningMsg += "* Enter Car Brand (Min: 3 Char; Alphabets Only; 1 Space/Dash Between; Start Word with 1 Upper)!\n";
        passValidate = false;
    }
    
    res = /^[A-Za-z0-9]{1}[A-Za-z0-9]*([-\s]{1}[A-Za-z0-9]{1}[A-Za-z0-9]*)*$/;

    // Check car model.
    if (!checkStringInput(document.getElementById("car-model"), 2, 100, null, res)) {
        warningMsg += "* Enter Car Model (Min: 2 Char; Alphabets & Digits; 1 Space/Dash Between)!\n";
        passValidate = false;
    }
    
    // Check month price.
    if (!checkNumberInput(document.getElementById("month-price"), 100, 1000)) {
        warningMsg += "* Enter Price Per Month (Min: 100; Max: 1000)!\n";
        passValidate = false;
    }
    
    // Check lease time.
    if (!checkNumberInput(document.getElementById("lease-time"), 6, 60)) {
        warningMsg += "* Enter Lease Time (Min: 6; Max: 60; Months)!\n";
        passValidate = false;
    }
    
    // Check initial pay.
    if (!checkNumberInput(document.getElementById("initial-pay"), 3, 10)) {
        warningMsg += "* Enter Initial Pay (Min: 3; Max: 10; * Price)!\n";
        passValidate = false;
    }
    
    // Check car description.
    if (!checkStringInput(document.getElementById("car-desc"), 5, 512)) {
        warningMsg += "* Enter Car Description (Min: 5 Char)!\n";
        passValidate = false;
    }
    
    // Check car image.
    if (!checkUploadFile(document.getElementById("car-image"), null, 2097152, ['png', 'jpg', 'jpeg'], true)) {
        warningMsg += "* Upload a Car Image (Max: 2 MB; Only PNG or JPG)!\n";
        passValidate = false;
    }
    
    if (!passValidate && showAlert) {
        alert(warningMsg);
    }

    return passValidate;
}

// For Manage Vehicle: Edit Car.
function editCarValidation(emptyMsg = true, showAlert = true) {
    if (emptyMsg) {
        warningMsg = "";
    }
    passValidate = true;

    // Check month price.
    if (!checkNumberInput(document.getElementById("month-price"), 100, 1000)) {
        warningMsg += "* Enter Price Per Month (Min: 100; Max: 1000)!\n";
        passValidate = false;
    }
    
    // Check lease time.
    if (!checkNumberInput(document.getElementById("lease-time"), 6, 60)) {
        warningMsg += "* Enter Lease Time (Min: 6; Max: 60; Months)!\n";
        passValidate = false;
    }
    
    // Check initial pay.
    if (!checkNumberInput(document.getElementById("initial-pay"), 3, 10)) {
        warningMsg += "* Enter Initial Pay (Min: 3; Max: 10; * Price)!\n";
        passValidate = false;
    }
    
    // Check car description.
    if (!checkStringInput(document.getElementById("car-desc"), 5, 512)) {
        warningMsg += "* Enter Car Description (5 - 512 Char)!\n";
        passValidate = false;
    }

    // Check car image if provided.
    if (document.getElementById("car-image").value.length > 0 && !checkUploadFile(document.getElementById("car-image"), null, 2097152, ['png', 'jpg', 'jpeg'], true)) {
        warningMsg += "* Upload a Car Image (Max: 2 MB; Only PNG or JPG)!\n";
        passValidate = false;
    }

    if (!passValidate && showAlert) {
        alert(warningMsg);
    }

    return passValidate;
}

// For Manage Order: Edit Order.
function approveOrderValidation(emptyMsg = true, showAlert = true) {
    if (emptyMsg) {
        warningMsg = "";
    }
    passValidate = true;

    res = /^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/;

    // Check current admin password if provided.
    if (!checkStringInput(document.getElementById("current-admin-password"), 6, 256, null, res, true)) {
        warningMsg += "* Enter Your Password (Must be Valid) to Confirm New Status!\n";
        passValidate = false;
    }
    
    if (!passValidate && showAlert) {
        alert(warningMsg);
    }

    return passValidate;
}
