<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Cliente;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'activitable_type' => [
                'nullable',
                'required_with:activitable_id',
                Rule::in(['cliente', 'proveedor']),
            ],
            'activitable_id' => [
                'nullable',
                'integer',
                'required_with:activitable_type',
            ],
            'type' => ['nullable', Rule::in($this->activityTypes())],
            'status' => ['nullable', Rule::in($this->activityStatuses())],
        ]);

        $query = Activity::with(['activitable', 'user'])->latest('id');

        if (!empty($filters['activitable_type'])) {
            $activitableClass = $this->activitableClass($filters['activitable_type']);

            $query->where('activitable_type', (new $activitableClass)->getMorphClass())
                ->where('activitable_id', $filters['activitable_id']);
        }

        $query->when(isset($filters['type']), function ($query) use ($filters) {
            $query->where('type', $filters['type']);
        });

        $query->when(isset($filters['status']), function ($query) use ($filters) {
            $query->where('status', $filters['status']);
        });

        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return response()->json($query->paginate($perPage));
    }

    public function show(Activity $activity)
    {
        return response()->json($activity->load(['activitable', 'user']));
    }

    public function store(Request $request)
    {
        $parent = $request->validate([
            'activitable_type' => ['required', Rule::in(['cliente', 'proveedor'])],
        ]);

        $activitableClass = $this->activitableClass($parent['activitable_type']);
        $validated = $request->validate(array_merge([
            'activitable_id' => [
                'required',
                'integer',
                Rule::exists((new $activitableClass)->getTable(), 'id'),
            ],
        ], $this->activityRules(true)));

        $activitable = $activitableClass::findOrFail($validated['activitable_id']);
        unset($validated['activitable_id']);

        $activity = new Activity($validated);
        $activity->activitable()->associate($activitable);
        $activity->user()->associate($request->user());
        $activity->save();

        return response()->json([
            'message' => 'Actividad registrada correctamente.',
            'data' => $activity->load(['activitable', 'user']),
        ], 201);
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate($this->activityRules());

        if ($request->hasAny(['activitable_type', 'activitable_id'])) {
            $parent = $request->validate([
                'activitable_type' => ['required', Rule::in(['cliente', 'proveedor'])],
                'activitable_id' => ['required', 'integer'],
            ]);

            $activitableClass = $this->activitableClass($parent['activitable_type']);
            $request->validate([
                'activitable_id' => [
                    Rule::exists((new $activitableClass)->getTable(), 'id'),
                ],
            ]);

            $activity->activitable()->associate(
                $activitableClass::findOrFail($parent['activitable_id'])
            );
        }

        $activity->update($validated);

        return response()->json([
            'message' => 'Actividad actualizada correctamente.',
            'data' => $activity->fresh()->load(['activitable', 'user']),
        ]);
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return response()->json([
            'message' => 'Actividad eliminada correctamente.',
        ]);
    }

    private function activitableClass(string $type): string
    {
        return [
            'cliente' => Cliente::class,
            'proveedor' => Proveedor::class,
        ][$type];
    }

    private function activityRules(bool $isCreate = false): array
    {
        return [
            'title' => [$isCreate ? 'required' : 'sometimes', 'string', 'max:255'],
            'type' => ['sometimes', Rule::in($this->activityTypes())],
            'description' => ['sometimes', 'nullable', 'string'],
            'scheduled_at' => ['sometimes', 'nullable', 'date'],
            'completed_at' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', Rule::in($this->activityStatuses())],
        ];
    }

    private function activityTypes(): array
    {
        return ['call', 'meeting', 'email', 'task', 'other'];
    }

    private function activityStatuses(): array
    {
        return ['pending', 'in_progress', 'completed', 'cancelled'];
    }
}