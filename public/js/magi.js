/* ============================================================
   STS Production Dashboard — Enhanced JavaScript v2
   File: public/js/dashboard.js
   ============================================================ */

/* ── Constants ── */
const PALETTE = [
    '#1A56DB', '#0EA5E9', '#059669', '#7C3AED',
    '#DC2626', '#D97706', '#EA580C', '#0891B2', '#9333EA'
];
const PAGE_SIZE = 20;

const CHART_OPTS = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            labels: {
                color: '#5A7299',
                font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '500' },
                boxWidth: 10,
                boxHeight: 10,
                borderRadius: 3,
                padding: 16,
                usePointStyle: true,
                pointStyle: 'circle',
            }
        },
        tooltip: {
            backgroundColor: '#ffffff',
            titleColor: '#0C1A35',
            bodyColor: '#5A7299',
            borderColor: 'rgba(26,86,219,0.12)',
            borderWidth: 1,
            padding: 13,
            cornerRadius: 10,
            titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13, weight: '700' },
            bodyFont:  { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
            boxShadow: '0 4px 20px rgba(0,20,60,0.12)',
        }
    }
};

/* ── State ── */
let allProjects   = [];
let currentFilter = 'All';
let currentPage   = 1;
const chartInstances = {};

/* ── Helpers ── */
const alpha = (hex, a) => hex + Math.round(a * 255).toString(16).padStart(2, '0');
function $id(id)  { return document.getElementById(id); }
function show(id) { $id(id).style.display = 'block'; }
function hide(id) { $id(id).style.display = 'none'; }

/* ══ DB STATE ══ */
let _dbTables    = [];
let _dbFKs       = [];
let _dbRelations = {};
let _selectedConn  = '';
let _selectedTable = '';

/* ══ INIT — fully auto: connect + pick best table + load dashboard ══ */
async function initUpload() {
  // Hide the upload screen immediately, show the splash loader
  $('upload-screen').style.display = 'none';
  $('static-loader').style.display = 'flex';

  // Animate the loader dots
  let _di = 0;
  const _dt = setInterval(() => {
    _di = (_di + 1) % 3;
    [1,2,3].forEach(n => {
      const d = $(`sl-dot-${n}`);
      if (d) d.style.opacity = (n - 1) === _di ? '1' : '.3';
    });
  }, 500);

  const setStep = (text, pct) => {
    const s = $('sl-step'); if (s) s.textContent = text;
    const b = $('sl-progress-bar'); if (b) b.style.width = pct + '%';
  };

  try {
    setStep('Connecting to database…', 15);
    const res  = await fetch('/db/tables', {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
    });
    const data = await res.json();
    if (!data.success) throw new Error(data.message);

    const tables     = data.tables       || [];
    const relations  = data.relations    || {};
    const connection = data.connection;

    if (!tables.length) throw new Error('No tables found in database.');

    setStep('Selecting best table…', 35);

    // Pick the table with the most rows (most data = most interesting)
    // Fallback: first table alphabetically
    let bestTable = tables.reduce((best, t) => {
      return (parseInt(t.row_estimate) || 0) > (parseInt(best.row_estimate) || 0) ? t : best;
    }, tables[0]);

    setStep(`Loading table: ${bestTable.name}…`, 55);

    const fetchRes = await fetch('/db/fetch-table', {
      method:  'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({ connection, table: bestTable.name })
    });

    let json;
    try   { json = await fetchRes.json(); }
    catch { throw new Error(`Server error ${fetchRes.status}`); }

    if (!fetchRes.ok || !json.success) throw new Error(json.message || `HTTP ${fetchRes.status}`);

    setStep('Building AI schema…', 75);
    await new Promise(r => setTimeout(r, 200));

    ALL_SHEETS = json.sheets || [];

    setStep('Generating dashboard…', 92);
    await new Promise(r => setTimeout(r, 200));

    renderDashboard(ALL_SHEETS[0], json.filename, 0);

    setStep('Ready!', 100);
    await new Promise(r => setTimeout(r, 300));

  } catch (err) {
    // On any error, show the table picker as fallback
    clearInterval(_dt);
    $('static-loader').style.display = 'none';
    $('upload-screen').style.display = 'flex';
    const el = $('up-err');
el.style.display = 'block';
el.innerHTML = '🚀  <strong>Almost live!</strong> Your dashboard is warming up — the data engine is spinning into gear. Refresh in just a moment and your insights will be ready to shine. ✨';
    return;
  }

  clearInterval(_dt);
  // Fade out loader
  const loader = $('static-loader');
  if (loader) {
    loader.style.opacity = '0';
    loader.style.transition = 'opacity .4s ease';
    setTimeout(() => { loader.style.display = 'none'; loader.style.opacity = ''; loader.style.transition = ''; }, 420);
  }
}

