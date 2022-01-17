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

const form = document.loginForm;

const userEmail = new FormElement(form.userEmail, function() {
    if(this.inputElement.value == '') {
        this.showWarning('Enter your email');
    } else if(this.inputElement.value.search(/\s/) >= 0) {
        this.showWarning('Email cannot contain any whitespace character (spaces, tabs, line breaks)');
    } else if(this.inputElement.value.search(/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i) !== 0) {
        this.showWarning('Invalid email format. Email should have a format similar to <em>username@domain.com</em>');
    } else {
        this.hideWarning();
        return true;
    }
    return false;
});

const userPassword = new FormElement(form.userPassword, function() {
    if(this.inputElement.value == '') {
        this.showWarning('Enter your password');
    } else if (this.inputElement.value.search(/\s/) >= 0) {
        this.showWarning('Password cannot contain any whitespace character (spaces, tabs, line breaks)');
    } else if (this.inputElement.value.search(/^(?=.*[A-Z])(?=.*[a-z])(?=.*\W)(?=.*\d).{1,}$/) !== 0) {
        this.showWarning('Password must contain at least 1 uppercase character (A-Z), 1 lowercase character (a-z), 1 special character (!, @, #, $, %, ^, &, *) and 1 number (0-9)');
    } else if (this.inputElement.value.length != 6) {
        this.showWarning('Password must have exact 6 characters');
    } else {
        this.hideWarning();
        return true;
    }
    return false;
});

function validateForm() {
    // first invalid form element, of which will be focused upon submission of form (when user clicks REGISTER)
    let invalidFormElementToFocus;

    if(!userEmail.validate() && !invalidFormElementToFocus) {
        invalidFormElementToFocus = userEmail;
    }
   
    if(!userPassword.validate() && !invalidFormElementToFocus) {
        invalidFormElementToFocus = userPassword;
    }
 
    if(invalidFormElementToFocus) {
        invalidFormElementToFocus.inputElement.focus();
        return false;
    } else {
        return true;
    }
}

window.addEventListener('load', function() {
    
    form.userEmail.addEventListener('blur', () => {
        userEmail.validate();
    });
    
    form.userPassword.addEventListener('blur', () => {
        userPassword.validate();
    });
    
});


