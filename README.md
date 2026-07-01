# 🌳 Sistem Teknologi Cerdas Monitoring Pohon KRJ Parepare

![Status](https://img.shields.io/badge/Status-Prototyping-brightgreen)
![Version](https://img.shields.io/badge/Version-1.0-blue)
![Tech Stack](https://img.shields.io/badge/Tech_Stack-PHP%20|%20Python%20|%20IoT-orange)

Sistem Teknologi Cerdas untuk Monitoring dan Pelestarian Pohon Terlindungi di Kebun Raya Jompie (KRJ) Parepare. Proyek ini merupakan implementasi *Capstone Project* (Proyek Teknologi Cerdas) yang mengintegrasikan **Internet of Things (IoT)**, **Kecerdasan Buatan (AI)**, dan **Rekayasa Perangkat Lunak (Web Dashboard)** untuk mendukung konsep *Smart City*.

---

## ✨ Keunggulan Utama (Key Features)

Sistem ini tidak hanya sekadar mencatat data, melainkan bertindak sebagai asisten cerdas bagi petugas pelestari lingkungan dengan keunggulan berikut:

1. **🤖 AI-Powered Tree Health Analysis (Sistem Cerdas)**
   - Menggunakan model *Deep Learning* (MobileNetV2/CNN) berbasis Python dan TensorFlow untuk mendeteksi status kesehatan pohon dari foto laporan.
   - Mengklasifikasikan kondisi pohon menjadi 3 kategori secara otomatis: **Sehat**, **Butuh Perhatian**, dan **Kritis**.
   - Terintegrasi langsung dengan backend PHP; AI langsung berjalan di latar belakang saat petugas mengunggah foto.

2. **📡 Real-Time IoT Environment Monitoring**
   - Mendapatkan data lingkungan secara *real-time* dari perangkat sensor hardware (ESP32).
   - Memonitor parameter kritis: **Suhu Udara**, **Kelembapan Tanah**, dan **Kadar Gas/Asap (Sensor MQ-2)** untuk deteksi dini kebakaran.
   - Sistem *Alerting* dinamis pada dashboard jika sensor mendeteksi kondisi bahaya (misal: "Bahaya Asap" atau "Kering Kritis").

3. **📊 Smart Dashboard terintegrasi (Web App)**
   - **Dashboard Admin:** Menampilkan statistik prediktif kesehatan seluruh pohon di kawasan konservasi.
   - **Dashboard Petugas:** Mempermudah pelaporan di lapangan, pemindaian data pohon, dan riwayat penanganan (*tindakan preventif*).

---

## 🛠️ Teknologi yang Digunakan (Tech Stack)

### Software & Backend
* **Web Server:** Apache (XAMPP)
* **Bahasa Pemrograman:** PHP 8.x, Python 3.x
* **Database:** MySQL / MariaDB
* **Frontend:** HTML5, Tailwind CSS, JavaScript

### Artificial Intelligence (AI)
* **Framework:** TensorFlow & Keras
* **Model Architecture:** Custom CNN / Transfer Learning
* **Library Tambahan:** NumPy, Pillow (PIL)

### Internet of Things (IoT)
* **Microcontroller:** ESP32
* **Sensors:** DHT22 (Suhu/Kelembapan), Soil Moisture (Kapasitif), MQ-2 (Gas/Asap)

---

## 🚀 Panduan Instalasi (Local Development)

### Prasyarat
* XAMPP / Laragon (Web Server & Database)
* Python 3.x terinstal di sistem
* Composer & Git

### Langkah Instalasi
1. **Clone Repositori**
   ```bash
   git clone [https://github.com/aksaex/Sistem-Monitoring-Cerdas-KRJ-Parepare.git](https://github.com/aksaex/Sistem-Monitoring-Cerdas-KRJ-Parepare.git)
   cd Sistem-Monitoring-Cerdas-KRJ-Parepare