function onTableChange() {
  _selectedTable = $('db-table-sel').value;
  const fkPreview  = $('db-fk-preview');
  const fkList     = $('db-fk-list');
  const connectBtn = $('btn-db-connect');

  if (!_selectedTable) {
    fkPreview.style.display  = 'none';
    connectBtn.style.display = 'none';
    return;
  }

  const related = _dbRelations[_selectedTable] || [];
  if (related.length) {
    fkList.innerHTML = related.map(t =>
      `<span class="chip" style="border-color:rgba(255,90,0,.3);color:#FF5A00;background:rgba(255,90,0,.06)">🔗 ${t}</span>`
    ).join('');
    fkPreview.style.display = 'block';
  } else {
    fkList.innerHTML        = '<span style="font-size:11px;color:var(--text3);font-family:var(--f2)">No FK relations — table will load standalone.</span>';
    fkPreview.style.display = 'block';
  }

  connectBtn.style.display = 'flex';
}

async function connectAndLoad() {
  if (!_selectedTable) return;

  $('db-connect-card').style.display = 'none';
  $('up-loader').style.display       = 'block';
  $('up-err').style.display          = 'none';
  $('loader-step').textContent       = 'Connecting to database…';
  startLd();

  try {
    const res = await fetch('/db/fetch-table', {
      method:  'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({ connection: _selectedConn, table: _selectedTable })
    });

    let json;
    try   { json = await res.json(); }
    catch { throw new Error(`Server error ${res.status}`); }

    stopLd();
    $('up-loader').style.display = 'none';

    if (!res.ok || !json.success) throw new Error(json.message || `HTTP ${res.status}`);

    ALL_SHEETS = json.sheets || [];
    renderDashboard(ALL_SHEETS[0], json.filename, 0);

  } catch (err) {
    stopLd();
    $('up-loader').style.display       = 'none';
    $('db-connect-card').style.display = 'block';
    const el = $('up-err');
    el.style.display = 'block';
    el.textContent   = '⚠  ' + (err.message || 'Failed to load table.');
  }
}

function resetAll() {
  $('upload-screen').style.display   = 'flex';
  $('db-connect-card').style.display = 'block';
  $('dash').style.display            = 'none';
  $('ai-panel').classList.remove('open');
  $('up-err').style.display          = 'none';
  $('upload-status').classList.remove('show');
  ['btn-sheet','btn-insights','btn-export','btn-reset'].forEach(id => $(id).classList.remove('show'));

  $('db-fk-preview').style.display  = 'none';
  $('btn-db-connect').style.display = 'none';
  $('db-table-sel').value           = '';
  _selectedTable = '';

  SCHEMA = {}; ALL = []; FILT = []; FILTERS = {}; SEARCH = ''; RANGE = { min: null, max: null };
  PAGE = 1; SORT = { col: null, dir: 1 }; destroyAll(); AI_OPEN = false;
}

/* ── Reset ── */
function resetDashboard() {
    show('upload-section');
    show('upload-zone');
    hide('dashboard-content');
    hide('error-state');
    $id('upload-status').style.display = 'none';
    $id('file-input').value = '';
    Object.values(chartInstances).forEach(c => c.destroy());
    for (const k in chartInstances) delete chartInstances[k];
}

