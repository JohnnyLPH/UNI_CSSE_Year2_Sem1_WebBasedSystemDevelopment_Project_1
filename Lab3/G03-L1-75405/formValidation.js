function validate(){
    //make sure the alert string is reseted before everytime validation 
    resetAlertString();

    if(fname_validation()){
        document.getElementById("user_fname_input").focus();
        return false;
    }else if(lname_validation()){
        document.getElementById("user_lname_input").focus();
        return false;
    }else if(email_validation()){
        document.getElementById("user_email_input").focus();
        return false;
    }else if(mobile_validation()){
        document.getElementById("user_mobile_input").focus();
        return false;
    }else if(password_validation()){
        document.getElementById("user_password_input").focus();
        return false;
    }else if(cpassword_validation()){
        document.getElementById("user_cpassword_input").focus();
        return false;
    }else if(gender_validation()){
        return false;
    }else if(state_validation()){
        return false;
    }else if(provision_validation()){
        return false;
    }
    confirm("Submit");

}

function fname_validation(){
    var user_fname = document.getElementById("user_fname_input").value;
    
    if(user_fname == ""){
        alert("Please provide your first name!");
        alertForEmptyInput("user_fname_alert_string", "First Name");
        return true;
    }else if(user_fname.trim() == ""){
        alertForCertainInputError("user_fname_alert_string", "empty space");
        alert("Your input cannot only contain space character !");
        return true;
    }else if(!(/^[A-Z][a-z]{0}/g.test(user_fname))  ||  /\s/g.test(user_fname)){
        alert("Your name input must be the first character uppercase and followed by lowercase without any space character!");
        document.getElementById("user_fname_alert_string").innerHTML = "&#8727 Your name input must be the first character uppercase and followed by lowercase !";
        return true;
    }
    document.getElementById("fname_img").src="source/icon/Verified/id-card.png";
   
    return false;
}

function lname_validation(){
    var user_lname = document.getElementById("user_lname_input").value;
    if(user_lname == ""){
        alert("Please provide your last name!");
        alertForEmptyInput("user_lname_alert_string", "last Name");
        return true;
    }else if(user_lname.trim() == ""){
        alertForCertainInputError("user_lname_alert_string", "empty space");
        alert("Your input cannot only contain space character !");
        return true;
    }else if(!(/^[A-Z][a-z]{0}/g.test(user_lname))  ||  /\s/g.test(user_lname)){
        alert("Your name input must be the first character uppercase and followed by lowercase withoud any space character!");
        document.getElementById("user_lname_alert_string").innerHTML = "&#8727 Your name input must be the first character uppercase and followed by lowercase !";
        return true;
    }
    document.getElementById("lname_img").src="source/icon/Verified/id-card.png";
    return false;
}

function email_validation(){
    
    var user_email = document.getElementById("user_email_input").value;

    if(user_email == ""){
        alert("Please provide your email!");
        alertForEmptyInput("user_email_alert_string", "Email");
        return true;
    }else if(!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(user_email))){
        alert("Invalid email format inputting");
        document.getElementById("user_email_alert_string").innerHTML = "&#8727 Invalid email inputting";
        return true;
    }
    document.getElementById("email_img").src="source/icon/Verified/email.png";
    return false;
}

