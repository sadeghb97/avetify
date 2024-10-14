function isSelect(selArray, id){
    for(var i=0; ind>i; i++){
        if(selArray[i]===id){
            return true;
        }
    }
    return false;
}

function enterBabe(e, selArray, saKey, txtKey, selKey, hidKey, boobKey, babesListKey){
    var inpName=document.getElementById(txtKey).value;
    
    if(inpName!="" && (e===null || e.keyCode==13)){
        for(var k=0; len>k; k++){
            if(inpName.toUpperCase()===babes[k].toUpperCase() && !isSelect(selArray, ids[k])){
                selected=document.getElementById(selKey);

                const hidQ = "'" + hidKey + "'";
                const boobQ = "'" + boobKey + "'";
                const listQ = "'" + babesListKey + "'";
                const onSelectProcess = ('<img src="'+imgs[k]+'" id="'+ids[k]+'" class="selimg" title="'+babes[k]+'" onclick="delBabe(event, ' + saKey + ', ' + hidQ + ', ' + boobQ + ', ' + listQ + ');">');
                selected.innerHTML += onSelectProcess;
                document.getElementById(boobKey+ids[k]).remove();
                document.getElementById(txtKey).value="";
                selArray.push(ids[k]);
                document.getElementById(hidKey).value=selArray.toString();
                //alert(document.getElementById(hidKey).value);
            }
        }
        
        if(!(e===null) && e.keyCode==13){
            e.preventDefault();
            return false;
        }
    }
}

function addBabe(selArray, saKey, txtKey, selKey, hidKey, boobKey, babesListKey){
    enterBabe(null, selArray, saKey, txtKey, selKey, hidKey, boobKey, babesListKey);
    document.getElementById(txtKey).focus();
}

function delBabe(e, selArray, hidKey, boobKey, babesListKey){
    //console.log(e, selArray, hidKey, boobKey, babesListKey);
    var paramId=e.target.id;
    e.target.remove();
    
    var name;
    var id;
    var k;
    for(j=0; ids.length>j; j++){
        if(ids[j]===paramId){
            k=j;
            name=babes[j];
            id= boobKey +ids[j];
        }
    }

    var targetSelIndex;
    for(j=0; selArray.length>j; j++){
        if(selArray[j] == paramId) targetSelIndex = j;
    }
    
    document.getElementById(babesListKey).innerHTML+=('<option id="'+id+'">'+name+'</option>');
    selArray.splice(targetSelIndex, 1);
    document.getElementById(hidKey).value=selArray.toString();
}

function enterTag(tagsDivId, e){
    var inputElement = document.getElementById(tagsDivId+"_input");
    var tagsDiv = document.getElementById(tagsDivId+"_div");

    if(e==null || e.keyCode==13){
        var found = false;
        tagsDiv.childNodes.forEach(function(child){
            if(child.innerHTML == ("#" + inputElement.value)) found = true;
        });

        if(!found && inputElement.value){
            var spanNode = document.createElement("SPAN");
            var textnode = document.createTextNode("#" + inputElement.value);
            spanNode.appendChild(textnode);
            spanNode.style="display: block; color: black;";
            spanNode.id = tagsDivId + "_item_" + inputElement.value;
            tagsDiv.appendChild(spanNode);

            spanNode.onclick = function() { deleteTag(tagsDivId, spanNode.id); }
            inputElement.value = "";
            inputElement.focus();
            updateTagsSingleValue(tagsDivId);
        }

        if(e!=null && e.keyCode==13){
            e.preventDefault();
            return false;
        }
    }
}

function addTag(tagsDivId){
    enterTag(tagsDivId, null);
}

function deleteTag(tagsDivId, tagId){
    var targetTagElement = document.getElementById(tagId);
    if(targetTagElement.style.color != "brown"){
        targetTagElement.style.color = "brown";
    }
    else targetTagElement.style.color = "black";
    updateTagsSingleValue(tagsDivId);
}

function updateTagsSingleValue(tagsDivId){
    var tagsDiv = document.getElementById(tagsDivId+"_div");
    var hiddenSingleElement = document.getElementById(tagsDivId+"_hidden_single");

    var resStr = "";
    tagsDiv.childNodes.forEach(function(child){
        if(child.style.color != "brown"){
            if(resStr) resStr += ",";
            resStr += child.innerHTML.substring(1);
        }
    });
    hiddenSingleElement.value = resStr;
}

function addedImage(e){
    console.log(e)
}

function addGalImgValidateForm(){
    if(document.getElementById("nm").value==""){
        alert("Please Enter a name for Gallery");
        return false;
    }

    if(!(document.getElementById("img1")===null) 
            && document.getElementById("img1").value==""
            && document.getElementById("webimgs").value==""){
        alert("Please Select at least 1 Image!");
        return false;  
    }
}

function selDeleteImage(e){
    var k=-1;
    for(var i=0; dels.length>i; i++){
        if(e.target.id===dels[i]){
            k=i;
            break;
        }
    }
    
    if(k>=0){
        e.target.className="galimg";
        dels[k]=dels[dels.length-1];
        dels.length=--galInd;
    }
    else{
        e.target.className="delgalimg";
        dels[galInd++]=e.target.id;
    }
    //alert(dels.toString());
    document.getElementById("hdels").value=dels.toString();
    //alert(document.getElementById("hdels").value);
    e.preventDefault();
    return false;
}

