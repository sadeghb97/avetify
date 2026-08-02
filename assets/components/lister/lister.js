function str_rot13(str) {
	return (str + '').replace(/[a-z]/gi, function (s) {
		return String.fromCharCode(s.charCodeAt(0) + (s.toLowerCase() < 'n' ? 13 : -13));
	});
}

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

function fetchPageListers(){
  const pageListers = [];
  const listersContainer = document.getElementById('lister_form');

  for (const child of listersContainer.children) {
    if (child.classList.contains('js__avt-lister')) {
      pageListers.push(child);
    }
  }

  return pageListers;
}

function fetchGridElementFromLister(lister){
  return lister.querySelector('.js__avt-grid');
}

function fetchSpanTitleElementFromLister(lister){
  return lister.querySelector('.js__avt-lister-title');
}

function fetchListersLegacyGrids(){
  const pageListers = fetchPageListers();
  const constLegacyGrids = [];

  for (const lister of pageListers) {
    const grid = fetchGridElementFromLister(lister);
    constLegacyGrids.push(grid)
  }

  return constLegacyGrids;
}

function initListers(){
  const legacyGrids = fetchListersLegacyGrids();
  for (const grid of legacyGrids) {
    new Sortable(grid, {
      animation: 150,
      group: 'shared', // set both lists to same group
      ghostClass: 'blue-background-class'
    });
  }
}

