<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegionController extends Controller
{
    public function index(Request $request): Response
    {
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->lower()->toString() === 'desc' ? 'desc' : 'asc';

        $regions = Region::query()
            ->withCount(['shipments', 'users'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search'));

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when(in_array($sort, ['code', 'name', 'shipments_count', 'users_count'], true), function ($query) use ($sort, $direction) {
                $query->orderBy($sort, $direction);
            })
            ->when($sort === 'no', function ($query) use ($direction) {
                $query->orderBy('id', $direction);
            })
            ->when($sort === '', fn ($query) => $query->latest())
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render('regions/Index', [
            'regions' => $regions,
            'filters' => $request->only(['search', 'per_page', 'sort', 'direction']),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('regions/Create', [
            'back' => $request->string('back')->trim()->toString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:regions,code'],
        ]);

        Region::query()->create([
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Wilayah IKSASS berhasil ditambahkan.']);

        return $this->redirectToList($request, '/regions', 'regions.index');
    }

    public function edit(Request $request, Region $region): Response
    {
        return Inertia::render('regions/Edit', [
            'region' => $region,
            'back' => $request->string('back')->trim()->toString(),
        ]);
    }

    public function update(Request $request, Region $region): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:regions,code,'.$region->id],
        ]);

        $region->update([
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Wilayah IKSASS berhasil diperbarui.']);

        return $this->redirectToList($request, '/regions', 'regions.index');
    }

    public function destroy(Region $region): RedirectResponse
    {
        if (User::query()->where('region_iksass_id', $region->id)->exists()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Wilayah tidak dapat dihapus karena masih memiliki pengurus IKSASS.']);

            return back();
        }

        if ($region->shipments()->exists()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Wilayah tidak dapat dihapus karena masih memiliki data kiriman.']);

            return back();
        }

        $region->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Wilayah IKSASS berhasil dihapus.']);

        return $this->redirectToList($request, '/regions', 'regions.index');
    }
}
