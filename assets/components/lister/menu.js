function initMenu(menuId, jsArgs){
  if(!menuId) menuId = 'context-menu';
  window.oncontextmenu = function (e) {
    let fileTriggered = e.srcElement
    if(!fileTriggered) return false

    for(let i=0; 2>i; i++) {
      if (!fileTriggered.id.includes("lister-item"))
        fileTriggered = fileTriggered.parentElement
    }

    if(fileTriggered && fileTriggered.id.includes("lister-item")){
      showMenu(menuId, jsArgs);
      triggeredFile = fileTriggered
      return false
    }

    fileTriggered = e.srcElement.parentElement.parentElement
    if(fileTriggered && fileTriggered.id.includes("msec")){
      showMenu("context-shift");

      triggeredFile = fileTriggered
      return false
    }

    hideContextMenu(menuId)
    return true
  }

  const scope = document.querySelector("body");
  scope.addEventListener("click", () => {
    hideContextMenu(menuId)
  });
}

function hideContextMenu (menuId){
	try {
		const contextMenu = document.getElementById(menuId);
		if(contextMenu) contextMenu.classList.remove("visible");
	}
	catch (ex){}

	try {
		const contextShift = document.getElementById("context-shift");
		if(contextShift) contextShift.classList.remove("visible");
	}
	catch (ex){}
}

function action(menuId, arg, menuArgs){
	if(arg === 0) {
		const parentDiv = triggeredFile.parentElement
		parentDiv.insertBefore(triggeredFile, parentDiv.firstChild);
	}
	else if(arg === 1) {
		const parentDiv = triggeredFile.parentElement
		parentDiv.removeChild(triggeredFile)
		parentDiv.appendChild(triggeredFile)
	}
	else if(arg === 2) {
		//promote
		const curGrid = triggeredFile.parentElement
    const curLister = curGrid.parentElement
    const targetLister = curLister.previousElementSibling
    if(!targetLister) return;
    const targetGrid = fetchGridElementFromLister(targetLister)
    console.log("TL", targetLister)
    console.log("TG", targetGrid)

    curGrid.removeChild(triggeredFile)
    targetGrid.appendChild(triggeredFile);
	}
	else if(arg === 3) {
		//relegate
    const curGrid = triggeredFile.parentElement
    const curLister = curGrid.parentElement
    const targetLister = curLister.nextElementSibling
    if(!targetLister) return;
    const targetGrid = fetchGridElementFromLister(targetLister)

    curGrid.removeChild(triggeredFile)
    targetGrid.insertBefore(triggeredFile, targetGrid.firstChild);
	}
	else if(arg === 4){
		const parentDiv = triggeredFile.parentElement
		const childCount = parentDiv.children.length
		let currentRank = 0
		for(let i=0; parentDiv.children.length>i; i++){
			if(parentDiv.children[i] === triggeredFile) currentRank = i + 1
		}

		const number = prompt("Enter new rank: ", currentRank.toString())
		if(number && !isNaN(number)){
			const pureNumber = parseInt(number)
			if(pureNumber <= 1) action(0, menuArgs)
			else if(pureNumber >= childCount) action(1, menuArgs)
			else {
				parentDiv.removeChild(triggeredFile)
				parentDiv.insertBefore(triggeredFile, parentDiv.children[pureNumber - 1]);
			}
		}
	}
	else if(arg === 5){
		const triggeredImage = findClosestChildrenByTag(triggeredFile, "img");
		if(triggeredImage != null) window.open(triggeredImage.src, '_blank');
	}
	else if(arg === 6){
		const triggeredImage = findClosestChildrenByTag(triggeredFile, "img");
		if(triggeredImage != null) copyToClipboard(triggeredImage.src)
	}
	hideContextMenu(menuId)
}

function transfer(menuId, listerId){
  const targetLister = fetchPageListerByDatasetId(listerId);
  const targetGrid = fetchGridElementFromLister(targetLister);

  const parentDiv = triggeredFile.parentElement
  parentDiv.removeChild(triggeredFile)
  targetGrid.appendChild(triggeredFile)

  hideContextMenu(menuId)
}

function showMenu(menuId, jsArgs){
  const menu = document.getElementById(menuId);
  if(!menu) return;

  const screenWidth = window.innerWidth;
  const screenHeight = window.innerHeight;
  const { clientX: mouseX, clientY: mouseY } = event;

  const menuWidth = (jsArgs && "menu_width" in jsArgs) ? jsArgs['menu_width'] : 0;
  const menuHeight = (jsArgs && "menu_height" in jsArgs) ? jsArgs['menu_height'] : 0;

  const finalTop = (mouseY + menuHeight) > screenHeight ? screenHeight - menuHeight : mouseY;
  const finalLeft = (mouseX + menuWidth) > screenWidth ? screenWidth - menuWidth : mouseX;

  console.log(mouseX, menuWidth, screenWidth, finalLeft);
  console.log(mouseY, menuHeight, screenHeight, finalTop);

  menu.style.top = `${finalTop}px`;
  menu.style.left = `${finalLeft}px`;

  const listerOptions = getListersOptions();
  const menuDirectsContainer = menu.querySelector("#menu_directs");
  const menuDirectsSeparator = menu.querySelector("#menu_directs_separator");

  menuDirectsSeparator.style.display = listerOptions.length > 0 ? "block" : "none";
  menuDirectsContainer.innerHTML = '';
  for (let i= 0; listerOptions.length > i; i++){
    const isLastItem = (i === (listerOptions.length - 1));
    const isOddItem = (i % 2) === 0;
    const fullOption = isLastItem && isOddItem;
    const itemWidth = fullOption ? 230 : 115;
    const itemHeight = 20;

    const optionDiv = document.createElement("div");
    optionDiv.classList.add("item");
    optionDiv.style.height = itemHeight + "px";
    optionDiv.style.width = itemWidth + "px";

    optionDiv.innerHTML = listerOptions[i].title;
    optionDiv.addEventListener('click', function () {
      transfer(menuId, listerOptions[i].id)
    });
    menuDirectsContainer.appendChild(optionDiv);
  }

  menu.classList.remove("visible");
  setTimeout(() => {
    menu.classList.add("visible");

    setTimeout(() => {
      const menuRealSize = menu.getBoundingClientRect();
      if(menuRealSize.width > 0 && menuRealSize.height > 0) {
        if (jsArgs && "menu_width" in jsArgs) jsArgs['menu_width'] = menuRealSize.width;
        if (jsArgs && "menu_height" in jsArgs) jsArgs['menu_height'] = menuRealSize.height;
        console.log("RealSize", jsArgs['menu_width'], jsArgs['menu_height'])
      }
    }, 220)
  });
}