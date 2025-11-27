@extends('layouts.dashboard')

@section('title', 'Create Customer Order - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Create Customer Order</h2>
        <a href="{{ route('customer-orders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customer-orders.store') }}" method="POST" enctype="multipart/form-data" id="customerOrderForm">
        @csrf

        <!-- Header Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Customer Order Details</h3>
            </div>
            <div style="padding: 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Order No</label>
                    <input type="text" name="order_no" value="{{ old('order_no', $orderNo) }}" readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Order Date <span style="color: red;">*</span></label>
                    <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Tender No <span style="color: red;">*</span></label>
                    <select name="tender_id" id="tender_id" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        <option value="">Select Tender</option>
                        @foreach($tenders as $tender)
                            <option value="{{ $tender->id }}" {{ old('tender_id') == $tender->id ? 'selected' : '' }}>
                                {{ $tender->tender_no }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Items (from Tender)</h3>
            </div>
            <div style="padding: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Title</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO SR No</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Ordered Qty</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Select</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr>
                            <td colspan="5" style="padding: 12px; text-align: center; color: #777;">
                                Select a Tender to load items.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Schedule Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Schedule</h3>
                <button type="button" onclick="openSchedulePopup()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add / Edit Schedule
                </button>
            </div>
            <div style="padding: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Product</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO SR No</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Start Date</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">End Date</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Inspection Clause</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleGrid">
                        <tr>
                            <td colspan="7" style="padding: 12px; text-align: center; color: #777;">
                                No schedule lines added.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Amendment Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Amendments</h3>
                <button type="button" onclick="openAmendmentPopup()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add / Edit Amendment
                </button>
            </div>
            <div style="padding: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Product</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO SR No</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Amendment No</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Amendment Date</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Existing Qty</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">New Qty</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="amendmentGrid">
                        <tr>
                            <td colspan="7" style="padding: 12px; text-align: center; color: #777;">
                                No amendments added.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hidden containers for schedules and amendments arrays -->
        <div id="hiddenScheduleInputs"></div>
        <div id="hiddenAmendmentInputs"></div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-save"></i> Submit
            </button>
        </div>
    </form>
</div>

@include('customer_orders.partials.schedule_modal')
@include('customer_orders.partials.amendment_modal')

@push('scripts')
<script>
    const tendersData = @json($tendersData);
    const units = @json($unitsData);

    let selectedItemIndex = null; // index into items array for schedule/amendment
    let schedules = []; // { item_index, product_name, po_sr_no, ordered_qty, quantity, unit_id, unit_symbol, start_date, end_date, inspection_clause }
    let amendments = []; // { item_index, product_name, po_sr_no, ordered_qty, amendment_no, amendment_date, existing_quantity, new_quantity, remarks }

    document.getElementById('tender_id').addEventListener('change', function () {
        const tenderId = this.value;
        const tbody = document.getElementById('itemsBody');
        tbody.innerHTML = '';
        if (!tenderId || !tendersData[tenderId] || tendersData[tenderId].length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" style="padding: 12px; text-align: center; color: #777;">No items found for this tender.</td></tr>`;
            return;
        }

        tendersData[tenderId].forEach((item, index) => {
            const row = document.createElement('tr');
            row.style.borderBottom = '1px solid #dee2e6';
            row.innerHTML = `
                <td style="padding: 10px; color: #333;">${item.title}</td>
                <td style="padding: 10px;">
                    <input type="text" name="items[${index}][po_sr_no]" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;">
                </td>
                <td style="padding: 10px; text-align: right;">
                    <input type="number" name="items[${index}][ordered_qty]" value="${item.qty}" step="0.01" min="0" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; text-align: right;">
                </td>
                <td style="padding: 10px; color: #333;">${item.unit || ''}</td>
                <td style="padding: 10px; text-align: center;">
                    <input type="hidden" name="items[${index}][tender_item_id]" value="${item.id}">
                    <input type="radio" name="selected_item" value="${index}" onclick="onItemSelected(${index})">
                </td>
            `;
            tbody.appendChild(row);
        });
    });

    function onItemSelected(index) {
        selectedItemIndex = index;
    }

    function getSelectedItem() {
        if (selectedItemIndex === null) {
            alert('Please select a product row in the Items grid first.');
            return null;
        }
        const tenderId = document.getElementById('tender_id').value;
        if (!tenderId || !tendersData[tenderId]) {
            alert('Please select a Tender first.');
            return null;
        }
        const item = tendersData[tenderId][selectedItemIndex];
        if (!item) {
            alert('Invalid product selection.');
            return null;
        }
        const poInput = document.querySelector(`input[name="items[${selectedItemIndex}][po_sr_no]"]`);
        const qtyInput = document.querySelector(`input[name="items[${selectedItemIndex}][ordered_qty]"]`);
        return {
            index: selectedItemIndex,
            product_name: item.title,
            po_sr_no: poInput ? poInput.value : '',
            ordered_qty: parseFloat(qtyInput ? qtyInput.value : '0') || 0,
            unit_symbol: item.unit || '',
        };
    }

    function openSchedulePopup() {
        const item = getSelectedItem();
        if (!item) return;
        window.CustomerOrderScheduleModal.open(item, schedules, units, function (updatedSchedules) {
            schedules = updatedSchedules;
            renderSchedules();
            syncScheduleHiddenInputs();
        });
    }

    function openAmendmentPopup() {
        const item = getSelectedItem();
        if (!item) return;
        window.CustomerOrderAmendmentModal.open(item, amendments, function (updatedAmendments) {
            amendments = updatedAmendments;
            renderAmendments();
            syncAmendmentHiddenInputs();
        });
    }

    function renderSchedules() {
        const tbody = document.getElementById('scheduleGrid');
        tbody.innerHTML = '';
        if (schedules.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="padding: 12px; text-align: center; color: #777;">No schedule lines added.</td></tr>`;
            return;
        }
        schedules.forEach((s) => {
            const row = document.createElement('tr');
            row.style.borderBottom = '1px solid #dee2e6';
            row.innerHTML = `
                <td style="padding: 10px; color: #333;">${s.product_name}</td>
                <td style="padding: 10px; color: #333;">${s.po_sr_no || ''}</td>
                <td style="padding: 10px; text-align: right; color: #333;">${s.quantity}</td>
                <td style="padding: 10px; color: #333;">${s.unit_symbol || ''}</td>
                <td style="padding: 10px; color: #333;">${s.start_date}</td>
                <td style="padding: 10px; color: #333;">${s.end_date}</td>
                <td style="padding: 10px; color: #333;">${s.inspection_clause || ''}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function renderAmendments() {
        const tbody = document.getElementById('amendmentGrid');
        tbody.innerHTML = '';
        if (amendments.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="padding: 12px; text-align: center; color: #777;">No amendments added.</td></tr>`;
            return;
        }
        amendments.forEach((a) => {
            const row = document.createElement('tr');
            row.style.borderBottom = '1px solid #dee2e6';
            row.innerHTML = `
                <td style="padding: 10px; color: #333;">${a.product_name}</td>
                <td style="padding: 10px; color: #333;">${a.po_sr_no || ''}</td>
                <td style="padding: 10px; color: #333;">${a.amendment_no || ''}</td>
                <td style="padding: 10px; color: #333;">${a.amendment_date}</td>
                <td style="padding: 10px; text-align: right; color: #333;">${a.existing_quantity || ''}</td>
                <td style="padding: 10px; text-align: right; color: #333;">${a.new_quantity}</td>
                <td style="padding: 10px; color: #333;">${a.remarks || ''}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function syncScheduleHiddenInputs() {
        const container = document.getElementById('hiddenScheduleInputs');
        container.innerHTML = '';
        schedules.forEach((s, index) => {
            const base = `schedules[${index}]`;
            container.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="${base}[item_index]" value="${s.item_index}">
                <input type="hidden" name="${base}[quantity]" value="${s.quantity}">
                <input type="hidden" name="${base}[unit_id]" value="${s.unit_id}">
                <input type="hidden" name="${base}[start_date]" value="${s.start_date}">
                <input type="hidden" name="${base}[end_date]" value="${s.end_date}">
                <input type="hidden" name="${base}[inspection_clause]" value="${s.inspection_clause || ''}">
            `);
        });
    }

    function syncAmendmentHiddenInputs() {
        const container = document.getElementById('hiddenAmendmentInputs');
        container.innerHTML = '';
        amendments.forEach((a, index) => {
            const base = `amendments[${index}]`;
            container.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="${base}[item_index]" value="${a.item_index}">
                <input type="hidden" name="${base}[amendment_no]" value="${a.amendment_no || ''}">
                <input type="hidden" name="${base}[amendment_date]" value="${a.amendment_date}">
                <input type="hidden" name="${base}[existing_quantity]" value="${a.existing_quantity || ''}">
                <input type="hidden" name="${base}[new_quantity]" value="${a.new_quantity}">
                <input type="hidden" name="${base}[existing_info]" value="${a.existing_info || ''}">
                <input type="hidden" name="${base}[new_info]" value="${a.new_info || ''}">
                <input type="hidden" name="${base}[remarks]" value="${a.remarks || ''}">
            `);
        });
    }
</script>
@endpush
@endsection


