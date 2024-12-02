
function redir(newUrl){
    window.location.assign(newUrl);
}

function loadFormData(){
    const dataElement = document.getElementById("main_form_data");
    dataElement.value = JSON.stringify(formData);
}