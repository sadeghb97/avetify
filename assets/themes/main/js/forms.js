function avtTablesIntegratedPrimaryTrigger(){
  const primaryTriggers = globalThis.avt_global__table_primary_triggers ?? [];

  const form = document.createElement("form");

  form.method = "POST";
  form.action = location.href;

  for (const trigger of primaryTriggers) {
    if (trigger.confirmMessage && !confirm(trigger.confirmMessage)) {
      return;
    }

    const sourceForm = document.getElementById(trigger.formIdentifier);

    if (trigger.formTriggerElementId) {
      console.log("TRG", trigger.formTriggerElementId);

      const triggerElement = document.getElementById(
        trigger.formTriggerElementId
      );

      triggerElement.value = trigger.triggerIdentifier;
    }

    const event = new Event("submit", {
      bubbles: true,
      cancelable: true
    });

    sourceForm.dispatchEvent(event);

    for (const [key, value] of new FormData(sourceForm)) {
      const input = document.createElement("input");

      input.type = "hidden";
      input.name = key;
      input.value = value;

      form.appendChild(input);
    }
  }

  document.body.appendChild(form);
  form.submit();
}