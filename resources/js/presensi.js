import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('qr-scan');
    if (!root || !root.dataset.scanUrl) return;

    const readerEl = document.getElementById('qr-reader');
    const idleEl = document.getElementById('qr-idle');
    const idleText = document.getElementById('qr-idle-text');
    const startBtn = document.getElementById('qr-start');
    const statusEl = document.getElementById('qr-status');
    const modeBtns = root.querySelectorAll('[data-mode]');
    const uploadPanel = document.getElementById('qr-upload-panel');
    const fileInput = document.getElementById('qr-file');
    const modal = document.getElementById('pretest-modal');
    const titleEl = document.getElementById('modal-session-title');
    const pretestBtn = document.getElementById('btn-go-pretest');

    const scanner = new Html5Qrcode('qr-reader', {
        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
        useBarCodeDetectorIfSupported: true,
    });

    let mode = 'camera';
    let running = false;
    let starting = false;
    let busy = false;
    let stopping = null;

    const setStatus = (text, tone = 'muted') => {
        const color = tone === 'error' ? 'text-rose-600' : tone === 'ok' ? 'text-emerald-600' : 'text-slate-500';
        statusEl.className = `mt-3 text-sm ${color}`;
        statusEl.textContent = text;
    };

    const tuneVideo = () => {
        const video = readerEl.querySelector('video');
        if (!video) return;
        video.setAttribute('playsinline', 'true');
        video.setAttribute('webkit-playsinline', 'true');
        video.setAttribute('muted', 'true');
        video.setAttribute('autoplay', 'true');
        video.muted = true;
        video.style.width = '100%';
        video.style.objectFit = 'cover';
        const played = video.play();
        if (played && played.catch) played.catch(() => {});
    };

    const stopCamera = () => {
        if (!running) return stopping || Promise.resolve();
        running = false;
        stopping = scanner
            .stop()
            .catch(() => {
                // Kamera sudah berhenti sendiri (misalnya tab dipindah); abaikan.
            })
            .finally(() => {
                stopping = null;
            });
        return stopping;
    };

    const showIdle = (text) => {
        idleText.textContent = text;
        idleEl.classList.remove('hidden');
    };

    const cameraErrorMessage = (error) => {
        const name = (error && error.name) || '';
        const raw = typeof error === 'string' ? error : (error && error.message) || '';

        if (name === 'NotAllowedError' || name === 'SecurityError' || /permission|denied/i.test(raw)) {
            return 'Izin kamera ditolak. Buka pengaturan peramban, izinkan kamera untuk situs ini, lalu coba lagi.';
        }
        if (name === 'NotFoundError' || name === 'DevicesNotFoundError' || name === 'OverconstrainedError') {
            return 'Kamera tidak ditemukan pada perangkat ini.';
        }
        if (name === 'NotReadableError' || name === 'TrackStartError') {
            return 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi tersebut lalu coba lagi.';
        }
        return `Kamera gagal dinyalakan${name ? ` (${name})` : ''}. Coba muat ulang halaman atau gunakan mode Upload Gambar.`;
    };

    const handleDecoded = async (decodedText) => {
        if (busy) return;
        busy = true;
        setStatus('Memproses QR Code...', 'muted');
        await stopCamera();

        try {
            const response = await fetch(root.dataset.scanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': root.dataset.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ qr_data: decodedText }),
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.success) {
                busy = false;
                setStatus(data.message || 'QR Code tidak dapat diproses. Coba lagi.', 'error');
                if (mode === 'camera') showIdle('QR belum berhasil diproses. Nyalakan kamera untuk mencoba lagi.');
                return;
            }

            setStatus('');
            titleEl.textContent = data.session_title || '';
            if (data.has_pretest && data.pretest_url) {
                pretestBtn.href = data.pretest_url;
                pretestBtn.classList.remove('hidden');
            } else {
                pretestBtn.classList.add('hidden');
            }
            modal.classList.remove('hidden');
            // Kamera sudah berhenti; lepas kunci agar scan berikutnya (atau
            // kembali lewat tombol Back) tidak macet.
            busy = false;
        } catch (error) {
            busy = false;
            setStatus('Gagal menghubungi server. Periksa koneksi lalu coba lagi.', 'error');
            if (mode === 'camera') showIdle('Koneksi bermasalah. Nyalakan kamera untuk mencoba lagi.');
        }
    };

    const startCamera = async () => {
        if (running || starting || busy) return;

        if (!window.isSecureContext) {
            setStatus('Kamera hanya bisa dipakai melalui HTTPS. Hubungi pengelola situs.', 'error');
            return;
        }

        starting = true;
        startBtn.disabled = true;
        showIdle('Meminta izin kamera...');
        setStatus('');

        try {
            await stopCamera();
            await scanner.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: (viewfinderWidth, viewfinderHeight) => {
                        const size = Math.floor(Math.min(viewfinderWidth, viewfinderHeight) * 0.7);
                        return { width: size, height: size };
                    },
                },
                (decodedText) => handleDecoded(decodedText),
                () => {},
            );
            running = true;
            idleEl.classList.add('hidden');
            tuneVideo();
            setStatus('Arahkan kamera ke QR Code yang ditampilkan asisten.', 'muted');
        } catch (error) {
            running = false;
            showIdle('Kamera belum aktif.');
            setStatus(cameraErrorMessage(error), 'error');
        } finally {
            starting = false;
            startBtn.disabled = false;
        }
    };

    const setMode = (nextMode) => {
        if (busy || nextMode === mode) return;
        mode = nextMode;

        modeBtns.forEach((button) => {
            const active = button.dataset.mode === nextMode;
            button.setAttribute('aria-pressed', String(active));
            button.classList.toggle('btn-primary', active);
            button.classList.toggle('btn-light', !active);
        });

        if (nextMode === 'upload') {
            stopCamera();
            idleEl.classList.add('hidden');
            uploadPanel.classList.remove('hidden');
            setStatus('');
        } else {
            uploadPanel.classList.add('hidden');
            showIdle('Kamera belum aktif.');
            setStatus('');
        }
    };

    startBtn.addEventListener('click', startCamera);
    modeBtns.forEach((button) => {
        button.addEventListener('click', () => setMode(button.dataset.mode));
    });

    fileInput.addEventListener('change', async () => {
        const file = fileInput.files && fileInput.files[0];
        fileInput.value = '';
        if (!file || busy) return;

        busy = true;
        setStatus('Membaca QR Code dari gambar...', 'muted');
        await stopCamera();
        try {
            const result = await scanner.scanFileV2(file, true);
            busy = false;
            await handleDecoded(result.decodedText);
        } catch (error) {
            busy = false;
            setStatus('QR Code tidak terbaca pada gambar. Pastikan kode tidak buram dan memenuhi gambar.', 'error');
        }
    });
});
