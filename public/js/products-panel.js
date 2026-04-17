/**
 * products-panel.js
 * =========================================================
 * Self-contained IIFE. Registers all functions on window.*
 * so onclick= attributes work even inside partials where
 * DOMContentLoaded has already fired.
 * =========================================================
 */
(function (PP) {
    'use strict';

    /* ── Config (injected from Blade via window.PP_CONFIG) ───────── */
    var cfg = window.PP_CONFIG || {};
    var LEAD_ID   = cfg.leadId   || 0;
    var API_BASE  = cfg.apiBase  || '/api/v1';
    var CSRF      = cfg.csrf     || '';

    /* ── Local state ─────────────────────────────────────────────── */
    var ppState = {
        products    : [],   // catalogue fetched from GET /api/v1/products
        selected    : {},   // { product_id: { ...product, remarks, qty, disc } }
        deals       : [],   // fetched deals (accordion)
        summary     : {},
        activePayProdId : null,   // which lead_product is open in payment modal
    };

    /* ─────────────────────────────────────────────────────────────
       UTILITY
    ───────────────────────────────────────────────────────────── */
    function fmt(n) {
        return '₹' + parseFloat(n || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2, maximumFractionDigits: 2,
        });
    }

    function el(id)   { return document.getElementById(id); }
    function qs(sel)  { return document.querySelector(sel); }
    function qsa(sel) { return document.querySelectorAll(sel); }

    /* ── Toast ───────────────────────────────────────────────────── */
    function toast(msg, type) {
        type = type || 'success';
        var wrap = el('pp-toast-wrap');
        if (!wrap) return;
        var t = document.createElement('div');
        t.className = 'pp-toast pp-toast--' + type;
        t.innerHTML = (type === 'success' ? '✓ ' : '✕ ') + msg;
        wrap.appendChild(t);
        setTimeout(function () { t.classList.add('pp-toast--out'); }, 2800);
        setTimeout(function () { t.remove(); }, 3200);
    }

    /* ── Spinner helpers ─────────────────────────────────────────── */
    function showLoader(id) {
        var e = el(id); if (e) { e.style.display = 'flex'; }
    }
    function hideLoader(id) {
        var e = el(id); if (e) { e.style.display = 'none'; }
    }

    /* ── API fetch wrapper ───────────────────────────────────────── */
    function api(method, path, body) {
        var opts = {
            method  : method,
            headers : {
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : CSRF,
            },
        };
        if (body) opts.body = JSON.stringify(body);
        return fetch(API_BASE + path, opts).then(function (res) {
            return res.json().then(function (data) {
                if (!res.ok) throw data;
                return data;
            });
        });
    }

    /* ─────────────────────────────────────────────────────────────
       MODAL HELPERS
    ───────────────────────────────────────────────────────────── */
    var MODALS = ['pp-modal-add-product', 'pp-modal-payment', 'pp-modal-history'];

    function ppShow(id) {
        var e = el(id);
        if (e) { e.classList.add('pp-show'); document.body.style.overflow = 'hidden'; }
    }

    PP.ppHideModal = function (id) {
        var e = el(id);
        if (e) { e.classList.remove('pp-show'); document.body.style.overflow = ''; }
    };

    /* Close on backdrop */
    MODALS.forEach(function (id) {
        document.addEventListener('click', function (e) {
            var modal = el(id);
            if (modal && e.target === modal) PP.ppHideModal(id);
        });
    });

    /* Close on Escape */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') MODALS.forEach(PP.ppHideModal);
    });

    /* ─────────────────────────────────────────────────────────────
       ADD PRODUCT MODAL
    ───────────────────────────────────────────────────────────── */

    /** Open modal and fetch catalogue if needed */
    PP.ppShowAddProduct = function () {
        resetAddModal();
        ppShow('pp-modal-add-product');
        if (ppState.products.length === 0) {
            fetchProductCatalogue();
        } else {
            renderProductMultiSelect();
        }
    };

    function resetAddModal() {
        var dealInp = el('pp-deal-name');
        if (dealInp) dealInp.value = '';
        ppState.selected = {};
        var multiSel = el('pp-product-multi-select');
        if (multiSel) {
            Array.from(multiSel.options).forEach(function (o) { o.selected = false; });
        }
        renderSelectedTable();
    }

    function fetchProductCatalogue() {
        showLoader('pp-product-loading');
        api('GET', '/products').then(function (res) {
            ppState.products = res.data || [];
            renderProductMultiSelect();
        }).catch(function () {
            toast('Failed to load products', 'error');
        }).finally(function () {
            hideLoader('pp-product-loading');
        });
    }

    function renderProductMultiSelect() {
        var sel = el('pp-product-multi-select');
        if (!sel) return;
        sel.innerHTML = '';
        ppState.products.forEach(function (p) {
            var opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = (p.category ? p.category + ' | ' : '') + p.name + ' — ' + fmt(p.price);
            opt.dataset.product = JSON.stringify(p);
            sel.appendChild(opt);
        });
    }

    /** Called when multi-select changes */
    PP.ppOnProductSelect = function () {
        var sel = el('pp-product-multi-select');
        if (!sel) return;

        // Add newly selected
        Array.from(sel.selectedOptions).forEach(function (opt) {
            var pid = parseInt(opt.value);
            if (!ppState.selected[pid]) {
                var p = JSON.parse(opt.dataset.product);
                ppState.selected[pid] = {
                    id         : p.id,
                    name       : p.name,
                    description: p.description || '',
                    price      : p.price,
                    qty        : 1,
                    disc       : 0,
                    remarks    : '',
                };
            }
        });

        // Remove deselected
        var selIds = Array.from(sel.selectedOptions).map(function (o) { return parseInt(o.value); });
        Object.keys(ppState.selected).forEach(function (pid) {
            if (!selIds.includes(parseInt(pid))) delete ppState.selected[pid];
        });

        renderSelectedTable();
    };

    function renderSelectedTable() {
        var wrap = el('pp-selected-products-wrap');
        var tbody = el('pp-selected-tbody');
        if (!wrap || !tbody) return;

        var ids = Object.keys(ppState.selected);
        wrap.style.display = ids.length > 0 ? 'block' : 'none';
        if (ids.length === 0) { tbody.innerHTML = ''; return; }

        tbody.innerHTML = ids.map(function (pid) {
            var p = ppState.selected[pid];
            var total = p.price * p.qty * (1 - p.disc / 100);
            return '<tr data-pid="' + pid + '">' +
                '<td class="pp-td-name"><strong>' + escHtml(p.name) + '</strong>' +
                    '<div class="pp-td-desc">' + escHtml(p.description) + '</div></td>' +
                '<td class="pp-td-price">' + fmt(p.price) + '</td>' +
                '<td class="pp-td-qty">' +
                    '<input type="number" class="ppf-inp ni pp-qty-inp" value="' + p.qty + '" ' +
                    'min="1" data-pid="' + pid + '" onchange="PP.ppUpdateRow(this,\'qty\')">' +
                '</td>' +
                '<td class="pp-td-disc">' +
                    '<input type="number" class="ppf-inp ni pp-disc-inp" value="' + p.disc + '" ' +
                    'min="0" max="100" data-pid="' + pid + '" onchange="PP.ppUpdateRow(this,\'disc\')">' +
                '</td>' +
                '<td class="pp-td-total"><strong>' + fmt(total) + '</strong></td>' +
                '<td class="pp-td-remarks">' +
                    '<input type="text" class="ppf-inp ni" placeholder="Remarks…" ' +
                    'value="' + escHtml(p.remarks) + '" data-pid="' + pid + '" ' +
                    'onchange="PP.ppUpdateRow(this,\'remarks\')">' +
                '</td>' +
                '<td><button type="button" class="pp-remove-row" onclick="PP.ppRemoveSelected(' + pid + ')" title="Remove">' +
                    '<svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
                '</button></td>' +
            '</tr>';
        }).join('');
    }

    PP.ppUpdateRow = function (inp, field) {
        var pid = inp.dataset.pid;
        if (!ppState.selected[pid]) return;
        ppState.selected[pid][field] = field === 'remarks' ? inp.value : parseFloat(inp.value) || 0;
        // Re-render just the total cell
        var row   = inp.closest('tr');
        var totalEl = row ? row.querySelector('.pp-td-total strong') : null;
        if (totalEl) {
            var p     = ppState.selected[pid];
            var total = p.price * p.qty * (1 - p.disc / 100);
            totalEl.textContent = fmt(total);
        }
    };

    PP.ppRemoveSelected = function (pid) {
        delete ppState.selected[pid];
        var sel = el('pp-product-multi-select');
        if (sel) {
            Array.from(sel.options).forEach(function (o) {
                if (parseInt(o.value) === parseInt(pid)) o.selected = false;
            });
        }
        renderSelectedTable();
    };

    /** Submit the Add Product form */
    PP.ppSubmitDeal = function () {
        var dealName = (el('pp-deal-name') || {}).value || '';
        console.log(dealName);
        if (!dealName.trim()) { toast('Please enter a Deal Name.', 'error'); return; }

        var products = Object.values(ppState.selected);
        if (products.length === 0) { toast('Select at least one product.', 'error'); return; }

        var btnEl = el('pp-submit-deal-btn');
        if (btnEl) { btnEl.disabled = true; btnEl.textContent = 'Saving…'; }

        api('POST', '/lead-products', {
            lead_id   : LEAD_ID,
            deal_name : dealName.trim(),
            products  : products.map(function (p) {
                return {
                    product_id       : p.id,
                    unit_price       : p.price,
                    quantity         : p.qty,
                    discount_percent : p.disc,
                    remarks          : p.remarks,
                };
            }),
        })
        .then(function () {
            PP.ppHideModal('pp-modal-add-product');
            toast('Deal "' + dealName + '" created!');
            loadDeals();
        })
        .catch(function (err) {
            var msg = (err.errors && Object.values(err.errors)[0]) || err.message || 'Something went wrong.';
            toast(msg, 'error');
        })
        .finally(function () {
            if (btnEl) { btnEl.disabled = false; btnEl.textContent = 'Create Deal'; }
        });
    };

    /* ─────────────────────────────────────────────────────────────
       DEAL ACCORDION — LOAD & RENDER
    ───────────────────────────────────────────────────────────── */
    function loadDeals() {
        showLoader('pp-deals-loading');
        var container = el('pp-deals-container');
        if (container) container.style.opacity = '0.5';

        api('GET', '/lead-products/' + LEAD_ID)
        .then(function (res) {
            ppState.deals   = res.deals   || [];
            ppState.summary = res.summary || {};
            renderSummaryBar();
            renderDeals();
        })
        .catch(function () {
            toast('Failed to refresh deals.', 'error');
        })
        .finally(function () {
            hideLoader('pp-deals-loading');
            var c = el('pp-deals-container');
            if (c) c.style.opacity = '1';
        });
    }

    function renderSummaryBar() {
        var s = ppState.summary;
        setInner('pp-sum-total',   fmt(s.total_value));
        setInner('pp-sum-paid',    fmt(s.total_paid));
        setInner('pp-sum-pending', fmt(s.total_pending));
        setInner('pp-sum-count',   s.product_count || 0);
        setInner('pp-sum-converted', (s.converted || 0) + ' of ' + (s.product_count || 0));
    }

    function renderDeals() {
        var container = el('pp-deals-container');
        if (!container) return;

        if (!ppState.deals.length) {
            container.innerHTML = renderEmptyState();
            return;
        }

        container.innerHTML = ppState.deals.map(function (deal, di) {
            return renderDealAccordion(deal, di);
        }).join('');

        // Expand first deal by default
        var firstBody = container.querySelector('.pp-deal-body');
        if (firstBody) firstBody.style.display = 'block';
        var firstChev = container.querySelector('.pp-deal-chevron');
        if (firstChev) firstChev.style.transform = 'rotate(180deg)';
    }

    function renderDealAccordion(deal, di) {
        var statusCfg = STATUS_CONFIG[deal.status] || STATUS_CONFIG['new'];
        var progress  = deal.total_value > 0
            ? Math.min(100, Math.round((deal.total_paid / deal.total_value) * 100))
            : 0;
        var prgColor  = progress >= 100 ? '#16a34a' : (progress > 0 ? '#fe5f04' : '#e1dee3');

        return '<div class="pp-deal-accordion" data-deal="' + escAttr(deal.deal_name) + '">' +

            // ── Header
            '<div class="pp-deal-header" onclick="PP.ppToggleDeal(this)">' +
                '<span class="pp-deal-chevron">▼</span>' +
                '<div class="pp-deal-icon">🤝</div>' +
                '<div class="pp-deal-info">' +
                    '<div class="pp-deal-name">' + escHtml(deal.deal_name) + '</div>' +
                    '<div class="pp-deal-meta">' + deal.products.length + ' product(s) · ' + fmt(deal.total_value) + '</div>' +
                '</div>' +
                '<div class="pp-deal-right">' +
                    // Status dropdown
                    '<div class="pp-status-select-wrap">' +
                        '<select class="pp-status-select" ' +
                            'style="background:' + statusCfg.bg + ';color:' + statusCfg.text + ';border-color:' + statusCfg.border + '" ' +
                            'data-lead="' + LEAD_ID + '" data-deal="' + escAttr(deal.deal_name) + '" ' +
                            'onchange="PP.ppUpdateDealStatus(this)" ' +
                            'onclick="event.stopPropagation()">' +
                            Object.keys(STATUS_CONFIG).map(function (sk) {
                                var sc = STATUS_CONFIG[sk];
                                return '<option value="' + sk + '" ' + (deal.status === sk ? 'selected' : '') + '>' +
                                    sc.icon + ' ' + sk.charAt(0).toUpperCase() + sk.slice(1) + '</option>';
                            }).join('') +
                        '</select>' +
                        '<svg class="pp-status-caret" style="color:' + statusCfg.text + '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>' +
                    '</div>' +
                    // Amounts pill
                    '<div class="pp-deal-amounts">' +
                        '<span class="pp-pill pp-pill-green">' + fmt(deal.total_paid) + ' paid</span>' +
                        (deal.total_pending > 0
                            ? '<span class="pp-pill pp-pill-red">' + fmt(deal.total_pending) + ' due</span>'
                            : '<span class="pp-pill pp-pill-green">Settled ✓</span>') +
                    '</div>' +
                '</div>' +
            '</div>' +

            // ── Progress
            '<div class="pp-deal-progress" style="padding:0 18px 0">' +
                '<div class="pp-progress-bar-outer" style="margin-bottom:4px">' +
                    '<div class="pp-progress-bar-inner" style="width:' + progress + '%;background:' + prgColor + '"></div>' +
                '</div>' +
            '</div>' +

            // ── Body (collapsible)
            '<div class="pp-deal-body" style="display:none">' +
                deal.products.map(function (p) {
                    return renderProductCard(p);
                }).join('') +
            '</div>' +

        '</div>';
    }

    function renderProductCard(p) {

        var progress  = p.total > 0 ? Math.min(100, Math.round((p.paid / p.total) * 100)) : 0;
        var prgColor  = progress >= 100 ? '#16a34a' : (progress > 0 ? '#fe5f04' : '#e1dee3');
        var pending   = p.total - p.paid;
        var pendingClr = pending > 0 ? '#dc2626' : '#16a34a';

        return '<div class="pp-prod-card pp-prod-card--sub" id="pp-prod-' + p.id + '">' +
            '<div class="pp-prod-inner">' +
                '<div class="pp-prod-name-row">' +
                    '<span class="pp-prod-name">' + escHtml(p.name) + '</span>' +
                '</div>' +
                '<div class="pp-amounts-row">' +
                    '<div class="pp-amt-item"><div class="pp-amt-label">Total</div>' +
                        '<div class="pp-amt-value">' + fmt(p.total) + '</div></div>' +
                    '<div class="pp-amt-item"><div class="pp-amt-label" style="color:#16a34a">Paid</div>' +
                        '<div class="pp-amt-value" style="color:#16a34a">' + fmt(p.paid) + '</div></div>' +
                    '<div class="pp-amt-item"><div class="pp-amt-label" style="color:' + pendingClr + '">Pending</div>' +
                        '<div class="pp-amt-value" style="color:' + pendingClr + '">' + fmt(pending) + '</div></div>' +
                    '<div class="pp-amt-item"><div class="pp-amt-label">Progress</div>' +
                        '<div class="pp-amt-value" style="color:' + prgColor + '">' + progress + '%</div></div>' +
                '</div>' +
                '<div class="pp-progress-wrap">' +
                    '<div class="pp-progress-bar-outer"><div class="pp-progress-bar-inner" style="width:' + progress + '%;background:' + prgColor + '"></div></div>' +
                    '<div class="pp-progress-label"><span>' + p.payments.length + ' payment(s)</span>' +
                        '<span style="color:' + prgColor + ';font-weight:700">' + progress + '% collected</span></div>' +
                '</div>' +
                '<div class="pp-prod-footer">' +
                    '<div class="pp-footer-actions">' +
                        '<button type="button" class="pp-act-btn pp-btn-pay" onclick="PP.ppShowPayment(' + p.id + ')">' +
                            '<svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>' +
                            'Add Payment' +
                        '</button>' +
                        '<button type="button" class="pp-act-btn pp-btn-hist" onclick="PP.ppShowHistory(' + p.id + ')">' +
                            '<svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="12 8 12 12 14 14"/><circle cx="12" cy="12" r="10"/></svg>' +
                            'History (' + p.payments.length + ')' +
                        '</button>' +
                        '<button type="button" class="pp-act-btn pp-btn-del" ' +
                            'onclick="PP.ppDeleteProduct(' + p.id + ',\'' + escAttr(p.name) + '\')">' +
                            '<svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>' +
                        '</button>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    function renderEmptyState() {
        return '<div class="pp-empty-state">' +
            '<div class="pp-empty-icon">📦</div>' +
            '<div class="pp-empty-title">No deals yet</div>' +
            '<div class="pp-empty-sub">Click "Add Product" to create your first deal</div>' +
        '</div>';
    }

    /* ─────────────────────────────────────────────────────────────
       ACCORDION TOGGLE
    ───────────────────────────────────────────────────────────── */
    PP.ppToggleDeal = function (header) {
        var accordion = header.closest('.pp-deal-accordion');
        var body      = accordion.querySelector('.pp-deal-body');
        var chev      = header.querySelector('.pp-deal-chevron');
        var open      = body.style.display !== 'none';
        body.style.display  = open ? 'none' : 'block';
        chev.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
    };

    /* ─────────────────────────────────────────────────────────────
       STATUS UPDATE
    ───────────────────────────────────────────────────────────── */
    PP.ppUpdateDealStatus = function (sel) {
        var dealName = sel.dataset.deal;
        var status   = sel.value;
        var cfg      = STATUS_CONFIG[status] || STATUS_CONFIG['new'];

        // Optimistic UI
        sel.style.background   = cfg.bg;
        sel.style.color        = cfg.text;
        sel.style.borderColor  = cfg.border;
        var caret = sel.parentNode.querySelector('.pp-status-caret');
        if (caret) caret.style.color = cfg.text;

        api('PUT', '/lead-products/status', {
            lead_id        : LEAD_ID,
            deal_name      : dealName,
            product_status : status,
        })
        .then(function () { toast('Status updated to ' + status); })
        .catch(function () { toast('Failed to update status', 'error'); loadDeals(); });
    };

    /* ─────────────────────────────────────────────────────────────
       PAYMENT MODAL
    ───────────────────────────────────────────────────────────── */
    var ppProductCache = {};   // { id: jsPayload } to avoid re-searching nested structure

    function findProduct(id) {
        if (ppProductCache[id]) return ppProductCache[id];
        for (var di = 0; di < ppState.deals.length; di++) {
            for (var pi = 0; pi < ppState.deals[di].products.length; pi++) {
                var p = ppState.deals[di].products[pi];
                if (p.id === id) { ppProductCache[id] = p; return p; }
            }
        }
        return null;
    }

    PP.ppShowPayment = function (prodId) {
        var p = findProduct(prodId);
        if (!p) { toast('Product not found. Try refreshing.', 'error'); return; }

        ppState.activePayProdId = prodId;

        setInner('pp-pay-name',    p.name);
        setInner('pp-pay-total',   fmt(p.total));
        setInner('pp-pay-paid',    fmt(p.paid));
        setInner('pp-pay-balance', fmt(p.total - p.paid));

        var amtInp = el('pp-pay-amount');
        if (amtInp) amtInp.value = (p.total - p.paid) > 0
            ? (p.total - p.paid).toFixed(2) : '';

        // Reset mode to UPI
        qsa('.ppf-mode-tile').forEach(function (t) { t.classList.remove('pp-sel'); });
        var upi = qs('[data-val="upi"].ppf-mode-tile');
        if (upi) upi.classList.add('pp-sel');
        var modeInp = el('pp-mode-val');
        if (modeInp) modeInp.value = 'upi';

        // Reset date to today
        var dateInp = el('pp-pay-date');
        if (dateInp) dateInp.value = todayStr();

        ppShow('pp-modal-payment');
    };

    PP.ppSubmitPayment = function () {
        var pid    = ppState.activePayProdId;
        var amount = parseFloat((el('pp-pay-amount') || {}).value || 0);
        var mode   = (el('pp-mode-val')     || {}).value || 'upi';
        var date   = (el('pp-pay-date')     || {}).value || todayStr();
        var ref    = (el('pp-pay-ref')      || {}).value || '';
        var notes  = (el('pp-pay-notes')    || {}).value || '';

        if (!pid)         { toast('No product selected.', 'error'); return; }
        if (amount <= 0)  { toast('Enter a valid amount.', 'error'); return; }

        var btnEl = el('pp-submit-pay-btn');
        if (btnEl) { btnEl.disabled = true; btnEl.textContent = 'Saving…'; }

        api('POST', '/payments', {
            lead_product_id  : pid,
            amount           : amount,
            payment_mode     : mode,
            payment_date     : date,
            reference_number : ref,
            notes            : notes,
        })
        .then(function () {
            PP.ppHideModal('pp-modal-payment');
            toast('Payment of ' + fmt(amount) + ' recorded!');
            ppProductCache = {};   // clear cache
            loadDeals();
        })
        .catch(function (err) {
            var msg = (err.errors && Object.values(err.errors)[0]) || err.message || 'Error saving payment.';
            toast(msg, 'error');
        })
        .finally(function () {
            if (btnEl) { btnEl.disabled = false; btnEl.textContent = 'Save Payment'; }
        });
    };

    /* ─────────────────────────────────────────────────────────────
       PAYMENT HISTORY MODAL
    ───────────────────────────────────────────────────────────── */
    PP.ppShowHistory = function (prodId) {
        setInner('pp-hist-body', '<div class="pp-hist-loading"><div class="pp-spinner"></div></div>');
        ppShow('pp-modal-history');

        api('GET', '/payments/' + prodId)
        .then(function (res) {
            var p = res.product;
            setInner('pp-hist-name', p.name);

            // Wire Add Payment button
            var addBtn = el('pp-hist-add-btn');
            if (addBtn) addBtn.onclick = function () {
                PP.ppHideModal('pp-modal-history');
                PP.ppShowPayment(prodId);
            };

            setInner('pp-hist-body', renderHistoryBody(p, res.overall || []));
        })
        .catch(function () {
            setInner('pp-hist-body', '<div class="pp-hist-empty"><div class="pp-hist-empty-ico">⚠️</div><div>Failed to load history.</div></div>');
        });
    };

    function renderHistoryBody(p, overall) {
        var progress  = p.total > 0 ? Math.min(100, Math.round((p.paid / p.total) * 100)) : 0;
        var progColor = progress >= 100 ? '#16a34a' : (progress > 0 ? '#fe5f04' : '#e1dee3');
        var pending   = p.total - p.paid;
        var html      = '';

        // Totals grid
        html += '<div class="pp-hist-totals">' +
            '<div><div class="pp-htl">Total Value</div><div class="pp-htv">'  + fmt(p.total) + '</div></div>' +
            '<div><div class="pp-htl">Collected</div><div class="pp-htv" style="color:#16a34a">' + fmt(p.paid) + '</div></div>' +
            '<div><div class="pp-htl">Pending</div><div class="pp-htv" style="color:' + (pending > 0 ? '#dc2626' : '#16a34a') + '">' + fmt(pending) + '</div></div>' +
        '</div>';

        // Progress bar
        html += '<div class="pp-hist-prog">' +
            '<div class="pp-hist-pbar"><div class="pp-hist-pfill" style="width:' + progress + '%;background:' + progColor + '"></div></div>' +
            '<div class="pp-hist-plabels">' +
                '<span>' + p.payments.length + ' payment(s)</span>' +
                '<span style="font-weight:700;color:' + progColor + '">' + progress + '% collected</span>' +
            '</div></div>';

        // Product payments
        html += '<h4 class="pp-hist-section-title">This Product</h4>';
        if (p.payments.length === 0) {
            html += renderHistEmpty();
        } else {
            var running = 0;
            html += '<div class="pp-hist-list">';
            p.payments.forEach(function (pmt) {
                running += pmt.amount;
                html += renderHistItem(pmt, running);
            });
            html += '</div>';
        }

        // Overall for the lead
        if (overall.length > 0) {
            html += '<h4 class="pp-hist-section-title" style="margin-top:16px">All Payments (This Lead)</h4>';
            html += '<div class="pp-hist-list">';
            var overallRunning = 0;
            overall.forEach(function (pmt) {
                overallRunning += pmt.amount;
                html += renderHistItem(pmt, overallRunning, true);
            });
            html += '</div>';
        }

        return html;
    }

    function renderHistItem(pmt, running, isOverall) {
        return '<div class="pp-hist-item">' +
            '<div class="pp-hist-mode-wrap" style="background:' + pmt.modeColor + '20">' + pmt.modeIcon + '</div>' +
            '<div class="pp-hist-info">' +
                '<div class="pp-hist-mname">' + pmt.modeLabel + '</div>' +
                '<div class="pp-hist-date">'  + pmt.date + ' · By ' + escHtml(pmt.by) + '</div>' +
                (pmt.ref   ? '<div class="pp-hist-ref">Ref: ' + escHtml(pmt.ref)   + '</div>' : '') +
                (pmt.notes ? '<div class="pp-hist-note">'     + escHtml(pmt.notes) + '</div>' : '') +
            '</div>' +
            '<div class="pp-hist-right">' +
                '<div class="pp-hist-amt">' + fmt(pmt.amount) + '</div>' +
                '<div class="pp-hist-run">Cumulative: ' + fmt(running) + '</div>' +
            '</div>' +
            (!isOverall ?
                '<button type="button" class="pp-hist-del" onclick="PP.ppDeletePayment(' + pmt.id + ')" title="Remove">' +
                    '<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>' +
                '</button>' : '') +
        '</div>';
    }

    function renderHistEmpty() {
        return '<div class="pp-hist-empty">' +
            '<div class="pp-hist-empty-ico">💸</div>' +
            '<div style="font-size:14px;font-weight:700;color:#7c7c7c">No payments recorded yet</div>' +
            '<div style="font-size:12px;color:#9e9e9e;margin-top:4px">Use "Add Payment" below.</div>' +
        '</div>';
    }

    /* ─────────────────────────────────────────────────────────────
       DELETE HELPERS
    ───────────────────────────────────────────────────────────── */
    PP.ppDeleteProduct = function (id, name) {
        if (!confirm('Remove "' + name + '" from this lead?')) return;
        api('DELETE', '/lead-products/' + id)
        .then(function () {
            toast('Product removed.'); ppProductCache = {}; loadDeals();
        })
        .catch(function () { toast('Failed to remove product.', 'error'); });
    };

    PP.ppDeletePayment = function (id) {
        if (!confirm('Remove this payment?')) return;
        api('DELETE', '/payments/' + id)
        .then(function () {
            toast('Payment removed.'); ppProductCache = {}; loadDeals();
            PP.ppHideModal('pp-modal-history');
        })
        .catch(function () { toast('Failed to remove payment.', 'error'); });
    };

    /* ─────────────────────────────────────────────────────────────
       MODE TILE PICKER  (payment form)
    ───────────────────────────────────────────────────────────── */
    PP.ppPickMode = function (tile) {
        qsa('.ppf-mode-tile').forEach(function (t) { t.classList.remove('pp-sel'); });
        tile.classList.add('pp-sel');
        var inp = el('pp-mode-val');
        if (inp) inp.value = tile.dataset.val;
    };

    /* ─────────────────────────────────────────────────────────────
       STATUS CONFIG  (local mirror of PHP constant)
    ───────────────────────────────────────────────────────────── */
    var STATUS_CONFIG = {
        'new'      : { icon:'🆕', bg:'#eff6ff', text:'#1d4ed8', border:'#bfdbfe' },
        'hot'      : { icon:'🔥', bg:'#fff7ed', text:'#c2410c', border:'#fed7aa' },
        'warm'     : { icon:'☀️', bg:'#fefce8', text:'#a16207', border:'#fde68a' },
        'cold'     : { icon:'❄️', bg:'#f0f9ff', text:'#0369a1', border:'#bae6fd' },
        'converted': { icon:'✅', bg:'#f0fdf4', text:'#15803d', border:'#bbf7d0' },
    };

    /* ─────────────────────────────────────────────────────────────
       HELPERS
    ───────────────────────────────────────────────────────────── */
    function setInner(id, html) {
        var e = el(id); if (e) e.innerHTML = html;
    }

    function todayStr() {
        return new Date().toISOString().split('T')[0];
    }

    function escHtml(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function escAttr(str) {
        return String(str || '').replace(/'/g,'&#39;').replace(/"/g,'&quot;');
    }

    /* ─────────────────────────────────────────────────────────────
       BOOT — run once on load
    ───────────────────────────────────────────────────────────── */
    function boot() {
        if (!LEAD_ID) return;
        loadDeals();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    // Expose namespace
    window.PP = PP;

}(window.PP || {}));
