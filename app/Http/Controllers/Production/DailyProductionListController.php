<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\DailyProductionList;
use App\Models\ProformaInvoice;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DailyProductionListController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $activeBranchId = $this->getActiveBranchId();

        $groupQuery = DailyProductionList::query()
            ->join('work_orders', 'work_orders.id', '=', 'daily_production_lists.work_order_id')
            ->select(
                'daily_production_lists.production_order_id',
                'work_orders.customer_po_no',
                DB::raw('MAX(daily_production_lists.id) as latest_id'),
                DB::raw('COUNT(*) as group_count')
            );

        if (!$activeBranchId) {
            $groupQuery->whereRaw('1=0');
        } else {
            $groupQuery->where('work_orders.branch_id', $activeBranchId);
        }

        if ($search !== '') {
            $this->applyDplSearchFilter($groupQuery, $search);
        }

        $groupQuery->groupBy('daily_production_lists.production_order_id', 'work_orders.customer_po_no')
            ->orderByDesc('latest_id');

        $dplPaginator = $groupQuery->paginate(15)->withQueryString();

        $groupKeys = $dplPaginator->getCollection()->map(function ($g) {
            return [
                'production_order_id' => (int) $g->production_order_id,
                'customer_po_no' => $g->customer_po_no,
            ];
        })->values()->all();

        $allDplQuery = DailyProductionList::with(['workOrder', 'creator'])->orderByDesc('id');
        $allDplQuery = $this->filterByWorkOrderBranch($allDplQuery);

        if (!empty($groupKeys)) {
            $allDplQuery->where(function ($q) use ($groupKeys) {
                foreach ($groupKeys as $k) {
                    $q->orWhere(function ($inner) use ($k) {
                        $inner->where('daily_production_lists.production_order_id', $k['production_order_id'])
                            ->whereHas('workOrder', function ($woQ) use ($k) {
                                if ($k['customer_po_no'] === null) {
                                    $woQ->whereNull('customer_po_no');
                                } else {
                                    $woQ->where('customer_po_no', $k['customer_po_no']);
                                }
                            });
                    });
                }
            });
        } else {
            $allDplQuery->whereRaw('1=0');
        }

        if ($search !== '') {
            $this->applyDplSearchFilter($allDplQuery, $search);
        }

        $allDpls = $allDplQuery->get();

        $grouped = $allDpls->groupBy(function ($dpl) {
            return ((int) $dpl->production_order_id) . '|||' . (string) optional($dpl->workOrder)->customer_po_no;
        });

        $displayGroups = $dplPaginator->getCollection()->map(function ($g, $idx) use ($grouped) {
            $key = ((int) $g->production_order_id) . '|||' . (string) ($g->customer_po_no ?? '');
            $members = $grouped->get($key, collect())->sortByDesc('id')->values();
            return [
                'latest' => $members->first(),
                'children' => $members->slice(1)->values(),
                'group_count' => (int) $g->group_count,
                'production_order_id' => (int) $g->production_order_id,
                'customer_po_no' => (string) ($g->customer_po_no ?? ''),
                'index' => $idx,
            ];
        })->filter(fn($g) => $g['latest'] !== null)->values();

        return view('production.dpl.index', compact('dplPaginator', 'displayGroups'));
    }

    public function create()
    {
        return view('production.dpl.create', $this->buildFormData(new DailyProductionList(), false));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dpl_no' => 'required|string|max:255|unique:daily_production_lists,dpl_no',
            'sales_type' => 'required|in:Tender,Enquiry',
            'production_order_id' => 'required|integer|min:1',
            'work_order_id' => 'required|integer|exists:work_orders,id',
            'remarks' => 'nullable|string',
            'latest_date' => 'nullable|date',
            'items' => 'required|array|min:1',
        ]);

        $workOrder = $this->validatedWorkOrder(
            (int) $validated['work_order_id'],
            $validated['sales_type'],
            (int) $validated['production_order_id']
        );

        $entries = $this->extractEntries($request->input('items', []), $workOrder->quantity_type);

        DB::transaction(function () use ($validated, $entries) {
            $dpl = DailyProductionList::create([
                'dpl_no' => $validated['dpl_no'],
                'production_order_id' => (int) $validated['production_order_id'],
                'work_order_id' => (int) $validated['work_order_id'],
                'remarks' => $validated['remarks'] ?? null,
                'latest_date' => $validated['latest_date'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($entries as $entry) {
                $dpl->entries()->create($entry);
            }
        });

        return redirect()->route('dpl.index')->with('success', 'Daily Production List created successfully.');
    }

    public function show($id)
    {
        $dpl = $this->findDpl((int) $id);
        return view('production.dpl.view', $this->buildFormData($dpl, true));
    }

    public function edit($id)
    {
        $dpl = $this->findDpl((int) $id);
        return view('production.dpl.edit', $this->buildFormData($dpl, false));
    }

    public function update(Request $request, $id)
    {
        $dpl = $this->findDpl((int) $id);

        $validated = $request->validate([
            'dpl_no' => 'required|string|max:255|unique:daily_production_lists,dpl_no,' . $dpl->id,
            'sales_type' => 'required|in:Tender,Enquiry',
            'production_order_id' => 'required|integer|min:1',
            'work_order_id' => 'required|integer|exists:work_orders,id',
            'remarks' => 'nullable|string',
            'latest_date' => 'nullable|date',
            'items' => 'required|array|min:1',
        ]);

        $workOrder = $this->validatedWorkOrder(
            (int) $validated['work_order_id'],
            $validated['sales_type'],
            (int) $validated['production_order_id']
        );

        $entries = $this->extractEntries($request->input('items', []), $workOrder->quantity_type);

        DB::transaction(function () use ($dpl, $validated, $entries) {
            $dpl->update([
                'dpl_no' => $validated['dpl_no'],
                'production_order_id' => (int) $validated['production_order_id'],
                'work_order_id' => (int) $validated['work_order_id'],
                'remarks' => $validated['remarks'] ?? null,
                'latest_date' => $validated['latest_date'] ?? null,
            ]);

            $dpl->entries()->delete();
            foreach ($entries as $entry) {
                $dpl->entries()->create($entry);
            }
        });

        return redirect()->route('dpl.index')->with('success', 'Daily Production List updated successfully.');
    }

    public function destroy($id)
    {
        $dpl = $this->findDpl((int) $id);

        DB::transaction(function () use ($dpl) {
            $dpl->entries()->delete();
            $dpl->delete();
        });

        return redirect()->route('dpl.index')->with('success', 'Daily Production List deleted successfully.');
    }

    public function apiNextNo()
    {
        return response()->json(['dpl_no' => DailyProductionList::nextDplNo()]);
    }

    public function apiPoOptions(Request $request)
    {
        $salesType = $request->get('sales_type', 'Tender');

        if ($salesType === 'Tender') {
            $woQ = WorkOrder::with('customerOrder.tender')
                ->where('sales_type', 'Tender')
                ->whereNotNull('customer_order_id')
                ->orderByDesc('id');
            $woQ = $this->applyBranchFilter($woQ, WorkOrder::class);
            $rows = $woQ->get();

            $unique = [];
            foreach ($rows as $row) {
                if (!$row->customer_order_id || isset($unique[$row->customer_order_id])) {
                    continue;
                }
                $co = $row->customerOrder;
                $unique[$row->customer_order_id] = [
                    'id' => (int) $row->customer_order_id,
                    'po_no' => $row->production_order_no ?: ($co->production_order_no ?? $co->order_no ?? ('CO-' . $row->customer_order_id)),
                    'customer_po_no' => $row->customer_po_no ?: ($co->customer_po_no ?? ''),
                    'tender_no' => optional($co->tender)->tender_no ?? '',
                ];
            }
            return response()->json(array_values($unique));
        }

        $woQ = WorkOrder::with('proformaInvoice')
            ->where('sales_type', 'Enquiry')
            ->whereNotNull('proforma_invoice_id')
            ->orderByDesc('id');
        $woQ = $this->applyBranchFilter($woQ, WorkOrder::class);
        $rows = $woQ->get();

        $unique = [];
        foreach ($rows as $row) {
            if (!$row->proforma_invoice_id || isset($unique[$row->proforma_invoice_id])) {
                continue;
            }
            $pi = $row->proformaInvoice;
            $unique[$row->proforma_invoice_id] = [
                'id' => (int) $row->proforma_invoice_id,
                'po_no' => $row->production_order_no ?: ($pi->invoice_no ?? ('PI-' . $row->proforma_invoice_id)),
                'customer_po_no' => '',
                'tender_no' => '',
            ];
        }

        return response()->json(array_values($unique));
    }

    public function apiWorkOrders(Request $request)
    {
        $salesType = $request->get('sales_type', 'Tender');
        $poId = (int) $request->get('production_order_id');

        if ($poId <= 0) {
            return response()->json([]);
        }

        $q = WorkOrder::query()->orderByDesc('id');
        $q = $this->applyBranchFilter($q, WorkOrder::class);

        if ($salesType === 'Tender') {
            $q->where('sales_type', 'Tender')->where('customer_order_id', $poId);
        } else {
            $q->where('sales_type', 'Enquiry')->where('proforma_invoice_id', $poId);
        }

        return response()->json(
            $q->get(['id', 'work_order_no', 'title'])
        );
    }

    public function apiWorkOrder($id)
    {
        $q = WorkOrder::with(['existingWorkOrder:id,work_order_no']);
        $q = $this->applyBranchFilter($q, WorkOrder::class);
        $wo = $q->findOrFail((int) $id);

        return response()->json([
            'id' => $wo->id,
            'sales_type' => $wo->sales_type,
            'production_order_id' => $wo->sales_type === 'Tender' ? $wo->customer_order_id : $wo->proforma_invoice_id,
            'work_order_no' => $wo->work_order_no,
            'worker_type' => $wo->worker_type,
            'worker_name' => $wo->worker_name,
            'title' => $wo->title,
            'product_name' => $wo->product_name,
            'starting_set_no' => $wo->starting_set_no,
            'ending_set_no' => $wo->ending_set_no,
            'sub_set_count' => $wo->no_of_sub_sets_per_set,
            'wo_type' => $wo->quantity_type,
            'total_qty' => $wo->no_of_quantity,
            'existing_work_order_no' => optional($wo->existingWorkOrder)->work_order_no,
            'document_file' => $wo->document_path ? basename($wo->document_path) : '',
            'no_of_sets' => $wo->no_of_sets,
            'total_sub_sets' => $wo->total_sub_sets,
            'quantity_per_set' => $wo->quantity_per_set,
        ]);
    }

    public function apiExistingDpl($id)
    {
        $dpl = $this->findDpl((int) $id);
        $wo = $dpl->workOrder;

        $entries = $dpl->entries->map(function ($e) {
            return [
                'item_name' => $e->item_name,
                'qty' => $e->qty,
                'completed_qty' => $e->completed_qty,
                'entry_date' => optional($e->entry_date)->format('Y-m-d'),
                'set_no' => $e->set_no,
                'sub_set_no' => $e->sub_set_no,
            ];
        })->values()->all();

        return response()->json([
            'id' => $dpl->id,
            'dpl_no' => $dpl->dpl_no,
            'sales_type' => optional($wo)->sales_type,
            'production_order_id' => $dpl->production_order_id,
            'work_order_id' => $dpl->work_order_id,
            'remarks' => $dpl->remarks,
            'latest_date' => optional($dpl->latest_date)->format('Y-m-d'),
            'entries' => $entries,
            'work_order_details' => [
                'id' => optional($wo)->id,
                'sales_type' => optional($wo)->sales_type,
                'production_order_id' => optional($wo)->sales_type === 'Tender' ? optional($wo)->customer_order_id : optional($wo)->proforma_invoice_id,
                'work_order_no' => optional($wo)->work_order_no,
                'worker_type' => optional($wo)->worker_type,
                'worker_name' => optional($wo)->worker_name,
                'title' => optional($wo)->title,
                'product_name' => optional($wo)->product_name,
                'starting_set_no' => optional($wo)->starting_set_no,
                'ending_set_no' => optional($wo)->ending_set_no,
                'sub_set_count' => optional($wo)->no_of_sub_sets_per_set,
                'wo_type' => optional($wo)->quantity_type,
                'total_qty' => optional($wo)->no_of_quantity,
                'existing_work_order_no' => optional(optional($wo)->existingWorkOrder)->work_order_no,
                'document_file' => optional($wo)->document_path ? basename($wo->document_path) : '',
                'no_of_sets' => optional($wo)->no_of_sets,
                'total_sub_sets' => optional($wo)->total_sub_sets,
                'quantity_per_set' => optional($wo)->quantity_per_set,
            ],
        ]);
    }

    protected function extractEntries(array $items, ?string $woType): array
    {
        $type = (string) $woType;
        $entries = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $itemName = trim((string) ($item['title'] ?? $item['item_name'] ?? ''));
            if ($itemName === '') {
                continue;
            }

            if ($type === 'Others') {
                $qty = (float) ($item['total_qty'] ?? 0);
                $completedQty = (float) ($item['completed_qty'] ?? 0);
                $entryDate = (string) ($item['date'] ?? '');
                if ($entryDate === '') {
                    throw ValidationException::withMessages(['items' => 'Date is required for quantity based rows.']);
                }
                if ($completedQty > $qty) {
                    throw ValidationException::withMessages(['items' => 'Completed Qty must be less than or equal to Total Qty.']);
                }
                $entries[] = [
                    'set_no' => null,
                    'sub_set_no' => null,
                    'item_name' => $itemName,
                    'qty' => $qty,
                    'completed_qty' => $completedQty,
                    'entry_date' => $entryDate,
                ];
                continue;
            }

            if ($type === 'Sub Sets') {
                $sets = $item['sets'] ?? [];
                foreach ($sets as $setNo => $subSets) {
                    if (!is_array($subSets)) {
                        continue;
                    }
                    foreach ($subSets as $subSetNo => $values) {
                        if (!is_array($values)) {
                            continue;
                        }
                        if (isset($values['entries']) && is_array($values['entries'])) {
                            foreach ($values['entries'] as $entry) {
                                if (!is_array($entry)) {
                                    continue;
                                }
                                $qty = (float) ($entry['qty'] ?? 0);
                                $date = (string) ($entry['date'] ?? '');
                                if ($date === '') {
                                    continue;
                                }
                                $entries[] = [
                                    'set_no' => (int) $setNo,
                                    'sub_set_no' => (int) $subSetNo,
                                    'item_name' => $itemName,
                                    'qty' => $qty,
                                    'completed_qty' => null,
                                    'entry_date' => $date,
                                ];
                            }
                            continue;
                        }
                        $qty = (float) ($values['qty'] ?? 0);
                        $date = (string) ($values['date'] ?? '');
                        if ($date === '') {
                            continue;
                        }
                        $entries[] = [
                            'set_no' => (int) $setNo,
                            'sub_set_no' => (int) $subSetNo,
                            'item_name' => $itemName,
                            'qty' => $qty,
                            'completed_qty' => null,
                            'entry_date' => $date,
                        ];
                    }
                }
                continue;
            }

            if ($type === 'Nos') {
                $sets = $item['sets'] ?? [];
                foreach ($sets as $setNo => $values) {
                    if (!is_array($values)) {
                        continue;
                    }
                    if (isset($values['entries']) && is_array($values['entries'])) {
                        foreach ($values['entries'] as $entry) {
                            if (!is_array($entry)) {
                                continue;
                            }
                            $qty = (float) ($entry['qty'] ?? 0);
                            $date = (string) ($entry['date'] ?? '');
                            if ($date === '') {
                                continue;
                            }
                            $entries[] = [
                                'set_no' => (int) $setNo,
                                'sub_set_no' => null,
                                'item_name' => $itemName,
                                'qty' => $qty,
                                'completed_qty' => null,
                                'entry_date' => $date,
                            ];
                        }
                        continue;
                    }
                    $qty = (float) ($values['qty'] ?? 0);
                    $date = (string) ($values['date'] ?? '');
                    if ($date === '') {
                        continue;
                    }
                    $entries[] = [
                        'set_no' => (int) $setNo,
                        'sub_set_no' => null,
                        'item_name' => $itemName,
                        'qty' => $qty,
                        'completed_qty' => null,
                        'entry_date' => $date,
                    ];
                }
                continue;
            }

            // Sets
            $qtyPerSet = (float) ($item['qty_per_set'] ?? 0);
            $sets = $item['sets'] ?? [];
            foreach ($sets as $setNo => $values) {
                $qtyVal = $qtyPerSet;
                $dateVal = '';

                if (is_array($values)) {
                    if (isset($values['entries']) && is_array($values['entries'])) {
                        foreach ($values['entries'] as $entry) {
                            if (!is_array($entry)) {
                                continue;
                            }
                            $qtyLocal = (float) ($entry['qty'] ?? $qtyPerSet);
                            $dateLocal = (string) ($entry['date'] ?? '');
                            if ($dateLocal === '') {
                                continue;
                            }
                            $entries[] = [
                                'set_no' => (int) $setNo,
                                'sub_set_no' => null,
                                'item_name' => $itemName,
                                'qty' => $qtyLocal,
                                'completed_qty' => null,
                                'entry_date' => $dateLocal,
                            ];
                        }
                        continue;
                    }
                    $qtyVal = (float) ($values['qty'] ?? $qtyPerSet);
                    $dateVal = (string) ($values['date'] ?? '');
                } else {
                    $dateVal = is_string($values) ? $values : '';
                }

                if ($dateVal === '') {
                    continue;
                }
                $entries[] = [
                    'set_no' => (int) $setNo,
                    'sub_set_no' => null,
                    'item_name' => $itemName,
                    'qty' => $qtyVal,
                    'completed_qty' => null,
                    'entry_date' => $dateVal,
                ];
            }
        }

        if (count($entries) === 0) {
            throw ValidationException::withMessages(['items' => 'At least one item/entry is required.']);
        }

        return $entries;
    }

    protected function validatedWorkOrder(int $workOrderId, string $salesType, int $productionOrderId): WorkOrder
    {
        $q = WorkOrder::query();
        $q = $this->applyBranchFilter($q, WorkOrder::class);
        $wo = $q->findOrFail($workOrderId);

        if ($wo->sales_type !== $salesType) {
            throw ValidationException::withMessages(['work_order_id' => 'Work Order does not match selected Sales Type.']);
        }

        if ($salesType === 'Tender') {
            if ((int) $wo->customer_order_id !== $productionOrderId) {
                throw ValidationException::withMessages(['production_order_id' => 'Production Order does not match Work Order.']);
            }
            CustomerOrder::findOrFail($productionOrderId);
        } else {
            if ((int) $wo->proforma_invoice_id !== $productionOrderId) {
                throw ValidationException::withMessages(['production_order_id' => 'Production Order does not match Work Order.']);
            }
            ProformaInvoice::findOrFail($productionOrderId);
        }

        return $wo;
    }

    protected function buildFormData(DailyProductionList $dpl, bool $viewOnly): array
    {
        $dpl->loadMissing(['entries', 'workOrder.existingWorkOrder', 'creator']);

        $existingQ = DailyProductionList::query()->select('id', 'dpl_no')->orderByDesc('id');
        $existingQ = $this->filterByWorkOrderBranch($existingQ);
        $existingDplNos = $existingQ->limit(200)->get();

        $entryRows = $dpl->entries->map(function ($e) {
            return [
                'item_name' => $e->item_name,
                'qty' => $e->qty,
                'completed_qty' => $e->completed_qty,
                'entry_date' => optional($e->entry_date)->format('Y-m-d'),
                'set_no' => $e->set_no,
                'sub_set_no' => $e->sub_set_no,
            ];
        })->values()->all();

        return [
            'dpl' => $dpl,
            'viewOnly' => $viewOnly,
            'existingDplNos' => $existingDplNos,
            'nextDplNo' => DailyProductionList::nextDplNo(),
            'entryRows' => $entryRows,
        ];
    }

    protected function findDpl(int $id): DailyProductionList
    {
        $q = DailyProductionList::with(['entries', 'workOrder', 'creator']);
        $q = $this->filterByWorkOrderBranch($q);
        return $q->findOrFail($id);
    }

    protected function filterByWorkOrderBranch($query)
    {
        return $query->whereHas('workOrder', function ($woQ) {
            $this->applyBranchFilter($woQ, WorkOrder::class);
        });
    }

    protected function applyDplSearchFilter($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('daily_production_lists.dpl_no', 'like', "%{$search}%")
                ->orWhereHas('workOrder', function ($wo) use ($search) {
                    $wo->where('production_order_no', 'like', "%{$search}%")
                        ->orWhere('customer_po_no', 'like', "%{$search}%")
                        ->orWhere('sales_type', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('work_order_no', 'like', "%{$search}%");
                })
                ->orWhereRaw("DATE_FORMAT(daily_production_lists.created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                ->orWhereRaw("DATE_FORMAT(daily_production_lists.created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
        });
    }
}
