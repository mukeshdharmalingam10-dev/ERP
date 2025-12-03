@extends('layouts.dashboard')

@section('title', 'Tenders - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Tenders</h2>
        <a href="{{ route('tenders.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Create Tender
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search Section - Only show if there are items or a search/date query is active -->
    @if($tenders->count() > 0 || request('search') || request('start_date') || request('end_date'))
    <form method="GET" action="{{ route('tenders.index') }}" id="searchForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1;">
                <label for="search" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Search:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Search by Tender No, Company, Title, Date (dd-mm-yyyy)...">
            </div>
            <div>
                <label for="start_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                    style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; min-width: 180px;">
            </div>
            <div>
                <label for="end_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                    style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; min-width: 180px;">
            </div>
            <div>
                <a href="{{ route('tenders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center;">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const searchForm = document.getElementById('searchForm');
            let searchTimeout;

            // Auto-focus the search input
            if (searchInput) {
                searchInput.focus();
                // Place cursor at the end of the text
                const length = searchInput.value.length;
                searchInput.setSelectionRange(length, length);
            }

            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(searchForm);
                const params = new URLSearchParams(formData);
                const url = '{{ route("tenders.index") }}?' + params.toString();
                
                // Use replace instead of href to avoid creating new history entry
                window.location.replace(url);
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const formData = new FormData(searchForm);
                    const params = new URLSearchParams(formData);
                    const url = '{{ route("tenders.index") }}?' + params.toString();
                    
                    // Use replace instead of href to avoid creating new history entry
                    window.location.replace(url);
                }, 500); // Wait 500ms after user stops typing
            });
        });
    </script>
    @endif

    @if($tenders->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 50px;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Tender No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Cust. Tender No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Closing Date &amp; Time</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Title</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Company Name</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Tender Type</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Bidding System</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Tender Status</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Rank</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Estimation Price Status</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenders as $index => $tender)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; text-align: center; color: #666;">{{ ($tenders->currentPage() - 1) * $tenders->perPage() + $index + 1 }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $tender->tender_no }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->customer_tender_no ?? '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->closing_date_time ? $tender->closing_date_time->format('d/m/Y H:i') : '-' }}</td>
                            @php
                                $firstItemTitle = optional($tender->items->first())->title;
                            @endphp
                            <td style="padding: 12px; color: #666;">{{ $firstItemTitle ?: '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->company->company_name ?? '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->tender_type ?? '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->bidding_system ?? '-' }}</td>
                            <td style="padding: 12px;">
                                <span style="background: #6c757d; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                                    @if($tender->tender_status === 'Bid Coated')
                                        Bid Quoted
                                    @elseif($tender->tender_status === 'Bid not coated')
                                        Bid Not Quoted
                                    @else
                                        {{ $tender->tender_status }}
                                    @endif
                                </span>
                            </td>
                            <td style="padding: 12px; color: #666;">{{ $tender->technical_spec_rank ?? '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->bid_result ?? '-' }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('tenders.show', $tender->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('tenders.edit', $tender->id) }}" style="padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('tenders.destroy', $tender->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this tender?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $tenders->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No tenders found.</p>
            <a href="{{ route('tenders.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First Tender
            </a>
        </div>
    @endif
</div>
@endsection

