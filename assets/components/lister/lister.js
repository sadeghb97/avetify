function str_rot13(str) {
	return (str + '').replace(/[a-z]/gi, function (s) {
		return String.fromCharCode(s.charCodeAt(0) + (s.toLowerCase() < 'n' ? 13 : -13));
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

function fetchPageListerByDatasetId(listerId){
  return document.querySelector(
    `[data-list-id="${listerId}"]`
  );
}

function fetchGridElementFromLister(lister){
  return lister.querySelector('.js__avt-grid');
}

function fetchItemsFromGridElement(grid){
  return grid.querySelectorAll(':scope > .js__avt-list-item');
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

function getListersOptions(){
  const pageListers = fetchPageListers();
  const options = [];

  for (const lister of pageListers) {
    const listerId = lister.dataset.listId
    const listerTitle = lister.dataset.listTitle
    options.push({id: listerId, title: listerTitle})
  }

  return options;
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