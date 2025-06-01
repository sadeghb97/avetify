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

function helloAvt(first, second, third){
    console.log("Hello AVT!", first, second, third)
}

function redir(newUrl, delay){
    if(delay){
        setTimeout(() => {
            window.location.assign(newUrl);
        }, delay)
    }
    else window.location.assign(newUrl);
}

function openTab(url){
    window.open(url, '_blank');
}

function loadFormData(){
    const dataElement = document.getElementById("main_form_data");
    if(dataElement && formData) {
        dataElement.value = JSON.stringify(formData);
    }
}

function copyToClipboard(text, silent = false){
    navigator.clipboard.writeText(text)
        .then(() => {
            //alert("copied!");
        })
        .catch(err => {
            console.error("Failed to copy: ", err);
        });
}

function openUrlOnNewTab(url){
    window.open(url, '_blank');
}

function submitForm(formId){
    const form = document.getElementById(formId)
    form.submit()
}

function addParamToLink(url, paramKey, paramValue) {
    const parsedUrl = new URL(url);
    parsedUrl.searchParams.set(paramKey, paramValue);
    return parsedUrl.toString();
}

function addParamToCurrentLink(paramKey, paramValue) {
    return addParamToLink(window.location.href, paramKey, paramValue)
}

function findClosestChildrenByTag(parent, tagName) {
    if (!(parent instanceof Element)) return null;
    tagName = tagName.toUpperCase();

    for (let child of parent.children) {
        if (child.tagName === tagName) {
            return child;
        }
    }

    for (let child of parent.children) {
        const found = findClosestChildrenByTag(child, tagName);
        if (found) {
            return found;
        }
    }

    return null;
}

document.addEventListener('keydown', function (e) {
    const target = e.target;
    if (target.classList.contains('numeric-text')) {
        const allowedKeys = [
            'Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End', 'Enter'
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

window.addEventListener('DOMContentLoaded', () => {
    if (window.hljs && typeof hljs.highlightAll === "function") {
        hljs.addPlugin(new CopyButtonPlugin());
        hljs.highlightAll();
    }

    const input = document.querySelector('input:not([type="hidden"]):not([type="checkbox"])');
    if(!input) return;

    let warmedUp = false;
    const warmUp = (e) => {
        if (warmedUp) return;
        warmedUp = true;

        let skipFocusing = false;
        if (e.type === 'click') {
            const target = e.target;
            if (
                target.tagName === 'INPUT' &&
                target.type !== 'hidden' &&
                target.type !== 'checkbox'
            ) {
                skipFocusing = true;
            }
        }

        if(!skipFocusing) {
            input.focus();
            input.select();
            input.blur();
        }

        window.removeEventListener('click', warmUp);
        window.removeEventListener('keydown', warmUp);
        window.removeEventListener('touchstart', warmUp);
        window.removeEventListener('mousemove', warmUp);
    };

    window.addEventListener('click', warmUp, { once: true });
    window.addEventListener('keydown', warmUp, { once: true });
    window.addEventListener('touchstart', warmUp, { once: true });
    window.addEventListener('mousemove', warmUp, { once: true });
});