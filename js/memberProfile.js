
function resetForm() {
    if(window.confirm("Remove the change made?")) {
        return true;
    } else {
        return false;
    }
}

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
            const boxInputTypes = ['text', 'tel', 'date'];
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


const form = document.registrationForm;

const firstName = new FormElement(form.firstName, function() {
    if(this.inputElement.value == '') {
        this.showWarning('Enter your first name');
    } else if(this.inputElement.value.search(/\s/) >= 0) {
        this.showWarning('First name cannot contain any whitespace character (spaces, tabs, line breaks)');
    } else if(this.inputElement.value.search(/[0-9]/) >= 0)  {
        this.showWarning('First name cannot contain number(s)');
    } else if(this.inputElement.value.search(/[A-Z]/) != 0)  {
        this.showWarning('First name must begin with an uppercase character (A-Z)');
    } else if(this.inputElement.value.substring(1).search(/[A-Z]/) >= 0)  {
        this.showWarning('All characters after the first character must be lowercase characters');
    } else if (this.inputElement.value.length < 2) {
        this.showWarning('First name must have at least 2 characters');
    } else {
        this.hideWarning();
        return true;
    }
    return false;
});
const lastName = new FormElement(form.lastName, function() {
    if(this.inputElement.value == '') {
        this.showWarning('Enter your last name');
    } else if(this.inputElement.value.search(/\s/) >= 0) {
        this.showWarning('Last name cannot contain any whitespace character (spaces, tabs, line breaks)');
    } else if(this.inputElement.value.search(/[0-9]/) >= 0)  {
        this.showWarning('Last name cannot contain number(s)');
    } else if(this.inputElement.value.search(/[A-Z]/) != 0)  {
        this.showWarning('Last name must begin with an uppercase character (A-Z)');
    } else if(this.inputElement.value.substring(1).search(/[A-Z]/) >= 0)  {
        this.showWarning('All characters after the first character must be lowercase characters');
    } else if (this.inputElement.value.length < 2) {
        this.showWarning('Last name must have at least 2 characters');
    } else {
        this.hideWarning();
        return true;
    }
    return false;
});
const phone = new FormElement(form.phone, function() {
    if(this.inputElement.value == '') {
        this.showWarning('Enter your mobile phone number');
    } else if(this.inputElement.value.search(/\s/) >= 0) {
        this.showWarning('Phone number cannot contain any whitespace character (spaces, tabs, line breaks)');
    } /*else if(this.inputElement.value[0] !== '1') {
        this.showWarning('Invalid format. Malaysia mobile phone number must begin with 1');
    }*/ else if(this.inputElement.value.search(/[^0-9]/) >= 0) {
        this.showWarning("Phone number can only contain numbers without any special character such as '-'");
    } else if(this.inputElement.value.length < 9 || this.inputElement.value.length > 10) {
        this.showWarning('Malaysia mobile phone number must have 9 - 10 digits (excluding +60)');
    }   else {
        this.hideWarning();
        return true;
    }
    return false;
});
const gender = new FormElement(form.gender[0], function() {
    if(!form.gender[0].checked && !form.gender[1].checked) {
        this.showWarning('Select your gender');
    } else {
        this.hideWarning();
        return true;
    }
    return false;
});
const state = new FormElement(form.state, function() {
    /* if(this.inputElement.value.search(/^(MY-)(0[1-9]|1[0-6])$/) !== 0) { */
    if(this.inputElement.value.search(/^(UK-)(0[1-4])$/) !== 0) {
        this.showWarning('Select your state');
    } else {
        this.hideWarning();
        return true;
    }
    return false;
});
const dob = new FormElement(form.dob, function() {
    var currentDate = new Date();
    var dobTime = new Date(this.inputElement.value);
    
    //user cannot select the date exceed of today
    if(this.inputElement.value === ''){
        this.showWarning('Select correct date of birth');
    }else if((currentDate.getTime() - dobTime.getTime()) <= 0) {
        this.showWarning('Select correct date of birth');
    } else {
        this.hideWarning();
        return true;
    }
    return false;
});

function validateForm() {
    // first invalid form element, of which will be focused upon submission of form (when user clicks REGISTER)
    let invalidFormElementToFocus;

    if(!firstName.validate()) {
        invalidFormElementToFocus = firstName;
    }
    if(!lastName.validate() && !invalidFormElementToFocus) {
        invalidFormElementToFocus = lastName;
    }
    if(!phone.validate() && !invalidFormElementToFocus) {
        invalidFormElementToFocus = phone;
    }
    if(!gender.validate() && !invalidFormElementToFocus) {
        invalidFormElementToFocus = gender;
    }
    if(!state.validate() && !invalidFormElementToFocus) {
        invalidFormElementToFocus = state;
    }
    if(!dob.validate() && !invalidFormElementToFocus) {
        invalidFormElementToFocus = dob;
    }

    if(invalidFormElementToFocus) {
        invalidFormElementToFocus.inputElement.focus();
        return false;
    } else {
        return true;
    }
}

window.addEventListener('load', function() {
    form.firstName.addEventListener('blur', () => {
        firstName.validate();
    });
    form.lastName.addEventListener('blur', () => {
        lastName.validate();
    });
    form.phone.addEventListener('blur', () => {
        phone.validate();
    });
    form.state.addEventListener('change', () => {
        state.validate();     
    });
    form.dob.addEventListener('change', ()=>{
        dob.validate();
    });
});