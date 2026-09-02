<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Contact;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'contactable_type' => ['nullable', 'required_with:contactable_id', Rule::in(['cliente', 'proveedor'])],
            'contactable_id' => ['nullable', 'integer', 'required_with:contactable_type'],
        ]);

        $query = Contact::with('contactable')->latest('id');

        if (!empty($filters['contactable_type'])) {
            $contactableClass = $this->contactableClass($filters['contactable_type']);
            $query->where('contactable_type', (new $contactableClass)->getMorphClass())
                ->where('contactable_id', $filters['contactable_id']);
        }

        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return response()->json($query->paginate($perPage));
    }

    public function show(Contact $contact)
    {
        return response()->json($contact->load('contactable'));
    }

    public function store(Request $request)
    {
        $validatedType = $request->validate([
            'contactable_type' => ['required', Rule::in(['cliente', 'proveedor'])],
        ]);

        $contactableClass = $this->contactableClass($validatedType['contactable_type']);

        $validated = $request->validate(array_merge([
            'contactable_id' => [
                'required',
                'integer',
                Rule::exists((new $contactableClass)->getTable(), 'id'),
            ],
        ], $this->contactRules(true)));

        $contactable = $contactableClass::findOrFail($validated['contactable_id']);
        unset($validated['contactable_id']);

        $contact = new Contact($validated);
        $contact->contactable()->associate($contactable);
        $contact->save();

        return response()->json([
            'message' => 'Contacto registrado correctamente.',
            'data' => $contact->load('contactable'),
        ], 201);
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate($this->contactRules());

        if ($request->hasAny(['contactable_type', 'contactable_id'])) {
            $parent = $request->validate([
                'contactable_type' => ['required', Rule::in(['cliente', 'proveedor'])],
                'contactable_id' => ['required', 'integer'],
            ]);

            $contactableClass = $this->contactableClass($parent['contactable_type']);
            $request->validate([
                'contactable_id' => [
                    'exists:' . (new $contactableClass)->getTable() . ',id',
                ],
            ]);

            $contact->contactable()->associate(
                $contactableClass::findOrFail($parent['contactable_id'])
            );
        }

        $contact->update($validated);

        return response()->json([
            'message' => 'Contacto actualizado correctamente.',
            'data' => $contact->fresh()->load('contactable'),
        ]);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json([
            'message' => 'Contacto eliminado correctamente.',
        ]);
    }

    private function contactableClass(string $type): string
    {
        return [
            'cliente' => Cliente::class,
            'proveedor' => Proveedor::class,
        ][$type];
    }

    private function contactRules(bool $isCreate = false): array
    {
        return [
            'first_name' => [$isCreate ? 'required' : 'sometimes', 'string', 'max:255'],
            'father_last_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'mother_last_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'job_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'extension' => ['sometimes', 'nullable', 'string', 'max:10'],
            'mobile_whatsapp' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'country' => ['sometimes', 'string', 'max:100'],
            'state' => ['sometimes', 'nullable', 'string', 'max:100'],
            'city_delegation' => ['sometimes', 'nullable', 'string', 'max:100'],
            'street' => ['sometimes', 'nullable', 'string', 'max:255'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:10'],
            'neighborhood' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notify_order' => ['sometimes', 'boolean'],
            'notify_quote' => ['sometimes', 'boolean'],
            'notify_invoice' => ['sometimes', 'boolean'],
            'notify_statement' => ['sometimes', 'boolean'],
            'notify_tracking' => ['sometimes', 'boolean'],
        ];
    }
}