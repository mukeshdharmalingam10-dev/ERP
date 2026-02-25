@extends('layouts.dashboard')

@section('title', 'Daily Production List - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="margin-bottom: 15px;">
        <span style="color: #666; font-size: 14px;">PRODUCTION</span>
        <h2 style="color: #333; font-size: 24px; margin: 8px 0 20px 0;">DAILY PRODUCTION LIST</h2>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <form method="GET" action="{{ route('dpl.index') }}" id="searchForm" style="display: flex; align-items: center; gap: 15px; flex: 1; min-width: 200px;">
            <label for="search" style="color: #333; font-weight: 500; white-space: nowrap;">SEARCH</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   style="flex: 1; max-width: 300px; padding: 10px 15px; border: 1px solid #ddd; border-radius: 20px; font-size: 14px;"
                   placeholder="Search DPL...">
        </form>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('dpl.create') }}" style="padding: 10px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 20px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; font-size: 14px;">
                <i class="fas fa-plus"></i> ADD
            </a>
        </div>
    </div>

    @if($displayGroups->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;" id="dplTable">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Cust. PO.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Sales Type</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Title</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">DPL No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Work Order No</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Expand</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($displayGroups as $groupIndex => $group)
                        @php
                            $dpl         = $group['latest'];
                            $wo          = $dpl->workOrder;
                            $children    = $group['children'];
                            $hasChildren = $children->count() > 0;
                            $groupId     = 'dpl-group-' . $groupIndex;
                            $sno         = ($dplPaginator->currentPage() - 1) * $dplPaginator->perPage() + $groupIndex + 1;
                        @endphp

                        <tr class="dpl-main-row" data-group="{{ $groupId }}"
                            style="border-bottom: {{ $hasChildren ? '0' : '1px' }} solid #dee2e6; background: #fff;">
                            <td style="padding: 12px; color: #666;">{{ $sno }}</td>
                            <td style="padding: 12px; color: #333;">{{ $wo->production_order_no ?? '-' }}</td>
                            <td style="padding: 12px; color: #333;">{{ $wo->customer_po_no ?? '-' }}</td>
                            <td style="padding: 12px; color: #333;">{{ $wo->sales_type ?? '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ (optional($dpl->latest_date)->format('d-m-Y')) ?: $dpl->created_at->format('d-m-Y') }}</td>
                            <td style="padding: 12px; color: #333;">{{ $wo->title ?? '' }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $dpl->dpl_no }}</td>
                            <td style="padding: 12px; color: #333;">{{ $wo->work_order_no ?? '-' }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                    <a href="{{ route('dpl.edit', $dpl->id) }}" style="padding: 6px 14px; background: #667eea; color: white; text-decoration: none; border-radius: 15px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> EDIT
                                    </a>
                                    <a href="{{ route('dpl.show', $dpl->id) }}" style="padding: 6px 14px; background: #28a745; color: white; text-decoration: none; border-radius: 15px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> VIEW
                                    </a>
                                    <form action="{{ route('dpl.destroy', $dpl->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="if(confirm('Are you sure you want to delete this record?')){this.form.submit();}"
                                                style="padding: 6px 14px; background: #dc3545; color: white; border:none; border-radius: 15px; font-size: 12px; cursor:pointer;">
                                            <i class="fas fa-trash"></i> DELETE
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                @if($hasChildren)
                                    <button class="dpl-expand-btn"
                                            data-group="{{ $groupId }}"
                                            title="Expand / Collapse"
                                            style="background: none; border: none; cursor: pointer; padding: 4px 8px; border-radius: 6px; color: #667eea; font-size: 18px; line-height: 1; transition: transform 0.2s;">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                @else
                                    <span style="color: #ccc; font-size: 13px;">—</span>
                                @endif
                            </td>
                        </tr>

                        @if($hasChildren)
                            @foreach($children as $child)
                                @php $childWo = $child->workOrder; @endphp
                                <tr class="dpl-child-row" data-group="{{ $groupId }}"
                                    style="display: none; border-bottom: 1px solid #f0f0f0; background: #fafbff;">
                                    <td style="padding: 10px 12px; color: #999;"></td>
                                    <td style="padding: 10px 12px; color: #aaa;"></td>
                                    <td style="padding: 10px 12px; color: #aaa;"></td>
                                    <td style="padding: 10px 12px; color: #555;">{{ $childWo->sales_type ?? '-' }}</td>
                                    <td style="padding: 10px 12px; color: #888;">{{ (optional($child->latest_date)->format('d-m-Y')) ?: $child->created_at->format('d-m-Y') }}</td>
                                    <td style="padding: 10px 12px; color: #555;">{{ $childWo->title ?? '' }}</td>
                                    <td style="padding: 10px 12px; padding-left: 28px; color: #444; font-weight: 500; border-left: 3px solid #667eea;">
                                        {{ $child->dpl_no }}
                                    </td>
                                    <td style="padding: 10px 12px; color: #444;">{{ $childWo->work_order_no ?? '-' }}</td>
                                    <td style="padding: 10px 12px; text-align: center;">
                                        <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                            <a href="{{ route('dpl.edit', $child->id) }}" style="padding: 5px 12px; background: #667eea; color: white; text-decoration: none; border-radius: 15px; font-size: 12px;">
                                                <i class="fas fa-edit"></i> EDIT
                                            </a>
                                            <a href="{{ route('dpl.show', $child->id) }}" style="padding: 5px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 15px; font-size: 12px;">
                                                <i class="fas fa-eye"></i> VIEW
                                            </a>
                                            <form action="{{ route('dpl.destroy', $child->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        onclick="if(confirm('Are you sure you want to delete this record?')){this.form.submit();}"
                                                        style="padding: 5px 12px; background: #dc3545; color: white; border:none; border-radius: 15px; font-size: 12px; cursor:pointer;">
                                                    <i class="fas fa-trash"></i> DELETE
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    <td style="padding: 10px 12px;"></td>
                                </tr>
                            @endforeach

                            <tr class="dpl-group-spacer" data-group="{{ $groupId }}" style="display: none;">
                                <td colspan="10" style="padding: 0; height: 4px; background: #eef0f8;"></td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $dplPaginator->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No DPL records found.</p>
            <a href="{{ route('dpl.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-plus"></i> Add First DPL
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('search');
        var searchForm = document.getElementById('searchForm');
        var searchTimeout;

        if (searchInput && searchForm) {
            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var params = new URLSearchParams(new FormData(searchForm));
                window.location.replace('{{ route("dpl.index") }}?' + params.toString());
            });

            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function () {
                    var params = new URLSearchParams(new FormData(searchForm));
                    window.location.replace('{{ route("dpl.index") }}?' + params.toString());
                }, 500);
            });
        }

        document.querySelectorAll('.dpl-expand-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var groupId = btn.getAttribute('data-group');
                var icon = btn.querySelector('i');
                var expanded = btn.getAttribute('data-expanded') === '1';

                document.querySelectorAll('.dpl-child-row[data-group="' + groupId + '"]').forEach(function (row) {
                    row.style.display = expanded ? 'none' : 'table-row';
                });

                var spacer = document.querySelector('.dpl-group-spacer[data-group="' + groupId + '"]');
                if (spacer) spacer.style.display = expanded ? 'none' : 'table-row';

                var mainRow = document.querySelector('.dpl-main-row[data-group="' + groupId + '"]');
                if (mainRow) mainRow.style.borderBottom = expanded ? '0' : '1px solid #dee2e6';

                if (icon) icon.style.transform = expanded ? '' : 'rotate(180deg)';

                btn.setAttribute('data-expanded', expanded ? '0' : '1');
            });
        });

    });
}());
</script>
@endpush
@endsection

