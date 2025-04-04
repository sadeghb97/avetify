let pressTimer = null;

function titleCase(str) {
    var splitStr = str.toLowerCase().split(' ');
    for (var i = 0; i < splitStr.length; i++) {
        // You do not need to check if i is larger than splitStr length, as your for does that for you
        // Assign it back to the array
        splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
    }
    // Directly return the joined string
    return splitStr.join(' ');
}

function apiMedalClickAction(fieldKey, recordId, medalKey, initValue, apiEndpoint){
    const newValue = prompt('Enter new ' + titleCase(medalKey) + ": ", initValue);
    if(isNaN(newValue)) return;
    const valueElement = document.getElementById(fieldKey);

    applyField(recordId, medalKey, newValue, apiEndpoint, (data) => {
        valueElement.innerHTML = data['value'];
    })
}

function apiTextEnterAction(fieldKey, recordId, medalKey, apiEndpoint, callback){
    const valueElement = document.getElementById(fieldKey);
    const newValue = valueElement.value
    if(!newValue) return

    applyField(recordId, medalKey, newValue, apiEndpoint, (data) => {
        callback(data)
    })
}

function applyField(recordId, medalKey, newValue, apiEndpoint, callback){
    // Data to send
    const data = {
        record: recordId,
        property: medalKey,
        value: newValue
    };

    // Sending POST request
    fetch(apiEndpoint, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if(data['success']){
                callback(data)
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

function addLongClickEvent(elementId, callback){
    const element = document.getElementById(elementId);

    element.addEventListener("mousedown", () => {
        if (event.button !== 0) return;
        pressTimer = setTimeout(() => {
            callback(elementId)
        }, 800);
    });

    element.addEventListener("mouseup", () => {
        clearTimeout(pressTimer);
    });

    element.addEventListener("mouseleave", () => {
        clearTimeout(pressTimer);
    });

    element.addEventListener("touchstart", () => {
        pressTimer = setTimeout(() => {
            callback(elementId)
        }, 800);
    });

    element.addEventListener("touchend", () => {
        clearTimeout(pressTimer);
    });
}