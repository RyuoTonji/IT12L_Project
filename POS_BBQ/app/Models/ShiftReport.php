<?php

namespace App\Models;

use App\Traits\SyncsToSupabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftReport extends Model
{
    use HasFactory, SyncsToSupabase;

    protected $table = 'pos_shift_reports';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);
    }


    protected $fillable = [
        'user_id',
        'report_type',
        'shift_date',
        'total_sales',
        'total_refunds',
        'total_orders',
        'stock_in',
        'stock_out',
        'remaining_stock',
        'spoilage',
        'returns',
        'return_reason',
        'content',
        'admin_reply',
        'status',
        'branch_id',
    ];

    protected $casts = [
        'shift_date' => 'date',
        'total_sales' => 'decimal:2',
        'total_refunds' => 'decimal:2',
        'stock_in' => 'decimal:2',
        'stock_out' => 'decimal:2',
        'remaining_stock' => 'decimal:2',
        'spoilage' => 'decimal:2',
        'returns' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function getParsedInventoryReportAttribute()
    {
        if (!in_array($this->report_type, ['inventory', 'inventory_start', 'inventory_end'])) {
            return null;
        }

        $content = $this->content;
        $lines = explode("\n", $content);
        $messageLines = [];
        $tableLines = [];
        $isTableSection = false;
        $tableType = null; // 'start' or 'end'

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            if (str_contains($line, 'DETAILED INVENTORY REPORT')) {
                $isTableSection = true;
                continue;
            }

            if (!$isTableSection) {
                if (!empty($trimmedLine)) {
                    $messageLines[] = $line; // Keep original formatting/spacing for message if needed
                }
            } else {
                // Table parsing logic
                if (str_contains($line, '------'))
                    continue; // Skip separator lines
                if (empty($trimmedLine))
                    continue;

                // Identify header to determine type
                if (str_contains($line, 'Item Name') && str_contains($line, 'Start Qty')) {
                    $tableType = 'start';
                    continue;
                }
                if (str_contains($line, 'Item Name') && str_contains($line, 'Added')) {
                    $tableType = 'end';
                    continue;
                }

                // Parse row data
                $parts = array_map('trim', explode('|', $line));
                if (count($parts) >= 2) {
                    $tableLines[] = $parts;
                }
            }
        }

        return [
            'message' => implode("\n", $messageLines),
            'tableType' => $tableType,
            'rows' => $tableLines
        ];
    }
}
