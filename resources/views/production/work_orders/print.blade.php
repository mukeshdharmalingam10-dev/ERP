<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order - {{ $workOrder->work_order_no }}</title>
    <style>
        /* ── Reset & base ─────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            background: #fff;
            padding: 0;
        }

        /* ── Print-only page setup ────────────────────────────── */
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm 12mm 18mm 12mm;
            }
            body { padding: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }

            /* freeze footer at page bottom */
            .print-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                border-top: 1px solid #999;
                padding-top: 4px;
            }
        }

        /* ── Screen wrapper (shows A4 shadow on screen) ───────── */
        @media screen {
            body { background: #e8e8e8; padding: 30px 0 60px; }
            .a4-wrapper {
                width: 210mm;
                min-height: 297mm;
                background: #fff;
                margin: 0 auto;
                box-shadow: 0 4px 20px rgba(0,0,0,0.25);
                padding: 15mm 12mm 20mm;
                position: relative;
            }
        }
        @media print {
            .a4-wrapper { padding: 0; box-shadow: none; }
        }

        /* ── Toolbar (screen only) ────────────────────────────── */
        .toolbar {
            width: 210mm;
            margin: 0 auto 16px;
            display: flex;
            gap: 10px;
        }
        .btn-print, .btn-close {
            padding: 9px 22px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print { background: #667eea; color: #fff; }
        .btn-print:hover { background: #5567d4; }
        .btn-close  { background: #6c757d; color: #fff; }
        .btn-close:hover { background: #565e64; }

        /* ── Header ───────────────────────────────────────────── */
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .company-block { flex: 1; }
        .company-name {
            font-size: 17px;
            font-weight: 700;
            color: #1a1a1a;
            letter-spacing: 0.5px;
        }
        .company-address {
            font-size: 10.5px;
            color: #444;
            margin-top: 3px;
            line-height: 1.5;
        }
        .doc-title-block {
            text-align: right;
            min-width: 180px;
        }
        .doc-title {
            font-size: 15px;
            font-weight: 700;
            color: #333;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: 2px solid #333;
            padding: 6px 12px;
            display: inline-block;
            margin-bottom: 4px;
        }
        .doc-wo-no {
            font-size: 12px;
            font-weight: 600;
            color: #555;
        }

        /* ── Details grid ─────────────────────────────────────── */
        .details-section {
            margin-bottom: 14px;
        }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #fff;
            background: #444;
            padding: 4px 8px;
            margin-bottom: 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
        }
        .details-table td {
            padding: 6px 9px;
            border: 1px solid #ccc;
            vertical-align: top;
            font-size: 11.5px;
        }
        .details-table td.lbl {
            font-weight: 600;
            background: #f5f5f5;
            width: 18%;
            white-space: nowrap;
            color: #333;
        }
        .details-table td.val {
            color: #1a1a1a;
            width: 32%;
        }

        /* ── Materials table ──────────────────────────────────── */
        .materials-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        .materials-table th {
            background: #444;
            color: #fff;
            padding: 7px 9px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
            border: 1px solid #333;
        }
        .materials-table td {
            padding: 6px 9px;
            border: 1px solid #ccc;
            font-size: 11.5px;
            vertical-align: middle;
        }
        .materials-table tbody tr:nth-child(even) td { background: #fafafa; }
        .materials-table .text-center { text-align: center; }
        .materials-table .text-right  { text-align: right; }
        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 14px;
        }

        /* ── Remarks & signature row ──────────────────────────── */
        .remarks-block {
            margin-top: 12px;
            border: 1px solid #ccc;
            padding: 8px 10px;
            min-height: 48px;
        }
        .remarks-label {
            font-weight: 700;
            font-size: 11px;
            color: #333;
            margin-bottom: 4px;
        }
        .remarks-text {
            font-size: 11.5px;
            color: #444;
            white-space: pre-wrap;
        }

        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            gap: 20px;
        }
        .sig-box {
            flex: 1;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #555;
            margin-bottom: 4px;
        }
        .sig-label {
            font-size: 10.5px;
            color: #555;
            font-weight: 600;
        }
        .sig-name {
            font-size: 11px;
            color: #333;
            margin-top: 2px;
        }

        /* ── Footer ───────────────────────────────────────────── */
        .print-footer {
            margin-top: 20px;
            border-top: 1px solid #bbb;
            padding-top: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>

    {{-- ── Screen-only toolbar ── --}}
    <div class="toolbar no-print">
        <button class="btn-print" onclick="window.print()">
            &#128438; Print / Save as PDF
        </button>
        <button class="btn-close" onclick="window.close()">
            &#10005; Close
        </button>
    </div>

    <div class="a4-wrapper">

        {{-- ═══════════ HEADER ═══════════ --}}
        <div class="print-header">
            <div class="company-block">
                <div class="company-name">
                    {{ $company->company_name ?? config('app.name', 'Company Name') }}
                </div>
                @if($company)
                    <div class="company-address">
                        @if($company->address_line_1){{ $company->address_line_1 }}@endif
                        @if($company->address_line_2), {{ $company->address_line_2 }}@endif
                        @if($company->city)<br>{{ $company->city }}@endif
                        @if($company->state), {{ $company->state }}@endif
                        @if($company->pincode) - {{ $company->pincode }}@endif
                        @if($company->phone)<br>Phone: {{ $company->phone }}@endif
                        @if($company->email) &nbsp;|&nbsp; {{ $company->email }}@endif
                        @if($company->gstin)<br>GSTIN: {{ $company->gstin }}@endif
                    </div>
                @endif
            </div>
            <div class="doc-title-block">
                <div class="doc-title">Production Work Order</div>
                <div class="doc-wo-no">{{ $workOrder->work_order_no }}</div>
            </div>
        </div>

        {{-- ═══════════ WORK ORDER DETAILS ═══════════ --}}
        <div class="details-section">
            <div class="section-title">Work Order Details</div>
            <table class="details-table">
                <tbody>
                    <tr>
                        <td class="lbl">Work Order No</td>
                        <td class="val">{{ $workOrder->work_order_no }}</td>
                        <td class="lbl">Sales Type</td>
                        <td class="val">{{ $workOrder->sales_type ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">PO No</td>
                        <td class="val">{{ $workOrder->production_order_no ?? '—' }}</td>
                        <td class="lbl">Customer PO No</td>
                        <td class="val">{{ $workOrder->customer_po_no ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Title</td>
                        <td class="val" colspan="3">{{ $workOrder->title ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Drawing No</td>
                        <td class="val">{{ $workOrder->drawing_no ?? '—' }}</td>
                        <td class="lbl">Layup Sequence</td>
                        <td class="val">{{ $workOrder->layup_sequence ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Date</td>
                        <td class="val">
                            @if($workOrder->work_order_date)
                                {{ $workOrder->work_order_date->format('d-m-Y') }}
                            @else
                                {{ $workOrder->created_at->format('d-m-Y') }}
                            @endif
                        </td>
                        <td class="lbl">Completion Date</td>
                        <td class="val">
                            {{ $workOrder->completion_date ? $workOrder->completion_date->format('d-m-Y') : '—' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Product Name</td>
                        <td class="val">{{ $workOrder->product_name ?? '—' }}</td>
                        <td class="lbl">Batch No</td>
                        <td class="val">{{ $workOrder->batch_no ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Worker Type</td>
                        <td class="val">{{ $workOrder->worker_type ?? '—' }}</td>
                        <td class="lbl">Nature of Work</td>
                        <td class="val">{{ $workOrder->nature_of_work ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Thickness</td>
                        <td class="val">{{ $workOrder->thickness ?? '—' }}</td>
                        <td class="lbl">Color</td>
                        <td class="val">{{ $workOrder->color ?? '—' }}</td>
                    </tr>
                    @php
                        $qtyType = $workOrder->quantity_type ?? '';
                    @endphp
                    <tr>
                        <td class="lbl">Quantity Type</td>
                        <td class="val">{{ $qtyType ?: '—' }}</td>
                        @if($qtyType === 'Sets')
                            <td class="lbl">No of Sets</td>
                            <td class="val">
                                {{ $workOrder->no_of_sets ?? '—' }}
                                @if($workOrder->starting_set_no && $workOrder->ending_set_no)
                                    &nbsp;<span style="color:#555;">(Set {{ $workOrder->starting_set_no }} – {{ $workOrder->ending_set_no }})</span>
                                @endif
                            </td>
                        @elseif($qtyType === 'Sub Sets')
                            <td class="lbl">Sub Sets / Set</td>
                            <td class="val">{{ $workOrder->no_of_sub_sets_per_set ?? '—' }} &nbsp;<span style="color:#555;">(Total: {{ $workOrder->total_sub_sets ?? '—' }})</span></td>
                        @elseif($qtyType === 'Nos')
                            <td class="lbl">No of Quantity</td>
                            <td class="val">{{ $workOrder->no_of_quantity ?? '—' }}</td>
                        @else
                            <td class="lbl">No of Quantity</td>
                            <td class="val">{{ $workOrder->no_of_quantity ?? '—' }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ═══════════ MATERIAL REQUEST TABLE ═══════════ --}}
        <div class="details-section">
            <div class="section-title">Material Request</div>
            @php $rawMaterials = $workOrder->rawMaterials ?? collect(); @endphp
            @if($rawMaterials->count() > 0)
                <table class="materials-table">
                    <thead>
                        <tr>
                            <th style="width:50px;" class="text-center">S.No</th>
                            <th>Raw Material</th>
                            <th style="width:140px;" class="text-right">Work Order Qty</th>
                            <th style="width:80px;" class="text-center">UOM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rawMaterials as $rmRow)
                            @php
                                $rmName = '—';
                                $uom    = '—';
                                if ($rmRow->rawMaterial) {
                                    $rmName = trim(($rmRow->rawMaterial->name ?? '') . ($rmRow->rawMaterial->grade ? ' - ' . $rmRow->rawMaterial->grade : ''));
                                    if ($rmRow->rawMaterial->unit && $rmRow->rawMaterial->unit->symbol) {
                                        $uom = $rmRow->rawMaterial->unit->symbol;
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $rmName }}</td>
                                <td class="text-right">{{ number_format((float)$rmRow->work_order_quantity, 2) }}</td>
                                <td class="text-center">{{ $uom }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <table class="materials-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:50px;">S.No</th>
                            <th>Raw Material</th>
                            <th class="text-right" style="width:140px;">Work Order Qty</th>
                            <th class="text-center" style="width:80px;">UOM</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="4" class="no-data">No raw materials added to this work order.</td></tr>
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ═══════════ REMARKS ═══════════ --}}
        <div class="remarks-block">
            <div class="remarks-label">Remarks</div>
            <div class="remarks-text">{{ $workOrder->remarks ?: '—' }}</div>
        </div>

        {{-- ═══════════ SIGNATURE STRIP ═══════════ --}}
        <div class="signature-row">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Prepared by</div>
                <div class="sig-name">{{ optional($workOrder->creator)->name ?? '—' }}</div>
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Checked by</div>
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Approved by</div>
            </div>
        </div>

        {{-- ═══════════ FOOTER ═══════════ --}}
        <div class="print-footer">
            <span>{{ $company->company_name ?? config('app.name') }}</span>
            <span>Printed on: {{ now()->format('d-m-Y H:i') }}</span>
        </div>

    </div>{{-- /.a4-wrapper --}}

    <script>
        // Auto-trigger browser print dialog when page loads
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
