function dialogSuccessMessage(dialogKey, message){
    const successMessage = document.getElementById("success_message_" + dialogKey)
    const errorMessage = document.getElementById("error_message_" + dialogKey)
    successMessage.innerText = message
    successMessage.style.display = "block"
    errorMessage.style.display = "none"
}

function dialogErrorMessage(dialogKey, message){
    const successMessage = document.getElementById("success_message_" + dialogKey)
    const errorMessage = document.getElementById("error_message_" + dialogKey)
    errorMessage.innerText = message
    errorMessage.style.display = "block"
    successMessage.style.display = "none"
}