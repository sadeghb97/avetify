(function () {
    const varName = window.__ENTITY_SEARCHER_BOOT__?.payloadVarName || "__ENTITY_SEARCHER_PAYLOAD__";
    const payload = window[varName];
    if (!payload) return;

    const entities = payload.entities || [];
    const tabs = payload.tabs || [];
    const maxResults = payload.maxResults || 80;
    const defaultTypeId = payload.defaultTypeId || (tabs[0]?.id || "");

    const elTabs = document.getElementById("ent_tabs");
    const elQuery = document.getElementById("ent_query");
    const elResults = document.getElementById("ent_results");
    const elEmpty = document.getElementById("ent_empty");
    const elStatEntity = document.getElementById("ent_stat_entity");
    const elStatCount = document.getElementById("ent_stat_count");

    function normalizeText(s) {
        if (!s) return "";
        try {
            return String(s)
                .normalize("NFD")
                .replace(/\p{Diacritic}/gu, "")
                .toLowerCase()
                .trim();
        } catch (e) {
            return String(s).toLowerCase().trim();
        }
    }

    const model = {};
    for (const e of entities) {
        const type = e.type;
        const items = (e.items || []).map(it => ({
            ...it,
            __type: type,
            __idx: it.searchIndex || ""
        }));
        model[type] = items;
    }

    let activeType = defaultTypeId;
    let timer = null;

    function setActiveType(type) {
        activeType = type;
        for (const btn of elTabs.querySelectorAll(".ent-tab")) {
            btn.classList.toggle("active", btn.dataset.type === type);
        }
        elStatEntity.textContent = type;
        render();
        setTimeout(() => elQuery && elQuery.focus(), 0);
    }

    function renderTabs() {
        elTabs.innerHTML = "";
        for (const t of tabs) {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className = "ent-tab" + (t.id === activeType ? " active" : "");
            btn.dataset.type = t.id;
            btn.textContent = t.label;
            btn.addEventListener("click", () => setActiveType(t.id));
            elTabs.appendChild(btn);
        }
    }

    async function copyText(text) {
        if (!text) return false;
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (e) {
            const ta = document.createElement("textarea");
            ta.value = text;
            ta.setAttribute("readonly", "");
            ta.style.position = "fixed";
            ta.style.left = "-9999px";
            document.body.appendChild(ta);
            ta.select();
            const ok = document.execCommand("copy");
            document.body.removeChild(ta);
            return ok;
        }
    }

    function createCopyButton(copyName) {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "ent-copy-btn";
        btn.title = "Copy name";
        btn.setAttribute("aria-label", "Copy name");
        btn.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>';

        btn.addEventListener("click", async (e) => {
            e.preventDefault();
            e.stopPropagation();
            const ok = await copyText(copyName);
            if (!ok) return;
            btn.classList.add("copied");
            btn.title = "Copied";
            setTimeout(() => {
                btn.classList.remove("copied");
                btn.title = "Copy name";
            }, 1200);
        });

        return btn;
    }

    function pill(text) {
        const s = document.createElement("span");
        s.className = "ent-pill";
        s.textContent = text;
        return s;
    }

    function appendLabels(target, labels) {
        for (const lbl of (labels || [])) {
            if (lbl) target.appendChild(pill(lbl));
        }
    }

    function renderCard(item) {
        const card = document.createElement("div");
        card.className = "ent-card";

        const display = item.display || {};
        const copyName = display.copyName || display.title || "";
        if (copyName) card.appendChild(createCopyButton(copyName));

        const a = document.createElement("a");
        a.className = "ent-card-link";
        a.href = item.link || "#";
        a.target = "_blank";

        const av = document.createElement("div");
        av.className = "ent-avatar";
        const img = document.createElement("img");
        img.loading = "lazy";
        img.alt = "";
        img.src = item.img || "";
        img.onerror = () => { img.style.display = "none"; };
        av.appendChild(img);

        const body = document.createElement("div");
        body.className = "ent-card-body";

        const h = document.createElement("p");
        h.className = "ent-card-title";
        h.textContent = display.title || `#${item.pk}`;

        const sub = document.createElement("p");
        sub.className = "ent-card-sub";
        sub.textContent = display.subtitle || "";

        const meta = document.createElement("div");
        meta.className = "ent-card-meta";
        for (const m of (display.meta || [])) {
            if (m) meta.appendChild(pill(m));
        }

        const labelsRow = document.createElement("div");
        labelsRow.className = "ent-card-labels";
        appendLabels(labelsRow, display.labels);

        body.appendChild(h);
        if (sub.textContent) body.appendChild(sub);

        a.appendChild(av);
        a.appendChild(body);
        if (meta.childNodes.length) a.appendChild(meta);
        card.appendChild(a);
        if (labelsRow.childNodes.length) card.appendChild(labelsRow);
        return card;
    }

    function renderEmpty(msg) {
        elEmpty.style.display = "block";
        elEmpty.textContent = msg;
    }

    function clearEmpty() {
        elEmpty.style.display = "none";
        elEmpty.textContent = "";
    }

    function computeMatches(items, q) {
        if (!q) return items.slice(0, maxResults);
        const nq = normalizeText(q);
        if (!nq) return items.slice(0, maxResults);

        const out = [];
        for (const it of items) {
            if (it.__idx.includes(nq)) out.push(it);
            if (out.length >= maxResults) break;
        }
        return out;
    }

    function render() {
        const q = elQuery.value || "";
        const items = model[activeType] || [];
        const matches = computeMatches(items, q);

        elResults.innerHTML = "";
        elStatCount.textContent = q
            ? `${matches.length} matches (showing max ${maxResults})`
            : `${Math.min(items.length, maxResults)} shown (type to search)`;

        if (!items.length) {
            renderEmpty("No records loaded.");
            return;
        }

        if (q && !matches.length) {
            renderEmpty("No matches. Try fewer characters or a different alias.");
            return;
        }

        clearEmpty();
        for (const it of matches) {
            elResults.appendChild(renderCard(it));
        }
    }

    function scheduleRender() {
        if (timer) clearTimeout(timer);
        timer = setTimeout(render, 30);
    }

    function updateQueryDirection() {
        const q = elQuery.value || "";
        const first = (q.match(/\S/) || [""])[0];
        if (!first) {
            elQuery.removeAttribute("dir");
            return;
        }
        if (/[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF]/.test(first)) {
            elQuery.setAttribute("dir", "rtl");
        } else {
            elQuery.setAttribute("dir", "ltr");
        }
    }

    function focusSearchField(selectAll = true) {
        if (!elQuery) return;
        elQuery.focus();
        if (selectAll) elQuery.select();
    }

    renderTabs();
    setActiveType(activeType);

    document.addEventListener("keydown", (e) => {
        if ((e.ctrlKey || e.metaKey) && (e.key === "f" || e.key === "F")) {
            e.preventDefault();
            focusSearchField(true);
        }
    });

    elQuery.addEventListener("input", () => {
        updateQueryDirection();
        scheduleRender();
    });
    elQuery.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            elQuery.value = "";
            updateQueryDirection();
            scheduleRender();
        }
    });
    elQuery.focus();
})();