function password_validation(){
    var user_password = document.getElementById("user_password_input").value;
    //test wether if space character can be checked or not 
    var isNoSpace = false;
    var isAtLeastOneUppercase = false;
    var isAtLeastOneLowercase = false;
    var isSixChar = (user_password.length == 6)?true:false;
    var isAtLeastOneSpecialChar = false;
    var isAtLeastOneNumeric = false;

    isNoSpace = (/\s/.test(user_password))?false:true;
    isAtLeastOneUppercase = /[A-Z]/.test(user_password);
    isAtLeastOneLowercase =  /[a-z]/.test(user_password);
    isAtLeastOneSpecialChar = /[\!\@\#\$\%\^\&\*\)\(\+\=\.\<\>\{\}\[\]\:\;\'\"\|\~\`\_\-]/g.test(user_password);
    isAtLeastOneNumeric = /\d/.test(user_password);
   
    if(!(isNoSpace && isAtLeastOneLowercase && isAtLeastOneUppercase && isAtLeastOneSpecialChar && isSixChar && isAtLeastOneNumeric)){
        alert("You are required to typing 6 alphanumeric characters with at least 1 Uppercase, 1 lowercase, number and no space as password");
        document.getElementById("user_password_alert_string").innerHTML = "You are required to typing 6 alphanumeric characters with at least 1 Uppercase, 1 lowercase, number and no space as password";
        return true;
    }
    document.getElementById("password_img").src="source/icon/Verified/password.png";
    return false;
}

function cpassword_validation(){
    var user_cpassword = document.getElementById("user_cpassword_input").value;
    if(user_cpassword.length != 6){
        alert("You are required to typing 6 characters as confirm password");
        return true;
    }else if(user_cpassword != document.getElementById("user_password_input").value){
        alert("Your confirmation password is not the same as your password !");
        alertForCertainInputError("user_cpassword_alert_string", "the password other than what you type previously !");
        return true;
    }
    document.getElementById("cpassword_img").src="source/icon/Verified/confirm_password.png";
    return false;
}

function mobile_validation(){
    var user_mobile = document.getElementById("user_mobile_input").value;
    if(user_mobile.trim() == ""){
        alert("Please provide your mobile number!");
        alertForEmptyInput("user_mobile_alert_string", "Mobile number");
        return true;
    }else if(!(/^[1-9][0-9]{7,9}$/.test(user_mobile)) || user_mobile.length > 10 || user_mobile < 8){
        alert("Please provide your correct mobile number!");
        alertForCertainInputError("user_mobile_alert_string", "mobile number that less than 8 or more than 10 or non-digit");
        return true;
    }
    document.getElementById("mobile_img").src="source/icon/Verified/smartphone.png";
    return false;
}

function gender_validation(){
    var user_gender = document.getElementsByName("user_gender");
    //extract value from radio input
    var i;
    var user_gender_value;
    for(i = 0; i < user_gender.length; i++){
        if(user_gender[i].checked){
            user_gender_value = user_gender[i].value;
        }
    }
    if(user_gender_value == undefined){
        alert("Please provide your gender information!");
        alertForEmptyInput("user_gender_alert_string", "Gender selection");
        return true;
    }
    document.getElementById("gender_img").src="source/icon/Verified/gender.png";
    return false;
}

function state_validation(){
    var state_list = document.getElementById("user_state_id");
    var user_state = state_list.options[state_list.selectedIndex].value;

    if(user_state == "none"){
        alert("Please provide your state information!");
        alertForEmptyInput("user_state_alert_string", "State selection");
        state_list.focus();
        return true;
    }
    document.getElementById("state_img").src="source/icon/Verified/region.png";
    return false;
}

function provision_validation(){
    var user_provision = document.getElementById("user_provision_id");

    if(user_provision.checked == false){
        alert("You must be accept the provision before registration !");
        document.getElementById("user_provision_alert_string").innerHTML = "&#8727 You must be accept the provision before registration !";
        user_provision.focus();
        return true;
    }
    return false;
}

function alertForEmptyInput(elementId, inputEmptyElement){
    document.getElementById(elementId).innerHTML = "&#8727 " + inputEmptyElement + " cannot be emptied";
}

function alertForCertainInputError(elementId, inputErrorInfo){
    document.getElementById(elementId).innerHTML = "&#8727 Your input cannot contain " + inputErrorInfo + " !";
}

function resetAlertString(){
    document.getElementById("user_fname_alert_string").innerHTML = "";
    document.getElementById("user_lname_alert_string").innerHTML = "";
    document.getElementById("user_email_alert_string").innerHTML = "";
    document.getElementById("user_mobile_alert_string").innerHTML = "";
    document.getElementById("user_password_alert_string").innerHTML = "";
    document.getElementById("user_cpassword_alert_string").innerHTML = "";
    document.getElementById("user_gender_alert_string").innerHTML = "";
    document.getElementById("user_state_alert_string").innerHTML = "";
    document.getElementById("user_provision_alert_string").innerHTML = "";
}

function resetInputFocus(){
    document.getElementById("user_fname_input").blur();
    document.getElementById("user_lname_input").blur();
    document.getElementById("user_email_input").blur();
    document.getElementById("user_mobile_input").blur();
    document.getElementById("user_password_input").blur();
    document.getElementById("user_cpassword_input").blur();
    document.getElementsByName("user_gender").blur();
    document.getElementById("user_state_id").blur();
    document.getElementsByName("user_provision_id").blur();
}

function clearInput(){
    document.getElementById("user_fname_input").value = "";
    document.getElementById("user_lname_input").value = "";
    document.getElementById("user_email_input").value = "";
    document.getElementById("user_mobile_input").value = "";
    document.getElementById("user_password_input").value = "";
    document.getElementById("user_cpassword_input").value = "";
    document.getElementById("male").checked = false;
    document.getElementById("female").checked = false;
    document.getElementById("user_state_id").value = "none";
    document.getElementById("user_provision_id").checked = false;
    

    resetInputFocus();
    resetAlertString();
}