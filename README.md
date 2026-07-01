# 🌳 Sistem Teknologi Cerdas Monitoring Pohon KRJ Parepare

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

### 1. Dashboard Utama Admin & Ringkasan Analitik AI
Menampilkan data ringkasan kesehatan aset pohon terlindungi yang dikelompokkan secara langsung berdasarkan hasil klasifikasi prediktif kecerdasan buatan, serta metrik pemantauan data sensor lingkungan terakhir.
<p align="center">
  <img src="https://raw.githubusercontent.com/aksaex/Sistem-Monitoring-Cerdas-KRJ-Parepare/main/assets/images/screenshots/admin_dashboard.png" alt="Dashboard Utama Admin" width="90%">
</p>

### 2. Form Pelaporan Petugas Lapangan Terintegrasi AI
Antarmuka responsif yang digunakan petugas untuk melaporkan kondisi pohon terlindungi secara langsung. Saat berkas foto dipilih dan form dikirimkan, berkas akan diteruskan ke skrip mesin cerdas `prediksi.py`.
<p align="center">
  <img src="https://raw.githubusercontent.com/aksaex/Sistem-Monitoring-Cerdas-KRJ-Parepare/main/assets/images/screenshots/report_form.png" alt="Form Pelaporan Petugas" width="85%">
</p>

### 3. Log Riwayat Analisis Prediksi Kesehatan Pohon
Halaman yang menampilkan data historis laporan petugas lengkap dengan visualisasi berkas foto pohon, waktu pelaporan, detail status dari petugas, dan label diagnosis objektif dari sistem cerdas (`Sehat`, `Perlu Tindakan`, `Kritis`).
<p align="center">
  <img src="https://raw.githubusercontent.com/aksaex/Sistem-Monitoring-Cerdas-KRJ-Parepare/main/assets/images/screenshots/history_log.png" alt="Log Riwayat Analisis AI" width="90%">
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
