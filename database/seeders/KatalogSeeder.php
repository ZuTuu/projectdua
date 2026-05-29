<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\Layanan;

class KatalogSeeder extends Seeder
{
    public function run()
    {
        $dataProduk = [
            // PERAWATAN & SANITASI
            ['nama_produk' => 'Parfum Besar', 'kategori' => 'Perawatan', 'harga' => 35000, 'deskripsi' => 'Parfum wangi tahan lama untuk anabul.'],
            ['nama_produk' => 'Parfum Kecil', 'kategori' => 'Perawatan', 'harga' => 35000, 'deskripsi' => 'Parfum wangi kemasan praktis.'],
            ['nama_produk' => 'Bedak Kecil', 'kategori' => 'Perawatan', 'harga' => 25000, 'deskripsi' => 'Bedak kesehatan kulit.'],
            ['nama_produk' => 'Bedak Besar', 'kategori' => 'Perawatan', 'harga' => 35000, 'deskripsi' => 'Bedak kesehatan kemasan besar.'],
            ['nama_produk' => 'Sampo Kecil', 'kategori' => 'Perawatan', 'harga' => 20000, 'deskripsi' => 'Sampo lembut harian.'],
            ['nama_produk' => 'Sampo Besar', 'kategori' => 'Perawatan', 'harga' => 50000, 'deskripsi' => 'Sampo lembut kemasan ekonomis.'],
            ['nama_produk' => 'Sampo Jamur', 'kategori' => 'Perawatan', 'harga' => 95000, 'deskripsi' => 'Sampo medikasi anti jamur.'],
            ['nama_produk' => 'Sampo Pemutih', 'kategori' => 'Perawatan', 'harga' => 95000, 'deskripsi' => 'Sampo khusus bulu putih.'],
            ['nama_produk' => 'Sisir Kutu', 'kategori' => 'Perawatan', 'harga' => 35000, 'deskripsi' => 'Sisir khusus pembersih kutu.'],
            ['nama_produk' => 'Sisir Brush', 'kategori' => 'Perawatan', 'harga' => 35000, 'deskripsi' => 'Sisir harian bulu rapi.'],
            ['nama_produk' => 'Roll Bulu', 'kategori' => 'Perawatan', 'harga' => 35000, 'deskripsi' => 'Pembersih bulu di kain/baju.'],
            ['nama_produk' => 'Gunting Kuku', 'kategori' => 'Perawatan', 'harga' => 55000, 'deskripsi' => 'Gunting kuku khusus hewan.'],
            ['nama_produk' => 'Pasir Wangi 3L', 'kategori' => 'Sanitasi', 'harga' => 25000, 'deskripsi' => 'Pasir gumpal wangi 3L.'],
            ['nama_produk' => 'Pasir Wangi 5L', 'kategori' => 'Sanitasi', 'harga' => 40000, 'deskripsi' => 'Pasir gumpal wangi 5L.'],
            ['nama_produk' => 'Pasir Wangi 25L', 'kategori' => 'Sanitasi', 'harga' => 150000, 'deskripsi' => 'Pasir wangi kemasan karung.'],
            ['nama_produk' => 'Pasir Zeolit 20kg', 'kategori' => 'Sanitasi', 'harga' => 25000, 'deskripsi' => 'Pasir zeolit ekonomis.'],
            ['nama_produk' => 'Bak Pasir Kecil', 'kategori' => 'Sanitasi', 'harga' => 20000, 'deskripsi' => 'Bak pasir kucing size S.'],
            ['nama_produk' => 'Bak Pasir Sedang', 'kategori' => 'Sanitasi', 'harga' => 35000, 'deskripsi' => 'Bak pasir kucing size M.'],
            ['nama_produk' => 'Bak Pasir Besar', 'kategori' => 'Sanitasi', 'harga' => 45000, 'deskripsi' => 'Bak pasir kucing size L.'],
            ['nama_produk' => 'Serok', 'kategori' => 'Sanitasi', 'harga' => 30000, 'deskripsi' => 'Serokan kotoran pasir.'],

            // AKSESORIS & PERLENGKAPAN
            ['nama_produk' => 'Bantal Kucing', 'kategori' => 'Perlengkapan', 'harga' => 50000, 'deskripsi' => 'Bantal tidur nyaman.'],
            ['nama_produk' => 'Tas Kucing Kecil', 'kategori' => 'Aksesoris', 'harga' => 200000, 'deskripsi' => 'Tas travel size S.'],
            ['nama_produk' => 'Tas Kucing Besar', 'kategori' => 'Aksesoris', 'harga' => 220000, 'deskripsi' => 'Tas travel size L.'],
            ['nama_produk' => 'Ransel Astronot', 'kategori' => 'Aksesoris', 'harga' => 350000, 'deskripsi' => 'Tas astronot transparan.'],
            ['nama_produk' => 'Pet Cargo Kecil', 'kategori' => 'Aksesoris', 'harga' => 220000, 'deskripsi' => 'Pet cargo box size S.'],
            ['nama_produk' => 'Pet Cargo Besar', 'kategori' => 'Aksesoris', 'harga' => 240000, 'deskripsi' => 'Pet cargo box size L.'],
            ['nama_produk' => 'Kandang 02', 'kategori' => 'Perlengkapan', 'harga' => 250000, 'deskripsi' => 'Kandang besi lipat 02.'],
            ['nama_produk' => 'Kandang 03', 'kategori' => 'Perlengkapan', 'harga' => 450000, 'deskripsi' => 'Kandang besi lipat 03.'],
            ['nama_produk' => 'Kandang 04', 'kategori' => 'Perlengkapan', 'harga' => 550000, 'deskripsi' => 'Kandang besi lipat 04.'],
            ['nama_produk' => 'Kalung', 'kategori' => 'Aksesoris', 'harga' => 20000, 'deskripsi' => 'Kalung nilon lonceng.'],
            ['nama_produk' => 'Harness', 'kategori' => 'Aksesoris', 'harga' => 60000, 'deskripsi' => 'Tali tuntun badan.'],
            ['nama_produk' => 'Baju Kucing', 'kategori' => 'Aksesoris', 'harga' => 50000, 'deskripsi' => 'Pakaian hewan nyaman.'],
            ['nama_produk' => 'Tongkat Mainan', 'kategori' => 'Aksesoris', 'harga' => 20000, 'deskripsi' => 'Mainan interaksi kucing.'],
            ['nama_produk' => 'Colar', 'kategori' => 'Aksesoris', 'harga' => 45000, 'deskripsi' => 'Pelindung leher medis.'],
            ['nama_produk' => 'Dot', 'kategori' => 'Perlengkapan', 'harga' => 25000, 'deskripsi' => 'Botol susu bayi anabul.'],
            ['nama_produk' => 'Tempat Makan Single', 'kategori' => 'Perlengkapan', 'harga' => 20000, 'deskripsi' => 'Wadah makan satu lubang.'],
            ['nama_produk' => 'Tempat Makan Double', 'kategori' => 'Perlengkapan', 'harga' => 50000, 'deskripsi' => 'Wadah makan dua lubang.'],

            // MAKANAN UMUM & SUPLEMEN
            ['nama_produk' => 'Makanan Saset', 'kategori' => 'Makanan', 'harga' => 7000, 'deskripsi' => 'Wet food saset.'],
            ['nama_produk' => 'Makanan Kaleng', 'kategori' => 'Makanan', 'harga' => 25000, 'deskripsi' => 'Wet food kaleng.'],
            ['nama_produk' => 'Multivitamin 20g', 'kategori' => 'Suplemen', 'harga' => 35000, 'deskripsi' => 'Vitamin imun tubuh.'],
            ['nama_produk' => 'Minyak Ikan', 'kategori' => 'Suplemen', 'harga' => 30000, 'deskripsi' => 'Suplemen bulu & otak.'],
            ['nama_produk' => 'Nutriplus gel', 'kategori' => 'Suplemen', 'harga' => 185000, 'deskripsi' => 'Gel energi tinggi.'],
            ['nama_produk' => 'Meo Persian Adult 1,1kg', 'kategori' => 'Makanan', 'harga' => 70000, 'deskripsi' => 'Makanan kucing persia.'],
            ['nama_produk' => 'Meo Persian Kitten 1,1kg', 'kategori' => 'Makanan', 'harga' => 75000, 'deskripsi' => 'Makanan kitten persia.'],
            ['nama_produk' => 'Cleo Adult 1,5kg', 'kategori' => 'Makanan', 'harga' => 70000, 'deskripsi' => 'Cleo adult dry food.'],
            ['nama_produk' => 'Cleo Kitten 1,3kg', 'kategori' => 'Makanan', 'harga' => 75000, 'deskripsi' => 'Cleo kitten dry food.'],
            ['nama_produk' => 'Kitkat 450g', 'kategori' => 'Makanan', 'harga' => 35000, 'deskripsi' => 'Makanan kucing Kitkat.'],
            ['nama_produk' => 'Whiskas 450g', 'kategori' => 'Makanan', 'harga' => 35000, 'deskripsi' => 'Whiskas dry food 450g.'],
            ['nama_produk' => 'Whiskas 1,1kg', 'kategori' => 'Makanan', 'harga' => 85000, 'deskripsi' => 'Whiskas dry food 1.1kg.'],
            ['nama_produk' => 'Whiskas 8kg', 'kategori' => 'Makanan', 'harga' => 350000, 'deskripsi' => 'Whiskas karung ekonomis.'],

            // BRAND: PROPLAN
            ['nama_produk' => 'Proplan Adult 300g', 'kategori' => 'Makanan', 'harga' => 50000, 'deskripsi' => 'Proplan adult premium.'],
            ['nama_produk' => 'Proplan Kitten 300g', 'kategori' => 'Makanan', 'harga' => 55000, 'deskripsi' => 'Proplan kitten premium.'],
            ['nama_produk' => 'Proplan Adult 500g', 'kategori' => 'Makanan', 'harga' => 75000, 'deskripsi' => 'Proplan adult 500g.'],
            ['nama_produk' => 'Proplan Kitten 500g', 'kategori' => 'Makanan', 'harga' => 85000, 'deskripsi' => 'Proplan kitten 500g.'],
            ['nama_produk' => 'Proplan Intestinal 500g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'Diet khusus pencernaan.'],
            ['nama_produk' => 'Proplan Urinary 500g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'Diet khusus urinari.'],
            ['nama_produk' => 'Proplan Adult 1,5kg', 'kategori' => 'Makanan', 'harga' => 270000, 'deskripsi' => 'Proplan adult besar.'],
            ['nama_produk' => 'Proplan Kitten 1,5kg', 'kategori' => 'Makanan', 'harga' => 280000, 'deskripsi' => 'Proplan kitten besar.'],

            // BRAND: ROYAL CANIN CAT
            ['nama_produk' => 'Royal Canin Cat Persian Adult 400g', 'kategori' => 'Makanan', 'harga' => 85000, 'deskripsi' => 'RC Persian Adult.'],
            ['nama_produk' => 'Royal Canin Cat Persian Adult 2kg', 'kategori' => 'Makanan', 'harga' => 340000, 'deskripsi' => 'RC Persian Adult 2kg.'],
            ['nama_produk' => 'Royal Canin Cat Persian Kitten 400g', 'kategori' => 'Makanan', 'harga' => 90000, 'deskripsi' => 'RC Persian Kitten.'],
            ['nama_produk' => 'Royal Canin Cat Persian Kitten 2kg', 'kategori' => 'Makanan', 'harga' => 360000, 'deskripsi' => 'RC Persian Kitten 2kg.'],
            ['nama_produk' => 'Royal Canin Cat Mother Baby 400g', 'kategori' => 'Makanan', 'harga' => 85000, 'deskripsi' => 'RC Mother & Baby.'],
            ['nama_produk' => 'Royal Canin Cat Mother Baby 2kg', 'kategori' => 'Makanan', 'harga' => 340000, 'deskripsi' => 'RC Mother & Baby 2kg.'],
            ['nama_produk' => 'Royal Canin Cat Mother Baby Wet 195g', 'kategori' => 'Makanan', 'harga' => 60000, 'deskripsi' => 'RC Mother & Baby Wet.'],
            ['nama_produk' => 'Royal Canin Cat Kitten 400g', 'kategori' => 'Makanan', 'harga' => 85000, 'deskripsi' => 'RC Kitten Dry.'],
            ['nama_produk' => 'Royal Canin Cat Kitten 2kg', 'kategori' => 'Makanan', 'harga' => 340000, 'deskripsi' => 'RC Kitten Dry 2kg.'],
            ['nama_produk' => 'Royal Canin Cat Gastro Hairball 400g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'RC Gastro Hairball.'],
            ['nama_produk' => 'Royal Canin Cat Gastro Fibre 400g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'RC Gastro Fibre.'],
            ['nama_produk' => 'Royal Canin Cat Gastro Adult 400g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'RC Gastro Adult.'],
            ['nama_produk' => 'Royal Canin Cat Gastro Adult 2kg', 'kategori' => 'Makanan Medis', 'harga' => 380000, 'deskripsi' => 'RC Gastro Adult 2kg.'],
            ['nama_produk' => 'Royal Canin Cat Gastro Kitten 400g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'RC Gastro Kitten.'],
            ['nama_produk' => 'Royal Canin Cat Gastro Kitten 2kg', 'kategori' => 'Makanan Medis', 'harga' => 385000, 'deskripsi' => 'RC Gastro Kitten 2kg.'],
            ['nama_produk' => 'Royal Canin Cat Urinary S/O 400g', 'kategori' => 'Makanan Medis', 'harga' => 95000, 'deskripsi' => 'RC Urinary S/O.'],
            ['nama_produk' => 'Royal Canin Cat Urinary S/O 1,5kg', 'kategori' => 'Makanan Medis', 'harga' => 310000, 'deskripsi' => 'RC Urinary S/O 1.5kg.'],
            ['nama_produk' => 'Royal Canin Cat Skin and Coat 400g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'RC Skin & Coat.'],
            ['nama_produk' => 'Royal Canin Cat Skin and Coat 1,5kg', 'kategori' => 'Makanan Medis', 'harga' => 310000, 'deskripsi' => 'RC Skin & Coat 1.5kg.'],
            ['nama_produk' => 'Royal Canin Cat Gastro Kitten Wet 195g', 'kategori' => 'Makanan Medis', 'harga' => 60000, 'deskripsi' => 'RC Gastro Kitten Wet.'],
            ['nama_produk' => 'Royal Canin Cat Urinary 85g', 'kategori' => 'Makanan Medis', 'harga' => 35000, 'deskripsi' => 'RC Urinary Pouch.'],
            ['nama_produk' => 'Royal Canin Cat Early Renal 400g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'RC Early Renal.'],
            ['nama_produk' => 'Royal Canin Cat Mature Consult 400g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'RC Mature Consult.'],
            ['nama_produk' => 'Royal Canin Cat Hypoallergenic 500g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'RC Hypoallergenic.'],
            ['nama_produk' => 'Royal Canin Cat Regular Sterilized 400g', 'kategori' => 'Makanan Medis', 'harga' => 100000, 'deskripsi' => 'RC Regular Sterilized.'],
            ['nama_produk' => 'Royal Canin Cat Recovery 195g', 'kategori' => 'Makanan Medis', 'harga' => 60000, 'deskripsi' => 'RC Recovery Wet.'],
            ['nama_produk' => 'Royal Canin Cat Baby Cat Milk 300g', 'kategori' => 'Suplemen', 'harga' => 375000, 'deskripsi' => 'Susu bayi kucing RC.'],

            // BRAND: ROYAL CANIN DOG
            ['nama_produk' => 'Royal Canin Dog Pomeranian 500g', 'kategori' => 'Makanan', 'harga' => 95000, 'deskripsi' => 'RC khusus Pomeranian.'],
            ['nama_produk' => 'Royal Canin Dog Pomeranian 1,5kg', 'kategori' => 'Makanan', 'harga' => 250000, 'deskripsi' => 'RC Pomeranian 1.5kg.'],
            ['nama_produk' => 'Royal Canin Dog Poodle Adult 1,5kg', 'kategori' => 'Makanan', 'harga' => 250000, 'deskripsi' => 'RC khusus Poodle Adult.'],
            ['nama_produk' => 'Royal Canin Dog Poodle Puppy 1,5kg', 'kategori' => 'Makanan', 'harga' => 270000, 'deskripsi' => 'RC khusus Poodle Puppy.'],
            ['nama_produk' => 'Royal Canin Dog Shihtzu Adult 1,5kg', 'kategori' => 'Makanan', 'harga' => 250000, 'deskripsi' => 'RC khusus Shih Tzu Adult.'],
            ['nama_produk' => 'Royal Canin Dog Shihtzu Puppy 1,5kg', 'kategori' => 'Makanan', 'harga' => 270000, 'deskripsi' => 'RC khusus Shih Tzu Puppy.'],
            ['nama_produk' => 'Royal Canin Dog Chihuahua Adult 1,5kg', 'kategori' => 'Makanan', 'harga' => 250000, 'deskripsi' => 'RC khusus Chihuahua Adult.'],
            ['nama_produk' => 'Royal Canin Dog Chihuahua Puppy 1,5kg', 'kategori' => 'Makanan', 'harga' => 270000, 'deskripsi' => 'RC khusus Chihuahua Puppy.'],

            // OTHERS (BRAND CAMPUR)
            ['nama_produk' => 'Starter Mini 1kg', 'kategori' => 'Makanan', 'harga' => 150000, 'deskripsi' => 'Starter mini size.'],
            ['nama_produk' => 'Starter Medium 1kg', 'kategori' => 'Makanan', 'harga' => 150000, 'deskripsi' => 'Starter medium size.'],
            ['nama_produk' => 'Pedigree Wet 400g', 'kategori' => 'Makanan', 'harga' => 35000, 'deskripsi' => 'Pedigree kaleng 400g.'],
            ['nama_produk' => 'Pedigree Wet 700g', 'kategori' => 'Makanan', 'harga' => 55000, 'deskripsi' => 'Pedigree kaleng 700g.'],
            ['nama_produk' => 'Pedigree Wet 1,15kg', 'kategori' => 'Makanan', 'harga' => 75000, 'deskripsi' => 'Pedigree kaleng 1.15kg.'],
            ['nama_produk' => 'Pedigree Adult 1,5kg', 'kategori' => 'Makanan', 'harga' => 80000, 'deskripsi' => 'Pedigree adult dry.'],
            ['nama_produk' => 'Pedigree Puppy 1,3kg', 'kategori' => 'Makanan', 'harga' => 85000, 'deskripsi' => 'Pedigree puppy dry.'],
            ['nama_produk' => 'Dog Choice 800g', 'kategori' => 'Makanan', 'harga' => 25000, 'deskripsi' => 'Dog choice ekonomis.'],
            ['nama_produk' => 'Pedigree Pro Max 1,5kg', 'kategori' => 'Makanan', 'harga' => 135000, 'deskripsi' => 'Pedigree Pro Max.'],
            ['nama_produk' => 'Recovery Dog', 'kategori' => 'Makanan Medis', 'harga' => 60000, 'deskripsi' => 'Diet pemulihan anjing.'],
            ['nama_produk' => 'Healthy Pet 400g', 'kategori' => 'Makanan', 'harga' => 50000, 'deskripsi' => 'Healthy Pet dry food.'],
            ['nama_produk' => 'Healthy Pet 1,2kg', 'kategori' => 'Makanan', 'harga' => 145000, 'deskripsi' => 'Healthy Pet 1.2kg.'],
            ['nama_produk' => 'Healthy Pet 7,5kg', 'kategori' => 'Makanan', 'harga' => 750000, 'deskripsi' => 'Healthy Pet karung besar.'],
            ['nama_produk' => 'Felibite 500g', 'kategori' => 'Makanan', 'harga' => 15000, 'deskripsi' => 'Felibite ekonomis.'],
            ['nama_produk' => 'Felibite Kitten 500g', 'kategori' => 'Makanan', 'harga' => 18000, 'deskripsi' => 'Felibite kitten.'],
            ['nama_produk' => 'Excel 500g', 'kategori' => 'Makanan', 'harga' => 15000, 'deskripsi' => 'Excel ekonomis.'],
            ['nama_produk' => 'Mr. Puss 500g', 'kategori' => 'Makanan', 'harga' => 15000, 'deskripsi' => 'Mr. Puss ekonomis.'],
            ['nama_produk' => 'Cat Choize 800g', 'kategori' => 'Makanan', 'harga' => 25000, 'deskripsi' => 'Cat Choize adult.'],
            ['nama_produk' => 'Cat Choize Kitten 1kg', 'kategori' => 'Makanan', 'harga' => 30000, 'deskripsi' => 'Cat Choize kitten.'],
            ['nama_produk' => 'Cat Choize MB 800g', 'kategori' => 'Makanan', 'harga' => 30000, 'deskripsi' => 'Cat Choize Mother & Baby.'],
            ['nama_produk' => 'Cutties 1kg', 'kategori' => 'Makanan', 'harga' => 30000, 'deskripsi' => 'Cutties dry food.'],
            ['nama_produk' => 'Lezato 1kg', 'kategori' => 'Makanan', 'harga' => 25000, 'deskripsi' => 'Lezato dry food.'],
            ['nama_produk' => 'Maxi 400g', 'kategori' => 'Makanan', 'harga' => 30000, 'deskripsi' => 'Maxi dry food.'],
            ['nama_produk' => 'Meo 400g', 'kategori' => 'Makanan', 'harga' => 25000, 'deskripsi' => 'Meo dry food.'],
            ['nama_produk' => 'Nice 800g', 'kategori' => 'Makanan', 'harga' => 22000, 'deskripsi' => 'Nice dry food.'],
            ['nama_produk' => 'Oricat Adult 800g', 'kategori' => 'Makanan', 'harga' => 22000, 'deskripsi' => 'Oricat adult.'],
            ['nama_produk' => 'Oricat Kitten 800g', 'kategori' => 'Makanan', 'harga' => 24000, 'deskripsi' => 'Oricat kitten.'],
            ['nama_produk' => 'Kucingku 800g', 'kategori' => 'Makanan', 'harga' => 22000, 'deskripsi' => 'Kucingku dry food.'],
            ['nama_produk' => 'Chester 800g', 'kategori' => 'Makanan', 'harga' => 22000, 'deskripsi' => 'Chester dry food.'],
            ['nama_produk' => 'Beauty 1kg', 'kategori' => 'Makanan', 'harga' => 33000, 'deskripsi' => 'Beauty dry food.'],
            ['nama_produk' => 'Bolt 800g', 'kategori' => 'Makanan', 'harga' => 22000, 'deskripsi' => 'Bolt dry food.'],
            ['nama_produk' => 'Meo Adult 1,2kg', 'kategori' => 'Makanan', 'harga' => 70000, 'deskripsi' => 'Meo adult besar.'],
            ['nama_produk' => 'Meo Kitten 1,1kg', 'kategori' => 'Makanan', 'harga' => 75000, 'deskripsi' => 'Meo kitten besar.'],
        ];

        foreach ($dataProduk as $produk) {
            $produk['gambar'] = 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=400&auto=format&fit=crop';

            // Idempotent: mencegah produk dobel jika seeder dijalankan ulang
            // Stok dibuat random untuk kebutuhan data demo
            $stokRandom = random_int(20, 200);

            Produk::updateOrCreate(
                [
                    'nama_produk' => $produk['nama_produk'],
                    'kategori' => $produk['kategori'],
                ],
                [
                    'harga' => $produk['harga'],
                    'deskripsi' => $produk['deskripsi'],
                    'gambar' => $produk['gambar'],
                    'stok' => $stokRandom,
                ]
            );
        }

        // DATA LAYANAN
        $dataLayanan = [
            ['nama_layanan' => 'Grooming Sehat', 'kategori' => 'Grooming', 'harga' => 90000, 'deskripsi' => 'Mandi & potong kuku.', 'gambar' => 'https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?q=80&w=400&auto=format&fit=crop'],
            ['nama_layanan' => 'Konsultasi Dokter', 'kategori' => 'Klinik', 'harga' => 50000, 'deskripsi' => 'Pengecekan drh. profesional.', 'gambar' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?q=80&w=400&auto=format&fit=crop'],
            ['nama_layanan' => 'Penitipan Kamar VIP', 'kategori' => 'Pet Hotel', 'harga' => 120000, 'deskripsi' => 'Kamar AC & pengawasan 24 jam.', 'gambar' => 'https://images.unsplash.com/photo-1541781774459-bb2af2f05b55?q=80&w=400&auto=format&fit=crop'],
        ];

        foreach ($dataLayanan as $layanan) {
            // Idempotent layanan juga (hindari ganda jika seeder dijalankan ulang)
            Layanan::updateOrCreate(
                [
                    'nama_layanan' => $layanan['nama_layanan'],
                    'kategori' => $layanan['kategori'],
                ],
                [
                    'harga' => $layanan['harga'],
                    'deskripsi' => $layanan['deskripsi'],
                    'gambar' => $layanan['gambar'],
                ]
            );
        }
    }
}
