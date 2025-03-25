
function redir(newUrl){
    window.location.assign(newUrl);
}

function loadFormData(){
    const dataElement = document.getElementById("main_form_data");
    if(dataElement && formData) {
        dataElement.value = JSON.stringify(formData);
    }
}

function copyToClipboard(text){
    navigator.clipboard.writeText(text)
}

function openUrlOnNewTab(url){
    window.open(url, '_blank');
}