# Kontrak REST API Modul Tabel Dinamis Indikator Bidang IPW

Dokumen ini mendefinisikan spesifikasi REST API untuk Modul Tabel Dinamis BAPPEDA Provinsi Maluku Utara. Digunakan oleh antarmuka Web dan aplikasi Mobile (Flutter).

> **PENTING UNTUK PENGEMBANG MOBILE:**  
> Jangan mengubah struktur parameter maupun response JSON ini tanpa koordinasi, karena aplikasi mobile dan web bergantung penuh pada kontrak ini.

---

## 1. Konfigurasi Awalan & Protokol (Base URL)
- **Base Endpoint Versi Web**: `/{prefix}/api` (Default: `/tabel-dinamis/api`). Menggunakan middleware `web` dan token CSRF.
- **Base Endpoint Stateless Mobile (Flutter)**: `/api` (atau `/api/{prefix}`). Terdaftar di `routes/api.php`, **stateless** (tanpa session cookies dan tanpa token CSRF).
- **Format Pertukaran Data**: JSON (`Content-Type: application/json` & `Accept: application/json`), kecuali `/export` yang mengembalikan berkas stream biner.
- **Kebijakan Pembatasan Laju (Rate Limiting)**:
  - Endpoint Query Data & Referensi: **60 permintaan per menit** per IP/pengguna.
  - Endpoint Unduh Berkas Export: **10 permintaan per menit** per IP/pengguna.
  - Status `HTTP 429 Too Many Attempts` akan dikembalikan jika kuota terlampaui.

---

## 2. Endpoint Referensi Filter

### 2.1 GET `/api/subjek`
Mengambil seluruh kategori/subjek indikator bidang IPW.
- **Response 200 OK**:
```json
[
  {
    "id": 1,
    "nama": "Komunikasi dan Informatika"
  },
  {
    "id": 2,
    "nama": "Transportasi"
  }
]
```

---

### 2.2 GET `/api/indikator`
Mengambil indikator berdasarkan `subjek_id`, dengan fitur pencarian kata kunci `q` (case-insensitive `LOWER + LIKE`).
- **Parameter Query**:
  - `subjek_id` *(wajib, integer)*: ID subjek referensi.
  - `q` *(opsional, string)*: Kata kunci pencarian (mencocokkan nama, sub-indikator, atau kode).
- **Response 200 OK**:
```json
[
  {
    "id": 10,
    "kode": "KOMINFO-01",
    "label": "Persentase Desa Terjangkau Internet",
    "satuan": "Persen",
    "opd": "Dinas Komunikasi dan Informatika (Diskominfo)"
  },
  {
    "id": 11,
    "kode": "KOMINFO-02",
    "label": "Indeks SPBE — Domain Layanan",
    "satuan": "Indeks",
    "opd": "Dinas Komunikasi dan Informatika (Diskominfo)"
  }
]
```

---

### 2.3 GET `/api/tahun`
Mengambil ketersediaan tahun data untuk indikator dan jenis tertentu.
- **Parameter Query**:
  - `indikator_id` *(wajib, integer)*: ID indikator.
  - `jenis` *(wajib, string)*: Nilai harus `"Realisasi"` atau `"Target"`.
- **Response 200 OK**:
```json
[2021, 2022, 2023]
```

---

### 2.4 GET `/api/wilayah`
Mengambil daftar seluruh wilayah kabupaten/kota.
- **Response 200 OK**:
```json
[
  {
    "id": 1,
    "nama": "Kab. Halmahera Barat"
  },
  {
    "id": 2,
    "nama": "Kota Ternate"
  }
]
```

---

## 3. Endpoint Query & Export Tabel Dinamis

### 3.1 POST `/api/tabel`
Membangun matriks tabel dinamis.
- **Header**:
  - `Content-Type: application/json`
  - `Accept: application/json`
  - `X-CSRF-TOKEN: <token>` *(untuk web)*
- **Body Request**:
```json
{
  "wilayah_ids": [1, 2],
  "data": [
    {
      "indikator_id": 10,
      "jenis": "Realisasi",
      "tahun": [2021, 2022]
    },
    {
      "indikator_id": 11,
      "jenis": "Target",
      "tahun": [2022, 2023]
    }
  ]
}
```
*Aturan Validasi:*
- `wilayah_ids`: Array integer ID wilayah. Kirim `null` atau kosong untuk memuat seluruh wilayah.
- `data`: Array berisi **minimal 1 item** (tanpa batasan maksimal, atau dapat dibatasi via konfigurasi `maks_data_per_tabel`).
- `data.*.jenis`: Harus `"Realisasi"` atau `"Target"` (Aturan 3).
- `data.*.tahun`: Rentang integer **2021 s.d. 2026** (Aturan 1).

- **Response 200 OK**:
```json
{
  "grup": [
    {
      "judul": "Persentase Desa Terjangkau Internet",
      "satuan": "Persen",
      "jenis": "Realisasi",
      "tahun": [2021, 2022]
    },
    {
      "judul": "Indeks SPBE — Domain Layanan",
      "satuan": "Indeks",
      "jenis": "Target",
      "tahun": [2022, 2023]
    }
  ],
  "baris": [
    {
      "wilayah": "Kab. Halmahera Barat",
      "nilai": [
        [70.5, 75.0],
        [2.8, 3.1]
      ]
    },
    {
      "wilayah": "Kota Ternate",
      "nilai": [
        [82.5, null],
        [3.5, 3.8]
      ]
    }
  ]
}
```
*Keterangan Matriks Nilai:*  
`nilai[i][j]` = nilai pada grup indikator ke-`i`, tahun ke-`j`. Nilai `null` menandakan data belum ada atau belum diisi (di sisi UI ditampilkan tanda strip `'-'`).

---

### 3.2 POST `/api/export`
Mengunduh berkas biner matriks tabel hasil dalam format Excel (`.xlsx`) atau `.csv`.
- **Parameter URL / Body**: `format=xlsx` atau `format=csv` *(wajib)*.
- **Body Request**: Sama persis dengan body `POST /api/tabel`.
- **Rate Limit**: 10 permintaan per menit.
- **Karakteristik Berkas**:
  - `xlsx`: Header bergabung (merged cell), angka bertipe numerik murni, freeze pane pada sel `B3`, dan catatan keterangan di bawah tabel.
  - `csv`: Disertai UTF-8 BOM (`\xEF\xBB\xBF`) dan pemisah titik koma (`;`).
- **Response 200 OK**: Stream berkas biner (`Content-Disposition: attachment`).

---

## 4. Format Kesalahan (Error Responses)

### 4.1 Validasi Gagal (HTTP 422 Unprocessable Content)
Terjadi jika format ekspor tidak dikenal, jumlah data > 2, tahun di luar rentang, dsb.
```json
{
  "message": "Format ekspor tidak dikenal. Hanya format xlsx atau csv yang diizinkan.",
  "errors": {
    "format": [
      "Format ekspor tidak dikenal. Hanya format xlsx atau csv yang diizinkan."
    ]
  }
}
```

### 4.2 Laju Permintaan Terlampaui (HTTP 429 Too Many Requests)
```json
{
  "message": "Too Many Attempts."
}
```
