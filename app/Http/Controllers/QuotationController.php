<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Quotation::with(['client', 'contact', 'user'])
            ->withCount('items')
            ->when($request->filled('folio'), fn ($query) => $query->where('folio', 'like', '%' . $request->folio . '%'))
            ->when($request->filled('client_id'), fn ($query) => $query->where('client_id', $request->client_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('issued_from'), fn ($query) => $query->whereDate('issued_at', '>=', $request->issued_from))
            ->when($request->filled('issued_to'), fn ($query) => $query->whereDate('issued_at', '<=', $request->issued_to));

        return response()->json($query->latest('id')->paginate($request->integer('per_page', 20)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $quotation = $this->saveQuotation($data);

        return response()->json([
            'message' => 'Cotización creada correctamente.',
            'data' => $quotation,
        ], 201);
    }

    public function show(Quotation $quotation): JsonResponse
    {
        return response()->json($quotation->load(['client', 'contact', 'user', 'items.product']));
    }

    public function update(Request $request, Quotation $quotation): JsonResponse
    {
        $data = $this->validatedData($request, $quotation);
        $quotation = $this->saveQuotation($data, $quotation);

        return response()->json([
            'message' => 'Cotización actualizada correctamente.',
            'data' => $quotation,
        ]);
    }

    public function destroy(Quotation $quotation): JsonResponse
    {
        $quotation->delete();

        return response()->json([
            'message' => 'Cotización eliminada correctamente.',
        ]);
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['client', 'contact', 'user', 'items.product']);

        return Pdf::loadView('pdf.quotation', compact('quotation'))
            ->setPaper('a4', 'portrait')
            ->stream('Cotizacion_' . $quotation->folio . '.pdf');
    }

    private function validatedData(Request $request, ?Quotation $quotation = null): array
    {
        $folioRule = 'required|string|max:255|unique:quotations,folio';

        if ($quotation) {
            $folioRule .= ',' . $quotation->id;
        }

        return $request->validate([
            'folio' => $folioRule,
            'client_id' => 'required|integer|exists:customers,id',
            'contact_id' => 'nullable|integer|exists:contacts,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'issued_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:issued_at',
            'currency' => 'nullable|string|size:3',
            'exchange_rate' => 'nullable|numeric|gt:0',
            'status' => 'nullable|in:draft,sent,accepted,rejected,expired,converted',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer|exists:productos,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.sort_order' => 'nullable|integer|min:0',
        ]);
    }

    private function saveQuotation(array $data, ?Quotation $quotation = null): Quotation
    {
        return DB::transaction(function () use ($data, $quotation) {
            $items = $data['items'];
            unset($data['items']);

            $totals = $this->calculateTotals($items);
            $data = array_merge([
                'currency' => 'MXN',
                'exchange_rate' => 1,
                'status' => 'draft',
                'user_id' => auth()->id(),
            ], $data, $totals);

            $quotation ??= new Quotation();
            $quotation->fill($data);
            $quotation->save();

            $quotation->items()->delete();
            $quotation->items()->createMany($this->prepareItems($items));

            return $quotation->load(['client', 'contact', 'user', 'items.product']);
        });
    }

    private function calculateTotals(array $items): array
    {
        $subtotal = 0;
        $discount = 0;
        $tax = 0;

        foreach ($items as $item) {
            $lineSubtotal = (float) $item['quantity'] * (float) $item['unit_price'];
            $lineDiscount = $lineSubtotal * ((float) ($item['discount_percentage'] ?? 0) / 100);
            $taxableAmount = $lineSubtotal - $lineDiscount;

            $subtotal += $lineSubtotal;
            $discount += $lineDiscount;
            $tax += $taxableAmount * ((float) ($item['tax_percentage'] ?? 16) / 100);
        }

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'tax' => round($tax, 2),
            'total' => round($subtotal - $discount + $tax, 2),
        ];
    }

    private function prepareItems(array $items): array
    {
        return array_map(function (array $item, int $index): array {
            $lineSubtotal = (float) $item['quantity'] * (float) $item['unit_price'];
            $lineDiscount = $lineSubtotal * ((float) ($item['discount_percentage'] ?? 0) / 100);
            $lineTax = ($lineSubtotal - $lineDiscount) * ((float) ($item['tax_percentage'] ?? 16) / 100);

            return [
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'tax_percentage' => $item['tax_percentage'] ?? 16,
                'subtotal' => round($lineSubtotal - $lineDiscount, 2),
                'total' => round($lineSubtotal - $lineDiscount + $lineTax, 2),
                'sort_order' => $item['sort_order'] ?? $index,
            ];
        }, $items, array_keys($items));
    }
}