/* ── Render Dashboard ── */
function renderDashboard(data, filename) {
    hide('upload-section');
    show('dashboard-content');
    $id('upload-status').style.display = 'inline';
    $id('dash-filename').textContent   = filename || 'Dashboard';
    allProjects   = data.projects || [];
    currentFilter = 'All';
    currentPage   = 1;
    renderKPIs(data.kpi);
    renderCharts(data);
    renderTeamTable(data.team_wise);
    renderProjectTable();
    renderIndividualDist(data.individual_dist);
}

/* ── KPI Cards ── */
function renderKPIs(kpi) {
    const cards = [
        { label: 'Total Projects', value: kpi.total,     cls: 'blue',   icon: '📁', sub: 'All tracked' },
        { label: 'On Track',       value: kpi.on_track,  cls: 'green',  icon: '✅', sub: 'Active & progressing' },
        { label: 'Hold',           value: kpi.hold,      cls: 'yellow', icon: '⏸', sub: 'Temporarily paused' },
        { label: 'Delivered',      value: kpi.delivered, cls: 'purple', icon: '🚀', sub: 'Successfully completed' },
        { label: 'Lost',           value: kpi.lost,      cls: 'red',    icon: '❌', sub: 'Not proceeded' },
        { label: 'Delayed',        value: kpi.delayed,   cls: 'orange', icon: '⏰', sub: 'Past deadline' },
    ];
    $id('kpi-grid').innerHTML = cards.map(c => `
        <div class="kpi-card ${c.cls}" data-icon="${c.icon}">
            <div class="kpi-label">${c.label}</div>
            <div class="kpi-value">${c.value}</div>
            <div class="kpi-sub">${c.sub}</div>
        </div>`).join('');
}

