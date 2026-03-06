# HoyoDash 🌟

HoyoDash adalah sebuah dashboard interaktif berbasis web yang elegan dan modern untuk memantau statistik akun game HoYoverse Anda (Genshin Impact, Honkai: Star Rail, Honkai Impact 3rd, dan Zenless Zone Zero) dalam satu tempat.

Aplikasi ini menggunakan teknologi Laravel, Livewire, Alpine.js, dan didesain secara khusus menggunakan Tailwind CSS untuk memberikan antarmuka pengguna _(user interface)_ bergaya transparan _(glassmorphism)_ yang indah dan mulus.

---

## 🔥 Fitur Utama

- **Multi-Game Support**: Mendukung data dari berbagai game HoYoverse secara langsung menggunakan API internal HoYoLAB.
- **Login via Cookie**: Tidak perlu memasukkan _password_. Cukup berikan _cookie_ HoYoLAB browser Anda untuk mendapatkan akses yang aman secara _read-only_ karena seluruh data Anda hanya disimpan di _local storage_ Anda sendiri.
- **Daily Note (Real-Time)**: Pantau _Original Resin_, _Trailblaze Power_, _Expeditions_, _Realm Currency/Serenitea Pot_, serta _Parametric Transformer_ tanpa harus membuka game.
- **Game Stats Overview**: Lihat ringkasan akun Anda (Level, Spiral Abyss, jumlah karakter, peti yang diklaim, persentase eksplorasi map, hingga penyelesaian _quest_).
- **Koleksi Karakter**: Tinjau karakter-karakter yang Anda miliki beserta _Weapon_ dan Relik-nya.
- **Event Calendar**: Dapatkan pemberitahuan mengenai _Event Wishes_ terbaru dan _overview_ dari semua _event_ yang saat ini sedang berlangsung di tiap game.
- **Dark Mode Modern Aesthetic**: Desain warna bernuansa gradasi neon dengan fokus visual UX yang detail dan terkesan eksklusif.

---

## 🛠️ Tech Stack

- **[Laravel 12.x](https://laravel.com/)**: Framework mumpuni pada sisi server backend (_PHP_).
- **[Livewire 4](https://livewire.laravel.com/)**: Untuk membuat antarmuka web dinamis _(Single Page Application feel)_ tanpa harus menulis banyak _Vanilla JavaScript_ atau _Vue/React_.
- **[Alpine.js](https://alpinejs.dev/)**: Untuk interaksi UI minimalis yang efisien dan responsif (_dropdown_, akordion, tabulasi, dsb).
- **[Tailwind CSS](https://tailwindcss.com/)**: Sistem styling utilitas _Utility-First CSS_ super-cepat dengan kapabilitas desain kustom tanpa hambatan.
- **Guzzle / cURL**: _Library_ backend untuk menyambungkan permintaan _(request)_ ke Endpoint Rest API resmi dari HoYoLAB (via `api-os.hoyolab.com`).

---

## 🚀 Instalasi & Persiapan

1. **Clone repositori ini:**

    ```bash
    git clone https://github.com/akumano/hoyodash.git
    cd hoyodash
    ```

2. **Instal dependensi Composer dan NPM:**

    ```bash
    composer install
    npm install
    ```

3. **Salin _environment_ variable (Env) file:**

    ```bash
    cp .env.example .env
    ```

4. **Kompilasi _frontend assets_:**

    ```bash
    npm run build
    ```

    _(Atau jalankan `npm run dev` jika Anda ingin sambil memodifikasi tampilannya)._

5. **Generate Kunci Aplikasi _(App Key)_:**

    ```bash
    php artisan key:generate
    ```

6. **Jalankan Server Lokal:**
    ```bash
    php artisan serve
    ```
    Kini Anda dapat membuka: `http://localhost:8000` di peramban Anda.

---

## 🍪 Cara Login (Mendapatkan HoYoLAB Cookie)

Proyek ini tidak menyimpan data sensitif Anda ke _database_ server lokal. Aplikasi hanya berjalan sebagai saluran langsung antara Browser Anda dan API dari _HoYoverse_.

1. Buka situs [www.hoyolab.com](https://www.hoyolab.com) dan selesaikan **Log in**.
2. **Klik kanan** untuk membuka _Developer Tools_ (atau tekan `Ctrl`+`Shift`+`I` / `F12` lalu klik _Inspect_).
3. Buka tab berjudul **Application**.
4. Di panel sebelah kiri, temukan opsi **Storage > Cookies**, lalu pilih `https://www.hoyolab.com`.
5. Cari _key_ berikut dan salin isi tiap _valuenya_:
   `account_id_v2`, `account_mid_v2`, `cookie_token_v2`, `ltmid_v2`, `ltoken_v2`, `ltuid_v2`
6. Susun dan tempel di formulir _Login HoyoDash_ menggunakan format seperti ini:
   `account_id_v2=XXXX; account_mid_v2=XXXX; cookie_token_v2=XXXX; ltmid_v2=XXXX; ltoken_v2=XXXX; ltuid_v2=XXXX;`

---

## 👨‍💻 Kontribusi

Segala bentuk _Issues_ dan _Pull Requests_ sangat dipersilakan untuk memperluas kapabilitas dari HoYoDash. Semua kontribusi Anda akan saya jadikan evaluasi.

## 📝 Lisensi

Proyek **HoyoDash** adalah perangkat lunak sumber terbuka (Open Source) yang dilisensikan di bawah [Lisensi MIT](https://opensource.org/licenses/MIT). Data game Genshin Impact, Honkai: Star Rail, Zenless Zone Zero, serta maskot yang bersangkutan adalah properti terdaftar yang dimiliki oleh **COGNOSPHERE PTE. LTD. / HoYoverse**.
