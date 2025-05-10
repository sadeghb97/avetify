function str_rot13(str) {
	return (str + '').replace(/[a-z]/gi, function (s) {
		return String.fromCharCode(s.charCodeAt(0) + (s.toLowerCase() < 'n' ? 13 : -13));
	});
}

function initMenu(){
	window.oncontextmenu = function (e) {
		let fileTriggered = e.srcElement
		if(!fileTriggered) return false

		for(let i=0; 2>i; i++) {
			if (!fileTriggered.id.includes("lister-item"))
				fileTriggered = fileTriggered.parentElement
		}

		if(fileTriggered && fileTriggered.id.includes("lister-item")){
			const contextMenu = document.getElementById("context-menu");
			showMenu(contextMenu);

			triggeredFile = fileTriggered
			return false
		}

		fileTriggered = e.srcElement.parentElement.parentElement
		if(fileTriggered && fileTriggered.id.includes("msec")){
			const contextShift = document.getElementById("context-shift");
			showMenu(contextShift);

			triggeredFile = fileTriggered
			return false
		}

		hideContextMenu()
		return true
	}

	const scope = document.querySelector("body");
	scope.addEventListener("click", () => {
		hideContextMenu()
	});
}

function listerSubmit(moreArgs){
	const maxListerGrids = moreArgs.lists_count

	let ids = []
	for(let i=0; maxListerGrids>i; i++){
		ids[i] = ""
	}

	for(let listIndex = 0; maxListerGrids > listIndex; listIndex++) {
		for (let i = 0; grids[listIndex].children.length > i; i++) {
			if (ids[listIndex]) ids[listIndex] += ","
			ids[listIndex] += grids[listIndex].children[i].id
		}
	}

	let out = ""
	for(let listIndex = 0; maxListerGrids > listIndex; listIndex++) {
		if(listIndex > 0) out += "##"
		out += ids[listIndex]
	}

	document.getElementById("newlist").value = out
	return true;
}

function hideContextMenu (){
	try {
		const contextMenu = document.getElementById("context-menu");
		contextMenu.classList.remove("visible");
	}
	catch (ex){}

	try {
		const contextShift = document.getElementById("context-shift");
		contextShift.classList.remove("visible");
	}
	catch (ex){}
}

function action(arg, menuArgs){
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
		const parentDiv = triggeredFile.parentElement
		const parentDivId = parentDiv.id
		const fullTier = parentDivId.substr(8)
		if(fullTier <= 0) return

		const altListId = "gridDemo" + (fullTier - 1)
		const altListDiv = document.getElementById(altListId)
		parentDiv.removeChild(triggeredFile)
		altListDiv.appendChild(triggeredFile);
	}
	else if(arg === 3) {
		//relegate
		const parentDiv = triggeredFile.parentElement
		const parentDivId = parentDiv.id
		const fullTier = parentDivId.substr(8)
		if(fullTier >= (menuArgs.lists_count - 1)) return

		const altListId = "gridDemo" + (parseInt(fullTier) + 1)
		const altListDiv = document.getElementById(altListId)
		parentDiv.removeChild(triggeredFile)
		altListDiv.insertBefore(triggeredFile, altListDiv.firstChild);
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
		const triggeredImage = triggeredFile.firstElementChild.src
		window.open(triggeredImage, '_blank');
	}
	else if(arg === 6){
		const triggeredImage = triggeredFile.firstElementChild.src
		copyToClipboard(triggeredImage)
	}
	hideContextMenu()
}

function rearrangeRanks(){
	grids.forEach((grid, gridIndex) => {
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

function transfer(tier){
	const grid = document.getElementById("gridDemo" + tier)

	const parentDiv = triggeredFile.parentElement
	parentDiv.removeChild(triggeredFile)
	grid.appendChild(triggeredFile)

	hideContextMenu()
}

function shift(){
	const id = parseInt(triggeredFile.id.split("_")[1])
	console.log(id)
	let lastEmpty = 0

	for(let mval = 0; mval <= id; mval++){
		const curChildren = document.getElementById("gridDemo" + mval).children
		if(curChildren.length == 0) lastEmpty = mval;
	}

	for(let mval = lastEmpty + 1; mval <= id; mval++){
		const curGrid = document.getElementById("gridDemo" + mval)
		const targetGrid = document.getElementById("gridDemo" + (mval - 1))
		const curChildren = curGrid.children
		const targetGridFirstChild = targetGrid.firstChild
		console.log(curGrid, targetGrid, curChildren)

		while(curChildren.length > 0){
			targetGrid.insertBefore(curChildren[0], targetGridFirstChild)
		}
	}
}

function showMenu(menu){
	const { clientX: mouseX, clientY: mouseY } = event;
	menu.style.top = `${mouseY}px`;
	menu.style.left = `${mouseX}px`;

	menu.classList.remove("visible");
	setTimeout(() => {
		menu.classList.add("visible");
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


