<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\ShiftReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportInventory()
    {
        $inventoryItems = Inventory::all();
        $pdf = Pdf::loadView('exports.inventory_pdf', compact('inventoryItems'));
        return $pdf->download('inventory_report.pdf');
    }

    public function exportSales()
    {
        $orders = Order::with('user')->latest()->get();
        $pdf = Pdf::loadView('exports.sales_pdf', compact('orders'));
        return $pdf->download('sales_report.pdf');
    }

    public function exportShiftReport(ShiftReport $report)
    {
        $pdf = Pdf::loadView('exports.report_pdf', compact('report'));
        return $pdf->download('shift_report_' . $report->shift_date->format('Y-m-d') . '.pdf');
    }
}
