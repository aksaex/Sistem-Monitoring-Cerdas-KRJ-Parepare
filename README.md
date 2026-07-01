<img width="960" height="540" alt="daftar pohon" src="https://github.com/user-attachments/assets/6ea0dd82-bc7d-43fe-bccc-a7cab9d2d53d" /># Sistem Teknologi Cerdas Monitoring Pohon KRJ Parepare

![Status](https://img.shields.io/badge/Status-Prototyping-brightgreen)
![Version](https://img.shields.io/badge/Version-1.0-blue)
![Tech Stack](https://img.shields.io/badge/Tech_Stack-PHP%20|%20Python%20|%20IoT-orange)
![License](https://img.shields.io/badge/License-MIT-lightgrey)

Sistem Teknologi Cerdas untuk Monitoring dan Pelestarian Pohon Terlindungi di Kebun Raya Jompie (KRJ) Parepare. Proyek ini merupakan implementasi *Capstone Project* (Proyek Teknologi Cerdas) yang mengintegrasikan **Internet of Things (IoT)**, **Kecerdasan Buatan (AI)**, dan **Rekayasa Perangkat Lunak (Web Dashboard)** dalam menyajikan solusi pelestarian lingkungan yang selaras dengan pilar *Smart City*.

---

## ✨ Keunggulan Utama (Key Features)

Sistem ini bertindak sebagai asisten cerdas bagi petugas pelestari lingkungan dan pengelola Kebun Raya Jompie dengan keunggulan teknologi end-to-end:

1. **🤖 AI-Powered Tree Health Analysis (Sistem Cerdas)**
   - Menggunakan model *Deep Learning* berbasis arsitektur **MobileNetV2** (TensorFlow/Keras) yang dilatih menggunakan dataset gambar lokal untuk mengenali fitur visual kesehatan tanaman.
   - Mengklasifikasikan kondisi pohon menjadi 3 kategori secara otomatis: **Sehat**, **Butuh Perhatian** (atau *Perlu Tindakan*), dan **Kritis**.
   - Terintegrasi langsung dengan backend PHP melalui eksekusi sub-proses asinkronus (`exec()`), sehingga AI langsung berjalan otomatis saat petugas mengunggah foto laporan di lapangan.

2. **📡 Real-Time IoT Environment Monitoring**
   - Akuisisi data mikroklimat tanah dan udara secara *real-time* menggunakan unit komputer mikro **ESP32**.
   - Memonitor parameter kritis lingkungan pohon: **Suhu Udara & Kelembapan Udara** (Sensor DHT22), **Kelembapan Tanah** (Soil Moisture Sensor Kapasitif), dan **Kadar Gas/Asap** (Sensor MQ-2) untuk deteksi dini bahaya kebakaran hutan atau polusi zat berbahaya.
   - Dilengkapi logika penentu keputusan (*Rule-Based Alerting*) dinamis pada dashboard untuk memberikan status visual siaga bahaya apabila ambang batas parameter lingkungan terlampaui.

3. **📊 Multi-Role Smart Dashboard (Web Application)**
   - **Dashboard Admin:** Menyajikan komparasi analitik prediktif kesehatan seluruh pohon di kawasan konservasi, pengelolaan data pengguna (petugas), serta pelaporan komprehensif.
   - **Dashboard Petugas:** Mengakomodasi kebutuhan entri laporan lapangan secara cepat, log visual pemantauan sensor terbaru, penanganan tugas, dan pemindaian pohon terlindungi.

---

## 📸 Tampilan Antarmuka & Fitur Sistem (Screenshots)

Berikut adalah dokumentasi visual sistem monitoring cerdas yang telah diimplementasikan:

### 1. Form Pelaporan Petugas Lapangan Terintegrasi AI
Antarmuka responsif yang digunakan petugas untuk melaporkan kondisi pohon terlindungi secara langsung. Saat berkas foto dipilih dan form dikirimkan, berkas akan diteruskan ke skrip mesin cerdas `prediksi.py`.
<p align="center">
  <img width="950" height="540" alt="dashboar petugas" src="https://github.com/user-attachments/assets/6dd6aef9-1ee0-40ae-81ee-dde945d66556" />
</p>

### 2. Dashboard Utama Admin & Ringkasan Analitik AI
Menampilkan data ringkasan kesehatan aset pohon terlindungi yang dikelompokkan secara langsung berdasarkan hasil klasifikasi prediktif kecerdasan buatan, serta metrik pemantauan data sensor lingkungan terakhir.
<p align="center">
  <img width="960" height="540" alt="dashboard admin" src="https://github.com/user-attachments/assets/2c11096e-2662-48a2-8c1f-f8482cbd2c35" />

</p>

---

## 🛠️ Teknologi yang Digunakan (Tech Stack)

### Software & Backend Engineering (SE)
* **Web Server:** Apache (XAMPP Environment)
* **Bahasa Pemrograman:** PHP 8.2+ (Core Backend Architecture)
* **Database Management:** MySQL / MariaDB
* **Frontend Styles:** HTML5, Tailwind CSS framework, JavaScript (Vanilla ES6)
* **Reporting Engine:** FPDF Library (Ekspor dokumen PDF Laporan Konservasi)

### Artificial Intelligence & Computer Vision (SC)
* **Core Framework:** TensorFlow 2.x & Keras
* **Model Architecture:** MobileNetV2 (Transfer Learning & Custom Fine-Tuning Dense Layers)
* **Image Processing Libraries:** Pillow (PIL), NumPy
* **Deployment Script:** Python CLI Wrapper (`prediksi.py`) terintegrasi pipeline backend PHP (`save_report.php`)

### Internet of Things (IoT)
* **Microcontroller:** ESP32 Development Board (Wi-Fi Enabled Node)
* **Sensors Array:** - DHT22 (Akurasi tinggi untuk Suhu dan Kelembapan Udara)
  - Capacitive Soil Moisture Sensor v1.2 (Resisten terhadap korosi, membaca kelembapan tanah)
  - MQ-2 (Detektor elektrokimia untuk konsentrasi Gas Mudah Terbakar dan Asap)
* **Protocol & Data Injection:** HTTP POST REST API (`post_data.php`)

---

### 📸 Tampilan Antarmuka & Fitur Sistem

### PETUGAS
**1. Login**
<img width="960" height="540" alt="login" src="https://github.com/user-attachments/assets/ad203dbd-c8e0-4068-b6d8-5ba1a6ae1d26" />
**2. Daftar Pohon**
<img width="948" height="540" alt="daftar pohon" src="https://github.com/user-attachments/assets/2ca560d7-ce53-4506-803b-f21d6eb7877c" />
**3. Buat Laporan**
<img width="946" height="540" alt="buat laporan" src="https://github.com/user-attachments/assets/8136b191-564a-41d7-ac25-55cd24372da2" />
**4. Laporan Tersimpan**
<img width="947" height="540" alt="laporan tersimpan" src="https://github.com/user-attachments/assets/22b35f56-eb7f-4dcd-bf9e-19ba674352e3" />
**5. Edit Laporan**
<img width="947" height="540" alt="edit laporan" src="https://github.com/user-attachments/assets/d6f219e3-759e-4757-a3cf-1dc9d352a3ed" />
**6. Barcode Pohon**
<img width="953" height="540" alt="barcode pohon" src="https://github.com/user-attachments/assets/cd9e85b7-f077-4b7f-ade5-0604ba8efbb9" />

### ADMIN
**1. Daftar Pengguna**
<img width="960" height="540" alt="Daftar Pengguna" src="https://github.com/user-attachments/assets/6c670e8f-605b-468c-b36d-cce3b958677b" />
**2. Tambah Pengguna**
<img width="950" height="540" alt="Tambah Pengguna" src="https://github.com/user-attachments/assets/a15ea01d-ff6c-4ddf-9565-fe2ba709c96d" />
**3. Edit Pengguna**
<img width="948" height="540" alt="edit pengguna" src="https://github.com/user-attachments/assets/b5f68aec-04a6-4a4f-8368-42db9fca63de" />
**4. Daftar Pohon**
<img width="960" height="540" alt="daftar pohon" src="https://github.com/user-attachments/assets/442ba780-8758-4d7e-8542-b5c21a4ac9d9" />
**5. Edit Pohon**
<img width="950" height="540" alt="Edit Pohon" src="https://github.com/user-attachments/assets/ef552b6e-7480-46dc-a9fc-41020509bc37" />
**6. Lihat Laporan Petugas**
<img width="947" height="540" alt="Laporan Petugas" src="https://github.com/user-attachments/assets/eb769458-17dc-4527-9458-a2727d171ee3" />
**7. Hasil Unduh File csv Laporan Aktivitas Petugas**
<img width="757" height="448" alt="hasil unduh file csv Laporan Aktivitas Petugas" src="https://github.com/user-attachments/assets/84ba1756-a667-42a5-be0b-caf40c2bbe16" />
**8. Hasil Unduh File pdf Laporan Aktivitas Petugas**
<img width="906" height="527" alt="hasil unduh file pdf Laporan Aktivitas Petugas" src="https://github.com/user-attachments/assets/464f6eca-1bd9-44bb-bf37-ef7085f2271c" />

## 🚀 Panduan Instalasi & Konfigurasi (Local Development)

### Prasyarat Sistem
* XAMPP (dengan PHP versi 8.0 ke atas dan ekstensi MySQLi aktif)
* Python 3.9 s.d 3.11 (Disarankan untuk kompatibilitas TensorFlow 2.x native lokal)
* Git CLI

### Langkah Demi Langkah Instalasi

1. **Clone Repositori ke Direktori Web Server**
   Buka terminal/PowerShell Anda, masuk ke direktori `htdocs` milik XAMPP, jalankan perintah berikut:
   ```bash
   cd C:\xampp\htdocs
   git clone [https://github.com/aksaex/Sistem-Monitoring-Cerdas-KRJ-Parepare.git](https://github.com/aksaex/Sistem-Monitoring-Cerdas-KRJ-Parepare.git) monitoring-krj
   cd monitoring-krj

