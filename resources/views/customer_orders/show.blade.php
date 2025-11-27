@extends('layouts.dashboard')

@section('title', 'View Customer Order - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Customer Order Details</h2>
        <a href="{{ route('customer-orders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Header Section -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Customer Order</h3>
        </div>
        <div style="padding: 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Order No</div>
                <div style="color: #333; font-weight: 600;">{{ $order->order_no }}</div>
            </div>
            <div>
                <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Order Date</div>
                <div style="color: #333; font-weight: 600;">{{ optional($order->order_date)->format('d-m-Y') }}</div>
            </div>
            <div>
                <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Tender No</div>
                <div style="color: #333; font-weight: 600;">{{ optional($order->tender)->tender_no }}</div>
            </div>
        </div>
    </div>

    <!-- Items Section -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Items</h3>
        </div>
        <div style="padding: 20px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Title</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO SR No</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Ordered Qty</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px; color: #333;">{{ optional($item->tenderItem)->title }}</td>
                            <td style="padding: 10px; color: #555;">{{ $item->po_sr_no }}</td>
                            <td style="padding: 10px; text-align: right; color: #333;">{{ $item->ordered_qty }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional(optional($item->tenderItem)->unit)->symbol }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 12px; text-align: center; color: #777;">
                                No items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Schedule Section -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Schedule</h3>
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
                <tbody>
                    @forelse($order->schedules as $s)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px; color: #333;">{{ optional(optional($s->customerOrderItem)->tenderItem)->title }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($s->customerOrderItem)->po_sr_no }}</td>
                            <td style="padding: 10px; text-align: right; color: #333;">{{ $s->quantity }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($s->unit)->symbol }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($s->start_date)->format('Y-m-d') }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($s->end_date)->format('Y-m-d') }}</td>
                            <td style="padding: 10px; color: #333;">{{ $s->inspection_clause }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 12px; text-align: center; color: #777;">
                                No schedule lines found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Amendment Section -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Amendments</h3>
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
                <tbody>
                    @forelse($order->amendments as $a)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px; color: #333;">{{ optional(optional($a->customerOrderItem)->tenderItem)->title }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($a->customerOrderItem)->po_sr_no }}</td>
                            <td style="padding: 10px; color: #333;">{{ $a->amendment_no }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($a->amendment_date)->format('Y-m-d') }}</td>
                            <td style="padding: 10px; text-align: right; color: #333;">{{ $a->existing_quantity }}</td>
                            <td style="padding: 10px; text-align: right; color: #333;">{{ $a->new_quantity }}</td>
                            <td style="padding: 10px; color: #333;">{{ $a->remarks }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 12px; text-align: center; color: #777;">
                                No amendments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