/* ── Chart Factory ── */
function mkChart(id, type, labels, datasets, extraOpts = {}) {
    if (chartInstances[id]) chartInstances[id].destroy();
    const ctx = document.getElementById(id);
    if (!ctx) return;
    const isPolar = type === 'pie' || type === 'doughnut';
    chartInstances[id] = new Chart(ctx, {
        type,
        data: { labels, datasets },
        options: {
            ...CHART_OPTS,
            scales: isPolar ? {} : {
                x: {
                    ticks: { color: '#5A7299', font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" } },
                    grid:  { color: 'rgba(26,86,219,0.05)' }
                },
                y: {
                    ticks: { color: '#5A7299', font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" } },
                    grid:  { color: 'rgba(26,86,219,0.05)' },
                    beginAtZero: true
                }
            },
            ...extraOpts
        }
    });
}

/* ── All Charts ── */
function renderCharts(data) {

    /* Status Doughnut */
    const sK = Object.keys(data.status_distribution);
    mkChart('chart-status', 'doughnut', sK, [{
        data:            Object.values(data.status_distribution),
        backgroundColor: PALETTE.slice(0, sK.length),
        borderColor:     '#ffffff',
        borderWidth:     3,
        hoverOffset:     10,
    }], {
        cutout: '62%',
        plugins: { ...CHART_OPTS.plugins, legend: { ...CHART_OPTS.plugins.legend, position: 'bottom' } }
    });

    /* Project Type Horizontal Bar */
    const tK = Object.keys(data.type_distribution);
    mkChart('chart-type', 'bar', tK, [{
        label:           'Projects',
        data:            Object.values(data.type_distribution),
        backgroundColor: tK.map((_, i) => alpha(PALETTE[i % PALETTE.length], 0.82)),
        borderRadius:    7,
        borderSkipped:   false,
        borderWidth:     0,
    }], {
        indexAxis: 'y',
        plugins: { ...CHART_OPTS.plugins, legend: { display: false } }
    });

    /* Delay Doughnut */
    const dK = Object.keys(data.delay_distribution);
    mkChart('chart-delay', 'doughnut', dK, [{
        data:            Object.values(data.delay_distribution),
        backgroundColor: ['#EA580C', '#059669'],
        borderColor:     '#ffffff',
        borderWidth:     3,
        hoverOffset:     10,
    }], {
        cutout: '62%',
        plugins: { ...CHART_OPTS.plugins, legend: { ...CHART_OPTS.plugins.legend, position: 'bottom' } }
    });

    /* Monthly Trend — Mixed Bar + Line */
    const months = data.monthly_trend.map(m => m.month);
    mkChart('chart-monthly', 'bar', months, [
        {
            label:           'Allocated',
            data:            data.monthly_trend.map(m => m.allocated),
            backgroundColor: alpha('#1A56DB', 0.12),
            borderColor:     '#1A56DB',
            borderWidth:     2,
            borderRadius:    5,
            type:            'bar',
        },
        {
            label:                'Delivered',
            data:                 data.monthly_trend.map(m => m.delivered),
            backgroundColor:      'transparent',
            borderColor:          '#059669',
            borderWidth:          2.5,
            pointBackgroundColor: '#059669',
            pointBorderColor:     '#ffffff',
            pointBorderWidth:     2,
            pointRadius:          5,
            type:                 'line',
            tension:              0.45,
        }
    ]);

    /* Active vs Completed Doughnut */
    const aK = Object.keys(data.active_distribution);
    mkChart('chart-active', 'doughnut', aK, [{
        data:            Object.values(data.active_distribution),
        backgroundColor: ['#1A56DB', '#7C3AED'],
        borderColor:     '#ffffff',
        borderWidth:     3,
        hoverOffset:     10,
    }], {
        cutout: '62%',
        plugins: { ...CHART_OPTS.plugins, legend: { ...CHART_OPTS.plugins.legend, position: 'bottom' } }
    });

    /* Team Stacked Bar */
    const teams      = Object.keys(data.team_wise);
    const statuses   = ['On Track', 'Hold', 'Delivered', 'Lost'];
    const teamColors = ['#059669', '#D97706', '#7C3AED', '#DC2626'];
    mkChart('chart-team-stack', 'bar', teams,
        statuses.map((s, i) => ({
            label:           s,
            data:            teams.map(t => data.team_wise[t][s] || 0),
            backgroundColor: teamColors[i],
            borderRadius:    i === statuses.length - 1 ? 5 : 0,
            borderSkipped:   false,
        })),
        { scales: {
            x: { stacked: true, ticks: { color: '#5A7299' }, grid: { color: 'rgba(26,86,219,0.05)' } },
            y: { stacked: true, ticks: { color: '#5A7299' }, grid: { color: 'rgba(26,86,219,0.05)' } }
        }}
    );

    /* Developer Allocation Bar */
    const devs = Object.keys(data.developer_wise);
    mkChart('chart-dev', 'bar', devs, [{
        label:           'Projects',
        data:            Object.values(data.developer_wise),
        backgroundColor: devs.map((_, i) => alpha(PALETTE[i % PALETTE.length], 0.78)),
        borderRadius:    7,
        borderSkipped:   false,
        borderWidth:     0,
    }], {
        plugins: { ...CHART_OPTS.plugins, legend: { display: false } }
    });
}

/* ── Team Summary Table ── */
function renderTeamTable(teamWise) {
    const rows = Object.entries(teamWise).map(([team, d]) => `
        <tr>
            <td>${team}</td>
            <td><span class="badge badge-blue">${d.total}</span></td>
            <td><span class="badge badge-green">${d['On Track'] || 0}</span></td>
            <td><span class="badge badge-yellow">${d['Hold'] || 0}</span></td>
            <td><span class="badge badge-purple">${d['Delivered'] || 0}</span></td>
        </tr>`).join('');
    $id('team-table-wrap').innerHTML = `
        <table>
            <thead><tr>
                <th>Team</th><th>Total</th>
                <th>On Track</th><th>Hold</th><th>Delivered</th>
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>`;
}

/* ── Individual Distribution Charts ── */
function renderIndividualDist(indDist) {
    const container = $id('individual-charts-row');
    container.innerHTML = '';
    Object.entries(indDist).forEach(([team, devs], idx) => {
        const devNames = Object.keys(devs);
        const chartId  = `chart-ind-${idx}`;
        const card = document.createElement('div');
        card.className = 'chart-card';
        card.innerHTML = `
            <div class="chart-card-title">
                <span class="dot" style="background:${PALETTE[idx % PALETTE.length]}"></span>
                Team ${team} — Individual Distribution
            </div>
            <div class="chart-wrap">
                <canvas id="${chartId}" style="max-height:240px"></canvas>
            </div>`;
        container.appendChild(card);
        requestAnimationFrame(() => {
            mkChart(chartId, 'bar', devNames,
                ['On Track', 'Hold', 'Delivered'].map((s, i) => ({
                    label:           s,
                    data:            devNames.map(d => devs[d][s] || 0),
                    backgroundColor: ['#059669', '#D97706', '#7C3AED'][i],
                    borderRadius:    i === 2 ? 5 : 0,
                    borderSkipped:   false,
                })),
                { scales: {
                    x: { stacked: true, ticks: { color: '#5A7299' }, grid: { color: 'rgba(26,86,219,0.05)' } },
                    y: { stacked: true, ticks: { color: '#5A7299' }, grid: { color: 'rgba(26,86,219,0.05)' }, beginAtZero: true }
                }}
            );
        });
    });
}

/* ── Project Table ── */
function statusBadge(s) {
    const map = { 'On Track':'badge-green', 'Hold':'badge-yellow', 'Delivered':'badge-purple', 'Lost':'badge-red' };
    return `<span class="badge ${map[s] || ''}">${s || '-'}</span>`;
}
function delayBadge(s) {
    if (s === 'Delayed') return `<span class="badge badge-red">${s}</span>`;
    if (s === 'On Time') return `<span class="badge badge-green">${s}</span>`;
    return `<span style="color:var(--text-muted)">${s || '-'}</span>`;
}

function filterProjects(status, btn) {
    currentFilter = status;
    currentPage   = 1;
    if (btn) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
    renderProjectTable();
}

function getFiltered() {
    const q = ($id('project-search')?.value || '').toLowerCase().trim();
    return allProjects.filter(p => {
        const matchStatus = currentFilter === 'All' || p.status === currentFilter;
        const matchSearch = !q
            || (p.project       || '').toLowerCase().includes(q)
            || (p.developer     || '').toLowerCase().includes(q)
            || (p.project_code  || '').toLowerCase().includes(q)
            || (p.team          || '').toLowerCase().includes(q);
        return matchStatus && matchSearch;
    });
}

function renderProjectTable() {
    const filtered   = getFiltered();
    const total      = filtered.length;
    const totalPages = Math.ceil(total / PAGE_SIZE);
    const slice      = filtered.slice((currentPage - 1) * PAGE_SIZE, currentPage * PAGE_SIZE);

    $id('project-count').textContent = `Showing ${slice.length} of ${total} projects`;
    $id('project-tbody').innerHTML = slice.map(p => `
        <tr>
            <td><span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-muted)">${p.project_code || '-'}</span></td>
            <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${p.project || ''}">${p.project || '-'}</td>
            <td>${p.team || '-'}</td>
            <td>${p.developer || '-'}</td>
            <td><span style="font-size:12px;color:var(--text-muted)">${p.project_type || '-'}</span></td>
            <td>${statusBadge(p.status)}</td>
            <td><span style="font-size:12px;color:var(--text-muted)">${p.progress || '-'}</span></td>
            <td>${delayBadge(p.delay_status)}</td>
            <td><span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-muted)">${p.end_date || '-'}</span></td>
        </tr>`).join('');
    renderPagination(totalPages);
}

function renderPagination(totalPages) {
    const pag = $id('pagination');
    if (totalPages <= 1) { pag.innerHTML = ''; return; }
    let html = '';
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 2) {
            html += `<button class="page-btn${i === currentPage ? ' active' : ''}" onclick="goPage(${i})">${i}</button>`;
        } else if (Math.abs(i - currentPage) === 3) {
            html += `<span style="color:var(--text-muted);padding:4px 2px">…</span>`;
        }
    }
    pag.innerHTML = html;
}

function goPage(p) {
    currentPage = p;
    renderProjectTable();
    const tbl = $id('project-tbody');
    if (tbl) tbl.closest('.chart-card').scrollIntoView({ behavior:'smooth', block:'start' });
}

/* ── Boot ── */
document.addEventListener('DOMContentLoaded', initUpload);