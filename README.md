# Game RPG Berbasis Teks

Aplikasi ini adalah game RPG (Role-Playing Game) berbasis teks yang dibangun menggunakan framework Laravel. Game ini berfokus pada pertarungan giliran (turn-based combat) antara pemain dan musuh, serta memanfaatkan integrasi API eksternal sebagai mekanik utama dalam permainan.

## Deskripsi Permainan

Di dalam game ini, pemain akan melawan musuh dengan sistem poin kesehatan (HP). Masing-masing pihak, baik pemain maupun musuh, memulai permainan dengan 100 HP. Permainan berakhir ketika HP salah satu pihak mencapai angka 0.

Terdapat dua aksi utama yang dapat dilakukan oleh pemain:
1. Serang (Attack): Mengurangi HP musuh secara acak sebesar 10-20 poin. Setiap kali pemain menyerang, musuh akan langsung membalas serangan (mengurangi HP pemain sebesar 5-15 poin).
2. Dapatkan Motivasi (Get Motivation): Mengambil kutipan motivasi dari API eksternal untuk memulihkan HP pemain sebanyak 20 poin (maksimal 100 HP). Musuh juga akan melakukan serangan balasan setelah aksi ini.

Segala riwayat pertarungan akan dicatat dan ditampilkan pada log pertarungan di antarmuka game. Selain itu, kutipan motivasi yang didapatkan akan dikumpulkan dan ditampilkan sebagai daftar riwayat di panel motivasi.

## Integrasi API (Quotes API)

Game ini bertindak sebagai konsumer dari Quotes API (yang berjalan secara lokal) untuk mekanik pemulihan HP. 

Aplikasi ini memanggil endpoint berikut:
- `GET http://127.0.0.1:8001/api/quotes`: Endpoint utama yang digunakan untuk mengambil seluruh daftar kutipan favorit yang tersimpan di dalam database API tersebut. Game akan memilih satu kutipan secara acak dari daftar ini.
- `GET http://127.0.0.1:8001/api/quote/random`: Endpoint cadangan yang akan diakses apabila daftar kutipan favorit kosong atau tidak merespon. Endpoint API ini mengambil kutipan acak dari ZenQuotes.

## Cara Menjalankan Aplikasi

Pastikan Quotes API sudah berjalan di port 8001 sebelum menggunakan fitur Dapatkan Motivasi di dalam game.

1. Buka terminal dan masuk ke direktori Quotes API, lalu jalankan:
   `php artisan serve --port=8001`

2. Buka terminal baru, masuk ke direktori game ini, lalu jalankan:
   `composer install`
   `php artisan key:generate`
   `php artisan serve --port=8002`

3. Akses game melalui peramban web di URL: `http://127.0.0.1:8002`
