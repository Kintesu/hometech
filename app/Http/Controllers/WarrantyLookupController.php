<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WarrantyLookupController extends Controller
{
    public function index()
    {
        return view('client.warranty_lookup', [
            'lookupMethod' => 'phone',
            'keyword' => '',
            'results' => null,
        ]);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'lookup_method' => ['required', 'in:phone,serial'],
            'keyword' => ['required', 'string', 'max:100'],
        ], [
            'lookup_method.required' => 'Vui lòng chọn phương thức tra cứu.',
            'lookup_method.in' => 'Phương thức tra cứu không hợp lệ.',
            'keyword.required' => 'Vui lòng nhập thông tin cần tra cứu.',
        ]);

        $lookupMethod = $validated['lookup_method'];
        $keyword = trim($validated['keyword']);

        if (!Schema::hasTable('warranties') || !Schema::hasTable('product_serials')) {
            return view('client.warranty_lookup', [
                'lookupMethod' => $lookupMethod,
                'keyword' => $keyword,
                'results' => collect(),
            ]);
        }

        $query = DB::table('warranties')
            ->join('product_serials', 'warranties.product_serial_id', '=', 'product_serials.id')
            ->leftJoin('products', 'product_serials.product_id', '=', 'products.id')
            ->leftJoin('orders', 'warranties.order_id', '=', 'orders.id')
            ->leftJoin('users', 'warranties.user_id', '=', 'users.id')
            ->where(function ($query) {
                $query->whereNull('warranties.status')
                    ->orWhere('warranties.status', '<>', 'Voided');
            })
            ->select([
                'warranties.id',
                'warranties.purchase_date',
                'warranties.activation_date',
                'warranties.warranty_months',
                'warranties.status',
                'warranties.service_status',
                'product_serials.serial_number',
                'products.name as product_name',
                'orders.order_date',
            ]);

        if ($lookupMethod === 'phone') {
            $phoneDigits = preg_replace('/\D+/', '', $keyword);

            $query->where(function ($query) use ($keyword, $phoneDigits) {
                $query->where('warranties.customer_phone', 'like', '%' . $keyword . '%')
                    ->orWhere('users.phone', 'like', '%' . $keyword . '%');

                if ($phoneDigits !== '' && $phoneDigits !== $keyword) {
                    $query->orWhere('warranties.customer_phone', 'like', '%' . $phoneDigits . '%')
                        ->orWhere('users.phone', 'like', '%' . $phoneDigits . '%');
                }
            });
        } else {
            $query->where('product_serials.serial_number', $keyword);
        }

        $results = $query
            ->orderByDesc(DB::raw('COALESCE(warranties.activation_date, warranties.purchase_date, orders.order_date)'))
            ->get()
            ->map(fn ($warranty) => $this->formatWarrantyResult($warranty));

        return view('client.warranty_lookup', [
            'lookupMethod' => $lookupMethod,
            'keyword' => $keyword,
            'results' => $results,
        ]);
    }

    private function formatWarrantyResult(object $warranty): array
    {
        $startDate = $this->parseDate($warranty->activation_date)
            ?? $this->parseDate($warranty->purchase_date)
            ?? $this->parseDate($warranty->order_date);

        $warrantyMonths = (int) ($warranty->warranty_months ?? 12);
        $endDate = $startDate?->copy()->addMonthsNoOverflow($warrantyMonths)->endOfDay();
        $today = Carbon::today();
        $isExpired = $endDate ? $today->gt($endDate) : false;

        if ($warranty->status === 'Expired') {
            $isExpired = true;
        }

        $isProcessing = in_array($warranty->status, ['Repairing', 'Processing', 'InRepair'], true)
            || in_array($warranty->service_status, ['Repairing', 'Processing', 'InRepair'], true);

        return [
            'product_name' => $warranty->product_name ?: 'Sản phẩm HomeTech',
            'serial_number' => $warranty->serial_number,
            'start_date' => $startDate?->format('d/m/Y') ?: 'Chưa cập nhật',
            'remaining' => $this->formatRemainingWarranty($today, $endDate, $isExpired),
            'status_label' => $isExpired ? 'Đã hết hạn' : 'Còn hạn',
            'is_processing' => $isProcessing,
        ];
    }

    private function parseDate(?string $date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        return Carbon::parse($date);
    }

    private function formatRemainingWarranty(Carbon $today, ?Carbon $endDate, bool $isExpired): string
    {
        if (!$endDate) {
            return 'Chưa cập nhật';
        }

        if ($isExpired) {
            return 'Đã hết hạn';
        }

        $diff = $today->diff($endDate->copy()->startOfDay());
        $months = ($diff->y * 12) + $diff->m;

        if ($months > 0 && $diff->d > 0) {
            return 'Còn ' . $months . ' tháng ' . $diff->d . ' ngày';
        }

        if ($months > 0) {
            return 'Còn ' . $months . ' tháng';
        }

        return 'Còn ' . $diff->d . ' ngày';
    }
}
