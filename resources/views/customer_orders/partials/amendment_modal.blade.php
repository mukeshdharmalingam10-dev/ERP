<div id="amendmentModal" class="modal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; width: 900px; max-width: 95%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        <div style="padding: 15px 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; color: #333;">Amendments</h4>
            <button type="button" onclick="CustomerOrderAmendmentModal.close()" style="background: transparent; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 20px;">
            <div id="amendmentHeader" style="margin-bottom: 15px; background: #f8f9fa; padding: 12px; border-radius: 5px; font-size: 14px;">
                <!-- Filled dynamically -->
            </div>

            <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                <button type="button" onclick="CustomerOrderAmendmentModal.addRow()" style="padding: 6px 12px; background: #28a745; color: white; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>

            <div style="overflow-x: auto; max-height: 350px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">Amendment No</th>
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">Amendment Date</th>
                            <th style="padding: 8px; text-align: right; color: #333; font-weight: 600;">Existing Qty</th>
                            <th style="padding: 8px; text-align: right; color: #333; font-weight: 600;">New Qty</th>
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">Remarks</th>
                            <th style="padding: 8px; text-align: center; color: #333; font-weight: 600;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="amendmentModalBody">
                        <!-- rows -->
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="CustomerOrderAmendmentModal.close()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                    Cancel
                </button>
                <button type="button" onclick="CustomerOrderAmendmentModal.save()" style="padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.CustomerOrderAmendmentModal = (function () {
        let currentItem = null;
        let amendmentsRef = [];
        let onSaveCb = null;

        function open(item, allAmendments, onSave) {
            currentItem = item;
            amendmentsRef = allAmendments.filter(a => a.item_index === item.index);
            onSaveCb = onSave;

            document.getElementById('amendmentHeader').innerHTML = `
                <strong>Tender No:</strong> ${document.getElementById('tender_id').selectedOptions[0].text} &nbsp; | &nbsp;
                <strong>Product:</strong> ${item.product_name} &nbsp; | &nbsp;
                <strong>PO SR No:</strong> ${item.po_sr_no || '-'} &nbsp; | &nbsp;
                <strong>Ordered Qty:</strong> ${item.ordered_qty}
            `;

            renderRows();
            document.getElementById('amendmentModal').style.display = 'flex';
        }

        function close() {
            document.getElementById('amendmentModal').style.display = 'none';
        }

        function renderRows() {
            const tbody = document.getElementById('amendmentModalBody');
            tbody.innerHTML = '';
            if (amendmentsRef.length === 0) {
                addRow();
                return;
            }
            amendmentsRef.forEach((a, idx) => appendRowElement(a, idx));
        }

        function appendRowElement(a, idx) {
            const tbody = document.getElementById('amendmentModalBody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td style="padding: 6px;">
                    <input type="text" value="${a.amendment_no || ''}" onchange="CustomerOrderAmendmentModal.updateField(${idx}, 'amendment_no', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 6px;">
                    <input type="date" value="${a.amendment_date || ''}" onchange="CustomerOrderAmendmentModal.updateField(${idx}, 'amendment_date', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 6px;">
                    <input type="number" step="0.01" min="0" value="${a.existing_quantity || ''}" onchange="CustomerOrderAmendmentModal.updateField(${idx}, 'existing_quantity', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                </td>
                <td style="padding: 6px;">
                    <input type="number" step="0.01" min="0" value="${a.new_quantity || ''}" onchange="CustomerOrderAmendmentModal.updateField(${idx}, 'new_quantity', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                </td>
                <td style="padding: 6px;">
                    <input type="text" value="${a.remarks || ''}" onchange="CustomerOrderAmendmentModal.updateField(${idx}, 'remarks', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 6px; text-align: center;">
                    <button type="button" onclick="CustomerOrderAmendmentModal.removeRow(${idx})" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
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
                amendment_no: '',
                amendment_date: '',
                existing_quantity: '',
                new_quantity: '',
                existing_info: '',
                new_info: '',
                remarks: '',
            };
            amendmentsRef.push(newRow);
            renderRows();
        }

        function removeRow(idx) {
            amendmentsRef.splice(idx, 1);
            renderRows();
        }

        function updateField(idx, field, value) {
            amendmentsRef[idx][field] = value;
        }

        function save() {
            for (const a of amendmentsRef) {
                if (!a.amendment_date) {
                    alert('Amendment Date is required for all amendment rows.');
                    return;
                }
                if (!a.new_quantity) {
                    alert('New Quantity is required for all amendment rows.');
                    return;
                }
            }
            const others = window.amendments.filter(a => a.item_index !== currentItem.index);
            window.amendments = others.concat(amendmentsRef);
            if (typeof onSaveCb === 'function') {
                onSaveCb(window.amendments);
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


