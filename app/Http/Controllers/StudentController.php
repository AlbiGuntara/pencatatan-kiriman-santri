<?php

namespace App\Http\Controllers;

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
        'Ma\'had Aly',
        'Nurul Jadid/Qodliyah',
        'Al-Khuzaimah',
        'Az-Zainiyah',
    ];

    public function index(Request $request): Response
    {
        $students = Student::query()
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
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('students/Index', [
            'students' => $students,
            'filters' => $request->only(['search', 'location_type']),
            'locations' => self::LOCATIONS,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('students/Create', [
            'locations' => self::LOCATIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:255', 'unique:students,nis'],
            'name' => ['required', 'string', 'max:255'],
            'dormitory' => ['required', 'string', 'max:255'],
            'location_type' => ['required', 'in:'.implode(',', self::LOCATIONS)],
        ]);

        Student::query()->create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Data santri berhasil ditambahkan.']);

        return to_route('students.index');
    }

    public function edit(Student $student): Response
    {
        return Inertia::render('students/Edit', [
            'student' => $student,
            'locations' => self::LOCATIONS,
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:255', 'unique:students,nis,'.$student->id],
            'name' => ['required', 'string', 'max:255'],
            'dormitory' => ['required', 'string', 'max:255'],
            'location_type' => ['required', 'in:'.implode(',', self::LOCATIONS)],
        ]);

        $student->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Data santri berhasil diperbarui.']);

        return to_route('students.index');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Data santri berhasil dihapus.']);

        return to_route('students.index');
    }

    /**
     * JSON endpoint for quick student search during shipment input.
     */
    public function search(Request $request): JsonResponse
    {
        $search = trim($request->string('search'));

        $students = Student::query()
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
