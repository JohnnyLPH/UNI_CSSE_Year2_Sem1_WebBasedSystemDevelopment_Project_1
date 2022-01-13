class FormElement {
    constructor(inputElement, validate) {
        this.inputElement = inputElement;
        this.validate = validate; // returns true upon successful validation and false upon invalid or incomplete input
    }

    getWarningElement() {
        const warningClass = 'warning-text';
        let warningTextParentElement = this.inputElement.parentElement;
        
        if(warningTextParentElement.getElementsByClassName(warningClass).length <= 0) {
            // search outer level
            warningTextParentElement = warningTextParentElement.parentElement;
        }
    
        return warningTextParentElement.getElementsByClassName(warningClass)[0];
    }
    
    showWarning(text) {
        const warningText = this.getWarningElement();
        warningText.innerHTML = text;
        warningText.classList.remove('hidden'); 
    
        // set input box border color to red
        function setRedBorder(inputElement) {
            inputElement.classList.add('warning');
        }
    
        // if input has a box, set box border color to red
        let inputType = this.inputElement.getAttribute('type');
        if(inputType) {
            inputType = inputType.toLowerCase();
            const boxInputTypes = ['text', 'email', 'tel', 'password'];
            if(boxInputTypes.indexOf(inputType) >= 0) {
                setRedBorder(this.inputElement);
            }
        } else if(this.inputElement.tagName.toLowerCase() == 'select') {
            setRedBorder(this.inputElement);
        }
    }

    hideWarning() {
        const warningText = this.getWarningElement();
        warningText.innerText = '';
        warningText.classList.add('hidden');
        this.inputElement.classList.remove('warning');
    }
}

const form = document.changePasswordForm;

const userPassword = new FormElement(form.newPassword, function() {
    if(this.inputElement.value == '') {
        this.showWarning('Enter your password');
    } else if (this.inputElement.value.search(/\s/) >= 0) {
        this.showWarning('Password cannot contain any whitespace character (spaces, tabs, line breaks)');
    } else if (this.inputElement.value.search(/^(?=.*[A-Z])(?=.*[a-z])(?=.*\W)(?=.*\d).{1,}$/) !== 0) {
        this.showWarning('Password must contain at least 1 uppercase character (A-Z), 1 lowercase character (a-z), 1 special character (!, @, #, $, %, ^, &, *) and 1 number (0-9)');
    } else if (this.inputElement.value.length < 6) {
        this.showWarning('Password must have at least 6 characters');
    } else {
        this.hideWarning();
        return true;
    }
    return false;
});

const userConfirmPassword = new FormElement(form.newConfirmPassword, function() {
    if(this.inputElement.value == '') {
        this.showWarning('Enter your OTP');
    } else if (this.inputElement.value.length != 6) {
        this.showWarning('OTP must have  6 digits');
    } else {
        this.hideWarning();
        return true;
    }
    return false;
});



function validateForm() {
    // first invalid form element, of which will be focused upon submission of form (when user clicks REGISTER)
    let invalidFormElementToFocus;

    if(!userPassword.validate() && !invalidFormElementToFocus) {
        invalidFormElementToFocus = userPassword;
    }
   
    if(!userConfirmPassword.validate() && !invalidFormElementToFocus) {
        invalidFormElementToFocus = userConfirmPassword;
    }
 
    if(invalidFormElementToFocus) {
        invalidFormElementToFocus.inputElement.focus();
        return false;
    } else {
        return true;
    }
}

window.addEventListener('load', function() {
    
    form.newPassword.addEventListener('blur', () => {
        userEmail.validate();
    });
    
    form.newConfirmPassword.addEventListener('blur', () => {
        userOTP.validate();
    });
    
});


