<?php

namespace App\Http\Controllers\Api;

use App\Enums\PropertyStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class PropertyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Property::class);

        $properties = Property::visibleTo($request->user())
            ->with(['owner:id,name', 'manager:id,name'])
            ->withCount('units')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->string('search') . '%';
                $q->where(fn($w) => $w->where('name', 'ilike', $term)->orWhere('city', 'ilike', $term));
            })
            ->when($request->filled('city'), fn($q) => $q->where('city', $request->string('city')))
            ->when($request->filled('property_type'), fn($q) => $q->where('property_type', $request->string('property_type')))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->string('status')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return PropertyResource::collection($properties);
    }

    public function store(PropertyRequest $request): JsonResponse
    {
        Gate::authorize('create', Property::class);

        $data = $request->validated();

        // Managers always create inside their own organization.
        $data['organization_id'] = $request->user()->hasRole('super_admin')
            ? $data['organization_id']
            : $request->user()->organization_id;

        $data['country'] ??= 'Namibia';
        $data['status']  ??= PropertyStatus::Active->value;

        $property = Property::create($data)
            ->load(['owner:id,name', 'manager:id,name'])
            ->loadCount('units');

        return (new PropertyResource($property))->response()->setStatusCode(201);
    }

    public function show(Request $request, string $id): PropertyResource
    {
        $property = $this->findVisible($request, $id);
        Gate::authorize('view', $property);

        return new PropertyResource(
            $property->load(['owner:id,name', 'manager:id,name'])->loadCount('units')
        );
    }

    public function update(PropertyRequest $request, string $id): PropertyResource
    {
        $property = $this->findVisible($request, $id);
        Gate::authorize('update', $property);

        $property->update($request->validated());

        return new PropertyResource(
            $property->load(['owner:id,name', 'manager:id,name'])->loadCount('units')
        );
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $property = $this->findVisible($request, $id);
        Gate::authorize('delete', $property);

        // PENDING: once leases exist, also block deletion while any lease is active.
        if ($property->units()->exists()) {
            return response()->json([
                'message' => 'This property still has units. Remove its units before deleting it.',
            ], 409);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted.']);
    }

    /**
     * Find a property through the visibility scope. Anything outside the
     * user's reach is a 404, so we never confirm that the record exists.
     */
    private function findVisible(Request $request, string $id): Property
    {
        return Property::visibleTo($request->user())->findOrFail($id);
    }
}
