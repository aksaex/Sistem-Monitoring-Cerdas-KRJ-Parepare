# Nama file: prediksi.py
import sys
import json
import numpy as np

try:
    import os
    os.environ['TF_CPP_MIN_LOG_LEVEL'] = '2' 
    import tensorflow as tf
    from PIL import Image
except Exception as e:
    error_result = {'error': f"Gagal mengimpor library: {str(e)}"}
    print(json.dumps(error_result))
    sys.exit()

def run_prediction():
    try:
        # === PERUBAHAN UTAMA: Memuat model dengan 'compile=False' ===
        # Ini memberitahu TensorFlow untuk hanya memuat arsitektur dan bobot,
        # dan mengabaikan informasi lain yang mungkin tidak cocok antar versi.
        model_path = 'model_kesehatan_pohon.h5'
        if not os.path.exists(model_path):
            return {'error': f"File model '{model_path}' tidak ditemukan."}
        
        # Ini adalah perbaikan kuncinya
        model = tf.keras.models.load_model(model_path, compile=False)
        # === AKHIR PERUBAHAN ===

        # Muat label kelas dari file .npy
        classes_path = 'model_classes.npy'
        if not os.path.exists(classes_path):
            return {'error': f"File kelas '{classes_path}' tidak ditemukan."}
        class_labels = np.load(classes_path, allow_pickle=True)

    except Exception as e:
        # Menangkap error spesifik yang Anda dapatkan sebelumnya
        if "Layer \\\"dense\\\" expects 1 input(s)" in str(e):
             return {'error': f"Versi TensorFlow tidak cocok. Error: {str(e)}"}
        return {'error': f"Gagal memuat model: {str(e)}"}

    if len(sys.argv) < 2:
        return {'error': "Tidak ada path gambar yang diberikan."}
    
    image_path_from_php = sys.argv[1]
    if not os.path.exists(image_path_from_php):
        return {'error': f"File gambar tidak ditemukan di path: {image_path_from_php}"}

    try:
        # Siapkan gambar
        img = Image.open(image_path_from_php).resize((224, 224))
        img_array = np.array(img)
        
        if img_array.shape[2] == 4:
            img_array = img_array[:, :, :3]
            
        img_array = np.expand_dims(img_array, axis=0) / 255.0

        # Lakukan prediksi
        predictions = model.predict(img_array)

        # Dapatkan hasil
        predicted_class_index = np.argmax(predictions[0])
        predicted_class_label = class_labels[predicted_class_index]
        confidence_score = float(predictions[0][predicted_class_index])

        if predicted_class_label == 'butuh_perhatian':
             predicted_class_label = 'perlu_tindakan'

        return {
            'prediksi': predicted_class_label,
            'skor_kepercayaan': round(confidence_score * 100, 2)
        }
    except Exception as e:
        return {'error': f"Gagal saat prediksi: {str(e)}"}

if __name__ == "__main__":
    result = run_prediction()
    print(json.dumps(result))