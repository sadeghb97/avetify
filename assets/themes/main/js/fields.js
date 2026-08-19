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
        const record = records[map[recordId.toLowerCase()]]
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

function rawSelectorUpdateHidden(boxId, hiddenId) {
    const box = document.getElementById(boxId);
    const hidden = document.getElementById(hiddenId);

    const values = Array.from(box.querySelectorAll('.raw-selector-chip'))
        .map(el => el.dataset.value);

    hidden.value = values.join(',');
}

function rawSelectorAdd(box, input, hiddenId, text) {
    if (!text.trim()) return;

    const chip = document.createElement('span');
    chip.className = 'raw-selector-chip';
    chip.dataset.value = text;
    chip.innerHTML = text + '<button type="button">×</button>';

    chip.querySelector('button').onclick = () => {
        chip.remove();
        rawSelectorUpdateHidden(box.id, hiddenId);
    };

    box.insertBefore(chip, input);
    rawSelectorUpdateHidden(box.id, hiddenId);
}

function rawSelectorInit(boxId, inputId, hiddenId, rawString) {
    const box = document.getElementById(boxId);
    const input = document.getElementById(inputId);

    function load(raw) {
        if (!raw) return;
        raw.split(',').forEach(v => {
            rawSelectorAdd(box, input, hiddenId, v);
        });
    }

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            rawSelectorAdd(box, input, hiddenId, input.value);
            input.value = '';
        }
    });

    box.addEventListener('click', () => input.focus());

    load(rawString);
    rawSelectorUpdateHidden(boxId, hiddenId);
}

function safeGetJSVar(varName) {
    if (!varName) return null;
    if (typeof window[varName] !== 'undefined') return window[varName];
    try {
        return eval(varName);
    } catch (e) {
        return null;
    }
}

function updateTeamArrangeField(target, recordsList, recordsIdsMap) {
    let textarea = null;
    if (typeof target === 'string') {
        textarea = document.getElementById(target);
    } else if (target && target.nodeType) {
        textarea = target;
    }
    if (!textarea) return;

    const container = textarea.closest('.team-arrange-container') || textarea.parentElement;
    if (!container) return;

    const errorEl = container.querySelector('.team-arrange-error');
    const schematic = container.querySelector('.team-arrange-schematic');
    if (!errorEl || !schematic) return;

    if (typeof recordsList === 'string') recordsList = safeGetJSVar(recordsList);
    if (typeof recordsIdsMap === 'string') recordsIdsMap = safeGetJSVar(recordsIdsMap);

    const val = textarea.value.trim();

    if (!val) {
        errorEl.style.display = "none";
        errorEl.textContent = "";
        schematic.innerHTML = "";
        return;
    }

    const parts = val.split('##').map(p => p.trim()).filter(p => p.length > 0);
    if (parts.length === 0) {
        errorEl.style.display = "none";
        errorEl.textContent = "";
        schematic.innerHTML = "";
        return;
    }

    let isValid = true;
    const parsedTeams = [];

    for (let i = 0; i < parts.length; i++) {
        const part = parts[i];
        const colonIndex = part.indexOf(':');
        if (colonIndex === -1) {
            isValid = false;
            break;
        }
        const color = part.substring(0, colonIndex).trim();
        const idsString = part.substring(colonIndex + 1).trim();
        if (!color) {
            isValid = false;
            break;
        }
        const ids = idsString ? idsString.split(',').map(id => id.trim()).filter(id => id.length > 0) : [];
        parsedTeams.push({ color, ids });
    }

    if (!isValid) {
        errorEl.style.display = "block";
        errorEl.style.color = "#dc3545";
        errorEl.style.fontSize = "11px";
        errorEl.style.fontWeight = "bold";
        errorEl.style.marginTop = "3px";
        errorEl.textContent = "Invalid format";
        schematic.innerHTML = "";
        return;
    }

    errorEl.style.display = "none";
    errorEl.textContent = "";
    schematic.innerHTML = "";

    parsedTeams.forEach(team => {
        const teamBox = document.createElement("div");
        teamBox.className = "team-arrange-box";
        teamBox.style.border = "1px solid " + team.color;
        teamBox.style.borderRadius = "6px";
        teamBox.style.padding = "4px 8px";
        teamBox.style.backgroundColor = "rgba(0,0,0,0.015)";
        teamBox.style.width = "100%";
        teamBox.style.boxSizing = "border-box";
        teamBox.style.display = "flex";
        teamBox.style.flexDirection = "row";
        teamBox.style.alignItems = "center";
        teamBox.style.gap = "8px";

        const header = document.createElement("div");
        header.style.fontWeight = "bold";
        header.style.fontSize = "11px";
        header.style.color = team.color;
        header.style.display = "flex";
        header.style.alignItems = "center";
        header.style.gap = "4px";
        header.style.minWidth = "60px";

        const dot = document.createElement("span");
        dot.style.display = "inline-block";
        dot.style.width = "6px";
        dot.style.height = "6px";
        dot.style.borderRadius = "50%";
        dot.style.backgroundColor = team.color;

        const titleSpan = document.createElement("span");
        titleSpan.textContent = team.color;

        header.appendChild(dot);
        header.appendChild(titleSpan);
        teamBox.appendChild(header);

        const membersDiv = document.createElement("div");
        membersDiv.style.display = "flex";
        membersDiv.style.flexWrap = "wrap";
        membersDiv.style.gap = "4px";
        membersDiv.style.alignItems = "center";

        team.ids.forEach(id => {
            const lowerId = String(id).toLowerCase();
            const recIndex = (recordsIdsMap && typeof recordsIdsMap[lowerId] !== 'undefined') ? recordsIdsMap[lowerId] : undefined;
            if (recIndex !== undefined && recordsList && recordsList[recIndex]) {
                const rec = recordsList[recIndex];
                const avatarUrl = rec['main_jsdl_avatar'];
                const name = rec['main_jsdl_name'] || id;

                if (avatarUrl) {
                    const img = document.createElement("img");
                    img.src = avatarUrl;
                    img.alt = name;
                    img.title = name + " (" + id + ")";
                    img.style.width = "40px";
                    img.style.height = "auto";
                    img.style.borderRadius = "4px";
                    img.style.objectFit = "cover";
                    img.style.border = "1px solid #eee";
                    membersDiv.appendChild(img);
                } else {
                    const textCard = document.createElement("div");
                    textCard.title = name + " (" + id + ")";
                    textCard.textContent = name;
                    textCard.style.width = "40px";
                    textCard.style.padding = "2px 4px";
                    textCard.style.fontSize = "9px";
                    textCard.style.textAlign = "center";
                    textCard.style.backgroundColor = "#f5f5f5";
                    textCard.style.border = "1px solid #ddd";
                    textCard.style.borderRadius = "4px";
                    textCard.style.wordBreak = "break-word";
                    membersDiv.appendChild(textCard);
                }
            } else {
                const fallbackCard = document.createElement("div");
                fallbackCard.title = id;
                fallbackCard.textContent = id;
                fallbackCard.style.width = "40px";
                fallbackCard.style.padding = "2px 4px";
                fallbackCard.style.fontSize = "9px";
                fallbackCard.style.textAlign = "center";
                fallbackCard.style.backgroundColor = "#fff";
                fallbackCard.style.border = "1px dashed " + team.color;
                fallbackCard.style.borderRadius = "4px";
                fallbackCard.style.wordBreak = "break-word";
                membersDiv.appendChild(fallbackCard);
            }
        });

        teamBox.appendChild(membersDiv);
        schematic.appendChild(teamBox);
    });
}