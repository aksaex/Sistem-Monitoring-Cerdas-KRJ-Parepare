<?php 
require_once('../includes/auth_petugas.php'); 
$page_title = 'Scan QR Code';
require_once('../includes/header.php');
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Memindai QR Code</h2>
        <p class="text-gray-600 mb-6">Arahkan kamera ke QR code ke barcode pohon untuk melihat detail pohon</p>
        
        <div id="qr-reader" class="w-full max-w-sm mx-auto border-4 border-dashed border-gray-300 rounded-lg overflow-hidden"></div>
        
        <div id="scan-result" class="mt-4 text-center font-medium"></div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    // Fungsi yang akan dijalankan jika pemindaian berhasil
    function onScanSuccess(decodedText, decodedResult) {
        let resultContainer = document.getElementById('scan-result');
        
        // Hentikan pemindai setelah berhasil agar tidak terus berjalan
        html5QrcodeScanner.clear();

        // Tampilkan pesan sukses
        resultContainer.innerHTML = `<p class="text-green-600">Kode berhasil dipindai!</p>`;

        // Cek apakah hasil pindaian adalah sebuah URL yang valid
        try {
            // Coba buat objek URL. Jika gagal, akan error dan masuk ke blok catch.
            let url = new URL(decodedText);
            
            // Jika berhasil, berarti ini adalah URL. Tampilkan pesan dan arahkan.
            resultContainer.innerHTML += `<p class="mt-1 text-blue-600">Mengarahkan ke: ${decodedText}</p>`;
            window.location.href = decodedText;

        } catch (error) {
            // Jika bukan URL, tampilkan saja teksnya.
            resultContainer.innerHTML += `<p class="mt-1 text-gray-800">Data terdeteksi (bukan link): <span class="font-bold">${decodedText}</span></p>`;
        }
    }

    // Fungsi yang dijalankan jika ada error (bisa diabaikan)
    function onScanFailure(error) {
        // console.warn(`QR error = ${error}`);
    }

    // 2. Membuat instance pemindai baru
    let html5QrcodeScanner = new Html5QrcodeScanner(
      "qr-reader", // ID dari elemen viewfinder di atas
      { 
          fps: 10, // Frame per second
          qrbox: { width: 250, height: 250 } // Ukuran kotak pemindaian
      },
      /* verbose= */ false);

    // 3. Menjalankan pemindai
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>

<?php
require_once('../includes/footer.php');
?>