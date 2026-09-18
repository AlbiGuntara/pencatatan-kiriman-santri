<?php

use App\Models\Region;
use App\Models\Shipment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function makeSpreadsheet(array $rows, string $name = 'import.xlsx'): UploadedFile
{
    $spreadsheet = new Spreadsheet;
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray([
        ['NIS', 'Nama Pengirim', 'No. HP Pengirim', 'Jumlah Paket (Koli)', 'Jumlah Uang (Rp)', 'Lokasi Santri', 'Kode Wilayah'],
        ...$rows,
    ], null, 'A1');

    $path = tempnam(sys_get_temp_dir(), 'sipenri_').'.xlsx';
    (new Xlsx($spreadsheet))->save($path);

    return new UploadedFile(
        $path,
        $name,
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true,
    );
}

test('super admin can import shipments from an excel file', function () {
    $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $region = Region::factory()->create();
    $student = Student::factory()->create(['nis' => '240101', 'location_type' => 'Pusat Putra']);

    $file = makeSpreadsheet([
        ['240101', 'H. Ahmad Zainuddin', '081234567890', 2, 150000, 'Pusat Putra', ''],
    ]);

    $this->actingAs($admin)
        ->post(route('shipments.import'), [
            'region_iksass_id' => $region->id,
            'file' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('shipments', [
        'student_id' => $student->id,
        'region_iksass_id' => $region->id,
        'sender_name' => 'H. Ahmad Zainuddin',
        'sender_phone' => '081234567890',
        'amount_money' => 150000,
        'package_count' => 2,
        'location_type' => 'Pusat Putra',
        'status' => Shipment::STATUS_CREATED,
        'created_by' => $admin->id,
    ]);

    $this->assertDatabaseCount('shipment_logs', 1);
});

test('super admin can override the region per row using the region code column', function () {
    $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $region = Region::factory()->create();
    $otherRegion = Region::factory()->create();
    Student::factory()->create(['nis' => '240101']);

    $file = makeSpreadsheet([
        ['240101', 'H. Ahmad', '', 1, 0, '', $otherRegion->code],
    ]);

    $this->actingAs($admin)
        ->post(route('shipments.import'), [
            'region_iksass_id' => $region->id,
            'file' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('shipments', [
        'sender_name' => 'H. Ahmad',
        'region_iksass_id' => $otherRegion->id,
    ]);
});

test('rows with invalid data are skipped and reported', function () {
    $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $region = Region::factory()->create();
    Student::factory()->create(['nis' => '240101']);
    Student::factory()->create(['nis' => '240102']);

    $file = makeSpreadsheet([
        ['240101', 'H. Ahmad', '08123', 1, 100000, 'Pusat Putra', ''],
        ['999999', 'H. Badu', '08222', 1, 100000, 'Pusat Putra', ''],
        ['240102', 'H. Cici', '08333', 500, 100000, 'Tempat Salah', $region->code],
    ]);

    $this->actingAs($admin)
        ->post(route('shipments.import'), [
            'region_iksass_id' => $region->id,
            'file' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('shipments', 1);
    $this->assertDatabaseHas('shipments', ['sender_name' => 'H. Ahmad']);

    $this->get(route('shipments.index'))
        ->assertInertia(fn ($page) => $page
            ->component('shipments/Index')
            ->where('importErrors', [
                'Baris 4: santri dengan NIS "999999" tidak ditemukan.',
                'Baris 5: jumlah paket harus angka 1–99.',
                'Baris 5: lokasi santri "Tempat Salah" tidak valid.',
            ]));
});

test('iksass admin can only import to their own region', function () {
    $region = Region::factory()->create();
    $admin = User::factory()->create([
        'role' => User::ROLE_IKSASS_ADMIN,
        'region_iksass_id' => $region->id,
    ]);
    $student = Student::factory()->create(['nis' => '240101', 'location_type' => 'Pusat Putra']);
    $student->regions()->attach($region->id);

    $file = makeSpreadsheet([
        ['240101', 'H. Ahmad', '', 1, 50000, 'Pusat Putra', 'Lain'],
    ]);

    $this->actingAs($admin)
        ->post(route('shipments.import'), [
            'file' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('shipments', [
        'sender_name' => 'H. Ahmad',
        'region_iksass_id' => $region->id,
    ]);
});

test('super admin must pick a region when importing', function () {
    $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $student = Student::factory()->create(['nis' => '240101']);

    $file = makeSpreadsheet([
        ['240101', 'H. Ahmad', '', 1, 0, '', ''],
    ]);

    $this->actingAs($admin)
        ->post(route('shipments.import'), [
            'file' => $file,
        ])
        ->assertSessionHasErrors('region_iksass_id');

    $this->assertDatabaseCount('shipments', 0);
});
