<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@pinjambarang.test'],
            [
                'name' => 'Admin Barang',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create sample users
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user$i@pinjambarang.test"],
                [
                    'name' => "User $i",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            if (! $user->hasRole('user')) {
                $user->assignRole('user');
            }
        }

        // Create categories
        $categories = [
            ['nama_kategori' => 'Elektronik', 'deskripsi' => 'Perangkat elektronik dan gadget'],
            ['nama_kategori' => 'Furniture', 'deskripsi' => 'Perabotan kantor dan rumah'],
            ['nama_kategori' => 'Tools', 'deskripsi' => 'Peralatan dan alat-alat'],
            ['nama_kategori' => 'Multimedia', 'deskripsi' => 'Perangkat multimedia dan audio'],
            ['nama_kategori' => 'Office', 'deskripsi' => 'Perlengkapan kantor'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Create sample items
        $items = [
            [
                'nama_barang' => 'Laptop Lenovo',
                'deskripsi' => 'Laptop bisnis dengan spesifikasi standar',
                'category_id' => 1,
                'lokasi' => 'Ruang Server',
                'status' => 'tersedia',
                'created_by' => $admin->id,
            ],
            [
                'nama_barang' => 'Proyektor',
                'deskripsi' => 'Proyektor untuk presentasi',
                'category_id' => 1,
                'lokasi' => 'Ruang Rapat',
                'status' => 'tersedia',
                'created_by' => $admin->id,
            ],
            [
                'nama_barang' => 'Meja Kerja',
                'deskripsi' => 'Meja kerja standar dengan 4 laci',
                'category_id' => 2,
                'lokasi' => 'Gudang',
                'status' => 'tersedia',
                'created_by' => $admin->id,
            ],
            [
                'nama_barang' => 'Kursi Kerja',
                'deskripsi' => 'Kursi ergonomis warna hitam',
                'category_id' => 2,
                'lokasi' => 'Gudang',
                'status' => 'dipinjam',
                'created_by' => $admin->id,
            ],
            [
                'nama_barang' => 'Printer HP',
                'deskripsi' => 'Printer all-in-one jet ink',
                'category_id' => 1,
                'lokasi' => 'Ruang Kantor',
                'status' => 'perbaikan',
                'created_by' => $admin->id,
            ],
            [
                'nama_barang' => 'Speaker Wireless',
                'deskripsi' => 'Speaker Bluetooth portable',
                'category_id' => 4,
                'lokasi' => 'Ruang Meeting',
                'status' => 'tersedia',
                'created_by' => $admin->id,
            ],
            [
                'nama_barang' => 'Microfon Condenser',
                'deskripsi' => 'Mikrofon profesional untuk recording',
                'category_id' => 4,
                'lokasi' => 'Studio',
                'status' => 'tersedia',
                'created_by' => $admin->id,
            ],
            [
                'nama_barang' => 'Penggaris',
                'deskripsi' => 'Penggaris 30cm plastik',
                'category_id' => 5,
                'lokasi' => 'Gudang',
                'status' => 'tersedia',
                'created_by' => $admin->id,
            ],
        ];

        $createdItems = [];
        foreach ($items as $item) {
            $newItem = Item::create($item);
            $newItem->generateKodeBarang();
            $newItem->save();
            $createdItems[] = $newItem;
        }

        // Create sample loans
        $users = User::where('email', '!=', 'admin@pinjambarang.test')->get();

        // Active loan
        if ($users->count() > 0 && count($createdItems) > 1) {
            $loan = Loan::create([
                'user_id' => $users->first()->id,
                'item_id' => $createdItems[3]->id,
                'tanggal_pinjam' => Carbon::now()->subDays(5),
                'tanggal_kembali_rencana' => Carbon::now()->addDays(5),
                'status' => 'dipinjam',
                'catatan' => 'Kondisi barang dalam keadaan baik',
            ]);
            $loan->generateKodePeminjaman();
            $loan->save();
        }

        // Returned loan
        if ($users->count() > 1 && count($createdItems) > 0) {
            $loan2 = Loan::create([
                'user_id' => $users[1]->id,
                'item_id' => $createdItems[0]->id,
                'tanggal_pinjam' => Carbon::now()->subDays(15),
                'tanggal_kembali_rencana' => Carbon::now()->subDays(8),
                'tanggal_kembali_aktual' => Carbon::now()->subDays(7),
                'status' => 'dikembalikan',
                'dikonfirmasi_oleh' => $admin->id,
            ]);
            $loan2->generateKodePeminjaman();
            $loan2->save();
        }

        $this->command->info('Database seeded successfully!');
    }
}
