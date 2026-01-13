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

function acOnItemEntered(fieldKey, recordKey, recordsList, cData, callback){
    const field = document.getElementById(fieldKey)
    const fieldValue = field.value
    const selectedItem = recordsList.find((record) => {
        return record['main_jsdl_name'] === fieldValue
    })

    if(selectedItem != null) callback(field, recordKey, cData, selectedItem)
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

function addLongClickEvent(elementId, longClickCallback, normalClickCallback) {
    const element = document.getElementById(elementId);
    let pressTimer = null;
    let wasLongPress = false;

    const startPress = () => {
        wasLongPress = false;
        pressTimer = setTimeout(() => {
            wasLongPress = true;
            longClickCallback(elementId);
        }, 800);
    };

    const endPress = () => {
        clearTimeout(pressTimer);
        if (!wasLongPress) {
            normalClickCallback(elementId);
        }
    };

    element.addEventListener("mousedown", (event) => {
        if (event.button !== 0) return;
        startPress();
    });

    element.addEventListener("mouseup", endPress);
    element.addEventListener("mouseleave", () => clearTimeout(pressTimer));

    element.addEventListener("touchstart", startPress);
    element.addEventListener("touchend", endPress);
}

function logSelectedRecord(field, childKey, selectedItem){
    console.log("Entered Record", field, childKey, selectedItem)
}

function onSelectCountry(field, recordKey, cData, selectedCountry){
    const dataElement = document.getElementById(recordKey)
    const flagElement = document.getElementById(recordKey + "_flag")
    const linkElement = document.getElementById(recordKey + "_link")
    const countryCode = selectedCountry['alpha2']
    const disableAutoSubmit = 'disable_auto_submit' in cData && cData['disable_auto_submit']

    let countryLink = ""
    if('pre_link' in cData || 'post_link' in cData){
        const preLink = 'pre_link' in cData ? cData['pre_link'] : ""
        const postLink = 'post_link' in cData ? cData['post_link'] : ""
        if(preLink || postLink) {
            countryLink = preLink + countryCode + postLink
        }
    }

    dataElement.value = countryCode
    flagElement.src = selectedCountry['flag']
    flagElement.title = selectedCountry['short_name']
    if(linkElement) linkElement.href = countryLink
    field.value = ""
    if(disableAutoSubmit) field.blur()
}

function updateSelectorSet(selectorKey, records, map, sData){
    const selectedSetVarName = selectorKey + "_selected"
    window[selectedSetVarName].forEach((recordId) => {
        const record = records[map[recordId]]
        if(record) addRecordToSelector(null, selectorKey, sData, record)
    })
}

function addRecordToSelector(acField, selectorKey, cData, selectedRecord){
    const recordId = selectedRecord['main_jsdl_id']
    const recordName = selectedRecord['main_jsdl_name']
    const recordImage = selectedRecord['main_jsdl_avatar']
    const imagesDiv = document.getElementById(selectorKey + "_images")
    const valueElement = document.getElementById(selectorKey)
    const recordElementId = selectorKey + "_item_" + recordId
    const disableAutoSubmit = cData && 'disable_auto_submit' in cData && cData['disable_auto_submit']
    const tinyAvatars = cData && 'tiny_avatars' in cData && cData['tiny_avatars']
    const isReadonly = cData && 'is_readonly' in cData && cData['is_readonly']

    const selectedSetVarName = selectorKey + "_selected"
    window[selectedSetVarName].add(recordId)
    valueElement.value = [...window[selectedSetVarName]].join(',')

    let recordElement = document.getElementById(recordElementId)
    if(!recordElement){
        if(recordImage) {
            recordElement = document.createElement("img")
            recordElement.id = recordElementId
            recordElement.src = recordImage
            recordElement.title = recordName
            recordElement.classList.add("selbox-img")

            if (tinyAvatars) {
                recordElement.style.height = "50px";
                recordElement.style.width = "auto";
            }
        }
        else {
            recordElement = document.createElement("div")
            recordElement.id = recordElementId
            recordElement.innerHTML = "#" + recordName
            recordElement.classList.add("selbox-title")

            if (tinyAvatars) {
                recordElement.style.height = "50px";
                recordElement.style.width = "auto";
            }
        }

        if(!isReadonly) {
            recordElement.onclick = function () {
                if (window[selectedSetVarName].has(recordId)) {
                    removeSelectorItem(selectorKey, recordId)
                }
                else addRecordToSelector(acField, selectorKey, cData, selectedRecord)
            };
        }
        imagesDiv.appendChild(recordElement)
    }
    else {
        recordElement.style.opacity = "1"
        recordElement.style.filter = "none"
    }

    if(acField) {
        acField.value = ""
        if (disableAutoSubmit) acField.blur()
    }
}

function removeSelectorItem(selectorKey, recordId){
    const valueElement = document.getElementById(selectorKey)
    const selectedSetVarName = selectorKey + "_selected"
    window[selectedSetVarName].delete(recordId)
    valueElement.value = [...window[selectedSetVarName]].join(',')

    const imageElementId = selectorKey + "_item_" + recordId
    const imageElement = document.getElementById(imageElementId)
    if(imageElement){
        imageElement.style.opacity = "0.35"
        imageElement.style.filter = "grayscale(100%)"
    }
}

function updateSingleSelector(acField, selectorKey, cData, selectedRecord){
    const valueElement = document.getElementById(selectorKey)
    const imageBox = document.getElementById(selectorKey + "_avatar_box")
    const imageElement = document.getElementById(selectorKey + "_avatar")

    const selectedImageSrc = selectedRecord ? selectedRecord['main_jsdl_avatar'] : ""
    const selectedId = selectedRecord ? selectedRecord['main_jsdl_id'] : ""
    const disableAutoSubmit = cData && 'disable_auto_submit' in cData && cData['disable_auto_submit']

    if(selectedId) {
        imageBox.style.display = "block"
        imageElement.src = selectedImageSrc
        valueElement.value = selectedId
    }

    if(acField) {
        acField.value = ""
        if (disableAutoSubmit) acField.blur()
    }
}