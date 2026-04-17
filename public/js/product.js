/**
 * product.js
 * Product Master — dynamic attribute rows, AJAX category load, live price preview.
 * No external dependencies (vanilla JS only, no jQuery required).
 */

(function () {
    'use strict';

    /* ── State ─────────────────────────────────────────────────────── */
    let categoryAttributes = window.PM_EXISTING_ATTRIBUTES || [];  // [{id,name,key,field_type,unit,placeholder,preset_values}]
    let rowIndex = 0;   // increments for each new row added

    /* ── DOM refs (assigned after DOMContentLoaded) ─────────────────── */
    let $categorySelect;
    let $btnAddAttribute;
    let $container;
    let $noAttrsMsg;
    let $attributeHint;
    let $basePrice;
    let $taxType;
    let $taxValue;
    let $discountType;
    let $discountValue;
    let $finalPriceDisplay;

    /* ═══════════════════════════════════════════════════════════════════
       INIT
    ═══════════════════════════════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', function () {
        $categorySelect    = document.getElementById('product_category_id');
        $btnAddAttribute   = document.getElementById('btnAddAttribute');
        $container         = document.getElementById('attributeRowsContainer');
        $noAttrsMsg        = document.getElementById('noAttributesMsg');
        $attributeHint     = document.getElementById('attributeHint');
        $basePrice         = document.getElementById('base_price');
        $taxType           = document.getElementById('tax_type');
        $taxValue          = document.getElementById('tax_value');
        $discountType      = document.getElementById('discount_type');
        $discountValue     = document.getElementById('discount_value');
        $finalPriceDisplay = document.getElementById('finalPriceDisplay');

        if (!$categorySelect) return; // not on a product form page

        // ── Determine initial row index from already-rendered rows ──
        const existingRows = $container ? $container.querySelectorAll('.pm-attr-row') : [];
        rowIndex = existingRows.length;

        // ── Populate attribute selects for already-rendered rows ─────
        if (categoryAttributes.length > 0 && existingRows.length > 0) {
            existingRows.forEach(function (row) {
                const sel        = row.querySelector('.pm-attr-select');
                const hiddenId   = row.querySelector('.pm-attr-id-hidden');
                const unitEl     = row.querySelector('.pm-attr-unit-display');
                const datalist   = row.querySelector('datalist');
                const selectedId = hiddenId ? parseInt(hiddenId.value) : null;

                populateAttributeSelect(sel, selectedId);

                if (selectedId) {
                    const attr = categoryAttributes.find(a => a.id === selectedId);
                    if (attr) {
                        setUnitDisplay(unitEl, attr.unit);
                        setDatalistOptions(datalist, attr.preset_values || []);
                    }
                }
            });
        }

        // ── Wire events ──────────────────────────────────────────────
        if ($categorySelect) {
            $categorySelect.addEventListener('change', onCategoryChange);
        }
        if ($btnAddAttribute) {
            $btnAddAttribute.addEventListener('click', onAddRow);
        }
        if ($container) {
            $container.addEventListener('change',  onRowAttributeChange);
            $container.addEventListener('click',   onRowRemove);
        }

        // Price preview wiring
        [$basePrice, $taxType, $taxValue, $discountType, $discountValue].forEach(function (el) {
            if (el) el.addEventListener('input', debounce(updateFinalPrice, 350));
        });

        // Compute on load (edit page shows saved price; recalculate live)
        updateFinalPrice();

        // Refresh visibility of "no attributes" message
        refreshNoAttrsMsg();
    });

    /* ═══════════════════════════════════════════════════════════════════
       CATEGORY CHANGE → load attributes via AJAX
    ═══════════════════════════════════════════════════════════════════ */
    function onCategoryChange() {
        const categoryId = this.value;

        if (!categoryId) {
            categoryAttributes = [];
            clearAllRows();
            setAddButtonState(false);
            setHint('Select a category first.');
            return;
        }

        setHint('<span class="pm-spinner"></span> Loading attributes…');
        setAddButtonState(false);

        const url = window.PM_ROUTES.attributesByCategory + '/' + categoryId;

        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(function (data) {
            categoryAttributes = data.attributes || [];
            clearAllRows();
            setAddButtonState(categoryAttributes.length > 0);
            setHint(
                categoryAttributes.length > 0
                    ? categoryAttributes.length + ' attribute(s) available for this category.'
                    : 'No attributes configured for this category.'
            );
        })
        .catch(function (err) {
            console.error('Failed to load attributes:', err);
            setHint('<span style="color:#e53935">Failed to load attributes. Please try again.</span>');
            setAddButtonState(false);
        });
    }

    /* ═══════════════════════════════════════════════════════════════════
       ADD ROW
    ═══════════════════════════════════════════════════════════════════ */
    function onAddRow() {
        if (!categoryAttributes.length) return;

        const html = buildRowHTML(rowIndex, null, '');
        const tmp  = document.createElement('div');
        tmp.innerHTML = html;
        const newRow = tmp.firstElementChild;
        $container.appendChild(newRow);

        const sel      = newRow.querySelector('.pm-attr-select');
        const datalist = newRow.querySelector('datalist');
        const unitEl   = newRow.querySelector('.pm-attr-unit-display');

        populateAttributeSelect(sel, null);

        // Focus the select for immediate interaction
        setTimeout(function () { sel.focus(); }, 50);

        rowIndex++;
        refreshNoAttrsMsg();
    }

    /* ═══════════════════════════════════════════════════════════════════
       ATTRIBUTE SELECT CHANGE within a row
    ═══════════════════════════════════════════════════════════════════ */
    function onRowAttributeChange(e) {
        const sel = e.target;
        if (!sel.classList.contains('pm-attr-select')) return;

        const row      = sel.closest('.pm-attr-row');
        const hidden   = row.querySelector('.pm-attr-id-hidden');
        const valueIn  = row.querySelector('.pm-attr-value-input');
        const unitEl   = row.querySelector('.pm-attr-unit-display');
        const datalist = row.querySelector('datalist');

        const attrId = parseInt(sel.value);
        if (hidden) hidden.value = attrId || '';

        const attr = categoryAttributes.find(a => a.id === attrId);
        if (attr) {
            setUnitDisplay(unitEl, attr.unit);
            setDatalistOptions(datalist, attr.preset_values || []);
            if (attr.placeholder && valueIn) {
                valueIn.placeholder = attr.placeholder;
            }
        } else {
            setUnitDisplay(unitEl, null);
            setDatalistOptions(datalist, []);
        }
    }

    /* ═══════════════════════════════════════════════════════════════════
       REMOVE ROW
    ═══════════════════════════════════════════════════════════════════ */
    function onRowRemove(e) {
        const btn = e.target.closest('.pm-attr-remove');
        if (!btn) return;
        const row = btn.closest('.pm-attr-row');
        if (row) {
            row.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(10px)';
            setTimeout(function () {
                row.remove();
                reindexRows();
                refreshNoAttrsMsg();
            }, 180);
        }
    }

    /* ═══════════════════════════════════════════════════════════════════
       BUILD ROW HTML
    ═══════════════════════════════════════════════════════════════════ */
    function buildRowHTML(idx, selectedAttrId, value) {
        return `
<div class="pm-attr-row" data-row="${idx}">
    <div class="pm-attr-row__drag">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9"  cy="5"  r="1" fill="currentColor"/>
            <circle cx="15" cy="5"  r="1" fill="currentColor"/>
            <circle cx="9"  cy="12" r="1" fill="currentColor"/>
            <circle cx="15" cy="12" r="1" fill="currentColor"/>
            <circle cx="9"  cy="19" r="1" fill="currentColor"/>
            <circle cx="15" cy="19" r="1" fill="currentColor"/>
        </svg>
    </div>
    <div class="pm-attr-row__select">
        <select name="attributes[${idx}][attribute_id]"
                class="pm-select pm-attr-select"
                data-row="${idx}">
            <option value="">— Select attribute —</option>
        </select>
        <input type="hidden"
               name="attributes[${idx}][attribute_id]"
               class="pm-attr-id-hidden"
               value="${selectedAttrId || ''}">
    </div>
    <div class="pm-attr-row__value">
        <input type="text"
               name="attributes[${idx}][value]"
               class="pm-input pm-attr-value-input"
               value="${escapeHtml(value || '')}"
               placeholder="Enter value…"
               list="preset-${idx}"
               autocomplete="off">
        <datalist id="preset-${idx}"></datalist>
    </div>
    <div class="pm-attr-row__unit pm-attr-unit-display" id="unit-${idx}"></div>
    <button type="button"
            class="pm-icon-btn pm-icon-btn--delete pm-attr-remove"
            data-row="${idx}"
            title="Remove">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6"  y1="6" x2="18" y2="18"/>
        </svg>
    </button>
</div>`;
    }

    /* ═══════════════════════════════════════════════════════════════════
       POPULATE ATTRIBUTE SELECT OPTIONS
    ═══════════════════════════════════════════════════════════════════ */
    function populateAttributeSelect(sel, selectedId) {
        // Preserve existing selected option if it belongs to current category
        sel.innerHTML = '<option value="">— Select attribute —</option>';
        categoryAttributes.forEach(function (attr) {
            const opt   = document.createElement('option');
            opt.value   = attr.id;
            opt.textContent = attr.name + (attr.unit ? ' (' + attr.unit + ')' : '');
            if (selectedId && attr.id === selectedId) opt.selected = true;
            sel.appendChild(opt);
        });
    }

    /* ═══════════════════════════════════════════════════════════════════
       DATALIST + UNIT helpers
    ═══════════════════════════════════════════════════════════════════ */
    function setDatalistOptions(datalist, presets) {
        if (!datalist) return;
        datalist.innerHTML = '';
        (presets || []).forEach(function (val) {
            const opt = document.createElement('option');
            opt.value = val;
            datalist.appendChild(opt);
        });
    }

    function setUnitDisplay(el, unit) {
        if (!el) return;
        el.textContent = unit || '';
    }

    /* ═══════════════════════════════════════════════════════════════════
       REINDEX rows after deletion (keeps name[] arrays sequential)
    ═══════════════════════════════════════════════════════════════════ */
    function reindexRows() {
        const rows = $container.querySelectorAll('.pm-attr-row');
        rows.forEach(function (row, i) {
            row.dataset.row = i;
            const sel     = row.querySelector('.pm-attr-select');
            const hidden  = row.querySelector('.pm-attr-id-hidden');
            const valIn   = row.querySelector('.pm-attr-value-input');
            const unitEl  = row.querySelector('.pm-attr-unit-display');
            const datlist = row.querySelector('datalist');
            const rmBtn   = row.querySelector('.pm-attr-remove');

            if (sel)    { sel.name    = 'attributes[' + i + '][attribute_id]'; sel.dataset.row = i; }
            if (hidden) { hidden.name = 'attributes[' + i + '][attribute_id]'; }
            if (valIn)  { valIn.name  = 'attributes[' + i + '][value]'; }
            if (valIn)  { valIn.setAttribute('list', 'preset-' + i); }
            if (datlist){ datlist.id  = 'preset-' + i; }
            if (unitEl) { unitEl.id   = 'unit-' + i; }
            if (rmBtn)  { rmBtn.dataset.row = i; }
        });
        rowIndex = rows.length;
    }

    /* ═══════════════════════════════════════════════════════════════════
       CLEAR ALL ROWS
    ═══════════════════════════════════════════════════════════════════ */
    function clearAllRows() {
        if ($container) $container.innerHTML = '';
        rowIndex = 0;
        refreshNoAttrsMsg();
    }

    /* ═══════════════════════════════════════════════════════════════════
       VISIBILITY helpers
    ═══════════════════════════════════════════════════════════════════ */
    function refreshNoAttrsMsg() {
        if (!$noAttrsMsg || !$container) return;
        const hasRows = $container.querySelectorAll('.pm-attr-row').length > 0;
        $noAttrsMsg.style.display = hasRows ? 'none' : 'block';
    }

    function setAddButtonState(enabled) {
        if (!$btnAddAttribute) return;
        $btnAddAttribute.disabled = !enabled;
    }

    function setHint(html) {
        if ($attributeHint) $attributeHint.innerHTML = html;
    }

    /* ═══════════════════════════════════════════════════════════════════
       LIVE PRICE PREVIEW (client-side calculation)
    ═══════════════════════════════════════════════════════════════════ */
    function updateFinalPrice() {
        if (!$finalPriceDisplay) return;

        const base     = parseFloat($basePrice     ? $basePrice.value     : 0) || 0;
        const taxT     = $taxType      ? $taxType.value      : 'percentage';
        const taxV     = parseFloat($taxValue      ? $taxValue.value      : 0) || 0;
        const discT    = $discountType ? $discountType.value : 'percentage';
        const discV    = parseFloat($discountValue ? $discountValue.value : 0) || 0;

        // Apply discount
        let discounted;
        if (discT === 'percentage') {
            discounted = base - (base * discV / 100);
        } else {
            discounted = base - discV;
        }
        discounted = Math.max(0, discounted);

        // Apply tax
        let final;
        if (taxT === 'percentage') {
            final = discounted + (discounted * taxV / 100);
        } else {
            final = discounted + taxV;
        }
        final = Math.max(0, final);

        $finalPriceDisplay.textContent = '₹' + final.toFixed(2);

        // Pulse animation
        $finalPriceDisplay.classList.remove('pm-price-pulse');
        void $finalPriceDisplay.offsetWidth; // reflow
        $finalPriceDisplay.classList.add('pm-price-pulse');
    }

    /* ═══════════════════════════════════════════════════════════════════
       UTILITIES
    ═══════════════════════════════════════════════════════════════════ */
    function debounce(fn, delay) {
        let timer;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(fn.bind(this, ...arguments), delay);
        };
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    /* ── Price pulse animation (injected once into <head>) ────────── */
    (function injectPulseCSS() {
        if (document.getElementById('pm-pulse-style')) return;
        const style = document.createElement('style');
        style.id = 'pm-pulse-style';
        style.textContent = `
            @keyframes pmPulse {
                0%   { transform: scale(1); }
                40%  { transform: scale(1.06); color: #e55203; }
                100% { transform: scale(1); }
            }
            .pm-price-pulse { animation: pmPulse 0.3s ease; }
        `;
        document.head.appendChild(style);
    })();

})();
