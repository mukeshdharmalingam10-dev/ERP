<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Work Order - {{ $workOrder->work_order_no }}</title>
    <style>
        @page {
            margin: 15mm 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .logo-container {
            position: absolute;
            left: 0;
            top: 0;
        }
        .logo {
            max-width: 120px;
            max-height: 70px;
        }
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .certification {
            font-size: 9pt;
            font-weight: normal;
            margin-bottom: 2px;
        }
        .address {
            font-size: 9pt;
            line-height: 1.3;
        }
        .title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 10px 0 15px 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 5px;
        }
        .meta-table td {
            font-size: 10pt;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table td {
            padding: 4px 2px;
            vertical-align: top;
            font-size: 10pt;
        }
        .label {
            width: 180px;
            font-weight: normal;
        }
        .colon {
            width: 10px;
        }
        .value {
            border-bottom: 1px solid #000;
            padding-left: 5px;
        }
        .raw-material-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        .raw-material-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .raw-material-table th, .raw-material-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 9.5pt;
        }
        .raw-material-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            width: 100%;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            width: 50%;
            vertical-align: bottom;
            font-size: 10pt;
        }
        .disclaimer {
            font-size: 8pt;
            margin-top: 30px;
            text-align: left;
        }
        .page-info-table {
            width: 100%;
            margin-top: 20px;
            font-size: 9pt;
            color: #333;
        }
    </style>
</head>
<body>
    {{-- HEADER SECTION --}}
    <div class="header">
        <div class="logo-container">
            @if($company && $company->logo_path)
                @php $logoPath = storage_path('app/public/' . $company->logo_path); @endphp
                @if(file_exists($logoPath))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo">
                @endif
            @endif
        </div>
        <div class="company-name">{{ $company->company_name ?? 'Meena Fiberglas Industries' }}</div>
        <div class="certification">An IRIS Certified Company</div>
        <div class="certification">An ISO 9001:2015 Certified Company</div>
        <div class="address">
            {{ $company->address_line_1 ?? 'R.S.No. 151/3, Cuddalore-Pondy Main Road,' }}<br>
            {{ $company->address_line_2 ?? 'Kattukuppam,' }} {{ $company->city ?? 'Puducherry' }}-{{ $company->pincode ?? '607 402' }}.<br>
            Phone: {{ $company->phone ?? '0413-2611009' }}&nbsp;&nbsp;&nbsp;Mobile: 7358019212<br>
            Email: {{ $company->email ?? 'sales@meenafibres.com' }}
        </div>
    </div>

    <div class="title">Production Work Order</div>

    {{-- DATE RIGHT ALIGNED --}}
    <table class="meta-table">
        <tr>
            <td style="text-align: right; font-weight: bold;">
                Date: {{ $workOrder->work_order_date ? $workOrder->work_order_date->format('d-m-Y') : now()->format('d-m-Y') }}
            </td>
        </tr>
    </table>

    {{-- WORK ORDER DETAIL STRUCTURE --}}
    <table class="details-table">
        <tr>
            <td class="label">WorkOrderNo</td><td class="colon">:</td><td class="value">{{ $workOrder->work_order_no }}</td>
        </tr>
        <tr>
            <td class="label">Production Order No</td><td class="colon">:</td><td class="value">{{ $workOrder->production_order_no ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Employee Name</td><td class="colon">:</td><td class="value">{{ $workOrder->worker_type == 'Employee' ? $workOrder->worker_name : '—' }}</td>
        </tr>
        <tr>
            <td class="label">Supplier Name</td><td class="colon">:</td><td class="value">{{ $workOrder->worker_type == 'Sub-Contractor' ? $workOrder->worker_name : '—' }}</td>
        </tr>
        <tr>
            <td class="label">QC No</td><td class="colon">:</td><td class="value">—</td> {{-- Blank as per requirement --}}
        </tr>
        <tr>
            <td class="label">Quantity</td><td class="colon">:</td><td class="value">{{ $workOrder->no_of_quantity ?: $workOrder->no_of_sets }} {{ $workOrder->quantity_type }}</td>
        </tr>
        <tr>
            <td class="label">Starting Production No</td><td class="colon">:</td><td class="value">{{ $workOrder->starting_quantity_no ?: $workOrder->starting_set_no ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">Ending Production No</td><td class="colon">:</td><td class="value">{{ $workOrder->ending_quantity_no ?: $workOrder->ending_set_no ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">Rework</td><td class="colon">:</td><td class="value">—</td>
        </tr>
        <tr>
            <td class="label">DC Required</td><td class="colon">:</td><td class="value">—</td>
        </tr>
        <tr>
            <td class="label">Product Name</td><td class="colon">:</td><td class="value">{{ $workOrder->product_name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Process Name</td><td class="colon">:</td><td class="value">{{ $workOrder->nature_of_work ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Employee Type</td><td class="colon">:</td><td class="value">{{ $workOrder->worker_type ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Product Size</td><td class="colon">:</td><td class="value">{{ $workOrder->title ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Thickness</td><td class="colon">:</td><td class="value">{{ $workOrder->thickness ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Colour</td><td class="colon">:</td><td class="value">{{ $workOrder->color ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Design Completed Date</td><td class="colon">:</td><td class="value">{{ $workOrder->completion_date ? $workOrder->completion_date->format('d-m-Y') : '—' }}</td>
        </tr>
        <tr>
            <td class="label">Sequence</td><td class="colon">:</td><td class="value">{{ $workOrder->layup_sequence ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Material Issued Details</td><td class="colon">:</td><td class="value">—</td>
        </tr>
        <tr>
            <td class="label">Remarks</td><td class="colon">:</td><td class="value">{{ $workOrder->remarks ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Status</td><td class="colon">:</td><td class="value">—</td>
        </tr>
    </table>

    {{-- RAW MATERIAL TABLE --}}
    <div class="raw-material-title">Raw Material Details:</div>
    <table class="raw-material-table">
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;">S.No</th>
                <th>Rawmaterial Name</th>
                <th style="width: 100px; text-align: center;">Quantity</th>
                <th style="width: 80px; text-align: center;">Unit Name</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workOrder->rawMaterials as $index => $rm)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ trim(($rm->rawMaterial->name ?? '') . ($rm->rawMaterial->grade ? ' - ' . $rm->rawMaterial->grade : '')) }}</td>
                <td style="text-align: right;">{{ number_format($rm->work_order_quantity, 2) }}</td>
                <td style="text-align: center;">
                    @php
                        $unitName = '—';
                        if ($rm->rawMaterial && $rm->rawMaterial->unit) {
                            $unitName = $rm->rawMaterial->unit->name;
                        } elseif ($rm->unit) {
                            $unitName = $rm->unit->name;
                        }
                    @endphp
                    {{ $unitName }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; font-style: italic; color: #666;">No raw materials added.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER SECTION --}}
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="text-align: left; height: 60px;">
                    <br><br><br>
                    <strong>Signature of the Employee</strong>
                </td>
                <td style="text-align: right;">
                    <strong>For Meena Fibre Glass</strong><br><br><br><br>
                    <strong>Authorised Signatory</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="disclaimer">
        *Since this is an computer generated document, manual signature is not required.
    </div>

    <table class="page-info-table">
        <tr>
            <td style="text-align: left;">
                Generated Date: {{ now()->format('d-m-Y H:i') }}
            </td>
            <td style="text-align: right;">
                Page 1/1
            </td>
        </tr>
    </table>
</body>
</html>
