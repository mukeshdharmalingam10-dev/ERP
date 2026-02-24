@php
    $isEdit = $mode === 'edit';
    $viewOnly = $mode === 'view';
    $wo = $dpl->workOrder ?? null;
    $salesType = old('sales_type', $wo->sales_type ?? 'Tender');
    $productionOrderId = old('production_order_id', $dpl->production_order_id ?? '');
    $workOrderId = old('work_order_id', $dpl->work_order_id ?? '');
    $dplNo = old('dpl_no', $dpl->dpl_no ?? $nextDplNo);
    $latestDate = old('latest_date', $dpl->latest_date ? $dpl->latest_date->format('Y-m-d') : now()->format('Y-m-d'));
    $serverWo = null;
    if ($wo) {
        $serverWo = [
            'id' => $wo->id,
            'sales_type' => $wo->sales_type,
            'production_order_id' => $wo->sales_type === 'Tender' ? $wo->customer_order_id : $wo->proforma_invoice_id,
            'work_order_no' => $wo->work_order_no,
            'worker_type' => $wo->worker_type,
            'worker_name' => $wo->worker_name,
            'title' => $wo->title,
            'product_name' => $wo->product_name,
            'starting_set_no' => $wo->starting_set_no,
            'ending_set_no' => $wo->ending_set_no,
            'sub_set_count' => $wo->no_of_sub_sets_per_set,
            'wo_type' => $wo->quantity_type,
            'total_qty' => $wo->no_of_quantity,
            'existing_work_order_no' => optional($wo->existingWorkOrder)->work_order_no,
            'document_file' => $wo->document_path ? basename($wo->document_path) : '',
            'no_of_sets' => $wo->no_of_sets,
            'total_sub_sets' => $wo->total_sub_sets,
            'quantity_per_set' => $wo->quantity_per_set,
        ];
    }
@endphp

