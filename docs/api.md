# Kontrak API Modul Tabel Dinamis Indikator Bidang IPW

Dokumen ini mendefinisikan kontrak REST API untuk modul Tabel Dinamis BAPPEDA Provinsi Maluku Utara. Kontrak ini digunakan oleh antarmuka Web dan aplikasi Mobile (Flutter).

> **PENTING UNTUK PENGEMBANG MOBILE:**  
> Jangan mengubah struktur parameter maupun response JSON ini tanpa koordinasi, karena aplikasi mobile dan web bergantung penuh pada kontrak ini.

---

## 1. Konfigurasi Awalan Rute (Base URL)
- Default Awalan Rute: `/{prefix}/api` (secara default: `/tabel-dinamis/api`).
- Format Response: Seluruh endpoint mengembalikan JSON (header `Content-Type: application/json`), kecuali endpoint `/export` yang mengembalikan berkas stream biner.
- Rate Limiting: 60 permintaan per menit per pengguna / alamat IP (HTTP 429 jika melampaui batas).

---

## 2. Endpoint Referensi (Metadata Filter)

### 2.1 Ambil Daftar Subjek
Mengambil seluruh kategori/subjek bidang indikator IPW.

- **Metode**: `GET`
- **URL**: `/{prefix}/api/subjek`
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

### 2.2 Ambil Daftar Indikator per Subjek
Mengambil indikator berdasarkan `subjek_id`, dengan opsi pencarian kata kunci `q`.

- **Metode**: `GET`
- **URL**: `/{prefix}/api/indikator`
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
    "label": "Indeks SPBE — Indeks Kebijakan",
    "satuan": "Indeks",
    "opd": "Dinas Komunikasi dan Informatika (Diskominfo)"
  }
]
```
*Catatan: Atribut `label` otomatis menggabungkan `nama` dan `sub_indikator` jika sub-indikator tersedia.*

---

### 2.3 Ambil Ketersediaan Tahun per Indikator & Jenis
Mengambil daftar tahun (rentang 2021-2026) yang memiliki data untuk indikator dan jenis tertentu.

- **Metode**: `GET`
- **URL**: `/{prefix}/api/tahun`
- **Parameter Query**:
  - `indikator_id` *(wajib, integer)*: ID indikator.
  - `jenis` *(wajib, string)*: Nilai harus `"Realisasi"` atau `"Target"`.
- **Response 200 OK**:
```json
[2021, 2022, 2023]
```

---

### 2.4 Ambil Daftar Wilayah (Kabupaten/Kota)
Mengambil daftar wilayah yang menjadi judul baris tabel.

- **Metode**: `GET`
- **URL**: `/{prefix}/api/wilayah`
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

### 3.1 Dapatkan Data Tabel Dinamis
Membangun matriks tabel dinamis berdasarkan kombinasi indikator, tahun, jenis, dan pilihan wilayah.

- **Metode**: `POST`
- **URL**: `/{prefix}/api/tabel`
- **Header**:
  - `Content-Type: application/json`
  - `Accept: application/json`
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
*Catatan:*
- `wilayah_ids`: Berisi array ID wilayah. Jika bernilai `null` atau kosong, sistem otomatis memuat **seluruh** wilayah yang ada.
- `data`: Array berisi **1 sampai maksimal 2 item**. Lebih dari 2 item akan ditolak validasi.
- `tahun`: Hanya diperbolehkan integer dalam rentang **2021 sampai 2026**.

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
      "judul": "Indeks SPBE — Indeks Kebijakan",
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
        [85.2, null],
        [3.5, 3.8]
      ]
    }
  ]
}
```
*Matriks Nilai:*  
`baris[k].nilai[i][j]` = nilai wilayah ke-`k`, grup indikator ke-`i`, tahun ke-`j`.  
Jika data belum tersedia di database, nilai adalah `null`. Di tampilan UI, nilai `null` harus dirender sebagai tanda strip `'-'`.

---

### 3.2 Unduh Berkas Export Tabel (Excel / CSV)
Menghasilkan berkas biner `.xlsx` atau `.csv` dari konfigurasi tabel yang sama.

- **Metode**: `POST`
- **URL**: `/{prefix}/api/export?format=xlsx` (atau `format=csv`)
- **Body Request**: Sama persis dengan body `POST /{prefix}/api/tabel`.
- **Response**: File stream biner:
  - Header XLSX: `Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`
  - Header CSV: `Content-Type: text/csv; charset=UTF-8`
  - `Content-Disposition: attachment; filename="tabel-dinamis-YYYYMMDD-HHmmss.xlsx"`

---

## 4. Penanganan Kesalahan (Error Handling)

### 4.1 Validasi Gagal (HTTP 422 Unprocessable Content)
Terjadi jika aturan bisnis dilanggar (misal: lebih dari 2 data indikator, tahun di luar 2021-2026, atau jenis bukan 'Realisasi'/'Target').
```json
{
  "message": "Maksimal 2 data per permintaan tabel.",
  "errors": {
    "data": [
      "Maksimal 2 data per permintaan tabel."
    ]
  }
}
```

### 4.2 Terlalu Banyak Permintaan (HTTP 429 Too Many Requests)
```json
{
  "message": "Too Many Attempts."
}
```

