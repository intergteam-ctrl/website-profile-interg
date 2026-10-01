<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'komputer' => [
                'name' => 'Komputer / Laptop',
                'image' => 'kategori-komputer.jpg',
            ],
            'storage' => [
                'name' => 'Harddisk / Storage',
                'image' => 'kategori-storage.jpg',
            ],
            'software' => [
                'name' => 'Software / Lisensi',
                'image' => 'kategori-software.jpg',
            ],
            'aksesoris' => [
                'name' => 'Aksesoris',
                'image' => 'kategori-aksesoris.jpg',
            ],
        ];

        $categoryIds = [];

        foreach ($categories as $slug => $category) {
            $categoryIds[$slug] = Category::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $category['name'],
                    'image' => $category['image'],
                ]
            )->id;
        }

        $mpProducts = [
            [
                'id' => 1,
                'name' => 'Laptop Lenovo ThinkPad T14 Gen 3',
                'cat' => 'komputer',
                'catLabel' => 'Komputer / Laptop',
                'price' => 9800000,
                'status' => 'bekas',
                'date' => 5,
                'desc' => 'Laptop bisnis tangguh dengan build quality militer-grade, cocok untuk kerja kantoran maupun development ringan. Kondisi mulus, baterai masih sehat.',
                'specs' => [
                    'CPU' => 'Intel Core i5-1240P',
                    'RAM' => '16GB DDR4',
                    'Storage' => '512GB NVMe SSD',
                    'Display' => '14" FHD IPS',
                ],
            ],
            [
                'id' => 2,
                'name' => 'PC Rakitan Ryzen 5 5600 + RTX 3060',
                'cat' => 'komputer',
                'catLabel' => 'Komputer / Laptop',
                'price' => 12500000,
                'status' => 'baru',
                'date' => 1,
                'desc' => 'PC rakitan gaming/editing dengan performa tinggi, garansi toko 1 tahun, sudah termasuk Windows 11 original.',
                'specs' => [
                    'CPU' => 'AMD Ryzen 5 5600',
                    'RAM' => '16GB DDR4 3200MHz',
                    'Storage' => '1TB NVMe SSD',
                    'GPU' => 'RTX 3060 12GB',
                ],
            ],
            [
                'id' => 3,
                'name' => 'MacBook Air M1 2020 8/256GB',
                'cat' => 'komputer',
                'catLabel' => 'Komputer / Laptop',
                'price' => 10900000,
                'status' => 'bekas',
                'date' => 8,
                'desc' => 'MacBook Air M1 mulus, battery cycle rendah, cocok untuk desain dan produktivitas harian dengan efisiensi daya luar biasa.',
                'specs' => [
                    'CPU' => 'Apple M1',
                    'RAM' => '8GB Unified',
                    'Storage' => '256GB SSD',
                    'Display' => '13.3" Retina',
                ],
            ],
            [
                'id' => 4,
                'name' => 'SSD Samsung 970 EVO Plus 1TB NVMe',
                'cat' => 'storage',
                'catLabel' => 'Harddisk / Storage',
                'price' => 1350000,
                'status' => 'baru',
                'date' => 2,
                'desc' => 'SSD NVMe kecepatan tinggi untuk upgrade performa sistem secara signifikan, garansi resmi distributor 5 tahun.',
                'specs' => [
                    'Interface' => 'PCIe Gen3 NVMe',
                    'Capacity' => '1TB',
                    'Read Speed' => '3500 MB/s',
                    'Write Speed' => '3300 MB/s',
                ],
            ],
            [
                'id' => 5,
                'name' => 'Harddisk Seagate Barracuda 2TB',
                'cat' => 'storage',
                'catLabel' => 'Harddisk / Storage',
                'price' => 780000,
                'status' => 'baru',
                'date' => 14,
                'desc' => 'HDD internal 3.5" untuk kebutuhan penyimpanan data besar, cocok untuk backup, NAS, maupun PC harian.',
                'specs' => [
                    'Interface' => 'SATA III',
                    'Capacity' => '2TB',
                    'RPM' => '7200 RPM',
                    'Cache' => '256MB',
                ],
            ],
            [
                'id' => 6,
                'name' => 'SSD External WD My Passport 1TB',
                'cat' => 'storage',
                'catLabel' => 'Harddisk / Storage',
                'price' => 990000,
                'status' => 'baru',
                'date' => 6,
                'desc' => 'Penyimpanan eksternal portable dengan desain ringkas, ideal untuk mobilitas tinggi dan backup data penting.',
                'specs' => [
                    'Interface' => 'USB 3.2 Type-C',
                    'Capacity' => '1TB',
                    'Weight' => '58 gram',
                    'Warranty' => '3 Tahun',
                ],
            ],
            [
                'id' => 7,
                'name' => 'Lisensi Windows 11 Pro Original',
                'cat' => 'software',
                'catLabel' => 'Software / Lisensi',
                'price' => 450000,
                'status' => 'digital',
                'date' => 3,
                'desc' => 'Lisensi digital resmi Windows 11 Pro, aktivasi otomatis dan permanen, dikirim langsung via email setelah pembayaran.',
                'specs' => [
                    'Type' => 'Digital License',
                    'Activation' => 'Online/Otomatis',
                    'Validity' => 'Permanen',
                    'Support' => 'Lifetime Update',
                ],
            ],
            [
                'id' => 8,
                'name' => 'Microsoft Office 2021 Pro Plus',
                'cat' => 'software',
                'catLabel' => 'Software / Lisensi',
                'price' => 380000,
                'status' => 'digital',
                'date' => 10,
                'desc' => 'Paket lengkap Word, Excel, PowerPoint, Outlook untuk 1 PC, lisensi digital resmi dan dapat diaktivasi langsung.',
                'specs' => [
                    'Type' => 'Digital License',
                    'Apps' => 'Word, Excel, PPT, Outlook',
                    'Devices' => '1 PC',
                    'Validity' => 'Lifetime',
                ],
            ],
            [
                'id' => 9,
                'name' => 'Antivirus Kaspersky Total Security',
                'cat' => 'software',
                'catLabel' => 'Software / Lisensi',
                'price' => 265000,
                'status' => 'digital',
                'date' => 20,
                'desc' => 'Perlindungan menyeluruh dari malware, ransomware, dan ancaman online lainnya untuk 3 perangkat selama 1 tahun.',
                'specs' => [
                    'Type' => 'Digital License',
                    'Devices' => '3 Perangkat',
                    'Duration' => '1 Tahun',
                    'Platform' => 'Windows/Mac/Android',
                ],
            ],
            [
                'id' => 10,
                'name' => 'Keyboard Mechanical Logitech G413',
                'cat' => 'aksesoris',
                'catLabel' => 'Aksesoris',
                'price' => 1150000,
                'status' => 'baru',
                'date' => 4,
                'desc' => 'Keyboard mekanikal dengan switch tactile yang responsif, build aluminium kokoh untuk gaming maupun produktivitas.',
                'specs' => [
                    'Switch' => 'Romer-G Tactile',
                    'Connection' => 'USB Wired',
                    'Backlight' => 'White LED',
                    'Layout' => 'Full-size',
                ],
            ],
            [
                'id' => 11,
                'name' => 'Mouse Wireless Logitech MX Master 3S',
                'cat' => 'aksesoris',
                'catLabel' => 'Aksesoris',
                'price' => 1450000,
                'status' => 'baru',
                'date' => 7,
                'desc' => 'Mouse premium dengan sensor presisi tinggi dan ergonomi terbaik di kelasnya, cocok untuk produktivitas profesional.',
                'specs' => [
                    'Sensor' => '8000 DPI',
                    'Connection' => 'Bluetooth/USB',
                    'Battery' => '70 hari',
                    'Compatibility' => 'Windows/Mac',
                ],
            ],
            [
                'id' => 12,
                'name' => 'Headset Gaming HyperX Cloud II',
                'cat' => 'aksesoris',
                'catLabel' => 'Aksesoris',
                'price' => 980000,
                'status' => 'bekas',
                'date' => 12,
                'desc' => 'Headset gaming dengan suara surround 7.1 jernih, busa memory foam empuk untuk pemakaian jangka panjang.',
                'specs' => [
                    'Audio' => '7.1 Virtual Surround',
                    'Driver' => '53mm',
                    'Connection' => 'USB/3.5mm',
                    'Weight' => '320 gram',
                ],
            ],
            [
                'id' => 13,
                'name' => 'Webcam Logitech C920 HD Pro',
                'cat' => 'aksesoris',
                'catLabel' => 'Aksesoris',
                'price' => 850000,
                'status' => 'baru',
                'date' => 9,
                'desc' => 'Webcam Full HD 1080p dengan autofocus dan dual stereo mic, ideal untuk video conference dan streaming.',
                'specs' => [
                    'Resolution' => '1080p Full HD',
                    'Frame Rate' => '30fps',
                    'Connection' => 'USB 2.0',
                    'Field of View' => '78°',
                ],
            ],
        ];

        foreach ($mpProducts as $item) {
            $categoryId = $categoryIds[$item['cat']];
            $specLines = [];

            foreach ($item['specs'] as $label => $value) {
                $specLines[] = $label.': '.$value;
            }

            $description = $item['desc'].PHP_EOL.PHP_EOL.implode(PHP_EOL, $specLines);

            Product::query()->updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'category_id' => $categoryId,
                    'name' => $item['name'],
                    'brand' => $this->inferBrand($item['name']),
                    'price' => $item['price'],
                    'stock' => $this->inferStock($item['status']),
                    'description' => $description,
                    'image' => null,
                    'status' => $item['status'],
                ]
            );
        }
    }

    private function inferBrand(string $name): string
    {
        return match (true) {
            str_contains($name, 'Lenovo') => 'Lenovo',
            str_contains($name, 'Ryzen') => 'Custom',
            str_contains($name, 'MacBook') => 'Apple',
            str_contains($name, 'Samsung') => 'Samsung',
            str_contains($name, 'Seagate') => 'Seagate',
            str_contains($name, 'WD') => 'Western Digital',
            str_contains($name, 'Windows') => 'Microsoft',
            str_contains($name, 'Microsoft Office') => 'Microsoft',
            str_contains($name, 'Kaspersky') => 'Kaspersky',
            str_contains($name, 'Logitech') => 'Logitech',
            str_contains($name, 'HyperX') => 'HyperX',
            default => 'Generic',
        };
    }

    private function inferStock(string $status): int
    {
        return match ($status) {
            'digital' => 99,
            'baru' => 10,
            'bekas' => 1,
            default => 5,
        };
    }
}
