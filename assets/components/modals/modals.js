class Modal {
  constructor(manager, overlay, root) {
    this.manager = manager;
    this.overlay = overlay;
    this.root = root;
  }

  query(selector) {
    return this.root.querySelector(selector);
  }

  queryAll(selector) {
    return this.root.querySelectorAll(selector);
  }

  on(event, callback) {
    this.root.addEventListener(event, callback);
    return this;
  }

  close() {
    this.manager.close(this);
  }
}

class ModalManager {
  static stack = [];

  static show(templateId, callback = null) {
    const template = document.getElementById(templateId);

    if (!(template instanceof HTMLTemplateElement))
      throw new Error(`Template "${templateId}" not found.`);

    const overlay = document.createElement("div");
    overlay.className = "modal-overlay";

    const root = template.content.firstElementChild.cloneNode(true);

    overlay.append(root);
    document.body.append(overlay);

    const modal = new Modal(this, overlay, root);

    overlay.onclick = e => {
      if (e.target === overlay)
        modal.close();
    };

    this.stack.push(modal);

    if (callback)
      callback.call(modal);

    return modal;
  }

  static close(modal) {
    const index = this.stack.indexOf(modal);

    if (index === -1)
      return;

    this.stack.splice(index, 1);
    modal.overlay.remove();
  }

  static closeTop() {
    const modal = this.stack.at(-1);
    if (modal) modal.close();
  }
}