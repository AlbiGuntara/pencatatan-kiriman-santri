<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Shipment;
use App\Models\ShipmentLog;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShipmentController extends Controller
{
    public function index(Request $request): Response
    {
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->lower()->toString() === 'desc' ? 'desc' : 'asc';

        $shipments = Shipment::query()
            ->with(['student:id,nis,name,dormitory,location_type', 'region:id,name,code', 'creator:id,name'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search'));

                $query->where(function ($query) use ($search) {
                    $query->where('tracking_code', 'like', "%{$search}%")
                        ->orWhere('sender_name', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($student) use ($search) {
                            $student->where('name', 'like', "%{$search}%")
                                ->orWhere('nis', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status') && (string) $request->string('status') !== 'all', function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->when($request->filled('region_id') && $request->integer('region_id') > 0, function ($query) use ($request) {
                $query->where('region_iksass_id', $request->integer('region_id'));
            })
            ->when(in_array($sort, ['tracking_code', 'sender_name', 'package_count', 'amount_money', 'status', 'updated_at', 'created_at'], true), function ($query) use ($sort, $direction) {
                $query->orderBy($sort, $direction);
            })
            ->when($sort === 'no', function ($query) use ($direction) {
                $query->orderBy('id', $direction);
            })
            ->when($sort === 'student', function ($query) use ($direction) {
                $query->orderBy(
                    Student::select('name')->whereColumn('students.id', 'shipments.student_id'),
                    $direction,
                );
            })
            ->when($sort === 'region', function ($query) use ($direction) {
                $query->orderBy(
                    Region::select('name')->whereColumn('regions.id', 'shipments.region_iksass_id'),
                    $direction,
                );
            })
            ->when($sort === '', fn ($query) => $query->latest())
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render('shipments/Index', [
            'shipments' => $shipments,
            'filters' => $request->only(['search', 'status', 'region_id', 'per_page', 'sort', 'direction']),
            'statuses' => Shipment::STATUSES,
            'regions' => $request->user()->isSuperAdmin()
                ? Region::query()->orderBy('name')->get(['id', 'name', 'code'])
                : [],
            'isSuperAdmin' => $request->user()->isSuperAdmin(),
            'importErrors' => $request->session()->get('import_errors', []),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('shipments/Create', [
            'locations' => StudentController::LOCATIONS,
            'region' => $request->user()->isSuperAdmin()
                ? null
                : $request->user()->region()->first(['id', 'name', 'code']),
            'regions' => $request->user()->isSuperAdmin()
                ? Region::query()->orderBy('name')->get(['id', 'name', 'code'])
                : [],
            'back' => $request->string('back')->trim()->toString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_phone' => ['nullable', 'string', 'max:50'],
            'amount_money' => ['nullable', 'numeric', 'min:0'],
            'package_count' => ['required', 'integer', 'min:1', 'max:99'],
            'location_type' => ['nullable', 'string', 'max:255'],
        ]);

        $region = null;

        if ($user->isSuperAdmin()) {
            $regionId = $request->input('region_iksass_id', $request->input('region_id'));
            $region = is_numeric($regionId)
                ? Region::query()->whereKey((int) $regionId)->first()
                : null;
        } elseif ($user->region_iksass_id) {
            $region = Region::query()->whereKey($user->region_iksass_id)->first();
        }

        if (! $region) {
            return back()->withErrors(['region_iksass_id' => 'Wilayah IKSASS wajib dipilih.']);
        }

        $shipment = Shipment::query()->create([
            'tracking_code' => Shipment::generateTrackingCode($region),
            'student_id' => $data['student_id'],
            'region_iksass_id' => $region->id,
            'sender_name' => $data['sender_name'],
            'sender_phone' => $data['sender_phone'],
            'location_type' => $data['location_type'],
            'amount_money' => $data['amount_money'] ?? 0,
            'package_count' => $data['package_count'],
            'status' => Shipment::STATUS_CREATED,
            'created_by' => $user->id,
        ]);

        ShipmentLog::query()->create([
            'shipment_id' => $shipment->id,
            'status_from' => null,
            'status_to' => Shipment::STATUS_CREATED,
            'notes' => 'Kiriman dicatat dan stiker siap dicetak.',
            'logged_by' => $user->id,
            'created_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => "Kiriman {$shipment->tracking_code} berhasil dicatat."]);

        $params = ['shipment' => $shipment->id];
        $back = $this->internalBackUrl($request->string('back')->toString());

        if ($back !== '') {
            $params['back'] = $back;
        }

        return to_route('shipments.show', $params);
    }

    public function import(Request $request): RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'file' => ['required', 'file', 'extensions:xlsx,xls,csv', 'max:10240'],
            'region_iksass_id' => ['nullable', 'integer', 'exists:regions,id'],
        ];

        if ($user->isSuperAdmin()) {
            $rules['region_iksass_id'] = ['required', 'integer', 'exists:regions,id'];
        }

        $data = $request->validate($rules);

        /** @var Region|null $defaultRegion */
        $defaultRegion = $user->isSuperAdmin()
            ? Region::query()->whereKey((int) $data['region_iksass_id'])->first()
            : ($user->region_iksass_id ? Region::query()->whereKey($user->region_iksass_id)->first() : null);

        if (! $defaultRegion) {
            return back()->withErrors(['region_iksass_id' => 'Wilayah IKSASS wajib dipilih untuk mengimpor kiriman.']);
        }

        /** @var UploadedFile|null $file */
        $file = $request->file('file');

        if (! $file) {
            return back()->withErrors(['file' => 'File Excel wajib diunggah.']);
        }

        $worksheet = IOFactory::load($file->getRealPath())->getActiveSheet();

        /** @var array<int, array<string, mixed>> $rows */
        $rows = $worksheet->toArray(null, true, true, true);

        $headerIndex = null;

        foreach ($rows as $index => $row) {
            $firstCell = strtolower((string) ($row['A'] ?? ''));

            if (str_contains($firstCell, 'nis')) {
                $headerIndex = $index;
                break;
            }
        }

        if ($headerIndex === null) {
            $headerIndex = array_key_first($rows);
        }

        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if ($index <= $headerIndex) {
                continue;
            }

            $rowNumber = $index + 1;

            $nis = trim((string) ($row['A'] ?? ''));
            $senderName = trim((string) ($row['B'] ?? ''));
            $senderPhone = trim((string) ($row['C'] ?? ''));
            $packageCount = trim((string) ($row['D'] ?? ''));
            $amountMoney = trim((string) ($row['E'] ?? ''));
            $locationType = trim((string) ($row['F'] ?? ''));
            $regionCode = trim((string) ($row['G'] ?? ''));

            if ($nis === '' && $senderName === '' && $packageCount === '') {
                continue;
            }

            $rowErrors = [];
            $student = null;

            if ($nis === '') {
                $rowErrors[] = 'kolom NIS kosong';
            } else {
                $student = Student::query()->where('nis', $nis)->first();

                if (! $student) {
                    $rowErrors[] = sprintf('santri dengan NIS "%s" tidak ditemukan', $nis);
                }
            }

            if ($senderName === '') {
                $rowErrors[] = 'kolom nama pengirim kosong';
            }

            if ($packageCount === '' || ! ctype_digit($packageCount) || (int) $packageCount < 1 || (int) $packageCount > 99) {
                $rowErrors[] = 'jumlah paket harus angka 1–99';
            }

            $amountDigits = preg_replace('/\D/', '', $amountMoney) ?? '';

            if ($amountMoney !== '' && $amountDigits === '') {
                $rowErrors[] = 'jumlah uang tidak valid';
            }

            if ($locationType !== '' && ! in_array($locationType, StudentController::LOCATIONS, true)) {
                $rowErrors[] = sprintf('lokasi santri "%s" tidak valid', $locationType);
            }

            $region = $defaultRegion;

            if ($regionCode !== '' && $user->isSuperAdmin()) {
                $region = Region::query()->where('code', $regionCode)->first();

                if (! $region) {
                    $rowErrors[] = sprintf('kode wilayah "%s" tidak ditemukan', $regionCode);
                }
            }

            if ($rowErrors !== []) {
                foreach ($rowErrors as $rowError) {
                    $errors[] = "Baris {$rowNumber}: {$rowError}.";
                }

                continue;
            }

            $shipment = Shipment::query()->create([
                'tracking_code' => Shipment::generateTrackingCode($region),
                'student_id' => $student->id,
                'region_iksass_id' => $region->id,
                'sender_name' => $senderName,
                'sender_phone' => $senderPhone !== '' ? $senderPhone : null,
                'location_type' => $locationType !== '' ? $locationType : null,
                'amount_money' => $amountMoney === '' ? 0 : (int) $amountDigits,
                'package_count' => (int) $packageCount,
                'status' => Shipment::STATUS_CREATED,
                'created_by' => $user->id,
            ]);

            ShipmentLog::query()->create([
                'shipment_id' => $shipment->id,
                'status_from' => null,
                'status_to' => Shipment::STATUS_CREATED,
                'notes' => 'Kiriman diimpor dari file Excel.',
                'logged_by' => $user->id,
                'created_at' => now(),
            ]);

            $imported++;
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => "{$imported} kiriman berhasil diimpor."]);

        if ($errors !== []) {
            return back()->with('import_errors', $errors);
        }

        return back();
    }

    public function downloadImportTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'NIS',
            'Nama Pengirim',
            'No. HP Pengirim',
            'Jumlah Paket (Koli)',
            'Jumlah Uang (Rp)',
            'Lokasi Santri',
            'Kode Wilayah',
        ];

        $sheet->fromArray([$headers], null, 'A1');
        $sheet->fromArray([
            ['240101', 'H. Ahmad Zainuddin', '081234567890', 1, 100000, 'Pusat Putra', 'JBR'],
            ['', 'H. Ahmad Zainuddin', '081234567890', 2, 50000, 'Pusat Putri', ''],
        ], null, 'A2');

        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setWidth(22);
        }

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $guide = $spreadsheet->createSheet();
        $guide->setTitle('Petunjuk');
        $guide->setCellValue('A1', 'Petunjuk Pengisian File Impor Kiriman');
        $guide->fromArray([
            ['Kolom', 'Keterangan'],
            ['NIS (*wajib)', 'Nomor induk santri penerima. Harus sudah terdaftar di sistem.'],
            ['Nama Pengirim (*wajib)', 'Nama wali santri / pengirim.'],
            ['No. HP Pengirim', 'Nomor HP pengirim (opsional).'],
            ['Jumlah Paket (Koli) (*wajib)', 'Jumlah paket/koli, angka 1–99.'],
            ['Jumlah Uang (Rp)', 'Nominal uang kiriman (opsional). Boleh memakai tanda titik/format Rp.'],
            ['Lokasi Santri', 'Pilih salah satu: Pusat Putra, Pusat Putri, Ma\'had Aly, Nurul Jadid/Qodliyah, Al-Khuzaimah, Az-Zainiyah.'],
            ['Kode Wilayah', 'Khusus Super Admin. Isi kode wilayah untuk mengubah wilayah baris tersebut. Kosongkan untuk memakai wilayah yang dipilih saat mengimpor.'],
        ], null, 'A3');
        $guide->getColumnDimension('A')->setWidth(30);
        $guide->getColumnDimension('B')->setWidth(60);

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template-kiriman.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function show(Request $request, Shipment $shipment): Response
    {
        $shipment->load([
            'student:id,nis,name,dormitory,location_type',
            'region:id,name,code',
            'creator:id,name',
            'logs.logger:id,name',
        ]);

        return Inertia::render('shipments/Show', [
            'shipment' => [
                'id' => $shipment->id,
                'tracking_code' => $shipment->tracking_code,
                'status' => $shipment->status,
                'status_label' => Shipment::STATUSES[$shipment->status] ?? $shipment->status,
                'statuses' => Shipment::STATUSES,
                'sender_name' => $shipment->sender_name,
                'sender_phone' => $shipment->sender_phone,
                'amount_money' => (int) $shipment->amount_money,
                'package_count' => $shipment->package_count,
                'location_type' => $shipment->location_type,
                'created_at' => $shipment->created_at?->toISOString(),
                'updated_at' => $shipment->updated_at?->toISOString(),
                'student' => $shipment->student,
                'region' => $shipment->region,
                'creator' => $shipment->creator,
                'logs' => $shipment->logs->map(fn (ShipmentLog $log) => [
                    'id' => $log->id,
                    'status_from' => $log->status_from,
                    'status_to' => $log->status_to,
                    'status_to_label' => Shipment::STATUSES[$log->status_to] ?? $log->status_to,
                    'notes' => $log->notes,
                    'attachment_url' => $log->attachment_path ? Storage::disk('public')->url($log->attachment_path) : null,
                    'logger' => $log->logger?->name,
                    'created_at' => $log->created_at->toISOString(),
                ]),
            ],
            'canUpdateStatus' => $shipment->status !== Shipment::STATUS_DELIVERED,
            'printUrl' => route('shipments.print', ['shipments' => [$shipment->id]]),
            'back' => $request->string('back')->trim()->toString(),
        ]);
    }

    public function updateStatus(Request $request, Shipment $shipment): RedirectResponse
    {
        $allowedNext = match ($shipment->status) {
            Shipment::STATUS_CREATED => [Shipment::STATUS_IN_TRANSIT],
            Shipment::STATUS_IN_TRANSIT => [Shipment::STATUS_DELIVERED],
            default => [],
        };

        $data = $request->validate([
            'status' => ['required', Rule::in($allowedNext)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachment' => ['nullable', 'image', 'max:5120'],
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store(
                'shipment-photos/'.$shipment->id,
                'public',
            );
        }

        $previousStatus = $shipment->status;
        $shipment->status = $data['status'];
        $shipment->save();

        ShipmentLog::query()->create([
            'shipment_id' => $shipment->id,
            'status_from' => $previousStatus,
            'status_to' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'attachment_path' => $attachmentPath,
            'logged_by' => $request->user()->id,
            'created_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Status kiriman diperbarui menjadi '.($shipment::STATUSES[$data['status']] ?? $data['status']).'.']);

        return back();
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:shipments,id'],
            'status' => ['required', Rule::in(array_keys(Shipment::STATUSES))],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachment' => ['nullable', 'image', 'max:5120'],
        ]);

        $status = $data['status'];

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store(
                'shipment-photos/bulk-'.now()->format('Ymd-His'),
                'public',
            );
        }

        $shipments = Shipment::query()
            ->whereIn('id', $data['ids'])
            ->get();

        $updated = 0;
        $skipped = 0;

        foreach ($shipments as $shipment) {
            $allowedNext = match ($shipment->status) {
                Shipment::STATUS_CREATED => [Shipment::STATUS_IN_TRANSIT],
                Shipment::STATUS_IN_TRANSIT => [Shipment::STATUS_DELIVERED],
                default => [],
            };

            if (! in_array($status, $allowedNext, true)) {
                $skipped++;

                continue;
            }

            $previousStatus = $shipment->status;
            $shipment->status = $status;
            $shipment->save();

            ShipmentLog::query()->create([
                'shipment_id' => $shipment->id,
                'status_from' => $previousStatus,
                'status_to' => $status,
                'notes' => $data['notes'] ?? null,
                'attachment_path' => $attachmentPath,
                'logged_by' => $request->user()->id,
                'created_at' => now(),
            ]);

            $updated++;
        }

        if ($updated > 0) {
            Inertia::flash('toast', ['type' => 'success', 'message' => "{$updated} kiriman diubah menjadi ".(Shipment::STATUSES[$status] ?? $status).'.']);
        }

        if ($skipped > 0 && $updated === 0) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Tidak ada kiriman yang dapat diubah ke status tersebut.']);
        } elseif ($skipped > 0) {
            Inertia::flash('toast', ['type' => 'warning', 'message' => "{$skipped} kiriman dilewati karena statusnya tidak dapat diubah."]);
        }

        return back();
    }

    public function print(Request $request): Response
    {
        $ids = collect((array) $request->query('shipments', []))
            ->flatMap(fn ($value) => is_array($value) ? $value : explode(',', (string) $value))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();

        if (empty($ids)) {
            abort(404);
        }

        $shipments = Shipment::query()
            ->with(['student:id,nis,name,dormitory', 'region:id,name,code'])
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->get();

        if ($shipments->isEmpty()) {
            abort(404);
        }

        return Inertia::render('shipments/PrintStickers', [
            'shipments' => $shipments->map(fn (Shipment $shipment) => [
                'id' => $shipment->id,
                'tracking_code' => $shipment->tracking_code,
                'package_count' => $shipment->package_count,
                'student_name' => $shipment->student?->name,
                'nis' => $shipment->student?->nis,
                'dormitory' => $shipment->student?->dormitory,
                'sender_name' => $shipment->sender_name,
                'region_name' => $shipment->region?->name,
            ]),
        ]);
    }

    public function destroy(Request $request, Shipment $shipment): RedirectResponse
    {
        $trackingCode = $shipment->tracking_code;
        $shipment->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => "Kiriman {$trackingCode} berhasil dihapus."]);

        return $this->redirectToList($request, '/shipments', 'shipments.index');
    }

    public function destroyMany(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:shipments,id'],
        ]);

        $count = Shipment::query()
            ->whereIn('id', $data['ids'])
            ->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => "{$count} kiriman berhasil dihapus."]);

        return $this->redirectToList($request, '/shipments', 'shipments.index');
    }
}
