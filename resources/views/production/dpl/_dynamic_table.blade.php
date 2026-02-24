<div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:20px; padding:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:10px; flex-wrap:wrap;">
        <h3 style="margin:0; font-size:16px; color:#333;">Quantity Selection Child Table</h3>
        @if(!$viewOnly)
            <button type="button" id="addDynamicRow" style="padding:8px 14px; background:#667eea; color:white; border:none; border-radius:6px; cursor:pointer;">
                <i class="fas fa-plus"></i> Add Row
            </button>
        @endif
    </div>
    <div style="overflow-x:auto; border:1px solid #dee2e6; border-radius:8px;">
        <table id="dynamicTable" style="width:100%; border-collapse:collapse; min-width:960px;">
            <thead id="dynamicTableHead"></thead>
            <tbody id="dynamicTableBody"></tbody>
        </table>
    </div>
    <div id="dynamicHint" style="margin-top:8px; color:#666; font-size:12px;">Select Work Order to generate dynamic headers.</div>
</div>

