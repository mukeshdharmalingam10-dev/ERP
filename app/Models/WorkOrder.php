<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'sales_type',
        'customer_order_id',
        'proforma_invoice_id',
        'production_order_no',
        'customer_po_no',
        'work_order_no',
        'title',
        'worker_type',
        'worker_id',
        'product_name',
        'quantity_type',
        'no_of_sets',
        'starting_set_no',
        'ending_set_no',
        'no_of_sub_sets_per_set',
        'total_sub_sets',
        'quantity_per_set',
        'no_of_quantity',
        'starting_quantity_no',
        'ending_quantity_no',
        'thickness',
        'drawing_no',
        'color',
        'completion_date',
        'nature_of_work',
        'layup_sequence',
        'batch_no',
        'work_order_date',
        'document_path',
        'remarks',
        'reference_table_data',
        'quantity_blocks',
        'created_by_id',
        'existing_work_order_id',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'work_order_date' => 'date',
        'reference_table_data' => 'array',
        'quantity_blocks' => 'array',
        'no_of_sets' => 'decimal:2',
        'no_of_sub_sets_per_set' => 'decimal:2',
        'total_sub_sets' => 'decimal:2',
        'quantity_per_set' => 'decimal:2',
        'no_of_quantity' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function existingWorkOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'existing_work_order_id');
    }

    public function rawMaterials()
    {
        return $this->hasMany(WorkOrderRawMaterial::class)->orderBy('sr_no');
    }

    public function worker()
    {
        if ($this->worker_type === 'Employee') {
            return $this->belongsTo(Employee::class, 'worker_id');
        }
        if ($this->worker_type === 'Sub-Contractor') {
            return $this->belongsTo(Supplier::class, 'worker_id');
        }
        return null;
    }

    public function getWorkerNameAttribute()
    {
        if (!$this->worker_id) {
            return null;
        }
        if ($this->worker_type === 'Employee') {
            return Employee::find($this->worker_id)?->name;
        }
        if ($this->worker_type === 'Sub-Contractor') {
            return Supplier::find($this->worker_id)?->supplier_name;
        }
        return null;
    }

    /**
     * Generate next work order number based on sales type and PO.
     * Tender: T-{YY}/{Customer PO No}/{001} e.g. T-26/1234/001
     * Enquiry: ENQ-{YY}/{Enquiry PO No}/{001} e.g. ENQ-26/1234/001
     */
    public static function generateWorkOrderNo(string $salesType, ?string $poRef): string
    {
        $year = date('y');
        $prefix = $salesType === 'Tender' ? 'T' : 'ENQ';
        $poPart = preg_replace('/[^0-9A-Za-z]/', '', (string) ($poRef ?? '0')) ?: '0';
        $pattern = $prefix . '-' . $year . '/' . $poPart . '/%';
        $last = static::where('sales_type', $salesType)
            ->where('work_order_no', 'like', $pattern)
            ->orderByDesc('id')
            ->value('work_order_no');
        $seq = 1;
        if ($last && preg_match('/\/(\d+)$/', $last, $m)) {
            $seq = (int) $m[1] + 1;
        }
        return sprintf('%s-%s/%s/%03d', $prefix, $year, $poPart, $seq);
    }
}
