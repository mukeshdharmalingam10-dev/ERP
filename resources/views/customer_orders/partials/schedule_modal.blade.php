<div id="scheduleModal" class="modal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; width: 900px; max-width: 95%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        <div style="padding: 15px 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; color: #333;">Schedule</h4>
            <button type="button" onclick="CustomerOrderScheduleModal.close()" style="background: transparent; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 20px;">
            <div id="scheduleHeader" style="margin-bottom: 15px; background: #f8f9fa; padding: 12px; border-radius: 5px; font-size: 14px;">
                <!-- Filled dynamically -->
            </div>

            <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                <button type="button" onclick="CustomerOrderScheduleModal.addRow()" style="padding: 6px 12px; background: #28a745; color: white; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>

            <div style="overflow-x: auto; max-height: 350px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 8px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">Start Date</th>
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">End Date</th>
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">Inspection Clause</th>
                            <th style="padding: 8px; text-align: center; color: #333; font-weight: 600;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleModalBody">
                        <!-- rows -->
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 15px; text-align: right; font-size: 13px;">
                <span id="scheduleTotalInfo" style="color: #333;"></span>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="CustomerOrderScheduleModal.close()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                    Cancel
                </button>
                <button type="button" onclick="CustomerOrderScheduleModal.save()" style="padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.CustomerOrderScheduleModal = (function () {
        let currentItem = null;
        let schedulesRef = [];
        let unitsRef = [];
        let onSaveCb = null;

        function open(item, allSchedules, units, onSave) {
            currentItem = item;
            schedulesRef = allSchedules.filter(s => s.item_index === item.index);
            unitsRef = units;
            onSaveCb = onSave;

            document.getElementById('scheduleHeader').innerHTML = `
                <strong>Tender No:</strong> ${document.getElementById('tender_id').selectedOptions[0].text} &nbsp; | &nbsp;
                <strong>Product:</strong> ${item.product_name} &nbsp; | &nbsp;
                <strong>PO SR No:</strong> ${item.po_sr_no || '-'} &nbsp; | &nbsp;
                <strong>Ordered Qty:</strong> ${item.ordered_qty}
            `;

            renderRows();
            document.getElementById('scheduleModal').style.display = 'flex';
        }

        function close() {
            document.getElementById('scheduleModal').style.display = 'none';
        }

        function renderRows() {
            const tbody = document.getElementById('scheduleModalBody');
            tbody.innerHTML = '';
            if (schedulesRef.length === 0) {
                addRow();
                return;
            }

            schedulesRef.forEach((s, idx) => appendRowElement(s, idx));
            updateTotal();
        }

        function appendRowElement(s, idx) {
            const tbody = document.getElementById('scheduleModalBody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td style="padding: 6px;">
                    <input type="number" step="0.01" min="0" value="${s.quantity || ''}" onchange="CustomerOrderScheduleModal.updateField(${idx}, 'quantity', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                </td>
                <td style="padding: 6px;">
                    <select onchange="CustomerOrderScheduleModal.updateField(${idx}, 'unit_id', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                        ${unitsRef.map(u => `<option value="${u.id}" ${s.unit_id == u.id ? 'selected' : ''}>${u.symbol}</option>`).join('')}
                    </select>
                </td>
                <td style="padding: 6px;">
                    <input type="date" value="${s.start_date || ''}" onchange="CustomerOrderScheduleModal.updateField(${idx}, 'start_date', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 6px;">
                    <input type="date" value="${s.end_date || ''}" onchange="CustomerOrderScheduleModal.updateField(${idx}, 'end_date', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 6px;">
                    <input type="text" value="${s.inspection_clause || ''}" onchange="CustomerOrderScheduleModal.updateField(${idx}, 'inspection_clause', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 6px; text-align: center;">
                    <button type="button" onclick="CustomerOrderScheduleModal.removeRow(${idx})" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        }

        function addRow() {
            const newRow = {
                item_index: currentItem.index,
                product_name: currentItem.product_name,
                po_sr_no: currentItem.po_sr_no,
                ordered_qty: currentItem.ordered_qty,
                quantity: '',
                unit_id: unitsRef.length ? unitsRef[0].id : null,
                unit_symbol: unitsRef.length ? unitsRef[0].symbol : '',
                start_date: '',
                end_date: '',
                inspection_clause: '',
            };
            schedulesRef.push(newRow);
            renderRows();
        }

        function removeRow(idx) {
            schedulesRef.splice(idx, 1);
            renderRows();
        }

        function updateField(idx, field, value) {
            schedulesRef[idx][field] = value;
            if (field === 'unit_id') {
                const u = unitsRef.find(x => x.id == value);
                schedulesRef[idx].unit_symbol = u ? u.symbol : '';
            }
            if (field === 'quantity') {
                updateTotal();
            }
        }

        function updateTotal() {
            const total = schedulesRef.reduce((sum, s) => sum + (parseFloat(s.quantity || '0') || 0), 0);
            document.getElementById('scheduleTotalInfo').innerText =
                `Total scheduled quantity: ${total} / Ordered quantity: ${currentItem.ordered_qty}`;
        }

        function save() {
            // Validation: required fields + qty <= ordered
            let total = 0;
            for (const s of schedulesRef) {
                const q = parseFloat(s.quantity || '0') || 0;
                total += q;
                if (!s.start_date || !s.end_date) {
                    alert('Start Date and End Date are required for all schedule rows.');
                    return;
                }
            }
            if (total > currentItem.ordered_qty) {
                alert(`Total scheduled quantity (${total}) cannot exceed ordered quantity (${currentItem.ordered_qty}) for this product.`);
                return;
            }

            // Merge back into global schedules array
            const others = window.schedules.filter(s => s.item_index !== currentItem.index);
            window.schedules = others.concat(schedulesRef);

            if (typeof onSaveCb === 'function') {
                onSaveCb(window.schedules);
            }
            close();
        }

        return {
            open,
            close,
            addRow,
            removeRow,
            updateField,
            save,
        };
    })();
</script>
@endpush
*** End Patch
``` -->
*** End PatchEassistant to=functions.apply_patchusseglacommentary 	RTLUassistant to=functions.apply_patchมัครassistant to=functions.apply_patchಮಂತ್ರಿ to=functions.apply_patch_TYPED_ARGUMENTS_DISABLEDCRIPTOR assistant to=functions.apply_patchquotelev_OUTPUT_BOUNDARY_JSON  поскольassistant to=functions.apply_patchзаараassistant to=functions.apply_patchจ๊กassistant to=functions.apply_patch 博悦

