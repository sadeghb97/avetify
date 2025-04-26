document.addEventListener('keydown', function (e) {
    const target = e.target;
    if (target.classList.contains('numeric-text')) {
        const allowedKeys = [
            'Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End'
        ];

        const isOk = (e.key >= '0' && e.key <= '9') ||
            allowedKeys.includes(e.key) ||
            (e.key === '.' && !target.value.includes('.'))

        if(!isOk) e.preventDefault()
    }
});

document.addEventListener('keydown', function (e) {
    const target = e.target;
    if (target.classList.contains('submitter')) {
        if (e.key === 'Enter') {
            let parent = target;
            while (parent) {
                if (parent.tagName === 'FORM') {
                    const formId = parent.id;
                    triggerForm(formId);
                    return;
                }
                parent = parent.parentElement;
            }
        }
    }
});

function triggerForm(formId, confirmMessage, formTriggerElementId, triggerId) {
    loadFormData();
    let isOk = true;
    if (confirmMessage) {
        isOk = confirm(confirmMessage);
    }

    if (isOk) {
        if (formTriggerElementId) {
            const formTriggerElement = document.getElementById(formTriggerElementId);
            formTriggerElement.value = triggerId;
        }

        const event = new Event("submit", { cancelable: true });
        document.getElementById(formId).dispatchEvent(event);
    }
}


function redir(newUrl, delay){
    if(delay){
        setTimeout(() => {
            window.location.assign(newUrl);
        }, delay)
    }
    else window.location.assign(newUrl);
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

function submitForm(formId){
    const form = document.getElementById(formId)
    form.submit()
}