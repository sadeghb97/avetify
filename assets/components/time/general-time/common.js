function getFieldElementIds(baseId) {
  return {
    hidden: baseId,
    display: `${baseId}_display`,
    unixOutput: `${baseId}_unix`
  };
}

function setHiddenUnix(baseId, unix) {
  const ids = getFieldElementIds(baseId);
  const hiddenElement = document.getElementById(ids.hidden);
  if (hiddenElement) {
    hiddenElement.value = unix > 0 ? unix : 0;
  }
}

function formatUnixOutput(baseId, unix) {
  const ids = getFieldElementIds(baseId);
  const outputElement = document.getElementById(ids.unixOutput);
  if (outputElement) {
    outputElement.innerText = 'Unix time: ' + (unix > 0 ? unix : 0);
  }
}

function bindClearButton(baseId, onClearCallback) {
  const button = document.querySelector(`.avt-timepicker__clear-button[data-target="${baseId}"]`);
  if (button) {
    button.addEventListener('click', function() {
      onClearCallback();
    });
  }
}

function toGregorianValue(unix, hasTime = false) {
  if (unix <= 0) return '';
  const d = new Date(unix * 1000);
  const yyyy = d.getFullYear();
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const dd = String(d.getDate()).padStart(2, '0');

  if (hasTime) {
    const hh = String(d.getHours()).padStart(2, '0');
    const mi = String(d.getMinutes()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}T${hh}:${mi}`;
  }
  return `${yyyy}-${mm}-${dd}`;
}

function parseGregorianValue(value, hasTime = false) {
  if (!value) return 0;
  const dateString = hasTime ? value : `${value}T00:00:00`;
  const unix = Math.floor(new Date(dateString).getTime() / 1000);
  return Number.isFinite(unix) ? unix : 0;
}

function syncGregorianField(baseId, hasTime) {
  const ids = getFieldElementIds(baseId);
  const display = document.getElementById(ids.display);
  const unix = display ? parseGregorianValue(display.value, hasTime) : 0;

  setHiddenUnix(baseId, unix);
  formatUnixOutput(baseId, unix);
}

function initGregorianField(baseId, hasTime, initialUnix = 0) {
  const ids = getFieldElementIds(baseId);
  const display = document.getElementById(ids.display);

  if (!display) return;

  if (initialUnix > 0) {
    display.value = toGregorianValue(initialUnix, hasTime);
  } else {
    display.value = '';
  }
  syncGregorianField(baseId, hasTime);

  display.addEventListener('change', function() {
    syncGregorianField(baseId, hasTime);
  });

  display.addEventListener('input', function() {
    syncGregorianField(baseId, hasTime);
  });

  bindClearButton(baseId, function() {
    display.value = '';
    syncGregorianField(baseId, hasTime);
  });
}