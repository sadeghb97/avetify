function defaultInitEditor(editorId, contents, dir){
    const editor = document.getElementById(editorId);
    const quill = new Quill(editor, {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                [{ 'color': [] }, { 'background': [] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'align': [] }, { 'direction': 'rtl' }],
                ['link'],
                ['clean']
            ]
        }
    });

    const Delta = Quill.import('delta');
    quill.clipboard.addMatcher(Node.ELEMENT_NODE, function(node, delta) {
        const plaintext = node.innerText || node.textContent;
        return new Delta().insert(plaintext);
    });

    quill.root.innerHTML = contents;

    const length = quill.getLength();
    const align = dir !== "ltr" ? "right" : "left";
    quill.formatLine(0, length, {
        direction: dir,
        align: align
    });

    return quill;
}

function addChildAfter(wholeMainKey, childId, blocksData) {
    const containerId = wholeMainKey + '_main_container';
    const wrapperId = childId + '_wrapper';
    const container = document.getElementById(containerId);
    const childWrapper = document.getElementById(wrapperId);
    if (!container || !childWrapper || childWrapper.parentElement !== container) return;

    const childIndex = blocksData.findIndex(item => item.id === childId);
    if(childIndex < 0) return;

    const newChildId = wholeMainKey + "_editor_" + Date.now()
    const cloneWrapper = childWrapper.cloneNode(true);
    initClonedBlock(cloneWrapper, wholeMainKey, childId, newChildId, blocksData)

    const oldToolbar = cloneWrapper.querySelector('.ql-toolbar');
    if (oldToolbar) oldToolbar.remove();

    if (childWrapper.nextSibling) {
        container.insertBefore(cloneWrapper, childWrapper.nextSibling);
    } else {
        container.appendChild(cloneWrapper);
    }

    const direction = blocksData[childIndex].quill.getFormat().direction || 'ltr';
    const defContents = ""
    const newQuill = defaultInitEditor(newChildId, defContents, direction)
    blocksData.splice(childIndex + 1, 0, {
        id: newChildId,
        quill: newQuill
    });
}

function initClonedBlock(cloneWrapper, wholeMainKey, childId, newChildId, blocksData){
    cloneWrapper.id = newChildId + '_wrapper'
    const cloneEditor = cloneWrapper.querySelector('.editor_block')
    if(cloneEditor) cloneEditor.id = newChildId

    const createButton = cloneWrapper.querySelector("#" + childId + "_create")
    const moveUpButton = cloneWrapper.querySelector("#" + childId + "_moveup")
    const moveDownButton = cloneWrapper.querySelector("#" + childId + "_movedown")
    const plainButton = cloneWrapper.querySelector("#" + childId + "_plain")
    const typeInput = cloneWrapper.querySelector("#" + childId + "_type")

    createButton.id = newChildId + "_create"
    moveUpButton.id = newChildId + "_moveup"
    moveDownButton.id = newChildId + "_movedown"
    plainButton.id = newChildId + "_plain"
    typeInput.id = newChildId + "_type"

    createButton.onclick = function() {
        addChildAfter(wholeMainKey, newChildId, blocksData)
    }

    moveUpButton.onclick = function() {
        moveElementUp(wholeMainKey, newChildId, blocksData)
    };

    moveDownButton.onclick = function() {
        moveElementDown(wholeMainKey, newChildId, blocksData)
    };

    plainButton.onclick = function() {
        setPlainWrapper(wholeMainKey, newChildId, blocksData)
    };
}

function moveElementInContainer(wholeMainKey, childId, blocksData, moveUp){
    const containerId = wholeMainKey + '_main_container';
    const wrapperId = childId + '_wrapper';
    const container = document.getElementById(containerId);
    const childWrapper = document.getElementById(wrapperId);
    if (!container || !childWrapper || childWrapper.parentElement !== container) return;

    const childIndex = blocksData.findIndex(item => item.id === childId);
    if(childIndex < 0) return;

    if(moveUp){
        const prev = childWrapper.previousElementSibling;
        console.log("MoveUp", prev)
        if (prev) {
            console.log("MovingUp", prev)
            swapArrayElements(blocksData, childIndex, childIndex - 1)
            container.insertBefore(childWrapper, prev);
        }
    }
    else {
        const next = childWrapper.nextElementSibling;
        if (next) {
            swapArrayElements(blocksData, childIndex, childIndex + 1)
            container.insertBefore(next, childWrapper);
        }
    }
}

function moveElementUp(wholeMainKey, childId, blocksData) {
    moveElementInContainer(wholeMainKey, childId, blocksData, true)
}

function moveElementDown(wholeMainKey, childId, blocksData) {
    moveElementInContainer(wholeMainKey, childId, blocksData, false)
}

function setPlainWrapper(wholeMainKey, childId, blocksData){
    const wrapperInput = document.getElementById(childId + "_type")
    wrapperInput.value = "Plain"
}

function swapArrayElements(arr, index1, index2) {
    if (
        index1 < 0 || index1 >= arr.length ||
        index2 < 0 || index2 >= arr.length
    ) return;

    [arr[index1], arr[index2]] = [arr[index2], arr[index1]];
}

function refreshCodingFieldDataElement(wholeMainKey, blocksData){
    const resultFields = []
    const dataElement = document.getElementById(wholeMainKey)

    blocksData.forEach((blockData) => {
        const quill = blockData.quill
        const wrapperField = document.getElementById(blockData.id + "_type")
        if(!isQuillContentEmpty(quill)){
            const contents = quill.root.innerHTML
            const direction = quill.getFormat().direction || 'ltr';
            const wrapperValue = wrapperField.value
            console.log(wrapperField, blockData.id + "_type")

            resultFields.push({
                "contents": contents,
                "wrapper": wrapperValue,
                "dir": direction
            })
        }
    })

    dataElement.value = JSON.stringify(resultFields)
}

function isQuillContentEmpty(quill) {
    const html = quill.root.innerHTML;
    const text = quill.getText().trim();

    if (text.length === 0) return true;

    const stripped = html
        .replace(/<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, '')
        .replace(/&nbsp;/gi, '')
        .replace(/\s+/g, '')
        .trim();

    return stripped.length === 0;
}