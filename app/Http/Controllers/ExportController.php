<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function csv(Request $request): HttpResponse
    {
        $transactions = $this->getFilteredTransactions($request);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="transactions.csv"',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Arabic
            fputcsv($file, ['النوع', 'المبلغ', 'الوصف', 'التصنيف', 'طريقة الدفع', 'التاريخ', 'ملاحظات']);
            foreach ($transactions as $tx) {
                fputcsv($file, [
                    $tx->type === 'expense' ? 'مصروف' : 'إيراد',
                    $tx->amount,
                    $tx->description,
                    $tx->category?->name,
                    $this->paymentLabel($tx->payment_method),
                    $tx->transaction_date->format('Y-m-d'),
                    $tx->notes,
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function getFilteredTransactions(Request $request)
    {
        return Transaction::where('user_id', $request->user()->id)
            ->with('category')
            ->latest('transaction_date')
            ->get();
    }

    private function paymentLabel(string $method): string
    {
        return match ($method) {
            'cash' => 'كاش',
            'credit_card' => 'بطاقة ائتمان',
            'digital_wallet' => 'محفظة رقمية',
            'bank_transfer' => 'تحويل بنكي',
            default => $method,
        };
    }
}
