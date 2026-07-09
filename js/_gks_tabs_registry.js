/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

class gks_tabs_registry {
  constructor() {
    this.channel = new BroadcastChannel('gks_erp_app_tabs_registry');
    this.tabId = crypto.randomUUID();
    this.tabs = new Map();
    this.currentEntity = null;
    this._ready = false;

    this._listen();
    this._register();
    this._pingOthers();

    window.addEventListener("beforeunload", () => this._unregister());
  }

  setEntity(a, b, c) {
    this.currentEntity = {object_rel:a, rec_id:b, ctid:c};

    this.tabs.set(this.tabId, { tabId: this.tabId, ...this._myInfo() });

    this.channel.postMessage({
      type: "UPDATE",
      tabId: this.tabId,
      payload: this._myInfo(),
    });

    this._notify();
  }

  getOpenTabs() {
    return [...this.tabs.values()];
  }

  onChange(fn) {
    this._onChange = fn;
  }

  ready(fn) {
    if (this._ready) {
      fn(this.getOpenTabs());
    } else {
      this._onReady = fn;
    }
  }

  _myInfo() {
    return {
      url: location.href,
      title: document.title,
      ...(this.currentEntity || {}), // ← safe even if null
      timestamp: Date.now(),
    };
  }

  _register() {
    this.tabs.set(this.tabId, { tabId: this.tabId, ...this._myInfo() });
  }

  _unregister() {
    this.channel.postMessage({ type: "CLOSE", tabId: this.tabId });
  }

  _pingOthers() {
    this.channel.postMessage({ type: "PING", tabId: this.tabId });

    setTimeout(() => {
      this._ready = true;
      this._onReady?.(this.getOpenTabs());
      this._notify();
    }, 500);
  }

  _cleanup() {
    const now = Date.now();
    for (const [id, tab] of this.tabs) {
      if (id !== this.tabId && now - tab.timestamp > 5000) {
        this.tabs.delete(id);
      }
    }
    this._notify();
  }

  _listen() {
    this.channel.onmessage = ({ data }) => {
      switch (data.type) {
        case "PING":
          this.channel.postMessage({
            type: "PONG",
            tabId: this.tabId,
            payload: this._myInfo(),
          });
          break;

        case "PONG":
        case "UPDATE":
          this.tabs.set(data.tabId, { tabId: data.tabId, ...data.payload });
          this._notify();
          break;

        case "CLOSE":
          this.tabs.delete(data.tabId);
          this._notify();
          break;
      }
    };
  }

  _notify() {
    this._onChange?.([...this.tabs.values()]);
  }
}