<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1); max-width:1320px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <h2 style="margin:0;">{{ $viewOnly ? 'View' : ($isEdit ? 'Edit' : 'Create') }} Daily Production List</h2>
        <div style="display:flex; gap:8px;">
            @if($viewOnly)
                <a href="{{ route('dpl.edit', $dpl->id) }}" style="padding:10px 16px; background:#ffc107; color:#333; border-radius:6px; text-decoration:none;"><i class="fas fa-edit"></i> Edit</a>
            @endif
            <a href="{{ route('dpl.index') }}" style="padding:10px 16px; background:#6c757d; color:white; border-radius:6px; text-decoration:none;"><i class="fas fa-list"></i> List</a>
        </div>
    </div>

    @if($errors->any())
        <div style="background:#f8d7da; color:#721c24; padding:12px; border-radius:5px; margin-bottom:15px;">
            <ul style="margin:0; padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form id="dplForm" method="POST" action="{{ $isEdit ? route('dpl.update', $dpl->id) : route('dpl.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:15px; padding:15px;">
            <h3 style="margin:0 0 10px 0; font-size:16px;">DPL Number</h3>
            @if(!$isEdit && !$viewOnly)
            <div style="display:flex; gap:20px; margin-bottom:10px;">
                <label><input type="radio" name="dpl_mode" value="new" checked> NEW DPL NO</label>
                <label><input type="radio" name="dpl_mode" value="existing"> EXISTING DPL NO</label>
            </div>
            @endif
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <input type="text" id="dplNo" name="dpl_no" value="{{ $dplNo }}" readonly style="padding:9px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                <div id="existingDplWrap" style="display:none;">
                    <div style="display:flex; gap:8px;">
                        <select id="existingDplId" style="width:100%; padding:9px; border:1px solid #ddd; border-radius:6px;">
                            <option value="">Select Existing DPL</option>
                            @foreach($existingDplNos as $existingDpl)
                                <option value="{{ $existingDpl->id }}">{{ $existingDpl->dpl_no }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="loadExistingDpl" style="padding:9px 12px; border:none; border-radius:6px; background:#17a2b8; color:white; cursor:pointer;">Load</button>
                    </div>
                </div>
            </div>
            <div id="cloneIndicator" style="display:none; margin-top:8px; padding:8px 10px; border-radius:6px; background:#e8f4ff; color:#0b5394; font-size:13px;"></div>
        </div>

        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:15px; padding:15px;">
            <h3 style="margin:0 0 10px 0; font-size:16px;">Production Order -> Work Order</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                <div>
                    <label style="display:block; margin-bottom:4px;">Sales Type <span style="color:red;">*</span></label>
                    <select id="salesType" name="sales_type" style="width:100%; padding:9px; border:1px solid #ddd; border-radius:6px;" {{ $viewOnly ? 'disabled' : '' }}>
                        <option value="Tender" {{ $salesType === 'Tender' ? 'selected' : '' }}>Tender</option>
                        <option value="Enquiry" {{ $salesType === 'Enquiry' ? 'selected' : '' }}>Enquiry</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px;">PO No <span style="color:red;">*</span></label>
                    <select id="productionOrderId" name="production_order_id" style="width:100%; padding:9px; border:1px solid #ddd; border-radius:6px;" {{ $viewOnly ? 'disabled' : '' }}>
                        <option value="">Select PO</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px;">Work Order No <span style="color:red;">*</span></label>
                    <select id="workOrderId" name="work_order_id" style="width:100%; padding:9px; border:1px solid #ddd; border-radius:6px;" {{ $viewOnly ? 'disabled' : '' }}>
                        <option value="">Select Work Order</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:15px; padding:15px;">
            <h3 style="margin:0 0 10px 0; font-size:16px;">Existing Work Order (Reference)</h3>
            <div>
                <label style="display:block; margin-bottom:4px;">Existing Work Order No</label>
                <input id="f_existing_wo" readonly placeholder="-- None --" style="width:100%; padding:9px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
            </div>
        </div>

        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:15px; padding:15px;">
            <h3 style="margin:0 0 10px 0; font-size:16px;">Title & Worker Details</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div style="grid-column:1/-1;">
                    <label style="display:block; margin-bottom:4px;">Title</label>
                    <input id="f_title" readonly placeholder="Title" style="width:100%; padding:9px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px;">Work Type</label>
                    <input id="f_worker_type" readonly placeholder="Work Type" style="width:100%; padding:9px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px;">Work Name</label>
                    <input id="f_work_name" readonly placeholder="Work Name" style="width:100%; padding:9px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
            </div>
        </div>

        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:15px; padding:15px;">
            <h3 style="margin:0 0 10px 0; font-size:16px;">Quantity Selection</h3>
            <div style="display:grid; grid-template-columns:repeat(4, minmax(180px, 1fr)); gap:10px;">
                <div>
                    <label style="display:block; margin-bottom:4px;">Product Name</label>
                    <input id="f_product_name" readonly style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px;">Work Order Type</label>
                    <input id="f_wo_type" readonly style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px;">Starting Set No</label>
                    <input id="f_start_set" readonly style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px;">Ending Set No</label>
                    <input id="f_end_set" readonly style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px;">Sub-set Count</label>
                    <input id="f_sub_count" readonly style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px;">Total Quantity</label>
                    <input id="f_total_qty" readonly style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
            </div>
        </div>

        @include('production.dpl._dynamic_table', ['viewOnly' => $viewOnly])

        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:15px; padding:15px;">
            <h3 style="margin:0 0 10px 0; font-size:16px;">Bottom Section</h3>
            <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:12px;">
                <textarea name="remarks" rows="3" placeholder="Remarks" style="padding:9px; border:1px solid #ddd; border-radius:6px;" {{ $viewOnly ? 'readonly' : '' }}>{{ old('remarks', $dpl->remarks) }}</textarea>
                <input type="date" name="latest_date" value="{{ $latestDate }}" style="padding:9px; border:1px solid #ddd; border-radius:6px;" {{ $viewOnly ? 'readonly' : '' }}>
                <input type="text" readonly value="{{ optional($dpl->creator)->name ?? auth()->user()->name }}" style="padding:9px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
            </div>
        </div>

        @if(!$viewOnly)
            <div style="display:flex; gap:8px;">
                <button type="submit" style="padding:12px 20px; border:none; border-radius:6px; background:#28a745; color:white; cursor:pointer;"><i class="fas fa-save"></i> {{ $isEdit ? 'Update' : 'Save' }}</button>
                <a href="{{ route('dpl.index') }}" style="padding:12px 20px; border-radius:6px; background:#6c757d; color:white; text-decoration:none;">Cancel</a>
            </div>
        @endif
    </form>
</div>

@push('scripts')
<script>
(function () {
    const viewOnly = @json($viewOnly);
    const isEdit = @json($isEdit);
    const initialSalesType = @json($salesType);
    const initialPoId = @json((string) $productionOrderId);
    const initialWoId = @json((string) $workOrderId);
    const serverWo = @json($serverWo);
    const entryRows = @json($entryRows);

    const salesTypeEl = document.getElementById('salesType');
    const poEl = document.getElementById('productionOrderId');
    const woEl = document.getElementById('workOrderId');
    const dplNoEl = document.getElementById('dplNo');
    const tableHead = document.getElementById('dynamicTableHead');
    const tableBody = document.getElementById('dynamicTableBody');
    const hint = document.getElementById('dynamicHint');
    const cloneIndicator = document.getElementById('cloneIndicator');

    let woData = null;
    let setNos = [];
    let subCount = 0;
    let woType = '';
    let currentEntryRows = Array.isArray(entryRows) ? entryRows.slice() : [];

    function setVal(id, val) {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    }

    function fillWorkOrderFields(data) {
        setVal('f_existing_wo', data?.existing_work_order_no);
        setVal('f_title', data?.title);
        setVal('f_worker_type', data?.worker_type);
        setVal('f_work_name', data?.worker_name);
        setVal('f_product_name', data?.product_name);
        setVal('f_wo_type', data?.wo_type);
        setVal('f_start_set', data?.starting_set_no);
        setVal('f_end_set', data?.ending_set_no);
        setVal('f_sub_count', data?.sub_set_count);
        setVal('f_total_qty', data?.total_qty);
    }

    async function getJson(url) {
        const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
        if (!r.ok) throw new Error('Request failed');
        return r.json();
    }

    async function loadNextDplNo() {
        if (isEdit || viewOnly) return;
        const data = await getJson('{{ route("dpl.next-no") }}');
        if (data?.dpl_no) dplNoEl.value = data.dpl_no;
    }

    async function loadPoOptions(preferred) {
        const data = await getJson('{{ route("dpl.po-options") }}?sales_type=' + encodeURIComponent(salesTypeEl.value || 'Tender'));
        poEl.innerHTML = '<option value="">Select PO</option>';
        data.forEach(function (row) {
            const label = (row.tender_no ? (row.tender_no + ' / ') : '') + row.po_no + (row.customer_po_no ? (' / ' + row.customer_po_no) : '');
            const op = document.createElement('option');
            op.value = String(row.id);
            op.textContent = label;
            poEl.appendChild(op);
        });
        if (preferred) poEl.value = String(preferred);
    }

    async function loadWorkOrders(preferred) {
        woEl.innerHTML = '<option value="">Select Work Order</option>';
        if (!poEl.value) return;
        const data = await getJson('{{ route("dpl.work-orders") }}?sales_type=' + encodeURIComponent(salesTypeEl.value) + '&production_order_id=' + encodeURIComponent(poEl.value));
        data.forEach(function (row) {
            const op = document.createElement('option');
            op.value = String(row.id);
            op.textContent = row.work_order_no;
            woEl.appendChild(op);
        });
        if (preferred) woEl.value = String(preferred);
    }

    function buildSetNos() {
        setNos = [];
        const start = parseInt(woData?.starting_set_no || 0, 10);
        const end = parseInt(woData?.ending_set_no || 0, 10);
        if (start > 0 && end >= start) {
            for (let s = start; s <= end; s++) setNos.push(s);
        }
        subCount = parseInt(woData?.sub_set_count || 0, 10);
        woType = woData?.wo_type || '';
    }

    function clearTable() {
        tableHead.innerHTML = '';
        tableBody.innerHTML = '';
    }

    const thStyle = 'padding:10px 8px; border:1px solid #d9dee5; background:#f1f3f5; font-weight:600; white-space:nowrap;';
    const tdStyle = 'padding:8px; border:1px solid #e0e4ea; background:#fff;';
    const miniBtnPlus = 'background:#2f9e44;color:#fff;border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;font-weight:700;line-height:1;';
    const miniBtnMinus = 'background:#e03131;color:#fff;border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;font-weight:700;line-height:1;';

    function toDisplayDate(v) {
        if (!v) return '';
        if (/^\d{4}-\d{2}-\d{2}$/.test(v)) {
            const p = v.split('-');
            return p[2] + '-' + p[1] + '-' + p[0];
        }
        return v;
    }

    function toStoreDate(v) {
        if (!v) return '';
        if (/^\d{4}-\d{2}-\d{2}$/.test(v)) return v;
        if (/^\d{2}-\d{2}-\d{4}$/.test(v)) {
            const p = v.split('-');
            return p[2] + '-' + p[1] + '-' + p[0];
        }
        return '';
    }

    function makeCellEntriesHtml(kind, setNo, subNo, entries) {
        const rows = (Array.isArray(entries) && entries.length) ? entries : [{}];
        let html = '<div class="cell-entries" data-kind="' + kind + '" data-set="' + setNo + '" data-sub="' + (subNo || '') + '" style="display:flex; flex-direction:column; gap:6px;">';
        rows.forEach(function (row) {
            html += '<div class="cell-entry" style="display:flex; align-items:center; gap:6px;">'
                + '<input type="number" class="' + kind + '-qty" min="0" step="0.01" value="' + (row.qty || '') + '" placeholder="Qty" style="width:100px; padding:6px; border:1px solid #ddd; border-radius:6px;" ' + (viewOnly ? 'readonly' : '') + '>'
                + '<input type="text" class="' + kind + '-date date-input" value="' + toDisplayDate(row.date || '') + '" placeholder="DD-MM-YYYY" style="width:130px; padding:6px; border:1px solid #ddd; border-radius:6px;" ' + (viewOnly ? 'readonly' : '') + '>'
                + (viewOnly ? '' : '<button type="button" class="cell-add" style="' + miniBtnPlus + '">+</button><button type="button" class="cell-remove" style="' + miniBtnMinus + '">−</button>')
                + '</div>';
        });
        html += '</div>';
        return html;
    }

    function headerHtml() {
        if (woType === 'Others') {
            return '<tr><th style="' + thStyle + '">Sr.No</th><th style="' + thStyle + '">Item Name</th><th style="' + thStyle + '">Total Qty</th><th style="' + thStyle + '">Completed Qty</th><th style="' + thStyle + '">Date</th>' + (viewOnly ? '' : '<th style="' + thStyle + '">Action</th>') + '</tr>';
        }
        if (woType === 'Sub Sets') {
            let top = '<tr><th rowspan="3" style="' + thStyle + '">Sr.No</th><th rowspan="3" style="' + thStyle + '">Item Name</th>';
            setNos.forEach(function (s) { top += '<th colspan="' + (subCount * 2) + '" style="' + thStyle + ' text-align:center;">SET NO: ' + s + '</th>'; });
            top += viewOnly ? '' : '<th rowspan="3" style="' + thStyle + '">Action</th>';
            top += '</tr>';
            let sub = '<tr>';
            setNos.forEach(function () {
                for (let i = 1; i <= subCount; i++) sub += '<th colspan="2" style="' + thStyle + ' text-align:center;">SUB SET ' + i + '</th>';
            });
            sub += '</tr>';
            let sub2 = '<tr>';
            setNos.forEach(function () {
                for (let i = 1; i <= subCount; i++) sub2 += '<th style="' + thStyle + ' text-align:center;">Qty</th><th style="' + thStyle + ' text-align:center;">Date</th>';
            });
            sub2 += '</tr>';
            return top + sub + sub2;
        }
        let top = '<tr><th rowspan="2" style="' + thStyle + '">Sr.No</th><th rowspan="2" style="' + thStyle + '">Item Name</th>';
        setNos.forEach(function (s) { top += '<th colspan="2" style="' + thStyle + ' text-align:center;">SET NO: ' + s + '</th>'; });
        top += viewOnly ? '' : '<th rowspan="2" style="' + thStyle + '">Action</th>';
        top += '</tr>';
        let sub = '<tr>';
        setNos.forEach(function () {
            sub += '<th style="' + thStyle + ' text-align:center;">Qty</th><th style="' + thStyle + ' text-align:center;">Date</th>';
        });
        sub += '</tr>';
        return top + sub;
    }

    function regroupRows() {
        if (!currentEntryRows || !currentEntryRows.length) return [];
        if (woType === 'Others') {
            return currentEntryRows.map(function (e) {
                return { item_name: e.item_name || '', total_qty: e.qty || '', completed_qty: e.completed_qty || '', date: e.entry_date || '' };
            });
        }
        const map = {};
        currentEntryRows.forEach(function (e) {
            let key = e.item_name || '';
            if (!map[key]) map[key] = { item_name: e.item_name || '', qty_per_set: e.qty || '', sets: {} };
            if (woType === 'Sub Sets') {
                if (!map[key].sets[e.set_no]) map[key].sets[e.set_no] = {};
                if (!map[key].sets[e.set_no][e.sub_set_no]) map[key].sets[e.set_no][e.sub_set_no] = [];
                map[key].sets[e.set_no][e.sub_set_no].push({ qty: e.qty || '', date: e.entry_date || '' });
            } else if (woType === 'Nos') {
                if (!map[key].sets[e.set_no]) map[key].sets[e.set_no] = [];
                map[key].sets[e.set_no].push({ qty: e.qty || '', date: e.entry_date || '' });
            } else {
                if (!map[key].sets[e.set_no]) map[key].sets[e.set_no] = [];
                map[key].sets[e.set_no].push({ qty: e.qty || '', date: e.entry_date || '' });
            }
        });
        return Object.values(map);
    }

    function addRow(rowData) {
        const tr = document.createElement('tr');
        let html = '<td class="sr" style="' + tdStyle + '"></td>';
        html += '<td style="' + tdStyle + '"><input type="text" class="item-name" value="' + (rowData.item_name || '') + '" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;" ' + (viewOnly ? 'readonly' : '') + '></td>';

        if (woType === 'Others') {
            html += '<td style="' + tdStyle + '"><input type="number" class="total-qty" min="0" step="0.01" value="' + (rowData.total_qty || '') + '" style="width:130px; padding:8px; border:1px solid #ddd; border-radius:6px;" ' + (viewOnly ? 'readonly' : '') + '></td>';
            html += '<td style="' + tdStyle + '"><input type="number" class="completed-qty" min="0" step="0.01" value="' + (rowData.completed_qty || '') + '" style="width:130px; padding:8px; border:1px solid #ddd; border-radius:6px;" ' + (viewOnly ? 'readonly' : '') + '></td>';
            html += '<td style="' + tdStyle + '"><input type="text" class="entry-date date-input" value="' + toDisplayDate(rowData.date || '') + '" placeholder="DD-MM-YYYY" style="width:170px; padding:8px; border:1px solid #ddd; border-radius:6px;" ' + (viewOnly ? 'readonly' : '') + '></td>';
        } else if (woType === 'Sub Sets') {
            setNos.forEach(function (setNo) {
                for (let sub = 1; sub <= subCount; sub++) {
                    const list = rowData.sets && rowData.sets[setNo] && rowData.sets[setNo][sub] ? rowData.sets[setNo][sub] : [];
                    html += '<td colspan="2" style="' + tdStyle + '">' + makeCellEntriesHtml('sub', setNo, sub, list) + '</td>';
                }
            });
        } else if (woType === 'Nos') {
            setNos.forEach(function (setNo) {
                const list = rowData.sets && rowData.sets[setNo] ? rowData.sets[setNo] : [];
                html += '<td colspan="2" style="' + tdStyle + '">' + makeCellEntriesHtml('nos', setNo, null, list) + '</td>';
            });
        } else {
            setNos.forEach(function (setNo) {
                const list = rowData.sets && rowData.sets[setNo] ? rowData.sets[setNo] : [];
                html += '<td colspan="2" style="' + tdStyle + '">' + makeCellEntriesHtml('set', setNo, null, list) + '</td>';
            });
        }

        if (!viewOnly) {
            html += '<td style="' + tdStyle + ' text-align:center;"><button type="button" class="rm-row" style="background:#dc3545; color:white; border:none; border-radius:6px; padding:6px 10px; cursor:pointer;"><i class="fas fa-minus"></i></button></td>';
        }

        tr.innerHTML = html;
        tableBody.appendChild(tr);
        syncNames();
    }

    function syncNames() {
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        rows.forEach(function (tr, idx) {
            const sr = tr.querySelector('.sr');
            if (sr) sr.textContent = String(idx + 1);

            const itemName = tr.querySelector('.item-name');
            if (itemName) itemName.name = 'items[' + idx + '][title]';

            if (woType === 'Others') {
                tr.querySelector('.total-qty').name = 'items[' + idx + '][total_qty]';
                tr.querySelector('.completed-qty').name = 'items[' + idx + '][completed_qty]';
                tr.querySelector('.entry-date').name = 'items[' + idx + '][date]';
            } else if (woType === 'Sub Sets') {
                tr.querySelectorAll('.cell-entries[data-kind="sub"]').forEach(function (cell) {
                    const setNo = cell.dataset.set;
                    const subNo = cell.dataset.sub;
                    cell.querySelectorAll('.cell-entry').forEach(function (entry, eidx) {
                        const qty = entry.querySelector('.sub-qty');
                        const date = entry.querySelector('.sub-date');
                        if (qty) qty.name = 'items[' + idx + '][sets][' + setNo + '][' + subNo + '][entries][' + eidx + '][qty]';
                        if (date) date.name = 'items[' + idx + '][sets][' + setNo + '][' + subNo + '][entries][' + eidx + '][date]';
                    });
                });
            } else if (woType === 'Nos') {
                tr.querySelectorAll('.cell-entries[data-kind="nos"]').forEach(function (cell) {
                    const setNo = cell.dataset.set;
                    cell.querySelectorAll('.cell-entry').forEach(function (entry, eidx) {
                        const qty = entry.querySelector('.nos-qty');
                        const date = entry.querySelector('.nos-date');
                        if (qty) qty.name = 'items[' + idx + '][sets][' + setNo + '][entries][' + eidx + '][qty]';
                        if (date) date.name = 'items[' + idx + '][sets][' + setNo + '][entries][' + eidx + '][date]';
                    });
                });
            } else {
                tr.querySelectorAll('.cell-entries[data-kind="set"]').forEach(function (cell) {
                    const setNo = cell.dataset.set;
                    cell.querySelectorAll('.cell-entry').forEach(function (entry, eidx) {
                        const qty = entry.querySelector('.set-qty');
                        const date = entry.querySelector('.set-date');
                        if (qty) qty.name = 'items[' + idx + '][sets][' + setNo + '][entries][' + eidx + '][qty]';
                        if (date) date.name = 'items[' + idx + '][sets][' + setNo + '][entries][' + eidx + '][date]';
                    });
                });
            }
        });
    }

    function renderDynamicTable() {
        clearTable();
        if (!woType) {
            hint.textContent = 'Select Work Order to generate dynamic headers.';
            return;
        }
        tableHead.innerHTML = headerHtml();
        const rows = regroupRows();
        if (rows.length) {
            rows.forEach(addRow);
        } else {
            addRow({});
        }
        hint.textContent = 'Dynamic table ready (' + woType + ').';
    }

    async function loadWorkOrderDetails() {
        if (!woEl.value) {
            woData = null;
            setNos = [];
            woType = '';
            fillWorkOrderFields(null);
            renderDynamicTable();
            return;
        }
        woData = await getJson('{{ route("dpl.api.work-order", ['id' => 0]) }}'.replace('/0', '/' + encodeURIComponent(woEl.value)));
        buildSetNos();
        fillWorkOrderFields(woData);
        renderDynamicTable();
    }

    document.getElementById('addDynamicRow')?.addEventListener('click', function () {
        if (!woType) return;
        addRow({});
    });

    tableBody.addEventListener('click', function (e) {
        const rowRemove = e.target.closest('.rm-row');
        if (rowRemove) {
            if (tableBody.querySelectorAll('tr').length <= 1) return;
            rowRemove.closest('tr').remove();
            syncNames();
            return;
        }

        const addBtn = e.target.closest('.cell-add');
        if (addBtn) {
            const cell = addBtn.closest('.cell-entries');
            if (!cell) return;
            const kind = cell.dataset.kind;
            const setNo = cell.dataset.set;
            const subNo = cell.dataset.sub || '';
            const wrap = document.createElement('div');
            wrap.className = 'cell-entry';
            wrap.style.cssText = 'display:flex; align-items:center; gap:6px;';
            wrap.innerHTML = '<input type="number" class="' + kind + '-qty" min="0" step="0.01" placeholder="Qty" style="width:100px; padding:6px; border:1px solid #ddd; border-radius:6px;">'
                + '<input type="text" class="' + kind + '-date date-input" placeholder="DD-MM-YYYY" style="width:130px; padding:6px; border:1px solid #ddd; border-radius:6px;">'
                + '<button type="button" class="cell-add" style="' + miniBtnPlus + '">+</button>'
                + '<button type="button" class="cell-remove" style="' + miniBtnMinus + '">−</button>';
            cell.appendChild(wrap);
            syncNames();
            return;
        }

        const remBtn = e.target.closest('.cell-remove');
        if (remBtn) {
            const cell = remBtn.closest('.cell-entries');
            const entry = remBtn.closest('.cell-entry');
            if (!cell || !entry) return;
            const entries = cell.querySelectorAll('.cell-entry');
            if (entries.length <= 1) {
                const q = entry.querySelector('input[type="number"]');
                const d = entry.querySelector('.date-input');
                if (q) q.value = '';
                if (d) d.value = '';
            } else {
                entry.remove();
            }
            syncNames();
        }
    });

    salesTypeEl?.addEventListener('change', async function () {
        await loadPoOptions('');
        await loadWorkOrders('');
        woData = null;
        woType = '';
        fillWorkOrderFields(null);
        renderDynamicTable();
    });

    poEl?.addEventListener('change', async function () {
        await loadWorkOrders('');
        woData = null;
        woType = '';
        fillWorkOrderFields(null);
        renderDynamicTable();
    });

    woEl?.addEventListener('change', async function () {
        await loadWorkOrderDetails();
    });

    document.getElementById('loadExistingDpl')?.addEventListener('click', function () {
        const id = document.getElementById('existingDplId')?.value;
        if (!id || isEdit || viewOnly) return;
        cloneFromExisting(id);
    });

    document.querySelectorAll('input[name="dpl_mode"]').forEach(function (r) {
        r.addEventListener('change', function () {
            document.getElementById('existingDplWrap').style.display = (this.value === 'existing' && this.checked) ? 'block' : 'none';
            if (!(this.value === 'existing' && this.checked) && cloneIndicator) {
                cloneIndicator.style.display = 'none';
                cloneIndicator.textContent = '';
            }
            if (this.value === 'new' && this.checked) {
                loadNextDplNo().catch(function(){});
            }
        });
    });

    async function cloneFromExisting(id) {
        const data = await getJson('{{ route("dpl.api.existing", ["id" => 0]) }}'.replace('/0', '/' + encodeURIComponent(id)));
        if (!data) return;

        currentEntryRows = Array.isArray(data.entries) ? data.entries : [];

        if (salesTypeEl) salesTypeEl.value = data.sales_type || 'Tender';
        await loadPoOptions(String(data.production_order_id || ''));
        await loadWorkOrders(String(data.work_order_id || ''));
        if (woEl) woEl.value = String(data.work_order_id || '');

        woData = data.work_order_details || null;
        if (woData) {
            buildSetNos();
            fillWorkOrderFields(woData);
            renderDynamicTable();
        } else if (woEl && woEl.value) {
            await loadWorkOrderDetails();
        }

        const remarksEl = document.querySelector('textarea[name="remarks"]');
        const latestDateEl = document.querySelector('input[name="latest_date"]');
        if (remarksEl) remarksEl.value = data.remarks || '';
        if (latestDateEl) latestDateEl.value = data.latest_date || latestDateEl.value;

        await loadNextDplNo();

        if (cloneIndicator) {
            cloneIndicator.textContent = 'Cloning from ' + (data.dpl_no || ('DPL #' + id));
            cloneIndicator.style.display = 'block';
        }
    }

    document.getElementById('dplForm').addEventListener('submit', function (e) {
        const dateInputs = this.querySelectorAll('.date-input');
        for (let i = 0; i < dateInputs.length; i++) {
            const el = dateInputs[i];
            const original = (el.value || '').trim();
            if (original === '') continue;
            const converted = toStoreDate(original);
            if (!converted) {
                e.preventDefault();
                alert('Invalid date format. Use DD-MM-YYYY.');
                el.focus();
                return;
            }
            el.value = converted;
        }

        const items = tableBody.querySelectorAll('tr');
        if (!items.length) {
            e.preventDefault();
            alert('At least one item row is required.');
            return;
        }
        if (woType === 'Others') {
            const bad = Array.from(items).some(function (tr) {
                const total = parseFloat(tr.querySelector('.total-qty')?.value || 0);
                const done = parseFloat(tr.querySelector('.completed-qty')?.value || 0);
                return done > total;
            });
            if (bad) {
                e.preventDefault();
                alert('Completed Qty must be less than or equal to Total Qty.');
                return;
            }
        }
    });

    (async function init() {
        if (salesTypeEl) salesTypeEl.value = initialSalesType || 'Tender';
        await loadPoOptions(initialPoId);
        await loadWorkOrders(initialWoId);
        if (serverWo && initialWoId) {
            woData = serverWo;
            currentEntryRows = Array.isArray(entryRows) ? entryRows.slice() : [];
            buildSetNos();
            fillWorkOrderFields(woData);
            renderDynamicTable();
        } else if (woEl && woEl.value) {
            await loadWorkOrderDetails();
        } else {
            renderDynamicTable();
        }
        await loadNextDplNo();
    })();
})();
</script>
@endpush
