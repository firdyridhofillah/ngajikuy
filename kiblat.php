<?php
session_start();
$title = "Arah Kiblat - NgajiKuy";
include 'includes/header.php';
?>

<style>
    .kiblat-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 70vh;
        text-align: center;
    }

    .compass {
        position: relative;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: #f8fafc;
        border: 8px solid #00a86b;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        margin: 30px 0;
        transition: transform 0.1s ease-out;
    }

    .compass-arrow {
        position: absolute;
        top: 10%;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        z-index: 10;
    }

    .kaabah-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        opacity: 0.2;
    }

    .degree-text {
        font-size: 2rem;
        font-weight: 800;
        color: #00a86b;
    }

    .guide-text {
        background: #f0fdf4;
        padding: 15px;
        border-radius: 15px;
        border: 1px dashed #00a86b;
        font-size: 0.85rem;
    }
</style>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="index.php" class="text-dark me-3"><i class="bi bi-arrow-left fs-4"></i></a>
        <h4 class="fw-bold mb-0">Penunjuk Kiblat</h4>
    </div>

    <div class="kiblat-container">
        <div class="guide-text mb-2">
            <i class="bi bi-info-circle-fill me-1"></i> 
            Letakkan HP di permukaan datar dan putar badanmu hingga jarum mengarah ke atas.
        </div>

        <div class="degree-text"><span id="degree">0</span>°</div>
        
        <div class="compass" id="compassDisk">
            <img src="https://cdn-icons-png.flaticon.com/512/4462/4462310.png" class="kaabah-icon">
            <img src="https://cdn-icons-png.flaticon.com/512/10006/10006093.png" class="compass-arrow" id="arrow">
        </div>

        <button id="startBtn" class="btn btn-success rounded-pill px-5 py-3 fw-bold shadow">
            <i class="bi bi-compass me-2"></i> AKTIFKAN KOMPAS
        </button>
    </div>
</div>

<script>
    const compassDisk = document.getElementById('compassDisk');
    const degreeText = document.getElementById('degree');
    const startBtn = document.getElementById('startBtn');

    // Sudut kiblat dari Indonesia secara umum adalah sekitar 295 derajat
    const kiblatAngle = 295;

    function handleOrientation(event) {
        let alpha = event.alpha; // Rotasi HP (0-360)
        
        if (event.webkitCompassHeading) {
            alpha = event.webkitCompassHeading; // Untuk iPhone
        }

        if (alpha !== null) {
            // Kita putar piringan kompasnya sesuai arah HP
            const rotation = 360 - alpha;
            compassDisk.style.transform = `rotate(${rotation}deg)`;
            
            // Tampilkan derajat arah
            degreeText.innerText = Math.round(alpha);
            
            // Efek getar atau warna jika sudah mendekati arah kiblat (295 derajat)
            if (Math.abs(alpha - kiblatAngle) < 5) {
                compassDisk.style.borderColor = "#fbbf24"; // Warna emas jika tepat
            } else {
                compassDisk.style.borderColor = "#00a86b";
            }
        }
    }

    startBtn.addEventListener('click', () => {
        // Minta izin sensor untuk iPhone (iOS 13+)
        if (typeof DeviceOrientationEvent.requestPermission === 'function') {
            DeviceOrientationEvent.requestPermission()
                .then(permissionState => {
                    if (permissionState === 'granted') {
                        window.addEventListener('deviceorientation', handleOrientation, true);
                        startBtn.style.display = 'none';
                    }
                })
                .catch(console.error);
        } else {
            // Untuk Android langsung jalan
            window.addEventListener('deviceorientation', handleOrientation, true);
            startBtn.style.display = 'none';
        }
    });
</script>

<?php include 'includes/footer.php'; ?>