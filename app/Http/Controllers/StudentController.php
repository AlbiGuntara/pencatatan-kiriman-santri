<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public const LOCATIONS = [
        'Pusat Putra',
        'Pusat Putri',
        "Ma'had Aly",
        'Nurul Jadid/Qodliyah',
        'Al-Khuzaimah',
        'Az-Zainiyah',
    ];

    public function index(Request $request): Response
    {
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->lower()->toString() === 'desc' ? 'desc' : 'asc';

        $students = Student::query()
            ->with('regions:id,name,code')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search'));

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('dormitory', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('location_type'), function ($query) use ($request) {
                $query->where('location_type', $request->string('location_type'));
            })
            ->when($request->filled('region_iksass_id'), function ($query) use ($request) {
                $query->whereHas('regions', function ($q) use ($request) {
                    $q->where('regions.id', $request->integer('region_iksass_id'));
                });
            })
            ->when(in_array($sort, ['nis', 'name', 'dormitory', 'location_type'], true), function ($query) use ($sort, $direction) {
                $query->orderBy($sort, $direction);
            })
            ->when($sort === 'no', function ($query) use ($direction) {
                $query->orderBy('id', $direction);
            })
            ->when($sort === '', fn ($query) => $query->latest())
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render('students/Index', [
            'students' => $students,
            'filters' => $request->only(['search', 'location_type', 'region_iksass_id', 'per_page', 'sort', 'direction']),
            'locations' => self::LOCATIONS,
            'regions' => Region::query()->orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('students/Create', [
            'locations' => self::LOCATIONS,
            'regions' => $request->user()->isSuperAdmin()
                ? Region::query()->orderBy('name')->get(['id', 'name', 'code'])
                : [],
            'back' => $request->string('back')->trim()->toString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'nis' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'dormitory' => ['required', 'string', 'max:255'],
            'location_type' => ['required', 'in:'.implode(',', self::LOCATIONS)],
            'region_iksass_ids' => ['nullable', 'array'],
            'region_iksass_ids.*' => ['exists:regions,id'],
        ];

        if ($user->isSuperAdmin()) {
            $rules['nis'][] = 'unique:students,nis';
        }

        $data = $request->validate($rules);

        $regionIds = $data['region_iksass_ids'] ?? [];

        if ($user->region_iksass_id) {
            $regionIds = array_unique(array_merge($regionIds, [$user->region_iksass_id]));
        }

        $existingStudent = Student::where('nis', $data['nis'])->first();

        if ($existingStudent) {
            $existingStudent->regions()->syncWithoutDetaching($regionIds);

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => 'Santri berhasil ditambahkan ke IKSASS yang dipilih.',
            ]);

            return $this->redirectToList($request, '/students', 'students.index');
        }

        $student = Student::query()->create($data);
        $student->regions()->attach($regionIds);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Data santri berhasil ditambahkan.']);

        return $this->redirectToList($request, '/students', 'students.index');
    }

    public function edit(Request $request, Student $student): Response
    {
        $student->load('regions:id,name,code');

        return Inertia::render('students/Edit', [
            'student' => $student,
            'locations' => self::LOCATIONS,
            'regions' => $request->user()->isSuperAdmin()
                ? Region::query()->orderBy('name')->get(['id', 'name', 'code'])
                : [],
            'back' => $request->string('back')->trim()->toString(),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'nis' => ['required', 'string', 'max:255', 'unique:students,nis,'.$student->id],
            'name' => ['required', 'string', 'max:255'],
            'dormitory' => ['required', 'string', 'max:255'],
            'location_type' => ['required', 'in:'.implode(',', self::LOCATIONS)],
            'region_iksass_ids' => ['nullable', 'array'],
            'region_iksass_ids.*' => ['exists:regions,id'],
        ]);

        $regionIds = $data['region_iksass_ids'] ?? [];

        if ($user->region_iksass_id) {
            $regionIds = array_unique(array_merge($regionIds, [$user->region_iksass_id]));
        }

        $student->update($data);
        $student->regions()->sync($regionIds);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Data santri berhasil diperbarui.']);

        return $this->redirectToList($request, '/students', 'students.index');
    }

    public function destroy(Request $request, Student $student): RedirectResponse
    {
        $student->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Data santri berhasil dihapus.']);

        return $this->redirectToList($request, '/students', 'students.index');
    }

    public function search(Request $request): JsonResponse
    {
        $search = trim($request->string('search'));

        $students = Student::query()
            ->with('regions:id,name,code')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'nis', 'name', 'dormitory', 'location_type']);

        return response()->json($students);
    }
}
