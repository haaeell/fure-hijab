<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title'            => '5 Cara Memilih Hijab yang Cocok untuk Bentuk Wajah',
                'category'         => 'styling-guide',
                'excerpt'          => 'Menemukan hijab yang tepat bukan hanya soal warna atau bahan — bentuk wajah juga memengaruhi tampilan keseluruhan. Pelajari panduan lengkapnya di sini.',
                'content'          => '<p>Memilih hijab bukan hanya soal selera warna. Bentuk wajah adalah faktor penting yang sering diabaikan, padahal berpengaruh besar pada kesan akhir penampilan.</p><h2>1. Wajah Oval — Si Beruntung</h2><p>Hampir semua gaya hijab cocok untuk wajah oval. Kamu bebas bereksperimen dengan berbagai volume dan lipatan. Hindari poni besar agar proposi wajah tetap terlihat.</p><h2>2. Wajah Bulat — Ciptakan Ilusi Panjang</h2><p>Pilih gaya yang memberi kesan tinggi dan panjang. Pashmina dengan lipatan vertikal di sisi wajah, atau khimar panjang, bisa menjadi pilihan terbaik. Hindari volume besar di samping kepala.</p><h2>3. Wajah Lonjong — Tambahkan Lebar</h2><p>Tambahkan volume di sisi untuk menyeimbangkan. Hijab segi empat dengan model simpel atau turban bervolume cocok untuk wajah lonjong. Hindari gaya yang terlalu menonjolkan panjang wajah.</p><h2>4. Wajah Segitiga (Dahi Lebar) — Tutupi Sisi Atas</h2><p>Fokuskan volume di bagian bawah. Pashmina yang diikat ke belakang dengan sedikit volume di bawah dagu bisa membantu menyeimbangkan proporsi wajah.</p><h2>5. Wajah Persegi — Lembutkan Garis Tegas</h2><p>Pilih hijab dengan lipit-lipit lembut yang memperhalus sudut wajah. Hindari lipatan yang terlalu tegak lurus. Draping asimetris adalah pilihan yang tepat.</p><p>Kunci utamanya adalah percaya diri. Aturan ini hanyalah panduan, bukan pakem. Eksplorasi dan temukan gaya yang paling membuat kamu nyaman.</p>',
                'tags'             => ['hijab', 'styling', 'fashion tips', 'wajah oval', 'modest wear'],
                'author'           => 'Tim FURE',
                'read_time'        => 4,
                'meta_title'       => 'Cara Memilih Hijab Sesuai Bentuk Wajah | FURE',
                'meta_description' => 'Panduan lengkap memilih hijab berdasarkan bentuk wajah: oval, bulat, lonjong, segitiga, dan persegi. Tips dari tim FURE untuk tampilan maksimal.',
                'meta_keywords'    => 'cara memilih hijab, hijab bentuk wajah, styling hijab, tutorial hijab',
                'is_published'     => true,
                'published_at'     => now()->subDays(10),
                'view_count'       => 482,
            ],
            [
                'title'            => 'Mengenal Kain Voal: Si Ringan yang Adem Sepanjang Hari',
                'category'         => 'fabric-notes',
                'excerpt'          => 'Voal jadi favorit banyak perempuan karena ringan, jatuh, dan tidak panas. Tapi apa sebenarnya keistimewaan kain ini dibanding bahan lain?',
                'content'          => '<p>Kalau kamu sering berbelanja hijab premium, nama kain voal pasti sudah tidak asing. Tapi apa yang membuat bahan ini begitu populer dan layak menjadi investasi?</p><h2>Apa Itu Voal?</h2><p>Voal (atau voile dalam bahasa Prancis) adalah kain ringan yang dipintal dari serat halus, biasanya campuran polyester dan katun. Teksturnya semi-transparan, ringan, dan memiliki drapabilitas tinggi — artinya kain ini sangat mudah "jatuh" mengikuti bentuk tubuh dengan natural.</p><h2>Kenapa Voal Cocok untuk Hijab?</h2><ul><li><strong>Ringan dan adem:</strong> Sirkulasi udara yang baik membuat kulit tidak pengap meski dipakai seharian.</li><li><strong>Jatuh alami:</strong> Tanpa perlu banyak peniti atau pin, voal mudah dibentuk dan rapi secara alami.</li><li><strong>Warna tahan lama:</strong> Serat voal berkualitas tinggi mampu menahan warna lebih lama dibanding katun biasa.</li><li><strong>Mudah dirawat:</strong> Cukup dicuci dengan mesin di suhu normal, dan setrika pada suhu rendah.</li></ul><h2>Tips Merawat Voal</h2><p>Cuci dengan air dingin atau suhu maksimal 30°C. Hindari pemutih. Jemur di tempat teduh agar warna tidak pudar. Setrika dengan lapisan kain tipis untuk menghindari bekas gilap.</p><h2>Voal FURE</h2><p>Koleksi hijab FURE menggunakan voal premium dengan density yang lebih rapat, sehingga lebih opaque (tidak tembus pandang) namun tetap ringan. Cocok untuk iklim tropis seperti Indonesia.</p>',
                'tags'             => ['kain voal', 'bahan hijab', 'fabric guide', 'tips perawatan'],
                'author'           => 'Tim FURE',
                'read_time'        => 5,
                'meta_title'       => 'Kain Voal untuk Hijab: Kelebihan, Karakteristik & Tips Perawatan | FURE',
                'meta_description' => 'Pelajari mengapa kain voal menjadi pilihan utama untuk hijab premium: ringan, adem, jatuh natural, dan tahan lama. Panduan lengkap dari FURE.',
                'meta_keywords'    => 'kain voal, bahan hijab voal, hijab voal premium, merawat hijab voal',
                'is_published'     => true,
                'published_at'     => now()->subDays(8),
                'view_count'       => 319,
            ],
            [
                'title'            => 'OOTD Modest Wear untuk Acara Formal: Panduan Tampil Elegan',
                'category'         => 'occasion',
                'excerpt'          => 'Menghadiri acara formal dengan tampilan modest yang tetap anggun dan profesional. Dari pilihan warna hingga aksesori yang tepat.',
                'content'          => '<p>Acara formal seperti pernikahan, wisuda, atau pertemuan bisnis menuntut penampilan yang tepat. Sebagai perempuan berhijab, kamu punya banyak pilihan untuk tampil elegan tanpa mengorbankan nilai kesopanan.</p><h2>Pilih Palet Warna yang Tepat</h2><p>Warna netral selalu aman untuk acara formal: putih gading, nude, abu-abu muda, taupe, atau biru navy. Warna-warna ini memberi kesan profesional dan dewasa. Untuk pernikahan, kamu bisa memilih warna yang sedikit lebih berani seperti dusty rose atau sage green.</p><h2>Siluet yang Flatter</h2><p>Pilih potongan yang mengalir dan tidak ketat. Abaya dengan belt tipis di pinggang memberikan definisi siluet tanpa harus menonjolkan lekuk tubuh. Rok A-line dengan blouse panjang juga pilihan yang selalu berhasil.</p><h2>Hijab untuk Acara Formal</h2><p>Pashmina chiffon dengan drapery elegan cocok untuk pernikahan dan gala dinner. Sementara hijab segi empat voal premium dengan gaya simple tuck terlihat profesional untuk meeting dan wisuda.</p><h2>Aksesori yang Melengkapi</h2><p>Less is more. Pilih satu statement piece: anting besar atau kalung halus — tidak keduanya sekaligus. Tas clutch atau shoulder bag kecil lebih formalmedhani tas tote besar.</p><h2>Pilihan Sepatu</h2><p>Heels atau kitten heels selalu meninggikan kesan formal. Jika lebih menyukai flats, pilih material kulit atau satin agar tetap terlihat dressy.</p>',
                'tags'             => ['outfit formal', 'modest wear', 'OOTD hijab', 'acara formal', 'fashion muslim'],
                'author'           => 'Farah Nadia',
                'read_time'        => 6,
                'meta_title'       => 'OOTD Modest Wear untuk Acara Formal | FURE Journal',
                'meta_description' => 'Panduan lengkap tampil elegan dan profesional untuk perempuan berhijab di acara formal: pilihan warna, siluet, hijab style, dan aksesori yang tepat.',
                'meta_keywords'    => 'OOTD hijab formal, modest wear formal, outfit acara formal hijab, baju kondangan hijab',
                'is_published'     => true,
                'published_at'     => now()->subDays(6),
                'view_count'       => 561,
            ],
            [
                'title'            => 'Cara Merawat Hijab agar Tetap Segar dan Awet Bertahun-tahun',
                'category'         => 'tips',
                'excerpt'          => 'Hijab premium bisa bertahan lama jika dirawat dengan benar. Panduan cuci, jemur, setrika, dan simpan hijab agar tetap seperti baru.',
                'content'          => '<p>Investasi pada hijab berkualitas harus diimbangi dengan perawatan yang tepat. Dengan cara yang benar, hijab kamu bisa bertahan bertahun-tahun tanpa kehilangan warna atau bentuknya.</p><h2>Mencuci Hijab</h2><p>Cuci hijab dari bahan halus (voal, chiffon, satin) secara terpisah atau dalam laundry bag jaring. Gunakan detergen lembut tanpa pemutih. Suhu air maksimal 30°C. Mode mesin cuci: gentle/delicate. Jangan merendam terlalu lama — 15-20 menit sudah cukup.</p><h2>Menjemur dengan Benar</h2><p>Selalu jemur di tempat teduh, bukan di bawah sinar matahari langsung. Sinar UV memudarkan warna kain secara permanen. Gantung hijab dengan bentuk melebar agar tidak meninggalkan bekas lipatan.</p><h2>Menyetrika Hijab</h2><p>Setrika dalam keadaan sedikit lembap. Gunakan suhu rendah-sedang dan lapisi dengan kain tipis (pressing cloth) untuk menghindari bekas gilap. Untuk voal dan chiffon, setrika dari sisi dalam.</p><h2>Menyimpan Hijab</h2><p>Lipat dengan rapi atau gulung untuk menghindari garis lipat permanen. Simpan di tempat yang kering dan tidak terkena cahaya langsung. Gunakan lemari dengan pengharum alami (lavender sachet) untuk mencegah jamur.</p><h2>Menghilangkan Noda</h2><p>Tangani noda sesegera mungkin. Tepuk-tepuk (jangan digosok) dengan kain bersih yang dibasahi air dingin. Untuk noda membandel, gunakan sedikit sabun lembut dan bilas bersih.</p>',
                'tags'             => ['merawat hijab', 'tips hijab', 'laundry hijab', 'perawatan kain'],
                'author'           => 'Tim FURE',
                'read_time'        => 4,
                'meta_title'       => 'Cara Merawat Hijab agar Awet dan Segar | FURE Journal',
                'meta_description' => 'Tips lengkap merawat hijab: cara mencuci, menjemur, menyetrika, dan menyimpan hijab agar tetap seperti baru dan awet bertahun-tahun.',
                'meta_keywords'    => 'cara merawat hijab, mencuci hijab, menjemur hijab, tips hijab awet',
                'is_published'     => true,
                'published_at'     => now()->subDays(5),
                'view_count'       => 728,
            ],
            [
                'title'            => 'Tren Modest Fashion 2025 yang Perlu Kamu Tahu',
                'category'         => 'news',
                'excerpt'          => 'Industri modest fashion terus berkembang. Dari palet warna earth tone hingga layering minimalis, inilah tren yang mendominasi 2025.',
                'content'          => '<p>Modest fashion bukan lagi niche — ia telah menjadi arus utama global yang diperhitungkan di runway internasional. Tahun 2025 membawa arah baru yang menarik untuk para perempuan berhijab.</p><h2>Earth Tone Mendominasi</h2><p>Palet warna tanah (terracotta, rust, sage, camel, sand) mendominasi koleksi modest fashion 2025. Warna-warna ini memberi kesan hangat, natural, dan mudah dipadukan satu sama lain — konsep that goes well with everything.</p><h2>Layering Minimalis</h2><p>Teknik layering yang clean dan terstruktur menjadi kunci. Bukan berlapis-lapis yang tebal, tapi layering cerdas: outer tipis di atas dress panjang, atau kemeja longgar di atas celana wide-leg. Proporsi menjadi sangat penting.</p><h2>Tekstur sebagai Pernyataan</h2><p>Di tengah dominasi warna netral, tekstur kain menjadi cara untuk membuat tampilan lebih menarik. Jacquard, emboss halus, dan kain dengan sedikit sheen (kilau lembut) menjadi favorit tanpa harus bergantung pada warna mencolok.</p><h2>Modest Athleisure</h2><p>Perpaduan gaya sporty dan modest semakin diterima. Set olahraga dengan hijab sport yang fungsional namun tetap stylish menjadi kategori yang tumbuh paling cepat.</p><h2>FURE dan Tren 2025</h2><p>Koleksi terbaru FURE hadir dengan mengadopsi palet earth tone yang hangat, silhouette yang clean, dan bahan voal premium yang nyaman untuk iklim tropis. Eksplorasi koleksi lengkap kami dan temukan favoritmu.</p>',
                'tags'             => ['tren fashion 2025', 'modest fashion', 'earth tone', 'hijab trends', 'fashion muslim'],
                'author'           => 'Dina Rahmawati',
                'read_time'        => 5,
                'meta_title'       => 'Tren Modest Fashion 2025 | FURE Journal',
                'meta_description' => 'Panduan tren modest fashion 2025: palet earth tone, layering minimalis, tekstur kain, dan modest athleisure. Update style terkini dari FURE.',
                'meta_keywords'    => 'tren modest fashion 2025, fashion hijab 2025, tren hijab terbaru, earth tone hijab',
                'is_published'     => true,
                'published_at'     => now()->subDays(3),
                'view_count'       => 894,
            ],
            [
                'title'            => 'Panduan Memadukan Warna Hijab dengan Outfit Sehari-hari',
                'category'         => 'tips',
                'excerpt'          => 'Bingung memilih warna hijab yang cocok dengan baju? Pelajari teori warna dasar dan formula padu padan yang bisa langsung kamu praktikkan.',
                'content'          => '<p>Satu hal yang sering membuat perempuan berhijab kebingungan adalah memadukan warna hijab dengan outfit. Sebenarnya ada formula sederhana yang bisa membantu.</p><h2>Dasar Teori Warna</h2><p>Pahami tiga konsep dasar: warna komplementer (berlawanan di roda warna), warna analog (berdekatan di roda warna), dan warna netral (putih, hitam, abu, krem, nude). Memahami ini adalah fondasi dari semua kombinasi yang berhasil.</p><h2>Formula Aman: Satu Warna + Netral</h2><p>Jika outfit kamu berwarna, pilih hijab netral. Sebaliknya, jika outfit netral, kamu bisa bermain dengan warna hijab. Ini adalah formula yang hampir selalu berhasil dan mudah dipraktikkan setiap hari.</p><h2>Tone on Tone</h2><p>Padukan warna yang satu family tapi beda shade. Contoh: blush pink + dusty rose, atau mint + sage green. Tampilan terlihat cohesive dan sophisticated tanpa terlalu "safe".</p><h2>Hijab sebagai Focal Point</h2><p>Jika kamu ingin hijab menjadi pusat perhatian, pilih warna hijab yang satu hingga dua tingkat lebih berani dari outfit. Misalnya, outfit putih dengan hijab terracotta — mata langsung tertuju ke wajah dan hijab.</p><h2>Warna Kulit dan Undertone</h2><p>Perhatikan undertone kulit kamu. Kulit dengan undertone hangat (kekuningan/kemerahan) cocok dengan warna warm: terracotta, olive, gold, coral. Undertone dingin (kebiruan) cocok dengan warna cool: lavender, ice blue, sage, burgundy.</p>',
                'tags'             => ['padu padan warna', 'tips hijab', 'color matching', 'fashion tips'],
                'author'           => 'Farah Nadia',
                'read_time'        => 5,
                'meta_title'       => 'Panduan Memadukan Warna Hijab dengan Outfit | FURE Journal',
                'meta_description' => 'Formula dan tips praktis memadukan warna hijab dengan pakaian sehari-hari. Teori warna, tone on tone, dan tips berdasarkan warna kulit.',
                'meta_keywords'    => 'padukan warna hijab, warna hijab yang cocok, kombinasi warna hijab outfit',
                'is_published'     => true,
                'published_at'     => now()->subDays(2),
                'view_count'       => 637,
            ],
            [
                'title'            => 'Hijab untuk Aktivitas Outdoor: Bahan dan Gaya yang Tepat',
                'category'         => 'styling-guide',
                'excerpt'          => 'Aktivitas di luar ruangan tidak harus membuat penampilan berantakan. Pilih bahan dan gaya hijab yang tetap nyaman dan stylish saat outdoor.',
                'content'          => '<p>Hiking, jalan-jalan di pantai, bersepeda, atau sekadar outing di taman — semua aktivitas outdoor bisa tetap dinikmati dengan hijab yang tepat.</p><h2>Bahan yang Direkomendasikan</h2><p><strong>Jersey:</strong> Bahan stretchy yang pas di kepala, tidak mudah bergeser, dan menyerap keringat. Pilihan terbaik untuk aktivitas fisik.<br><strong>Rayon:</strong> Ringan dan adem, cocok untuk jalan-jalan santai di tempat panas.<br><strong>Dry-fit:</strong> Khusus untuk olahraga, bahan ini cepat kering dan mengalirkan keringat dengan baik.</p><h2>Gaya yang Praktis</h2><p>Untuk outdoor aktif, pilih gaya yang tidak memerlukan banyak jarum pentul. Hijab instan atau sport hijab adalah solusi terbaik. Pastikan bagian leher tertutup sempurna untuk perlindungan maksimal dari sinar matahari.</p><h2>Tips Ekstra untuk Pantai</h2><p>Pilih warna gelap yang tidak tembus pandang saat basah. Bawa hijab cadangan. Gunakan sunscreen di area wajah dan tangan. Setelah berenang atau kepanasan, bilas hijab dengan air bersih sesegera mungkin.</p><h2>Aksesori Pendukung</h2><p>Topi bucket atau topi lebar bisa dipadukan dengan hijab untuk perlindungan ekstra dari matahari. Kacamata hitam melengkapi look outdoor yang fungsional sekaligus stylish.</p>',
                'tags'             => ['hijab outdoor', 'sport hijab', 'aktivitas outdoor', 'bahan hijab'],
                'author'           => 'Tim FURE',
                'read_time'        => 4,
                'meta_title'       => 'Hijab untuk Aktivitas Outdoor: Pilihan Bahan & Gaya | FURE',
                'meta_description' => 'Panduan memilih bahan dan gaya hijab untuk aktivitas outdoor: hiking, pantai, olahraga. Tips praktis agar tetap nyaman dan stylish.',
                'meta_keywords'    => 'hijab outdoor, sport hijab, hijab untuk olahraga, bahan hijab adem',
                'is_published'     => true,
                'published_at'     => now()->subDays(1),
                'view_count'       => 412,
            ],
            [
                'title'            => 'Inspirasi Modest Look untuk Lebaran: Elegan Tanpa Berlebihan',
                'category'         => 'occasion',
                'excerpt'          => 'Tampil memukau di hari Lebaran dengan pilihan busana modest yang anggun. Dari pemilihan warna keluarga hingga detail yang menyempurnakan penampilan.',
                'content'          => '<p>Lebaran adalah momen paling ditunggu untuk tampil terbaik bersama keluarga. Berikut inspirasi modest look yang elegan dan tetap sesuai nilai kesopanan.</p><h2>Palet Warna Lebaran 2025</h2><p>Tahun ini, warna sage green, dusty blue, almond, dan lilac muda menjadi tren utama untuk busana Lebaran. Warna-warna pastel lembut ini tampak segar di foto, mudah dipadukan dalam konsep seragaman keluarga, dan tidak terlalu "ramai" di mata.</p><h2>Konsep Seragaman Keluarga</h2><p>Tidak harus sama persis — satu palet warna dengan siluet berbeda-beda justru terlihat lebih natural dan modern. Koordinasikan 2-3 warna dalam satu family, biarkan masing-masing anggota keluarga mengekspresikan style-nya.</p><h2>Detail yang Membuat Tampilan Spesial</h2><p>Bordir halus, ruffle kecil di pergelangan tangan, atau kain dengan tekstur subtle (jacquard, brocade tipis) memberikan kesan mewah tanpa berteriak. Hindari payet atau sequin yang berlebihan untuk tampilan pagi hari.</p><h2>Hijab yang Tepat</h2><p>Pashmina chiffon dengan draping elegan cocok untuk shalat Id dan silaturahmi. Untuk foto keluarga, pilih gaya yang tidak mudah berantakan saat bergerak. Warna hijab yang senada (bukan persis sama) dengan baju memberikan kesan polished.</p><h2>Persiapan Malam Sebelumnya</h2><p>Siapkan outfit lengkap termasuk aksesori malam sebelum Lebaran. Setrika semua kain dengan hati-hati. Pasang pin hijab di posisi yang tepat agar pagi hari bisa berpakaian lebih cepat.</p>',
                'tags'             => ['lebaran', 'outfit lebaran', 'baju lebaran 2025', 'modest look', 'eid fashion'],
                'author'           => 'Dina Rahmawati',
                'read_time'        => 5,
                'meta_title'       => 'Inspirasi Modest Look untuk Lebaran 2025 | FURE Journal',
                'meta_description' => 'Inspirasi busana modest elegan untuk Lebaran 2025: palet warna terkini, konsep seragaman keluarga, dan tips tampil sempurna di hari raya.',
                'meta_keywords'    => 'outfit lebaran 2025, baju lebaran hijab, modest look lebaran, eid outfit indonesia',
                'is_published'     => true,
                'published_at'     => now()->subHours(6),
                'view_count'       => 203,
            ],
        ];

        foreach ($articles as $data) {
            $data['slug'] = Str::slug($data['title']);
            Article::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
