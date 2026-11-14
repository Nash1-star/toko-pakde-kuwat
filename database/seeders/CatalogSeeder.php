<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Makanan Ringan
            ['name' => 'Keripik Singkong Balado 250g', 'category' => 'Makanan Ringan', 'satuan' => 'Bungkus', 'barcode' => '8990101144', 'purchase_price' => 12000, 'selling_price' => 15000, 'current_stock' => 25, 'min_stock' => 5],
            ['name' => 'Kue Kering Nastar 500g', 'category' => 'Makanan Ringan', 'satuan' => 'Toples', 'barcode' => '8990102845', 'purchase_price' => 45000, 'selling_price' => 55000, 'current_stock' => 15, 'min_stock' => 3],
            ['name' => 'Wafer Coklat Roll 350g', 'category' => 'Makanan Ringan', 'satuan' => 'Kotak', 'barcode' => '8990103868', 'purchase_price' => 18000, 'selling_price' => 22000, 'current_stock' => 30, 'min_stock' => 5],
            ['name' => 'Kacang Mede Oven 200g', 'category' => 'Makanan Ringan', 'satuan' => 'Pouch', 'barcode' => '8990104214', 'purchase_price' => 35000, 'selling_price' => 42000, 'current_stock' => 20, 'min_stock' => 4],
            ['name' => 'Biskuit Gandum Susu', 'category' => 'Makanan Ringan', 'satuan' => 'Pack', 'barcode' => '8990105494', 'purchase_price' => 8000, 'selling_price' => 10000, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Kerupuk Udang Mentah 500g', 'category' => 'Makanan Ringan', 'satuan' => 'Bungkus', 'barcode' => '8990106990', 'purchase_price' => 15000, 'selling_price' => 19000, 'current_stock' => 25, 'min_stock' => 5],
            ['name' => 'Astor Coklat Stik', 'category' => 'Makanan Ringan', 'satuan' => 'Kaleng', 'barcode' => '8990107199', 'purchase_price' => 28000, 'selling_price' => 35000, 'current_stock' => 12, 'min_stock' => 3],
            ['name' => 'Makaroni Pedas Daun Jeruk', 'category' => 'Makanan Ringan', 'satuan' => 'Pouch', 'barcode' => '8990108770', 'purchase_price' => 7000, 'selling_price' => 10000, 'current_stock' => 50, 'min_stock' => 10],
            ['name' => 'Kue Sagu Susu Keju', 'category' => 'Makanan Ringan', 'satuan' => 'Toples', 'barcode' => '8990109996', 'purchase_price' => 3000, 'selling_price' => 38000, 'current_stock' => 18, 'min_stock' => 4],
            ['name' => 'Emping Melinjo Asli 250g', 'category' => 'Makanan Ringan', 'satuan' => 'Bungkus', 'barcode' => '8990110565', 'purchase_price' => 22000, 'selling_price' => 28000, 'current_stock' => 20, 'min_stock' => 5],

            // Minuman
            ['name' => 'Aqua Botol 600ml', 'category' => 'Minuman', 'satuan' => 'Botol', 'barcode' => '8990201100', 'purchase_price' => 2500, 'selling_price' => 3500, 'current_stock' => 100, 'min_stock' => 20],
            ['name' => 'Teh Botol Sosro 450ml', 'category' => 'Minuman', 'satuan' => 'Botol', 'barcode' => '8990202944', 'purchase_price' => 4000, 'selling_price' => 5500, 'current_stock' => 60, 'min_stock' => 15],
            ['name' => 'Susu UHT Coklat 1L', 'category' => 'Minuman', 'satuan' => 'Kotak', 'barcode' => '8990203994', 'purchase_price' => 16000, 'selling_price' => 19500, 'current_stock' => 30, 'min_stock' => 8],
            ['name' => 'Kopi Susu Botol 250ml', 'category' => 'Minuman', 'satuan' => 'Botol', 'barcode' => '8990204150', 'purchase_price' => 6000, 'selling_price' => 8500, 'current_stock' => 45, 'min_stock' => 10],
            ['name' => 'Jus Jeruk Kotak 1L', 'category' => 'Minuman', 'satuan' => 'Kotak', 'barcode' => '8990205660', 'purchase_price' => 14000, 'selling_price' => 17500, 'current_stock' => 25, 'min_stock' => 5],
            ['name' => 'Minuman Isotonik 500ml', 'category' => 'Minuman', 'satuan' => 'Botol', 'barcode' => '8990206246', 'purchase_price' => 5000, 'selling_price' => 7000, 'current_stock' => 50, 'min_stock' => 12],
            ['name' => 'Soda Jeruk 330ml', 'category' => 'Minuman', 'satuan' => 'Kaleng', 'barcode' => '8990207554', 'purchase_price' => 5500, 'selling_price' => 7500, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Teh Hijau Melati 450ml', 'category' => 'Minuman', 'satuan' => 'Botol', 'barcode' => '8990208577', 'purchase_price' => 4500, 'selling_price' => 6000, 'current_stock' => 55, 'min_stock' => 15],
            ['name' => 'Susu Kedelai Manis 300ml', 'category' => 'Minuman', 'satuan' => 'Botol', 'barcode' => '8990209518', 'purchase_price' => 7000, 'selling_price' => 9500, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Air Mineral Galon 19L', 'category' => 'Minuman', 'satuan' => 'Galon', 'barcode' => '8990210819', 'purchase_price' => 18000, 'selling_price' => 22000, 'current_stock' => 15, 'min_stock' => 4],

            // Kebutuhan Mandi & Perawatan
            ['name' => 'Sabun Mandi Batang 80g', 'category' => 'Kebutuhan Mandi & Perawatan', 'satuan' => 'Pcs', 'barcode' => '8990301477', 'purchase_price' => 3500, 'selling_price' => 5000, 'current_stock' => 80, 'min_stock' => 20],
            ['name' => 'Shampoo Anti Ketombe 160ml', 'category' => 'Kebutuhan Mandi & Perawatan', 'satuan' => 'Botol', 'barcode' => '8990302477', 'purchase_price' => 19000, 'selling_price' => 24000, 'current_stock' => 35, 'min_stock' => 10],
            ['name' => 'Pasta Gigi 120g', 'category' => 'Kebutuhan Mandi & Perawatan', 'satuan' => 'Pcs', 'barcode' => '8990303810', 'purchase_price' => 9000, 'selling_price' => 12500, 'current_stock' => 50, 'min_stock' => 15],
            ['name' => 'Sikat Gigi Soft', 'category' => 'Kebutuhan Mandi & Perawatan', 'satuan' => 'Pcs', 'barcode' => '8990304852', 'purchase_price' => 6000, 'selling_price' => 9000, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Sabun Cair Refill 400ml', 'category' => 'Kebutuhan Mandi & Perawatan', 'satuan' => 'Pouch', 'barcode' => '8990305157', 'purchase_price' => 18000, 'selling_price' => 23000, 'current_stock' => 30, 'min_stock' => 8],
            ['name' => 'Conditioner Rambut 150ml', 'category' => 'Kebutuhan Mandi & Perawatan', 'satuan' => 'Botol', 'barcode' => '8990306369', 'purchase_price' => 22000, 'selling_price' => 28000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Pembersih Muka 100g', 'category' => 'Kebutuhan Mandi & Perawatan', 'satuan' => 'Tube', 'barcode' => '8990307959', 'purchase_price' => 25000, 'selling_price' => 32000, 'current_stock' => 25, 'min_stock' => 6],
            ['name' => 'Lulur Mandi Susu 250g', 'category' => 'Kebutuhan Mandi & Perawatan', 'satuan' => 'Pot', 'barcode' => '8990308835', 'purchase_price' => 15000, 'selling_price' => 20000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Deodorant Roll-on 50ml', 'category' => 'Kebutuhan Mandi & Perawatan', 'satuan' => 'Pcs', 'barcode' => '8990309684', 'purchase_price' => 16000, 'selling_price' => 21000, 'current_stock' => 25, 'min_stock' => 7],
            ['name' => 'Batu Apus Sikat Kaki', 'category' => 'Kebutuhan Mandi & Perawatan', 'satuan' => 'Pcs', 'barcode' => '8990310871', 'purchase_price' => 4000, 'selling_price' => 7000, 'current_stock' => 30, 'min_stock' => 10],

            // Bumbu Dapur
            ['name' => 'Minyak Goreng 2L', 'category' => 'Bumbu Dapur', 'satuan' => 'Pouch', 'barcode' => '8990401292', 'purchase_price' => 32000, 'selling_price' => 38000, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Garam Konsumsi 500g', 'category' => 'Bumbu Dapur', 'satuan' => 'Bungkus', 'barcode' => '8990402133', 'purchase_price' => 3000, 'selling_price' => 4500, 'current_stock' => 60, 'min_stock' => 15],
            ['name' => 'Gula Pasir Putih 1kg', 'category' => 'Bumbu Dapur', 'satuan' => 'Bungkus', 'barcode' => '8990403164', 'purchase_price' => 15500, 'selling_price' => 18000, 'current_stock' => 50, 'min_stock' => 12],
            ['name' => 'Tepung Terigu Serbaguna 1kg', 'category' => 'Bumbu Dapur', 'satuan' => 'Bungkus', 'barcode' => '8990404155', 'purchase_price' => 11000, 'selling_price' => 13500, 'current_stock' => 45, 'min_stock' => 10],
            ['name' => 'Kecap Manis 520ml', 'category' => 'Bumbu Dapur', 'satuan' => 'Pouch', 'barcode' => '8990405978', 'purchase_price' => 19000, 'selling_price' => 23500, 'current_stock' => 35, 'min_stock' => 8],
            ['name' => 'Saus Sambal Botol 335ml', 'category' => 'Bumbu Dapur', 'satuan' => 'Botol', 'barcode' => '8990406993', 'purchase_price' => 13000, 'selling_price' => 16500, 'current_stock' => 30, 'min_stock' => 8],
            ['name' => 'Merica Bubuk Sachet', 'category' => 'Bumbu Dapur', 'satuan' => 'Renceng', 'barcode' => '8990407885', 'purchase_price' => 5000, 'selling_price' => 7000, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Bumbu Kaldu Ayam 250g', 'category' => 'Bumbu Dapur', 'satuan' => 'Pouch', 'barcode' => '8990408902', 'purchase_price' => 10000, 'selling_price' => 13000, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Santan Kelapa Instant 65ml', 'category' => 'Bumbu Dapur', 'satuan' => 'Pcs', 'barcode' => '8990409260', 'purchase_price' => 3500, 'selling_price' => 5000, 'current_stock' => 60, 'min_stock' => 15],
            ['name' => 'Bawang Putih Kupas 250g', 'category' => 'Bumbu Dapur', 'satuan' => 'Pack', 'barcode' => '8990410901', 'purchase_price' => 9000, 'selling_price' => 12000, 'current_stock' => 25, 'min_stock' => 5],

            // Produk Susu & Olahan
            ['name' => 'Susu Kental Manis Putih', 'category' => 'Produk Susu & Olahan', 'satuan' => 'Kaleng', 'barcode' => '8990501303', 'purchase_price' => 11000, 'selling_price' => 14000, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Keju Cheddar Batang 165g', 'category' => 'Produk Susu & Olahan', 'satuan' => 'Pcs', 'barcode' => '8990502420', 'purchase_price' => 17000, 'selling_price' => 21500, 'current_stock' => 30, 'min_stock' => 8],
            ['name' => 'Mentega Serbaguna 200g', 'category' => 'Produk Susu & Olahan', 'satuan' => 'Sachet', 'barcode' => '8990503960', 'purchase_price' => 8000, 'selling_price' => 11000, 'current_stock' => 35, 'min_stock' => 10],
            ['name' => 'Susu Bubuk Full Cream 400g', 'category' => 'Produk Susu & Olahan', 'satuan' => 'Kotak', 'barcode' => '8990504313', 'purchase_price' => 42000, 'selling_price' => 50000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Yogurt Drink Rasa Buah 200ml', 'category' => 'Produk Susu & Olahan', 'satuan' => 'Botol', 'barcode' => '8990505803', 'purchase_price' => 7500, 'selling_price' => 10000, 'current_stock' => 30, 'min_stock' => 8],
            ['name' => 'Susu Steril Kaleng 189ml', 'category' => 'Produk Susu & Olahan', 'satuan' => 'Kaleng', 'barcode' => '8990506670', 'purchase_price' => 9500, 'selling_price' => 12000, 'current_stock' => 50, 'min_stock' => 15],
            ['name' => 'Keju Slice Isi 10', 'category' => 'Produk Susu & Olahan', 'satuan' => 'Pack', 'barcode' => '8990507709', 'purchase_price' => 15000, 'selling_price' => 19000, 'current_stock' => 25, 'min_stock' => 6],
            ['name' => 'Susu Evaporasi 380g', 'category' => 'Produk Susu & Olahan', 'satuan' => 'Kaleng', 'barcode' => '8990508164', 'purchase_price' => 14000, 'selling_price' => 18000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Margarine Serbaguna 250g', 'category' => 'Produk Susu & Olahan', 'satuan' => 'Pcs', 'barcode' => '8990509405', 'purchase_price' => 7000, 'selling_price' => 9500, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Susu Almond Kotak 250ml', 'category' => 'Produk Susu & Olahan', 'satuan' => 'Kotak', 'barcode' => '8990510232', 'purchase_price' => 18000, 'selling_price' => 23000, 'current_stock' => 15, 'min_stock' => 4],

            // Perawatan Rumah Tangga
            ['name' => 'Deterjen Bubuk 800g', 'category' => 'Perawatan Rumah Tangga', 'satuan' => 'Bungkus', 'barcode' => '8990601590', 'purchase_price' => 18500, 'selling_price' => 23000, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Pembersih Lantai 800ml', 'category' => 'Perawatan Rumah Tangga', 'satuan' => 'Pouch', 'barcode' => '8990602301', 'purchase_price' => 14000, 'selling_price' => 18000, 'current_stock' => 35, 'min_stock' => 8],
            ['name' => 'Pencuci Piring Jeruk Nipis 750ml', 'category' => 'Perawatan Rumah Tangga', 'satuan' => 'Pouch', 'barcode' => '8990603492', 'purchase_price' => 16000, 'selling_price' => 20500, 'current_stock' => 45, 'min_stock' => 12],
            ['name' => 'Pewangi Pakaian Refill 900ml', 'category' => 'Perawatan Rumah Tangga', 'satuan' => 'Pouch', 'barcode' => '8990604223', 'purchase_price' => 15000, 'selling_price' => 19000, 'current_stock' => 35, 'min_stock' => 10],
            ['name' => 'Pembersih Kaca 500ml', 'category' => 'Perawatan Rumah Tangga', 'satuan' => 'Botol', 'barcode' => '8990605973', 'purchase_price' => 13000, 'selling_price' => 17000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Tisu Toilet 10 Rol', 'category' => 'Perawatan Rumah Tangga', 'satuan' => 'Pack', 'barcode' => '8990606873', 'purchase_price' => 32000, 'selling_price' => 40000, 'current_stock' => 25, 'min_stock' => 6],
            ['name' => 'Tisu Wajah 200 Sheets', 'category' => 'Perawatan Rumah Tangga', 'satuan' => 'Pouch', 'barcode' => '8990607465', 'purchase_price' => 11000, 'selling_price' => 14500, 'current_stock' => 50, 'min_stock' => 15],
            ['name' => 'Kain Lap Serbaguna', 'category' => 'Perawatan Rumah Tangga', 'satuan' => 'Pcs', 'barcode' => '8990608736', 'purchase_price' => 4000, 'selling_price' => 7000, 'current_stock' => 60, 'min_stock' => 20],
            ['name' => 'Pemberantas Serangga Aerosol 600ml', 'category' => 'Perawatan Rumah Tangga', 'satuan' => 'Kaleng', 'barcode' => '8990609685', 'purchase_price' => 35000, 'selling_price' => 44000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Spon Cuci Piring Isi 3', 'category' => 'Perawatan Rumah Tangga', 'satuan' => 'Pack', 'barcode' => '8990610703', 'purchase_price' => 6000, 'selling_price' => 9000, 'current_stock' => 40, 'min_stock' => 10],

            // Perawatan Bayi
            ['name' => 'Popok Bayi M32', 'category' => 'Perawatan Bayi', 'satuan' => 'Pack', 'barcode' => '8990701142', 'purchase_price' => 52000, 'selling_price' => 62000, 'current_stock' => 25, 'min_stock' => 6],
            ['name' => 'Tisu Basah Bayi 50s', 'category' => 'Perawatan Bayi', 'satuan' => 'Pack', 'barcode' => '8990702114', 'purchase_price' => 11000, 'selling_price' => 15000, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Sabun & Shampoo Bayi 200ml', 'category' => 'Perawatan Bayi', 'satuan' => 'Botol', 'barcode' => '8990703520', 'purchase_price' => 24000, 'selling_price' => 30000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Minyak Telon 60ml', 'category' => 'Perawatan Bayi', 'satuan' => 'Botol', 'barcode' => '8990704883', 'purchase_price' => 16000, 'selling_price' => 21000, 'current_stock' => 30, 'min_stock' => 8],
            ['name' => 'Bedak Bayi 300g', 'category' => 'Perawatan Bayi', 'satuan' => 'Botol', 'barcode' => '8990705742', 'purchase_price' => 12000, 'selling_price' => 16000, 'current_stock' => 25, 'min_stock' => 6],
            ['name' => 'Bubur Bayi Instant 6+', 'category' => 'Perawatan Bayi', 'satuan' => 'Kotak', 'barcode' => '8990706128', 'purchase_price' => 15000, 'selling_price' => 19000, 'current_stock' => 30, 'min_stock' => 8],
            ['name' => 'Susu Formula Bayi 0-6 bln 400g', 'category' => 'Perawatan Bayi', 'satuan' => 'Kotak', 'barcode' => '8990707491', 'purchase_price' => 75000, 'selling_price' => 88000, 'current_stock' => 15, 'min_stock' => 4],
            ['name' => 'Krim Ruam Popok 50g', 'category' => 'Perawatan Bayi', 'satuan' => 'Tube', 'barcode' => '8990708354', 'purchase_price' => 28000, 'selling_price' => 35000, 'current_stock' => 15, 'min_stock' => 4],
            ['name' => 'Botol Susu Bayi 150ml', 'category' => 'Perawatan Bayi', 'satuan' => 'Pcs', 'barcode' => '8990709939', 'purchase_price' => 22000, 'selling_price' => 28000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Deterjen Khusus Pakaian Bayi 1L', 'category' => 'Perawatan Bayi', 'satuan' => 'Botol', 'barcode' => '8990710230', 'purchase_price' => 31000, 'selling_price' => 39000, 'current_stock' => 18, 'min_stock' => 4],

            // Alat Tulis Kantor
            ['name' => 'Buku Tulis 58 Lembar Isi 10', 'category' => 'Alat Tulis Kantor', 'satuan' => 'Pack', 'barcode' => '8990801432', 'purchase_price' => 32000, 'selling_price' => 40000, 'current_stock' => 30, 'min_stock' => 8],
            ['name' => 'Pulpen Hitam Standard', 'category' => 'Alat Tulis Kantor', 'satuan' => 'Pcs', 'barcode' => '8990802119', 'purchase_price' => 2500, 'selling_price' => 4000, 'current_stock' => 100, 'min_stock' => 20],
            ['name' => 'Pensil 2B Berkualitas', 'category' => 'Alat Tulis Kantor', 'satuan' => 'Pcs', 'barcode' => '8990803515', 'purchase_price' => 3000, 'selling_price' => 5000, 'current_stock' => 80, 'min_stock' => 15],
            ['name' => 'Penghapus Karet Soft', 'category' => 'Alat Tulis Kantor', 'satuan' => 'Pcs', 'barcode' => '8990804319', 'purchase_price' => 1500, 'selling_price' => 3000, 'current_stock' => 60, 'min_stock' => 15],
            ['name' => 'Penggaris Plastik 30cm', 'category' => 'Alat Tulis Kantor', 'satuan' => 'Pcs', 'barcode' => '8990805515', 'purchase_price' => 2000, 'selling_price' => 4000, 'current_stock' => 50, 'min_stock' => 10],
            ['name' => 'Spidol Whiteboard Hitam', 'category' => 'Alat Tulis Kantor', 'satuan' => 'Pcs', 'barcode' => '8990806328', 'purchase_price' => 6000, 'selling_price' => 9000, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Kertas HVS A4 70gsm 1 Rim', 'category' => 'Alat Tulis Kantor', 'satuan' => 'Rim', 'barcode' => '8990807792', 'purchase_price' => 42000, 'selling_price' => 51000, 'current_stock' => 25, 'min_stock' => 6],
            ['name' => 'Braket Binder Klip Kecil', 'category' => 'Alat Tulis Kantor', 'satuan' => 'Box', 'barcode' => '8990808693', 'purchase_price' => 4500, 'selling_price' => 7000, 'current_stock' => 45, 'min_stock' => 10],
            ['name' => 'Gunting Kertas Sedang', 'category' => 'Alat Tulis Kantor', 'satuan' => 'Pcs', 'barcode' => '8990809409', 'purchase_price' => 7000, 'selling_price' => 11000, 'current_stock' => 30, 'min_stock' => 8],
            ['name' => 'Lakban Bening 2 Inch', 'category' => 'Alat Tulis Kantor', 'satuan' => 'Pcs', 'barcode' => '8990810953', 'purchase_price' => 10000, 'selling_price' => 14000, 'current_stock' => 35, 'min_stock' => 10],

            // Obat-obatan & P3K
            ['name' => 'Tablet Paracetamol 500mg Strip', 'category' => 'Obat-obatan & P3K', 'satuan' => 'Strip', 'barcode' => '8990901810', 'purchase_price' => 3000, 'selling_price' => 5000, 'current_stock' => 80, 'min_stock' => 20],
            ['name' => 'Obat Batuk Hitam 100ml', 'category' => 'Obat-obatan & P3K', 'satuan' => 'Botol', 'barcode' => '8990902359', 'purchase_price' => 16000, 'selling_price' => 21000, 'current_stock' => 30, 'min_stock' => 8],
            ['name' => 'Vitamin C 1000mg Strip', 'category' => 'Obat-obatan & P3K', 'satuan' => 'Strip', 'barcode' => '8990903574', 'purchase_price' => 12000, 'selling_price' => 16000, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Salep Luka Bakar 15g', 'category' => 'Obat-obatan & P3K', 'satuan' => 'Tube', 'barcode' => '8990904447', 'purchase_price' => 18000, 'selling_price' => 24000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Minyak Kayu Putih 60ml', 'category' => 'Obat-obatan & P3K', 'satuan' => 'Botol', 'barcode' => '8990905832', 'purchase_price' => 22000, 'selling_price' => 28000, 'current_stock' => 35, 'min_stock' => 10],
            ['name' => 'Plester Luka Cepat Isi 10', 'category' => 'Obat-obatan & P3K', 'satuan' => 'Box', 'barcode' => '8990906143', 'purchase_price' => 5000, 'selling_price' => 8000, 'current_stock' => 50, 'min_stock' => 15],
            ['name' => 'Kasa Steril Pembalut 10s', 'category' => 'Obat-obatan & P3K', 'satuan' => 'Pack', 'barcode' => '8990907195', 'purchase_price' => 6000, 'selling_price' => 9500, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Antiseptik Cair 60ml', 'category' => 'Obat-obatan & P3K', 'satuan' => 'Botol', 'barcode' => '8990908636', 'purchase_price' => 14000, 'selling_price' => 19000, 'current_stock' => 25, 'min_stock' => 6],
            ['name' => 'Obat Sakit Kepala Strip', 'category' => 'Obat-obatan & P3K', 'satuan' => 'Strip', 'barcode' => '8990909114', 'purchase_price' => 4000, 'selling_price' => 6500, 'current_stock' => 60, 'min_stock' => 15],
            ['name' => 'Oralit Bubuk Elektrolit', 'category' => 'Obat-obatan & P3K', 'satuan' => 'Sachet', 'barcode' => '8990910599', 'purchase_price' => 1500, 'selling_price' => 2500, 'current_stock' => 100, 'min_stock' => 20],

            // Perawatan Pribadi
            ['name' => 'Parfum Spray Pria 100ml', 'category' => 'Perawatan Pribadi', 'satuan' => 'Botol', 'barcode' => '8991001395', 'purchase_price' => 45000, 'selling_price' => 58000, 'current_stock' => 15, 'min_stock' => 4],
            ['name' => 'Parfum Wanita Mist 125ml', 'category' => 'Perawatan Pribadi', 'satuan' => 'Botol', 'barcode' => '8991002442', 'purchase_price' => 40000, 'selling_price' => 52000, 'current_stock' => 15, 'min_stock' => 4],
            ['name' => 'Penyegar Nafas Mouthwash 250ml', 'category' => 'Perawatan Pribadi', 'satuan' => 'Botol', 'barcode' => '8991003889', 'purchase_price' => 21000, 'selling_price' => 27000, 'current_stock' => 25, 'min_stock' => 6],
            ['name' => 'Kapas Kecantikan 50g', 'category' => 'Perawatan Pribadi', 'satuan' => 'Pouch', 'barcode' => '8991004875', 'purchase_price' => 9000, 'selling_price' => 12500, 'current_stock' => 40, 'min_stock' => 10],
            ['name' => 'Pembaur Rambut Hair Tonic 100ml', 'category' => 'Perawatan Pribadi', 'satuan' => 'Botol', 'barcode' => '8991005189', 'purchase_price' => 32000, 'selling_price' => 41000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Sunscreen Wajah SPF 30', 'category' => 'Perawatan Pribadi', 'satuan' => 'Tube', 'barcode' => '8991006596', 'purchase_price' => 48000, 'selling_price' => 60000, 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Pelembab Wajah 50ml', 'category' => 'Perawatan Pribadi', 'satuan' => 'Jar', 'barcode' => '8991007386', 'purchase_price' => 35000, 'selling_price' => 45000, 'current_stock' => 22, 'min_stock' => 6],
            ['name' => 'Gunting Kuku Stainless', 'category' => 'Perawatan Pribadi', 'satuan' => 'Pcs', 'barcode' => '8991008754', 'purchase_price' => 8000, 'selling_price' => 12000, 'current_stock' => 35, 'min_stock' => 10],
            ['name' => 'Pembersih Kewanitaan 100ml', 'category' => 'Perawatan Pribadi', 'satuan' => 'Botol', 'barcode' => '8991009154', 'purchase_price' => 17000, 'selling_price' => 22000, 'current_stock' => 25, 'min_stock' => 6],
            ['name' => 'Pencukur Rambut Wajah (Razor)', 'category' => 'Perawatan Pribadi', 'satuan' => 'Pack', 'barcode' => '8991010300', 'purchase_price' => 15000, 'selling_price' => 20000, 'current_stock' => 30, 'min_stock' => 8],
        ];

        // Cache category instances
        $categories = [];
        foreach ($items as $item) {
            $catName = $item['category'];
            if (!isset($categories[$catName])) {
                $categories[$catName] = Category::firstOrCreate(
                    ['name' => $catName],
                    ['description' => 'Kategori produk ' . $catName]
                );
            }

            Product::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $categories[$catName]->id,
                    'satuan' => $item['satuan'],
                    'barcode' => $item['barcode'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price' => $item['selling_price'],
                    'current_stock' => $item['current_stock'],
                    'min_stock' => $item['min_stock'],
                ]
            );
        }
    }
}
