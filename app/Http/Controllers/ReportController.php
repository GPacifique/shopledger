<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | MAIN REPORT DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        [$startDate, $endDate] =
            $this->dateRange($request);

        $shop = $this->selectedShop($request);

        $data = $this->reportService->generate(
            $shop?->id,
            $startDate,
            $endDate
        );

        return view(
            'reports.index',
            compact(
                'shop',
                'startDate',
                'endDate',
                'data'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DAILY
    |--------------------------------------------------------------------------
    */

    public function daily(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : now();

        $startDate = $date->copy()->startOfDay();

        $endDate = $date->copy()->endOfDay();

        $shop = $this->selectedShop($request);

        $data = $this->reportService->generate(
            $shop?->id,
            $startDate,
            $endDate
        );

        return view(
            'reports.daily',
            compact(
                'shop',
                'date',
                'startDate',
                'endDate',
                'data'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | WEEKLY
    |--------------------------------------------------------------------------
    */

    public function weekly(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : now();

        $startDate = $date->copy()->startOfWeek();

        $endDate = $date->copy()->endOfWeek();

        $shop = $this->selectedShop($request);

        $data = $this->reportService->generate(
            $shop?->id,
            $startDate,
            $endDate
        );

        return view(
            'reports.weekly',
            compact(
                'shop',
                'date',
                'startDate',
                'endDate',
                'data'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MONTHLY
    |--------------------------------------------------------------------------
    */

    public function monthly(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : now();

        $startDate = $date->copy()->startOfMonth();

        $endDate = $date->copy()->endOfMonth();

        $shop = $this->selectedShop($request);

        $data = $this->reportService->generate(
            $shop?->id,
            $startDate,
            $endDate
        );

        return view(
            'reports.monthly',
            compact(
                'shop',
                'date',
                'startDate',
                'endDate',
                'data'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | YEARLY
    |--------------------------------------------------------------------------
    */

    public function yearly(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : now();

        $startDate = $date->copy()->startOfYear();

        $endDate = $date->copy()->endOfYear();

        $shop = $this->selectedShop($request);

        $data = $this->reportService->generate(
            $shop?->id,
            $startDate,
            $endDate
        );

        return view(
            'reports.yearly',
            compact(
                'shop',
                'date',
                'startDate',
                'endDate',
                'data'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SALES
    |--------------------------------------------------------------------------
    */

    public function sales(Request $request)
    {
        [$startDate, $endDate] =
            $this->dateRange($request);

        $shop = $this->selectedShop($request);

        $data = $this->reportService->sales(
            $shop?->id,
            $startDate,
            $endDate
        );

        return view(
            'reports.sales',
            compact(
                'shop',
                'startDate',
                'endDate',
                'data'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PURCHASES
    |--------------------------------------------------------------------------
    */

    public function purchases(Request $request)
    {
        [$startDate, $endDate] =
            $this->dateRange($request);

        $shop = $this->selectedShop($request);

        $data = $this->reportService->purchases(
            $shop?->id,
            $startDate,
            $endDate
        );

        return view(
            'reports.purchases',
            compact(
                'shop',
                'startDate',
                'endDate',
                'data'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPENSES
    |--------------------------------------------------------------------------
    */

    public function expenses(Request $request)
    {
        [$startDate, $endDate] =
            $this->dateRange($request);

        $shop = $this->selectedShop($request);

        $data = $this->reportService->expenses(
            $shop?->id,
            $startDate,
            $endDate
        );

        return view(
            'reports.expenses',
            compact(
                'shop',
                'startDate',
                'endDate',
                'data'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | OTHER INCOME
    |--------------------------------------------------------------------------
    */

    public function otherIncome(Request $request)
    {
        [$startDate, $endDate] =
            $this->dateRange($request);

        $shop = $this->selectedShop($request);

        $data = $this->reportService->otherIncome(
            $shop?->id,
            $startDate,
            $endDate
        );

        return view(
            'reports.other-income',
            compact(
                'shop',
                'startDate',
                'endDate',
                'data'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PROFIT & LOSS
    |--------------------------------------------------------------------------
    */

    public function profitLoss(Request $request)
    {
        [$startDate, $endDate] =
            $this->dateRange($request);

        $shop = $this->selectedShop($request);

        $data = $this->reportService->summary(
            $shop?->id,
            $startDate,
            $endDate
        );

        return view(
            'reports.profit-loss',
            compact(
                'shop',
                'startDate',
                'endDate',
                'data'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DATE RANGE
    |--------------------------------------------------------------------------
    */

    private function dateRange(Request $request): array
    {
        $startDate = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : now()->startOfMonth();

        $endDate = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : now()->endOfDay();

        return [
            $startDate,
            $endDate
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | SHOP
    |--------------------------------------------------------------------------
    */

    private function selectedShop(Request $request)
    {
        if ($request->filled('shop_id')) {
            return Shop::find($request->shop_id);
        }

        return auth()->user()->shop ?? null;
    }
    /*
|--------------------------------------------------------------------------
| PDF EXPORT
|--------------------------------------------------------------------------
*/

public function exportPdf(Request $request)
{
    [$startDate, $endDate] =
        $this->dateRange($request);

    $shop = $this->selectedShop($request);

    $data = $this->reportService->generate(
        $shop?->id,
        $startDate,
        $endDate
    );

    $pdf = Pdf::loadView(
        'reports.pdf',
        [
            'data' => $data,
            'shop' => $shop,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]
    );

    $pdf->setPaper('a4', 'landscape');

    return $pdf->download(
        'mahwi-report-' .
        $startDate->format('Y-m-d') .
        '-to-' .
        $endDate->format('Y-m-d') .
        '.pdf'
    );
}
/*
|--------------------------------------------------------------------------
| EXCEL EXPORT
|--------------------------------------------------------------------------
*/

public function exportExcel(Request $request)
{
    [$startDate, $endDate] =
        $this->dateRange($request);

    $shop = $this->selectedShop($request);

    $data = $this->reportService->sales(
        $shop?->id,
        $startDate,
        $endDate
    );

    return Excel::download(
        new ReportExport($data['items']),
        'report-sales-' .
        $startDate->format('Y-m-d') .
        '-to-' .
        $endDate->format('Y-m-d') .
        '.xlsx'
    );
}
/*
|--------------------------------------------------------------------------
| CSV EXPORT
|--------------------------------------------------------------------------
*/

public function exportCsv(Request $request)
{
    [$startDate, $endDate] =
        $this->dateRange($request);

    $shop = $this->selectedShop($request);

    $data = $this->reportService->sales(
        $shop?->id,
        $startDate,
        $endDate
    );

    $filename =
        'mahwi-sales-' .
        $startDate->format('Y-m-d') .
        '-to-' .
        $endDate->format('Y-m-d') .
        '.csv';

    return response()->streamDownload(function () use ($data) {

        $handle = fopen('php://output', 'w');

        /*
        |--------------------------------------------------------------------------
        | Headers
        |--------------------------------------------------------------------------
        */

        fputcsv($handle, [
            'Date',
            'Order',
            'Product',
            'SKU',
            'Quantity',
            'Buying Price',
            'Selling Price',
            'Revenue',
            'Profit',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Rows
        |--------------------------------------------------------------------------
        */

        foreach ($data['items'] as $row) {

            $item = $row['item'];

            $product = $row['product'];

            $quantity = (float) (
                $item->quantity ?? 0
            );

            $buyingPrice = (float) (
                $item->buying_price
                ?? $item->cost_price
                ?? $product?->buying_price
                ?? 0
            );

            $sellingPrice = (float) (
                $item->selling_price
                ?? $item->unit_price
                ?? $item->price
                ?? 0
            );

            $revenue =
                $quantity *
                $sellingPrice;

            $cost =
                $quantity *
                $buyingPrice;

            $profit =
                $revenue -
                $cost;

            fputcsv($handle, [

                $row['order']->created_at?->format(
                    'Y-m-d H:i'
                ),

                $row['order']->id,

                $product?->name
                    ?? 'Unknown Product',

                $product?->sku
                    ?? $item->sku
                    ?? '',

                $quantity,

                $buyingPrice,

                $sellingPrice,

                $revenue,

                $profit,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Total
        |--------------------------------------------------------------------------
        */

        fputcsv($handle, []);

        fputcsv($handle, [
            'TOTAL',
            '',
            '',
            '',
            $data['quantity'],
            '',
            '',
            $data['total'],
            $data['total'] - $data['cost'],
        ]);

        fclose($handle);

    }, $filename, [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
}
}