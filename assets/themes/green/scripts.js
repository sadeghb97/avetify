function fastCopy(e){
    let element = e.target
    window.getSelection().selectAllChildren(element)
    document.execCommand('copy');
}