function listerSubmit(moreArgs){
  const legacyGrids = fetchListersLegacyGrids()
	let outLists = []

  for (let gIndex = 0; legacyGrids.length > gIndex; gIndex++) {
    outLists[gIndex] = {
      index: gIndex,
      id: legacyGrids[gIndex].parentElement.dataset.listId,
      title: legacyGrids[gIndex].parentElement.dataset.listTitle,
      ids: []
    }

    for (let i = 0; legacyGrids[gIndex].children.length > i; i++) {
      outLists[gIndex].ids.push(legacyGrids[gIndex].children[i].id)
    }
  }

	document.getElementById("newlist").value = JSON.stringify(outLists);
	return true;
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

function rearrangeRanks(){
  const legacyGrids = fetchListersLegacyGrids()
  legacyGrids.forEach((grid, gridIndex) => {
		if(grid == null) return
		for(let i=0; grid.children.length>i; i++){
			const childSquareId = grid.children[i].id
			const pos = childSquareId.lastIndexOf("_")
			const itemId = childSquareId.substr(pos + 1)
			const rankElement = document.getElementById("lister-rank_" + itemId)
			if(rankElement != null){
				rankElement.innerText = (i + 1).toString()
			}
		}
	})
}

function addNewList(){
  const enteredTitle = prompt('Enter new list title:');
  if(!enteredTitle) return;

  const newListIndex = jsArgs.listers_safe_cursor;
  jsArgs.listers_safe_cursor++;

  const newCategoryId = crypto.randomUUID();
  const newCategoryGridId = "grid_" + newCategoryId;
  const listerForm = document.getElementById("lister_form");
  const zeroList = listerForm.lastElementChild;
  const clonedList = zeroList.cloneNode(true);

  clonedList.id = 'msec_' + newListIndex;
  clonedList.dataset.listTitle = enteredTitle;
  clonedList.dataset.listId = newCategoryId;

  const clSpanTitle = fetchSpanTitleElementFromLister(clonedList);
  clSpanTitle.id = "msec_title_" + newListIndex;
  clSpanTitle.innerHTML = enteredTitle;

  const innerGrid = fetchGridElementFromLister(clonedList);
  innerGrid.id = newCategoryGridId;
  innerGrid.innerHTML = '';

  listerForm.insertBefore(clonedList, zeroList);
  new Sortable(innerGrid, {
    animation: 150,
    group: 'shared',
    ghostClass: 'blue-background-class'
  });
}

function transfer(menuId, tier){
	const grid = document.getElementById("gridDemo" + tier)

	const parentDiv = triggeredFile.parentElement
	parentDiv.removeChild(triggeredFile)
	grid.appendChild(triggeredFile)

	hideContextMenu(menuId)
}

function showMenu(menuId, jsArgs){
	const menu = document.getElementById(menuId)
	if(!menu) return;

	const screenWidth = window.innerWidth;
	const screenHeight = window.innerHeight;
	const { clientX: mouseX, clientY: mouseY } = event;

	const menuWidth = (jsArgs && "menu_width" in jsArgs) ? jsArgs['menu_width'] : 0;
	const menuHeight = (jsArgs && "menu_height" in jsArgs) ? jsArgs['menu_height'] : 0;

	const finalTop = (mouseY + menuHeight) > screenHeight ? screenHeight - menuHeight : mouseY
	const finalLeft = (mouseX + menuWidth) > screenWidth ? screenWidth - menuWidth : mouseX

	console.log(mouseX, menuWidth, screenWidth, finalLeft)
	console.log(mouseY, menuHeight, screenHeight, finalTop)

	menu.style.top = `${finalTop}px`;
	menu.style.left = `${finalLeft}px`;

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

function setupArrangeModal(modal){
  const container = modal.query(".content-container");
  const pageListers = fetchPageListers();

  for (const lister of pageListers) {
    const listerId = lister.dataset.listId
    const listerTitle = lister.dataset.listTitle
    if(listerId === 'avt_zero_list') continue;

    const listerDiv = document.createElement("div");
    listerDiv.style.cursor = "grab";
    listerDiv.innerText = listerTitle;
    listerDiv.dataset.itemTitle = listerTitle;
    listerDiv.dataset.itemId = listerId;
    container.appendChild(listerDiv);
  }

  new Sortable(container, {
    animation: 150,
    group: 'shared', // set both lists to same group
    ghostClass: 'blue-background-class'
  });

  modal.query(".apply").onclick = () => {
    const listerOrders = [];
    for (const child of container.children) {
      listerOrders.push(child.dataset.itemId);
    }
    listerOrders.push("avt_zero_list")

    rearrangeLists(listerOrders)
    modal.close();
  };


  modal.query(".cancel").onclick = () => {
    modal.close();
  };
}

function rearrangeLists(orders) {
  const listersContainer = document.getElementById('lister_form');

  const itemMap = new Map(
    Array.from(listersContainer.children).map(item => [
      String(item.dataset.listId),
      item
    ])
  );

  const fragment = document.createDocumentFragment();

  orders.forEach(id => {
    const item = itemMap.get(String(id));

    if (item) {
      fragment.appendChild(item);
    }
  });

  listersContainer.appendChild(fragment);
}

function setupManageModal(modal){
  const container = modal.query(".content-container");
  const pageListers = fetchPageListers();

  const options = [];

  for (const lister of pageListers) {
    const listerId = lister.dataset.listId
    const listerTitle = lister.dataset.listTitle
    options.push({id: listerId, title: listerTitle})
  }

  const firstGroup = document.createElement('div');
  firstGroup.className = 'modal-select-group';

  const firstLabel = document.createElement('label');
  firstLabel.textContent = 'List to Remove';

  const firstSelect = document.createElement('select');
  firstSelect.className = 'modal-select';

  firstSelect.innerHTML = `
  <option value="">Select list...</option>
  ${options
    .filter(item => item.id !== 'avt_zero_list')
    .map(item => `
      <option value="${item.id}">${item.title}</option>
    `)
    .join('')}
`;

  firstGroup.appendChild(firstLabel);
  firstGroup.appendChild(firstSelect);


  const secondGroup = document.createElement('div');
  secondGroup.className = 'modal-select-group';
  secondGroup.style.display = 'none';

  const secondLabel = document.createElement('label');
  secondLabel.textContent = 'Items Will Be Moved To';

  const secondSelect = document.createElement('select');
  secondSelect.className = 'modal-select';

  secondGroup.appendChild(secondLabel);
  secondGroup.appendChild(secondSelect);


  firstSelect.addEventListener('change', () => {
    const selectedId = firstSelect.value;

    secondSelect.innerHTML = '';

    if (!selectedId) {
      secondGroup.style.display = 'none';
      return;
    }

    options
      .filter(item => item.id !== selectedId)
      .forEach(item => {
        secondSelect.insertAdjacentHTML(
          'beforeend',
          `<option value="${item.id}">${item.title}</option>`
        );
      });

    secondGroup.style.display = 'block';
  });

  container.appendChild(firstGroup);
  container.appendChild(secondGroup);


  modal.query(".apply").onclick = () => {
    const listToDeleteId = firstSelect.value;
    const destListId = secondSelect.value;
    if(!listToDeleteId || !destListId) return;

    deleteList(listToDeleteId, destListId);
    modal.close();
  };

  modal.query(".cancel").onclick = () => {
    modal.close();
  };
}

function deleteList(listToDeleteId, destListId){
  console.log("Delete List", listToDeleteId, destListId)
}

function resetLists(){
  if(confirm("All listing will be reset! are you sure?")){
    const form = document.getElementById("lister_form");
    const submitTypeElement = document.getElementById("submit_type");
    submitTypeElement.value = "reset"
    form.submit()
  }
}

function navigateBetweenLists(offset) {
  const points = document.querySelectorAll('.js__avt-listers-section');

  const middle = window.innerHeight / 2;
  let current = 0;
  let minDistance = Infinity;

  points.forEach((point, index) => {
    const distance = Math.abs(point.getBoundingClientRect().top - middle);

    if (distance < minDistance) {
      minDistance = distance;
      current = index;
    }
  });

  const next = current + offset;

  if (next < 0 || next >= points.length)
    return;

  points[next].scrollIntoView({
    behavior: 'smooth',
    block: 'start'
  });
}

function addVirtualGallery(){
	const galCountElement = document.getElementById("galleries_count");
	const vfDataElement = document.getElementById("virtual_folders");
	const menuDirects = document.getElementById("menu_directs");
	const galleryName = prompt("Please enter a value:")

	if(galleryName){
		const newCategoryIndex = galCountElement.value
		const newCategoryBox = document.getElementById("msec_" + newCategoryIndex);
		const newCategoryTitle = document.getElementById("msec_title_" + newCategoryIndex);
		galCountElement.value = (parseInt(galCountElement.value) + 1).toString();
		newCategoryBox.style.display = 'block';
		newCategoryTitle.innerText = galleryName
		vfDataElement.value = vfDataElement.value + (vfDataElement.value ? "," : "") + (newCategoryIndex + ":" + galleryName)
		menuDirects.innerHTML += ('<div class="item" style="width: 105px;" onclick="transfer('
			+ newCategoryIndex + ');">' + galleryName + '</div>');
	}
}

function resetGalleryConfigs(){
	if(confirm("All virtual galleries will be removed.\nare you sure?")){
		const form = document.getElementById("lister_form");
		const submitTypeElement = document.getElementById("submit_type");
		submitTypeElement.value = "reset"
		form.submit()
	}
}

function updateGalleryConfigs(moreArgs){
	const form = document.getElementById("lister_form");
	const submitTypeElement = document.getElementById("submit_type");
	submitTypeElement.value = "normal"
	if(listerSubmit(moreArgs)) form.submit()
}

function submitGalleries(moreArgs){
	if(confirm("Images permanently moves.\nare you sure?")){
		const form = document.getElementById("lister_form");
		const submitTypeElement = document.getElementById("submit_type");
		submitTypeElement.value = "finish"
		if(listerSubmit(moreArgs)) form.submit()
	}
}

function renameGalleries(moreArgs){
	if(confirm("Images permanently will rename.\nare you sure?")){
		const form = document.getElementById("lister_form");
		const submitTypeElement = document.getElementById("submit_type");
		submitTypeElement.value = "rename"
		if(listerSubmit(moreArgs)) form.submit()
	}
}


