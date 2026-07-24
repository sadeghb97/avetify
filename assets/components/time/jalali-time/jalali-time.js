function getJalaliPlaceholder(hasTime) {
  return hasTime ? '----/--/-- --:--' : '----/--/--';
}

function initJalaliField(baseId, hasTime, initialUnix = 0) {
  const $display = $(document.getElementById(getFieldElementIds(baseId).display));
  if (!$display.length) return null;

  const format = hasTime ? 'YYYY/MM/DD HH:mm' : 'YYYY/MM/DD';
  const placeholder = getJalaliPlaceholder(hasTime);

  const picker = $display.pDatepicker({
    format,
    autoClose: true,
    persianDigit: false,
    initialValue: false,
    timePicker: {
      enabled: hasTime,
      meridiem: false,
      second: { enabled: false }
    },
    onSelect(unixTime) {
      const unix = Math.floor(unixTime / 1000);
      setHiddenUnix(baseId, unix);
      formatUnixOutput(baseId, unix);
    }
  });

  if (initialUnix > 0) {
    $display.val(new persianDate(initialUnix * 1000).format(format));
    setHiddenUnix(baseId, initialUnix);
    formatUnixOutput(baseId, initialUnix);
  } else {
    $display.val(placeholder);
    setHiddenUnix(baseId, 0);
    formatUnixOutput(baseId, 0);
  }

  bindClearButton(baseId, () => {
    $display.val(placeholder);
    setHiddenUnix(baseId, 0);
    formatUnixOutput(baseId, 0);
  });

  return picker;
}