function getRandomIntInRange(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function getNewNoCalledIndex(){
    var out=getRandomIntInRange(0,called.length-1);
    while(called[out]) out=getRandomIntInRange(0,called.length-1);
    called[out]=true;
    selIds[ind++]=ids[out];
    return out;
}

function getNewOption(accepts){
    var out=Array();
    var outId;
    var ind;
    correct=false;
    
    while(!correct){
        ind=getRandomIntInRange(0,optIds.length-1);
        outId=optIds[ind];
        correct=true;
        
        for(var i=0; accepts.length>i; i++){
            if(outId===accepts[i]['id']){
                //console.log('A-Rejected: '+outId);
                correct=false;
                break;
            }
        }

        if(correct){
            for(var i=0; selIds.length>i; i++){
                if(outId===selIds[i]){
                    //console.log('S-Rejected: '+outId);
                    correct=false;
                    break;
                }
            }
        }
    }
    out['ind']=ind;
    out['id']=outId;
    return out;
}

function getOptionsIds(){
    var out=Array();
    for(var i=0; 7>i; i++){
        out[i]=getNewOption(out);
        //console.log(out[i]['id'])
        //console.log('------------');
    }
    return out;
}

function newQuest(){
    currentBabeInd=getNewNoCalledIndex();
    currentBabeId=ids[currentBabeInd];
    document.getElementById("questpic").src=imgs[currentBabeInd];
    
    opts=getOptionsIds();
    correctOptId=getRandomIntInRange(1,8);
    
    var sp;
    sp=document.getElementById("opt"+(correctOptId));
    sp.childNodes[1].nodeValue=babes[currentBabeInd];
    
    
    for(var i=0; 7>i; i++){
        var inc;
        if(correctOptId-1>i) inc=1;
        else inc=2;
        
        sp=document.getElementById("opt"+(i+inc));
        sp.childNodes[1].nodeValue=optNames[opts[i]['ind']];
    }
    
    document.getElementById(("opt1")).childNodes[0].checked=true;
    //document.getElementById(("points")).innerHTML=correctOptId;
}

function enterAnswer(){
    op=parseFloat(progImg.style.opacity);
    if(document.getElementById("opt"+correctOptId).firstChild.checked){
        points++;
        if(op<1){
            if((op+zaribOp)<=1) op+=zaribOp;
            else op=1;
            progImg.style.opacity=op;
        }
        else{
            progImg.style.filter='none';
        }
    }
    else{
        breasts--;
    }
    
    pointsSpan.innerHTML="Points: "+points;
    boobsSpan.innerHTML="Boobs: "+breasts;
    newQuest();
}

function adminSure(e,verId){
    if(document.getElementById(verId).value!="NiceWoman"){
        alert("Type 'NiceWoman' To Verify!");
        e.preventDefault();
        return false;
    }
    var pr=confirm("Are You Sure?");
    if(pr===false){
        e.preventDefault();
        return false;
    }
    
    return true;
}

function adminSure(e){
    adminSure(e, "verify");
}

function rangeChanged(formId, name, id){
    var showSpan = document.getElementById(id+'_val');
    var range = document.getElementById(id);
    var form = document.getElementById(formId);
    var hide = document.getElementById(id+'_hide');
    if(hide==null){
        hide = document.createElement('INPUT');
        hide.setAttribute('id', id+'_hide');
        hide.setAttribute('name', name);
        hide.setAttribute('type', 'hidden');
        form.appendChild(hide);
    }
    hide.value=range.value;
    showSpan.innerHTML = range.value;
}

function rangeDivLoad(formId, name, id, value){
    alert("dd");
    var form = document.getElementById(formId);
    var hide = document.createElement('INPUT');
    hide.setAttribute('id', id+'_hide');
    hide.setAttribute('name', name);
    hide.setAttribute('type', 'hidden');
    form.appendChild(hide);
}

function textInputGetChanged(formId, id, name){
    var form = document.getElementById(formId);
    var input = document.getElementById(id);
    var hide = document.getElementById(id+'_hide');
    if((input==null || input.value.length==0)){
        if(hide!=null) form.removeChild(hide);
    }
    else{
        if(hide==null){
            hide = document.createElement('INPUT');
            hide.setAttribute('id', id+'_hide');
            hide.setAttribute('name', name);
            hide.setAttribute('type', 'hidden');
            form.appendChild(hide);
        }
        hide.value=input.value;
    }
}

function cropBabe(){
    const avatar = document.getElementById('avatar');
    const formImage = document.getElementById('editlady');
    const inpX1 = document.getElementById('x1');
    const inpY1 = document.getElementById('y1');
    const inpX2 = document.getElementById('x2');
    const inpY2 = document.getElementById('y2');
    const cropper = new Cropper(avatar, {
        aspectRatio: 5 / 7,
        crop(event) {
            inpX1.value = event.detail.x;
            inpY1.value = event.detail.y;
            inpX2.value = event.detail.x + event.detail.width;
            inpY2.value = event.detail.y + event.detail.height;
        },
    });

    document.onkeydown = function(evt) {
        evt = evt || window.event;
        if (evt.keyCode == 27) {
            cropper.destroy()
            document.onkeydown = function (){}
        }
        else if (evt.keyCode == 13) {
            formImage.submit()
        }
    };
}


