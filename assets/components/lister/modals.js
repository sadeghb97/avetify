function setupArrangeModal(modal){
  const container = modal.query(".content-container");
  const pageListers = fetchPageListers();

  for (const lister of pageListers) {
    const listerId = lister.dataset.listId
    const listerTitle = lister.dataset.listTitle
    if(listerId === AVT_ZERO_LIST_ID) continue;

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
    listerOrders.push(AVT_ZERO_LIST_ID)

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
  const options = getListersOptions();

  const firstGroup = document.createElement('div');
  firstGroup.className = 'modal-select-group';

  const firstLabel = document.createElement('label');
  firstLabel.textContent = 'List to Remove';

  const firstSelect = document.createElement('select');
  firstSelect.className = 'modal-select';

  firstSelect.innerHTML = `
  <option value="">Select list...</option>
  ${options
    .filter(item => item.id !== AVT_ZERO_LIST_ID)
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
  const listToDelete = fetchPageListerByDatasetId(listToDeleteId);

  const gridToDelete = fetchGridElementFromLister(listToDelete);
  const delGridChildren = fetchItemsFromGridElement(gridToDelete);

  const destList = fetchPageListerByDatasetId(destListId);
  const destGrid = fetchGridElementFromLister(destList);

  delGridChildren.forEach(item => {
    destGrid.appendChild(item)
  })

  listToDelete.remove();
  rearrangeRanks()
}

function setupBatchTransferModal(modal) {
  const container = modal.query(".content-container");
  const options = getListersOptions();

  // ---------- Source List ----------
  const sourceGroup = document.createElement("div");
  sourceGroup.className = "modal-select-group";

  const sourceLabel = document.createElement("label");
  sourceLabel.textContent = "Source List";

  const sourceSelect = document.createElement("select");
  sourceSelect.className = "modal-select";
  sourceSelect.innerHTML = `
    <option value="">Select list...</option>
    ${options
    .map(item => `<option value="${item.id}">${item.title}</option>`)
    .join("")}
  `;

  sourceGroup.appendChild(sourceLabel);
  sourceGroup.appendChild(sourceSelect);

  // ---------- Destination List ----------
  const destGroup = document.createElement("div");
  destGroup.className = "modal-select-group";
  destGroup.style.display = "none";

  const destLabel = document.createElement("label");
  destLabel.textContent = "Destination List";

  const destSelect = document.createElement("select");
  destSelect.className = "modal-select";

  destGroup.appendChild(destLabel);
  destGroup.appendChild(destSelect);

  // ---------- Range Row ----------
  const rangeRow = document.createElement("div");
  rangeRow.style.display = "none";
  rangeRow.style.gap = "12px";

  // Offset
  const offsetGroup = document.createElement("div");
  offsetGroup.className = "modal-select-group";
  offsetGroup.style.flex = "1";

  const offsetLabel = document.createElement("label");
  offsetLabel.textContent = "Offset";

  const offsetInput = document.createElement("input");
  offsetInput.type = "number";
  offsetInput.min = "1";
  offsetInput.step = "1";
  offsetInput.value = "1";
  offsetInput.className = "modal-input";

  offsetGroup.appendChild(offsetLabel);
  offsetGroup.appendChild(offsetInput);

  // Length
  const lengthGroup = document.createElement("div");
  lengthGroup.className = "modal-select-group";
  lengthGroup.style.flex = "1";

  const lengthLabel = document.createElement("label");
  lengthLabel.textContent = "Length";

  const lengthInput = document.createElement("input");
  lengthInput.type = "number";
  lengthInput.min = "1";
  lengthInput.step = "1";
  lengthInput.value = "10";
  lengthInput.className = "modal-input";

  lengthGroup.appendChild(lengthLabel);
  lengthGroup.appendChild(lengthInput);

  rangeRow.appendChild(offsetGroup);
  rangeRow.appendChild(lengthGroup);

  // ---------- Events ----------
  sourceSelect.addEventListener("change", () => {
    const selectedId = sourceSelect.value;

    destSelect.innerHTML = "";

    if (!selectedId) {
      destGroup.style.display = "none";
      rangeRow.style.display = "none";
      return;
    }

    options
      .filter(item => item.id !== selectedId)
      .forEach(item => {
        destSelect.insertAdjacentHTML(
          "beforeend",
          `<option value="${item.id}">${item.title}</option>`
        );
      });

    destGroup.style.display = "block";
    rangeRow.style.display = "flex";
  });

  container.appendChild(sourceGroup);
  container.appendChild(rangeRow);
  container.appendChild(destGroup);

  modal.query(".apply").onclick = () => {
    const sourceListId = sourceSelect.value;
    const destListId = destSelect.value;
    const length = parseInt(lengthInput.value, 10);
    let offset = parseInt(offsetInput.value, 10);
    if(offset <= 0) offset = 1;
    offset--;

    if (
      !sourceListId ||
      !destListId ||
      Number.isNaN(offset) ||
      Number.isNaN(length) ||
      offset < 0 ||
      length <= 0
    ) {
      return;
    }

    moveItemsRange(sourceListId, destListId, offset, length);

    modal.close();
  };

  modal.query(".cancel").onclick = () => {
    modal.close();
  };
}

function moveItemsRange(sourceListId, destListId, offset, length){
  const sourceList = fetchPageListerByDatasetId(sourceListId);
  const destList = fetchPageListerByDatasetId(destListId);
  if(!sourceList || !destList) return;

  const sourceGrid = fetchGridElementFromLister(sourceList);
  const destGrid = fetchGridElementFromLister(destList);
  const sourceItems = fetchItemsFromGridElement(sourceGrid);

  for (let i=offset; sourceItems.length > i && offset+length > i; i++){
    const remItem = sourceGrid.removeChild(sourceItems[i]);
    destGrid.appendChild(remItem);
  }

  rearrangeRanks();
}