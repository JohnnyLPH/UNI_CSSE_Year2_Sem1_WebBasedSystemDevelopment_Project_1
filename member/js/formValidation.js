function cancel() {
    if(window.confirm('Cancel the order? This action cannot be undone.')) {
        proposalForm.cancel.value = true;
        proposalForm.submit();
    }
}

function goPrevious() {
    proposalForm.goPrevious.value = true;

    return true;
}

// prevent text input on numeric input
const numericTextInputs = document.querySelectorAll('input[type="text"][inputmode="numeric"]');
for (let i = 0; i < numericTextInputs.length; i++) {
    numericTextInputs[i].addEventListener("beforeinput", function(e) {
        if(e.data && e.data.search(/\D/) >= 0) {
            e.preventDefault();
        }
    });
}

const sortCode = document.getElementById('sortCode');
if(sortCode) {
    const sortCodeInputs = sortCode.querySelectorAll('input');
    for (let i = 0; i < sortCodeInputs.length; i++) {
        // auto focus on next consecutive input if exists
        const nextElementIndex = i + 1;
        if(nextElementIndex < sortCodeInputs.length) {
            sortCodeInputs[i].addEventListener("input", function(e) {
                if(e.data && e.target.value.length >= e.target.maxLength) {
                    sortCodeInputs[nextElementIndex].focus();
                }
            });
        }
        
        // auto focus on previous input upon Backspace for all inputs except the first input
        if(i > 0) {
            sortCodeInputs[i].addEventListener("keydown", function(e) {
                if(e.code === "Backspace" && e.target.value.length === 0) {
                    sortCodeInputs[i - 1].focus();
                }
            });
        }
    }
}