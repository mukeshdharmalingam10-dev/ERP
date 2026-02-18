<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Work Order - {{ $workOrder->work_order_no }}</title>
    <style>
        /* ── Base ──────────────────────────────────────────── */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            padding: 0;
        }

        /* ── Page wrapper ──────────────────────────────────── */
        .page {
            width: 100%;
            padding: 10px 14px;
        }

        /* ── Header ────────────────────────────────────────── */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header-table td { vertical-align: top; }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #111;
        }
        .company-address {
            font-size: 10px;
            color: #444;
            margin-top: 3px;
            line-height: 1.5;
        }
        .doc-title-cell { text-align: right; width: 40%; }
        .doc-title-box {
            display: inline-block;
            border: 2px solid #333;
            padding: 5px 10px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .doc-wo-no {
            font-size: 11px;
            font-weight: bold;
            color: #444;
        }

        /* ── Section title bar ─────────────────────────────── */
        .section-bar {
            background: #444;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 8px;
            margin-bottom: 0;
        }

        /* ── Details grid table ────────────────────────────── */
        .details-tbl {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
            margin-bottom: 12px;
        }
        .details-tbl td {
            padding: 5px 8px;
            border: 1px solid #ccc;
            font-size: 10.5px;
            vertical-align: top;
        }
        .details-tbl td.lbl {
            background: #f5f5f5;
            font-weight: bold;
            width: 17%;
            white-space: nowrap;
            color: #333;
        }
        .details-tbl td.val {
            color: #1a1a1a;
            width: 33%;
        }

        /* ── Materials table ───────────────────────────────── */
        .mat-tbl {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .mat-tbl th {
            background: #444;
            color: #fff;
            padding: 6px 8px;
            font-size: 10px;
            font-weight: bold;
            text-align: left;
            border: 1px solid #333;
        }
        .mat-tbl td {
            padding: 5px 8px;
            border: 1px solid #ccc;
            font-size: 10.5px;
            vertical-align: middle;
        }
        .mat-tbl tbody tr:nth-child(even) td { background: #fafafa; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .no-data     { text-align: center; color: #888; font-style: italic; padding: 10px; }

        /* ── Remarks ───────────────────────────────────────── */
        .remarks-box {
            border: 1px solid #ccc;
            padding: 7px 9px;
            min-height: 40px;
            margin-bottom: 14px;
            font-size: 10.5px;
        }
        .remarks-label {
            font-weight: bold;
            font-size: 10px;
            color: #333;
            margin-bottom: 3px;
        }

        /* ── Signature row ─────────────────────────────────── */
        .sig-tbl {
            width: 100%;
            margin-top: 28px;
            margin-bottom: 16px;
        }
        .sig-tbl td {
            text-align: center;
            width: 33%;
            padding: 0 10px;
            vertical-align: bottom;
        }
        .sig-line {
            border-top: 1px solid #555;
            margin-bottom: 3px;
        }
        .sig-label { font-size: 10px; font-weight: bold; color: #555; }
        .sig-name  { font-size: 10.5px; color: #333; margin-top: 2px; }

        /* ── Footer ────────────────────────────────────────── */
        .footer-bar {
            border-top: 1px solid #bbb;
            padding-top: 4px;
            font-size: 9.5px;
            color: #666;
        }
        .footer-bar table { width: 100%; }
        .footer-bar td { padding: 0; }
    </style>
</head>
<body>
<div class="page">

    {{-- ═══════════ HEADER ═══════════ --}}
    <table class="header-table" cellspacing="0" cellpadding="0">
        <tr>
            <td>
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
            </td>
            <td class="doc-title-cell">
                <div class="doc-title-box">Production Work Order</div><br>
                <span class="doc-wo-no">{{ $workOrder->work_order_no }}</span>
            </td>
        </tr>
    </table>

    {{-- ═══════════ WORK ORDER DETAILS ═══════════ --}}
    <div class="section-bar">Work Order Details</div>
    <table class="details-tbl" cellspacing="0" cellpadding="0">
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
            <td class="lbl">Product Name</td>
            <td class="val">{{ $workOrder->product_name ?? '—' }}</td>
            <td class="lbl">Batch No</td>
            <td class="val">{{ $workOrder->batch_no ?? '—' }}</td>
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
        @php $qtyType = $workOrder->quantity_type ?? ''; @endphp
        <tr>
            <td class="lbl">Quantity Type</td>
            <td class="val">{{ $qtyType ?: '—' }}</td>
            @if($qtyType === 'Sets')
                <td class="lbl">No of Sets</td>
                <td class="val">
                    {{ $workOrder->no_of_sets ?? '—' }}
                    @if($workOrder->starting_set_no && $workOrder->ending_set_no)
                        (Set {{ $workOrder->starting_set_no }} – {{ $workOrder->ending_set_no }})
                    @endif
                </td>
            @elseif($qtyType === 'Sub Sets')
                <td class="lbl">Sub Sets / Set</td>
                <td class="val">{{ $workOrder->no_of_sub_sets_per_set ?? '—' }}
                    (Total: {{ $workOrder->total_sub_sets ?? '—' }})</td>
            @else
                <td class="lbl">No of Quantity</td>
                <td class="val">{{ $workOrder->no_of_quantity ?? '—' }}</td>
            @endif
        </tr>
    </table>

    {{-- ═══════════ MATERIAL REQUEST TABLE ═══════════ --}}
    <div class="section-bar">Material Request</div>
    @php $rawMaterials = $workOrder->rawMaterials ?? collect(); @endphp
    <table class="mat-tbl" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th class="text-center" style="width:40px;">S.No</th>
                <th>Raw Material</th>
                <th class="text-right" style="width:130px;">Work Order Qty</th>
                <th class="text-center" style="width:70px;">UOM</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rawMaterials as $rmRow)
                @php
                    $rmName = '—';
                    $uom    = '—';
                    if ($rmRow->rawMaterial) {
                        $rmName = trim(($rmRow->rawMaterial->name ?? '') .
                                  ($rmRow->rawMaterial->grade ? ' - ' . $rmRow->rawMaterial->grade : ''));
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
            @empty
                <tr><td colspan="4" class="no-data">No raw materials added.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ═══════════ REMARKS ═══════════ --}}
    <div class="remarks-box">
        <div class="remarks-label">Remarks</div>
        <div>{{ $workOrder->remarks ?: '—' }}</div>
    </div>

    {{-- ═══════════ SIGNATURE ROW ═══════════ --}}
    <table class="sig-tbl" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="sig-line"></div>
                <div class="sig-label">Prepared by</div>
                <div class="sig-name">{{ optional($workOrder->creator)->name ?? '—' }}</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="sig-label">Checked by</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="sig-label">Approved by</div>
            </td>
        </tr>
    </table>

    {{-- ═══════════ FOOTER ═══════════ --}}
    <div class="footer-bar">
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td>{{ $company->company_name ?? config('app.name') }}</td>
                <td style="text-align:right;">Generated: {{ now()->format('d-m-Y H:i') }}</td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
