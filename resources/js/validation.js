/** 
 * Check if confirm password field is the same as password field. 
 * 
 * Function has to be passed in confirm password field's onInput attribute.
 * 
 * Also confirm password field has to have data-checkto attribute with password field's name.
 * */
const confirmPasswordValidation = (input) => {
    if (input && input.dataset.checkto) {
        const passwordField = document.querySelector(`input[name="${input.dataset.checkto}"]`);
        
        if (passwordField && passwordField.value) {
            if (input.value !== passwordField.value) {
                input.setCustomValidity('Passwords do not match!');
            } else {
                input.setCustomValidity('');
            }
        }
    }
};
