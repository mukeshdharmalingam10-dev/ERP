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
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer Order No</label>
                    <input type="text" name="order_no" value="{{ old('order_no', $orderNo) }}" readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Order Date <span style="color: red;">*</span></label>
                    <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer Tender No</label>
                    <input type="text" name="customer_tender_no" value="{{ old('customer_tender_no') }}" readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer PO Date</label>
                    <input type="date" name="customer_po_date" value="{{ old('customer_po_date') }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer Name</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Inspection Agency</label>
                    <input type="text" name="inspection_agency" value="{{ old('inspection_agency') }}" readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer PO No</label>
                    <input type="text" name="customer_po_no" value="{{ old('customer_po_no') }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Items</h3>
                <button type="button" onclick="addItemRow()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
            <div style="padding: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 20%;">Product Name <span style="color:red;">*</span></th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 25%;">Description</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 10%;">Unit <span style="color:red;">*</span></th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600; width: 12%;">Quantity <span style="color:red;">*</span></th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600; width: 15%;">Price per Qty <span style="color:red;">*</span></th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600; width: 12%;">Amount</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 6%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr>
                            <td colspan="7" style="padding: 12px; text-align: center; color: #777;">
                                Click "Add Item" to add products.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- GST / Tax Selection and Amount Summary -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <!-- Left: GST/Tax Selection and Additional Charges -->
                <div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; gap: 8px; color: #333; font-weight: 500; margin-bottom: 15px;">
                            <input type="radio" name="tax_type" value="cgst_sgst" id="tax_type_cgst_sgst" checked onchange="recalculateTax()"
                                   style="width: 18px; height: 18px; cursor: pointer;">
                            <span>CGST and SGST</span>
                        </label>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Freight</label>
                            <input type="number" name="freight" id="freight" step="0.01" min="0" value="0"
                                   oninput="recalculateTax()"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Inspection Charges</label>
                            <input type="number" name="inspection_charges" id="inspection_charges" step="0.01" min="0" value="0"
                                   oninput="recalculateTax()"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        </div>
                    </div>
                </div>

                <!-- Right: IGST Radio and Amount Summary -->
                <div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; gap: 8px; color: #333; font-weight: 500; margin-bottom: 15px;">
                            <input type="radio" name="tax_type" value="igst" id="tax_type_igst" onchange="recalculateTax()"
                                   style="width: 18px; height: 18px; cursor: pointer;">
                            <span>IGST</span>
                        </label>
                    </div>
                    
                    <!-- Amount Summary -->
                    <div>
                        <h4 style="margin: 0 0 15px 0; color: #333; font-size: 16px; font-weight: 600;">Amount</h4>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <label style="color: #333; font-weight: 500;">Total:</label>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <span style="color: #333;">₹</span>
                                    <input type="text" id="total_amount_display" value="0.00" readonly
                                           style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa;">
                                    <input type="hidden" name="total_amount" id="total_amount" value="0">
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <label style="color: #333; font-weight: 500;">GST <span style="color: red;">*</span>:</label>
                                <div style="display: flex; gap: 5px;">
                                    <input type="number" name="gst_percent" id="gst_percent" step="0.01" min="0" max="100" value="0"
                                           oninput="recalculateTax()"
                                           style="width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right;">
                                    <span style="color: #333; padding-top: 8px;">%</span>
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <label style="color: #333; font-weight: 500;">GST Amount:</label>
                                <input type="text" id="gst_amount_display" value="0.00" readonly
                                       style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa;">
                                <input type="hidden" name="gst_amount" id="gst_amount" value="0">
                            </div>
                            
                            <div id="cgst_sgst_section">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <label style="color: #333; font-weight: 500;">CGST:</label>
                                    <input type="text" id="cgst_amount_display" value="0" readonly
                                           style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa;">
                                    <input type="hidden" name="cgst_percent" id="cgst_percent" value="0">
                                    <input type="hidden" name="cgst_amount" id="cgst_amount" value="0">
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <label style="color: #333; font-weight: 500;">SGST:</label>
                                    <input type="text" id="sgst_amount_display" value="0" readonly
                                           style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa;">
                                    <input type="hidden" name="sgst_percent" id="sgst_percent" value="0">
                                    <input type="hidden" name="sgst_amount" id="sgst_amount" value="0">
                                </div>
                            </div>
                            
                            <div id="igst_section" style="display: none; margin-bottom: 12px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <label style="color: #333; font-weight: 500;">IGST:</label>
                                    <input type="text" id="igst_amount_display" value="0" readonly
                                           style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa;">
                                    <input type="hidden" name="igst_percent" id="igst_percent" value="0">
                                    <input type="hidden" name="igst_amount" id="igst_amount" value="0">
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid #eee;">
                                <label style="color: #333; font-weight: 600; font-size: 15px;">Net Amount:</label>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <span style="color: #333;">₹</span>
                                    <input type="text" id="net_amount_display" value="0.00" readonly
                                           style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa; font-weight: 600;">
                                    <input type="hidden" name="net_amount" id="net_amount" value="0">
                                </div>
                            </div>
                            
                            <div style="margin-top: 10px;">
                                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Note:</label>
                                <textarea name="amount_note" id="amount_note" rows="3"
                                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
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
    const tendersMeta = @json($tendersMeta);
    const units = @json($unitsData);
    const products = @json($productsData);

    let itemIndexCounter = 0;
    let selectedItemIndex = null; // index into items array for schedule/amendment
    let schedules = []; // { item_index, product_name, po_sr_no, ordered_qty, quantity, unit_id, unit_symbol, start_date, end_date, inspection_clause }
    let amendments = []; // { item_index, product_name, po_sr_no, ordered_qty, amendment_no, amendment_date, existing_quantity, new_quantity, remarks }

    document.getElementById('tender_id').addEventListener('change', function () {
        const tenderId = this.value;

        // Auto-populate header fields from selected tender (if data available)
        const meta = tendersMeta[tenderId] || {};
        const customerTenderInput = document.querySelector('input[name="customer_tender_no"]');
        const customerPoNoInput = document.querySelector('input[name="customer_po_no"]');
        const customerPoDateInput = document.querySelector('input[name="customer_po_date"]');
        const customerNameInput = document.querySelector('input[name="customer_name"]');
        const inspectionAgencyInput = document.querySelector('input[name="inspection_agency"]');

        if (customerTenderInput) {
            customerTenderInput.value = meta.customer_tender_no || '';
        }
        if (customerPoNoInput) {
            customerPoNoInput.value = meta.customer_po_no || '';
        }
        if (customerPoDateInput) {
            customerPoDateInput.value = meta.customer_po_date || '';
        }
        if (customerNameInput) {
            customerNameInput.value = meta.customer_name || '';
        }
        if (inspectionAgencyInput) {
            inspectionAgencyInput.value = meta.inspection_agency || '';
        }
    });

    function addItemRow() {
        const tbody = document.getElementById('itemsBody');
        const index = itemIndexCounter++;
        
        // Remove empty message if present
        if (tbody.querySelector('tr td[colspan]')) {
            tbody.innerHTML = '';
        }

        const row = document.createElement('tr');
        row.style.borderBottom = '1px solid #dee2e6';
        row.innerHTML = `
            <td style="padding: 10px;">
                <select name="items[${index}][product_id]" id="product_${index}" required
                        onchange="onProductSelect(${index})"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;">
                    <option value="">Select Product</option>
                    ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </td>
            <td style="padding: 10px;">
                <textarea name="items[${index}][description]" rows="2" placeholder="Enter description"
                          style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;"></textarea>
            </td>
            <td style="padding: 10px;">
                <input type="text" name="items[${index}][unit_symbol]" id="unit_symbol_${index}" readonly
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; background:#f8f9fa;">
                <input type="hidden" name="items[${index}][unit_id]" id="unit_id_${index}" value="">
            </td>
            <td style="padding: 10px; text-align: right;">
                <input type="number" name="items[${index}][ordered_qty]" value="0" step="0.01" min="0.01" required
                       oninput="recalcItemAmount(${index})"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; text-align: right;">
            </td>
            <td style="padding: 10px; text-align: right;">
                <input type="number" name="items[${index}][unit_price]" value="0" step="0.01" min="0" required
                       oninput="recalcItemAmount(${index})"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; text-align: right;">
            </td>
            <td style="padding: 10px; text-align: right; color:#333;">
                <span id="item_amount_display_${index}">0.00</span>
                <input type="hidden" name="items[${index}][line_amount]" id="item_amount_${index}" value="0">
            </td>
            <td style="padding: 10px; text-align: center;">
                <button type="button" onclick="removeItemRow(this)" style="padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function onProductSelect(index) {
        const productSelect = document.getElementById(`product_${index}`);
        const productId = productSelect.value;
        const product = products.find(p => p.id == productId);
        
        if (product) {
            document.getElementById(`unit_id_${index}`).value = product.unit_id;
            document.getElementById(`unit_symbol_${index}`).value = product.unit_symbol;
        } else {
            document.getElementById(`unit_id_${index}`).value = '';
            document.getElementById(`unit_symbol_${index}`).value = '';
        }
        recalcItemAmount(index);
    }

    function removeItemRow(button) {
        const row = button.closest('tr');
        row.remove();
        
        const tbody = document.getElementById('itemsBody');
        if (tbody.children.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="padding: 12px; text-align: center; color: #777;">Click "Add Item" to add products.</td></tr>`;
        }
        recalculateTotal();
    }

    function onItemSelected(index) {
        selectedItemIndex = index;
    }

    function getSelectedItem() {
        // Option 1: User selected a row - return that item
        if (selectedItemIndex !== null) {
            const productSelect = document.querySelector(`select[name="items[${selectedItemIndex}][product_id]"]`);
            const qtyInput = document.querySelector(`input[name="items[${selectedItemIndex}][ordered_qty]"]`);
            const unitSymbolInput = document.querySelector(`input[name="items[${selectedItemIndex}][unit_symbol]"]`);
            
            if (!productSelect || !productSelect.value) {
                alert('Please select a product first.');
                return null;
            }
            
            const product = products.find(p => p.id == productSelect.value);
            if (!product) {
                alert('Invalid product selection.');
                return null;
            }
            
            return {
                index: selectedItemIndex,
                product_name: product.name,
                po_sr_no: '',
                ordered_qty: parseFloat(qtyInput ? qtyInput.value : '0') || 0,
                unit_symbol: unitSymbolInput ? unitSymbolInput.value : product.unit_symbol,
            };
        }
        // Option 2: No row selected - return null to show dropdown in modal
        return null;
    }

    function getAvailableItems() {
        const tbody = document.getElementById('itemsBody');
        const rows = tbody.querySelectorAll('tr');
        const availableItems = [];
        
        rows.forEach((row, index) => {
            const productSelect = row.querySelector(`select[name="items[${index}][product_id]"]`);
            const qtyInput = row.querySelector(`input[name="items[${index}][ordered_qty]"]`);
            const unitSymbolInput = row.querySelector(`input[name="items[${index}][unit_symbol]"]`);
            
            if (productSelect && productSelect.value) {
                const product = products.find(p => p.id == productSelect.value);
                if (product) {
                    availableItems.push({
                        index: index,
                        product_name: product.name,
                        po_sr_no: '',
                        ordered_qty: parseFloat(qtyInput ? qtyInput.value : '0') || 0,
                        unit_symbol: unitSymbolInput ? unitSymbolInput.value : product.unit_symbol,
                    });
                }
            }
        });
        
        return availableItems;
    }

    function openSchedulePopup() {
        const item = getSelectedItem(); // May be null if no row selected
        const availableItems = getAvailableItems();
        if (availableItems.length === 0) {
            alert('Please add at least one product item first.');
            return;
        }
        window.CustomerOrderScheduleModal.open(item, availableItems, schedules, units, function (updatedSchedules) {
            schedules = updatedSchedules;
            renderSchedules();
            syncScheduleHiddenInputs();
        });
    }

    function openAmendmentPopup() {
        const item = getSelectedItem(); // May be null if no row selected
        const availableItems = getAvailableItems();
        if (availableItems.length === 0) {
            alert('Please add at least one product item first.');
            return;
        }
        window.CustomerOrderAmendmentModal.open(item, availableItems, amendments, function (updatedAmendments) {
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
                    <input type="hidden" name="${base}[po_sr_no]" value="${s.po_sr_no || ''}">
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
                    <input type="hidden" name="${base}[po_sr_no]" value="${a.po_sr_no || ''}">
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

    function recalcItemAmount(index) {
        const qtyInput = document.querySelector(`input[name="items[${index}][ordered_qty]"]`);
        const priceInput = document.querySelector(`input[name="items[${index}][unit_price]"]`);
        const qty = parseFloat(qtyInput ? qtyInput.value : '0') || 0;
        const price = parseFloat(priceInput ? priceInput.value : '0') || 0;
        const amount = qty * price;
        const amountField = document.getElementById(`item_amount_${index}`);
        const amountDisplay = document.getElementById(`item_amount_display_${index}`);
        if (amountField) amountField.value = amount.toFixed(2);
        if (amountDisplay) amountDisplay.textContent = amount.toFixed(2);
        recalculateTax();
    }

    function recalculateTotal() {
        recalculateTax();
    }

    function toggleShowMore(id) {
        const fullDiv = document.getElementById(id + '_full');
        if (fullDiv) {
            fullDiv.style.display = fullDiv.style.display === 'none' ? 'block' : 'none';
        }
    }

    function recalculateTax() {
        // Calculate Total from all items
        let total = 0;
        const itemAmountInputs = document.querySelectorAll('input[id^="item_amount_"]');
        itemAmountInputs.forEach(input => {
            total += parseFloat(input.value || '0') || 0;
        });
        
        document.getElementById('total_amount').value = total.toFixed(2);
        document.getElementById('total_amount_display').value = total.toFixed(2);
        
        // Get tax type
        const taxType = document.querySelector('input[name="tax_type"]:checked')?.value || 'cgst_sgst';
        const gstPercent = parseFloat(document.getElementById('gst_percent').value || '0') || 0;
        const freight = parseFloat(document.getElementById('freight').value || '0') || 0;
        const inspectionCharges = parseFloat(document.getElementById('inspection_charges').value || '0') || 0;
        
        let cgstPercent = 0;
        let sgstPercent = 0;
        let cgstAmount = 0;
        let sgstAmount = 0;
        let igstPercent = 0;
        let igstAmount = 0;
        
        if (taxType === 'cgst_sgst') {
            // Show CGST+SGST section, hide IGST
            document.getElementById('cgst_sgst_section').style.display = 'block';
            document.getElementById('igst_section').style.display = 'none';
            
            // Split GST% into two halves
            cgstPercent = gstPercent / 2;
            sgstPercent = gstPercent / 2;
            cgstAmount = (total * cgstPercent) / 100;
            sgstAmount = (total * sgstPercent) / 100;
            
            document.getElementById('cgst_percent').value = cgstPercent.toFixed(2);
            document.getElementById('cgst_amount').value = cgstAmount.toFixed(2);
            document.getElementById('cgst_amount_display').value = cgstAmount.toFixed(2);
            
            document.getElementById('sgst_percent').value = sgstPercent.toFixed(2);
            document.getElementById('sgst_amount').value = sgstAmount.toFixed(2);
            document.getElementById('sgst_amount_display').value = sgstAmount.toFixed(2);
            
            // Clear IGST
            document.getElementById('igst_percent').value = '0';
            document.getElementById('igst_amount').value = '0';
            document.getElementById('igst_amount_display').value = '0';
        } else {
            // Show IGST section, hide CGST+SGST
            document.getElementById('cgst_sgst_section').style.display = 'none';
            document.getElementById('igst_section').style.display = 'block';
            
            // IGST% = GST%
            igstPercent = gstPercent;
            igstAmount = (total * igstPercent) / 100;
            
            document.getElementById('igst_percent').value = igstPercent.toFixed(2);
            document.getElementById('igst_amount').value = igstAmount.toFixed(2);
            document.getElementById('igst_amount_display').value = igstAmount.toFixed(2);
            
            // Clear CGST+SGST
            document.getElementById('cgst_percent').value = '0';
            document.getElementById('cgst_amount').value = '0';
            document.getElementById('cgst_amount_display').value = '0';
            document.getElementById('sgst_percent').value = '0';
            document.getElementById('sgst_amount').value = '0';
            document.getElementById('sgst_amount_display').value = '0';
        }
        
        // Calculate total GST amount (CGST+SGST or IGST)
        const totalGstAmount = cgstAmount + sgstAmount + igstAmount;
        document.getElementById('gst_amount').value = totalGstAmount.toFixed(2);
        document.getElementById('gst_amount_display').value = totalGstAmount.toFixed(2);
        
        // Calculate Net Amount = Total + All tax amounts + Freight + Inspection Charges
        const netAmount = total + cgstAmount + sgstAmount + igstAmount + freight + inspectionCharges;
        document.getElementById('net_amount').value = netAmount.toFixed(2);
        document.getElementById('net_amount_display').value = netAmount.toFixed(2);
    }

    // Initialize tax calculation on page load
    document.addEventListener('DOMContentLoaded', function() {
        recalculateTax();
    });
</script>
@endpush
@endsection


