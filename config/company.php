<?php

/*
|--------------------------------------------------------------------------
| Company profile content
|--------------------------------------------------------------------------
|
| Content for the public site, taken from "Company Profile Inter G 2025".
| Contact fields are fallbacks: values saved in "Pengaturan Situs"
| (site_settings table) take precedence.
|
| Images live in public/images/profile/ (extracted from the same PDF).
|
*/

return [

    'name' => 'Inter G Queen Bumindo',
    'short_name' => 'IGB',
    'tagline' => 'Accelerating Innovation and Technology',

    'phone' => '+62 341 400 272',
    'whatsapp' => '+62 812-3356-956',
    'email' => env('COMPANY_EMAIL', 'sales@interg.co.id'),
    'website' => 'interg.co.id',
    'address' => "Perum Permata Jingga Blok AA No. 27,\nTunggulwulung, Lowokwaru, Kota Malang 65143",

    // Organisations named in the company profile's project pages.
    'clients' => [
        'Dishub Prov. Jawa Timur',
        'Dinas SDA Prov. Jawa Timur',
        'Kementerian PUPR',
        'BPOM',
        'Bank Mandiri',
        'BNI',
        'ICON+',
        'PT SMART Tbk',
        'PT Petrosea Tbk',
    ],

    'intro' => [
        'Seiring cepatnya perkembangan revolusi teknologi 5.0, kebutuhan teknologi terkait otomasi, analisis big data, Internet of Things (IoT), dan Artificial Intelligence (AI) semakin banyak dicari.',
        'Perusahaan dan pemerintahan berlomba mencari solusi teknologi untuk meningkatkan kinerja, skalabilitas, efisiensi, fleksibilitas, kontinuitas, dan kemampuan beradaptasi — agar mampu bersaing dan menjadi yang terbaik di bidangnya.',
    ],

    'challenges' => ['Kompleksitas sistem', 'Kesiapan tenaga', 'Biaya implementasi', 'Keterbatasan pengetahuan'],

    'services' => [
        [
            'title' => 'System Integrator',
            'anchor' => 'contact',
            'desc' => 'Solusi terpadu yang mengintegrasikan berbagai komponen sistem untuk meningkatkan efisiensi operasional dan kinerja bisnis. Kami menggabungkan perangkat keras dan perangkat lunak terkini menjadi solusi yang disesuaikan dengan kebutuhan spesifik Anda.',
            'icon' => 'M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z',
        ],
        [
            'title' => 'Software & App Development',
            'anchor' => 'software',
            'desc' => 'Pengembangan perangkat lunak yang didasarkan pada inovasi, kehandalan, dan teknologi terbaru — mulai dari aplikasi web, desktop, hingga mobile yang sesuai dengan kebutuhan unik organisasi Anda.',
            'icon' => 'M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z',
        ],
        [
            'title' => 'IoT Solution & Surveillance Camera',
            'anchor' => 'iot',
            'desc' => 'Menghubungkan perangkat pintar dan mengoptimalkan proses bisnis. Dari pemantauan dengan kamera pengawas hingga integrasi sensor IoT, dengan keamanan dan efisiensi tinggi.',
            'icon' => 'M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3 3-3c-1.65-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2C15.14 9.14 8.87 9.14 5 13z',
        ],
        [
            'title' => 'Professional Integrated Display Solution',
            'anchor' => 'display',
            'desc' => 'Penyedia videotron, VMS, dan LED display. Kami menciptakan pengalaman visual yang menarik — di ruang publik, acara besar, maupun ruang kendali perusahaan dan instansi.',
            'icon' => 'M21 3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h5v2h8v-2h5c1.1 0 1.99-.9 1.99-2L23 5c0-1.1-.9-2-2-2zm0 14H3V5h18v12z',
        ],
        [
            'title' => 'Hardware Service & Maintenance',
            'anchor' => 'contact',
            'desc' => 'Perawatan dan pemeliharaan perangkat keras untuk memastikan ketersediaan optimal dan kinerja stabil sistem IT Anda, dengan dukungan teknis yang handal dan pemeliharaan rutin untuk menjaga investasi teknologi.',
            'icon' => 'M22 9V7h-2V5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-2h2v-2h-2v-2h2v-2h-2V9h2zm-4 10H4V5h14v14zM6 13h5v4H6zm6-6h4v3h-4zM6 7h5v5H6zm6 4h4v6h-4z',
        ],
    ],

    'display' => [
        'applications' => ['Control Room', 'Command Center', 'NOC', 'Data Center', 'War Room', 'Showcase', 'Executive Briefing', 'CCTV Room', 'Traffic Control', 'Digital Signage', 'TV Backdrop'],
        'products' => ['Video / TV Wall Display', 'LED Display / Videotron / VMS', 'Digital Signage', 'Interactive Display', 'Video Wall Processor / Controller'],
        'services' => ['Desain dan Konsultasi', 'Instalasi, Integrasi, dan Pelatihan', 'Support & Service', 'Pemeliharaan / Perawatan', 'Extended Warranty'],
        'realisations' => [
            ['title' => 'Control Room Dinas Sumber Daya Air Prov. Jatim', 'design' => 'cr-sda-design.jpg', 'built' => 'cr-sda.jpg'],
            ['title' => 'Control Room Dinas Perhubungan Prov. Jatim', 'design' => 'cr-dishub-design.jpg', 'built' => 'cr-dishub.jpg'],
        ],
        'projects' => [
            ['type' => 'Promotion Room', 'client' => 'Kementerian PUPR', 'tech' => 'Planar Leyard TWA 0.9', 'image' => 'proj-pupr.jpg'],
            ['type' => 'Meeting Room', 'client' => 'BPOM', 'tech' => 'ImagePath ICT86u', 'image' => 'proj-bpom.jpg'],
            ['type' => 'Monitoring Room', 'client' => 'PT Bank Mandiri (Rempoa)', 'tech' => 'Planar C67RX LED', 'image' => 'proj-mandiri.jpg'],
            ['type' => 'Monitoring Room', 'client' => 'Dinas Perhubungan Prov. Jawa Timur', 'tech' => 'ImagePath Infinite VW-5502', 'image' => 'proj-dishub-monitoring.jpg'],
            ['type' => 'Command Center', 'client' => 'BNI Sudirman', 'tech' => 'ImagePath Infinite VW-5501', 'image' => 'proj-bni-sudirman.jpg'],
            ['type' => 'War Room', 'client' => 'PT SMART Tbk', 'tech' => 'Planar Margay CRX50 LED', 'image' => 'proj-smart.jpg'],
            ['type' => 'NOC Room', 'client' => 'ICON+', 'tech' => 'ImagePath Infinite VW-5502', 'image' => 'proj-iconplus.jpg'],
            ['type' => 'Command Center', 'client' => 'BNI Pejompongan', 'tech' => 'ImagePath Infinite VW-5501', 'image' => 'proj-bni-pejompongan.jpg'],
            ['type' => 'ROC Room', 'client' => 'PT Petrosea Tbk', 'tech' => 'Unilumin USF 1.5', 'image' => 'proj-petrosea.jpg'],
        ],
    ],

    'apps' => [
        ['name' => 'Mudik Gratis', 'client' => 'Program pemerintah', 'image' => 'app-mudik.jpg', 'desc' => 'Pendaftaran dan pemantauan program mudik gratis: registrasi, pemilihan jadwal keberangkatan, serta informasi rute dan fasilitas perjalanan.'],
        ['name' => 'Ruang Perintis', 'client' => 'Coworking space', 'image' => 'app-ruang-perintis.jpg', 'desc' => 'Sistem pengelolaan coworking space, mulai dari pendaftaran hingga layanan berlangganan anggota.'],
        ['name' => 'Jembatan Timbang', 'client' => 'Perhubungan', 'image' => 'app-jembatan-timbang.jpg', 'desc' => 'Mengukur berat kendaraan yang melintas agar tidak melebihi batas muatan — menjaga keamanan jalan, mendukung penegakan hukum, dan mengumpulkan data infrastruktur.'],
        ['name' => 'JT Command Center', 'client' => 'Dishub Jatim', 'image' => 'app-jtcc.jpg', 'desc' => 'Pemantauan kondisi lalu lintas secara real-time, analisis data lalu lintas, dan pengelolaan kejadian untuk transportasi Jawa Timur yang lebih efektif dan transparan.'],
        ['name' => 'Perizinan Kendaraan', 'client' => 'Dishub Jatim', 'image' => 'app-perizinan.jpg', 'desc' => 'Sistem perizinan kendaraan angkutan umum di Jawa Timur.'],
        ['name' => 'SINTA', 'client' => 'Dishub Jatim', 'image' => 'app-sinta.jpg', 'desc' => 'Sistem Interoperabilitas Database JT & UPKB: pertukaran data terintegrasi antara Jembatan Timbang dan Unit Pelaksana Penimbangan Kendaraan Bermotor se-Jawa Timur.'],
        ['name' => 'EWT', 'client' => 'Estimasi Waktu Tempuh', 'image' => 'app-ewt.jpg', 'desc' => 'Memprediksi durasi perjalanan antar titik dengan mempertimbangkan jarak, kondisi lalu lintas, cuaca, dan rute — membantu pengguna memilih rute paling efisien.'],
        ['name' => 'SISWA', 'client' => 'Sistem Informasi Sungai & Waduk', 'image' => 'app-siswa.jpg', 'desc' => 'Menampilkan data waduk, embung, ranu, dan sungai di Jawa Timur sehingga kondisi dan lokasi sumber daya air bisa dipantau dengan mudah.'],
        ['name' => 'Lab Data Sungram', 'client' => 'Dishub Jatim', 'image' => 'app-labdata.jpg', 'desc' => 'Portal data Dinas Perhubungan Prov. Jawa Timur yang menyajikan informasi transportasi dan infrastruktur.'],
    ],

    'iot' => [
        'intro' => 'Transformasi digital yang efektif dimulai dengan solusi IoT. Kami menyediakan platform yang memungkinkan perangkat terhubung, analisis data real-time, dan pengambilan keputusan yang cerdas — untuk efisiensi operasional dan proses yang lebih optimal.',
        'projects' => [
            ['title' => 'Radar Traffic Counting', 'image' => 'iot-radar.jpg', 'desc' => 'Penghitungan lalu lintas presisi dengan sensor radar. Analisis kendaraan dan pola lalu lintas yang akurat untuk pengambilan keputusan.'],
            ['title' => 'Time Travel Estimation', 'image' => 'iot-vms-gantry.jpg', 'desc' => 'Sensor menangkap MAC address kendaraan dari titik ke titik, dikombinasikan dengan data satelit, lalu dianalisis otomatis untuk menampilkan estimasi waktu tempuh.'],
            ['title' => 'Telemetri AWLR', 'image' => 'iot-awlr-site.jpg', 'desc' => 'Pengumpulan data ketinggian air jarak jauh dengan teknologi telemetri, memberikan akses real-time ke data yang diperlukan.'],
        ],
        'layers' => ['Monitoring Room', 'App Monitoring', 'Server', 'Media Transmisi', 'Sumber Daya', 'Kontroler', 'Sensor'],
    ],

    'surveillance' => [
        'intro' => 'Pemantauan yang handal dan analisis video cerdas untuk melindungi aset serta meningkatkan kontrol operasional.',
        'ai' => ['Analisa', 'Deteksi', 'Identifikasi', 'Klasifikasi', 'Perhitungan', 'Pengenalan'],
        'central' => 'Sistem monitoring terpusat yang mengintegrasikan kamera dari berbagai merek vendor ke dalam satu tampilan — sekaligus memantau apakah setiap perangkat bekerja pada performa terbaiknya.',
    ],

    // Leave a URL empty to hide that icon instead of showing a dead "#" link.
    'socials' => [
        'linkedin' => env('SOCIAL_LINKEDIN'),
        'instagram' => env('SOCIAL_INSTAGRAM'),
        'facebook' => env('SOCIAL_FACEBOOK'),
        'youtube' => env('SOCIAL_YOUTUBE'),
    ],

];
