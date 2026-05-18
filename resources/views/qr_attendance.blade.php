<?php

/** @var string $csrfToken */
$csrfToken = csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?= e($csrfToken) ?>">
    @php
        $systemSettings = \App\Models\SystemSetting::query()->first();
        $primaryColor = optional($systemSettings)->primary_color ?? '#2E86C1';
        $secondaryColor = optional($systemSettings)->secondary_color ?? '#1B4F72';
        $buttonColor = optional($systemSettings)->button_color ?? '#3498DB';
        $headingTextColor = optional($systemSettings)->heading_text_color ?? '#333333';
        $bodyTextColor = optional($systemSettings)->body_text_color ?? '#333333';
    @endphp
    @if ($systemSettings && $systemSettings->system_logo)
        <link rel="icon" href="{{ asset($systemSettings->system_logo) }}">
    @endif
    <title>{{ $systemSettings->system_short_name ?? 'Internship System' }}</title>
<style>
:root {
    --bg: {{ $primaryColor }};
    --panel: {{ $secondaryColor }};
    --panel2: {{ $secondaryColor }};
    --text: {{ $bodyTextColor }};
    --muted: {{ $headingTextColor }};
    --ok: #2e7d32;
    --warn: #c77d00;
    --err: #c62828;
    --border: {{ $primaryColor }};
    --hover: {{ $secondaryColor }};
    --card-bg: {{ $secondaryColor }};
}

* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", sans-serif;
    background: var(--bg);
    color: var(--text);
    line-height: 1.5;
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    background: var(--panel);
    position: sticky;
    top: 0;
    z-index: 10;
}

.title {
    font-weight: 700;
    font-size: 18px;
    letter-spacing: .3px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.status {
    font-size: 13px;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.pill {
    display: inline-flex;
    gap: 6px;
    align-items: center;
    padding: 5px 12px;
    border-radius: 999px;
    background: var(--panel2);
    border: 1px solid var(--border);
    font-size: 13px;
}

.layout {
    display: flex;
    height: calc(100vh - 70px);
    gap: 16px;
    padding: 16px;
}

.left {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.right {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: var(--panel2);
    border-radius: 14px;
    padding: 14px 14px 0px 14px;
    border: 1px solid var(--border);
}

.card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.scannerBox {
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex: 1;
    min-height: 0;
}

.scannerHeader {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.scannerHeader .headerText {
    color: var(--muted);
    font-weight:800;
    margin-bottom:4px;
}

.scannerHeader .hint {
    font-size: 13px;
    color: var(--text);
    max-width: 400px;
}

.controls {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

button {
    appearance: none;
    border: 1px solid var(--border);
    background: var(--panel);
    color: var(--text);
    padding: 9px 14px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.2s ease;
}

button:hover { background: var(--hover); }

button:disabled {
    opacity: .55;
    cursor: not-allowed;
}

#qr-reader {
    width: 100%;
    flex: 1;
    height: 100%;
    max-height: 100%;
    min-height: 0;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--border);
    background: #000;
}

.msg {
    font-size: 14px;
    color: var(--text);
    min-height: 22px;
}

.msg.ok { color: var(--ok); }
.msg.warn { color: var(--warn); }
.msg.err { color: var(--err); }

.panelTitle {
    font-weight: 700;
    font-size: 16px;
    margin: 0;
    color: var(--muted);
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

th, td {
    padding: 7px 10px;
    border-bottom: 1px solid var(--border);
    vertical-align: top;
}

th {
    text-align: left;
    color: var(--body-text-color);
    font-weight: 700;
    position: sticky;
    top: 0;
    background: #faf7f2;
}

tbody tr:nth-child(even) {
    background: #faf7f2;
}

.tag {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-weight: 800;
    letter-spacing: .2px;
    border: 1px solid var(--border);
    background: var(--panel2);
    min-width: 52px;
    text-align: center;
}

.tag.in {
    color: #2e7d32;
    background: #e8f5e9;
}

.tag.out {
    color: #c77d00;
    background: #fff3e0;
}

.scroll {
    overflow-y: auto;
    flex: 1;
}

.small {
    font-size: 12px;
    color: var(--text);
}

@media (max-width: 1100px) {
    .layout { flex-direction: column; height: auto; }
    .left, .right { width: 100%; }
    .right { border-top: 1px solid var(--border); }
    .scroll { max-height: 320px; }
}

#qr-reader video, #qr-reader canvas {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
    transform: scaleX(-1);
}

#qr-reader.flipped video, #qr-reader.flipped canvas {
    transform: scaleX(1);
}

.pin-boxes {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin: 1rem 0;
}
.pin-box {
    width: 3.5rem;
    height: 3.5rem;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    border: 2px solid var(--border);
    border-radius: 10px;
    background: var(--panel2);
    color: var(--text);
}
.pin-box:focus {
    outline: none;
    border-color: var(--ok);
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.3);
}
</style>
</head>
<body>
@php
    $systemSettings = \App\Models\SystemSetting::query()->first();
@endphp
<div class="topbar">
    <div class="title">
        @if ($systemSettings && $systemSettings->system_logo)
            <img src="{{ asset($systemSettings->system_logo) }}" alt="Logo"  style="height: 40px; margin-right: 10px;">
        @else
            <i class="bi bi-graph-up" style="font-size: 24px;" style="height: 40px; margin-right: 10px;"></i>
        @endif
        {{ $systemSettings->system_long_name }}
    </div>
    <div class="status">
        <span class="pill">Server time: <span id="serverTime">—</span></span>
        <span class="pill">Camera: <span id="camState">idle</span></span>
    </div>
    <button id="flipBtn">Flip Camera</button>
</div>

<div class="layout">
    <div class="left">
        <div class="card scannerBox">
            <div class="scannerHeader">
                <div>
                    <div class="headerText">Scanner</div>
                    <div class="hint">Align the QR code within the frame.</div>
                </div>
            </div>
            <div id="qr-reader"></div>
            <div class="msg" id="message">Ready.</div>
        </div>
    </div>

    <div class="right">
        <h3 class="panelTitle">Latest Attendance Records</h3>
        <div class="card scroll">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th style="width:92px;">Time</th>
                        <th>Intern</th>
                        <th style="width:70px;">Type</th>
                    </tr>
                </thead>
                <tbody id="latestBody">
                    <tr><td colspan="4" class="small">Loading…</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<audio id="beepAudio" src="/audio/beep.mp3" preload="auto"></audio>

<script src="https://unpkg.com/html5-qrcode"></script>
<!-- sweetalert2 for modal notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const latestUrl = "<?= e(route('qr-attendance.latest')) ?>";
        const scanUrl = "<?= e(route('qr-attendance.scan')) ?>";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const elMsg = document.getElementById('message');
        const elBody = document.getElementById('latestBody');
        const elCamState = document.getElementById('camState');
        const elServerTime = document.getElementById('serverTime');

        // controls removed - camera auto-starts
        // const startBtn = document.getElementById('startBtn');
        // const stopBtn = document.getElementById('stopBtn');

        let scanner = null;
        let isPosting = false;
        let lastText = null;
        let lastTextAt = 0;
        let isFlipped = false;

        function setMsg(text, type) {
            elMsg.className = "msg" + (type ? (" " + type) : "");
            elMsg.textContent = text;
        }

        function rowHtml(item, index) {
            // format time as 12-hour (e.g. 8:00 AM or 5:23 PM)
            const t = formatTime(item.scan_time);
            const type = (item.scan_type || "").toUpperCase();
            const cls = type === 'IN' ? 'in' : 'out';
            return `
                <tr>
                    <td>${index + 1}.</td>
                    <td>${t}</td>
                    <td>${escapeHtml(item.name || '—')}</td>
                    <td><span class="tag ${cls}">${type || '—'}</span></td>
                </tr>
            `;
        }

        function escapeHtml(str) {
            return String(str)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        // convert ISO timestamp string to 12-hour time string
        function formatTime(ts) {
            if (!ts) return '—';
            const d = new Date(ts);
            if (isNaN(d.getTime())) return '—';
            return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        }

        async function refreshLatest() {
            try {
                const res = await fetch(latestUrl, { headers: { 'Accept': 'application/json' }});
                const json = await res.json();
                const items = Array.isArray(json.data) ? json.data : [];
                if (items.length === 0) {
                    elBody.innerHTML = `<tr><td colspan="3" class="small">No scans yet.</td></tr>`;
                    return;
                }
                elBody.innerHTML = items.map(rowHtml).join('');
            } catch (e) {
                elBody.innerHTML = `<tr><td colspan="3" class="small">Failed to load latest scans.</td></tr>`;
            }
        }

        async function postScan(qrText, pin = null) {
            if (isPosting) return;
            isPosting = true;
            try {
                const res = await fetch(scanUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        qr_code: qrText,
                        pin: pin,
                    }),
                });

                const json = await res.json().catch(() => null);
                // use SweetAlert2 for feedback
                if (json && json.requires_pin) {
                    isPosting = false;
                    await promptForPinAndResubmit(qrText, json.intern?.name ?? null);
                    return;
                }

                if (!res.ok) {
                        const msg = (json && json.message) ? json.message : "Invalid QR code.";
                    Swal.fire({
                        icon: 'error',
                        title: 'Scan failed',
                        text: msg,
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: true
                    });
                    if (json && json.data) {
                        renderLatestFromPayload(json.data);
                    }
                    return;
                }

                if (json && json.ok) {
                    const text = `${json.message} — ${json.intern?.name ?? ''}`.trim();
                    if (json.ignored) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text,
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: true
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text,
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: true
                        });
                    }

                    if (json.data) {
                        renderLatestFromPayload(json.data);
                    } else {
                        refreshLatest();
                    }
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unexpected response.',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: true
                });
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Network/server error while recording scan.',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: true
                });
            } finally {
                isPosting = false;
            }
        }

        async function promptForPinAndResubmit(qrText, internName) {
            const pin = await new Promise((resolve) => {
                let resolved = false;
                const completeWithPin = (val) => {
                    if (resolved) return;
                    resolved = true;
                    Swal.close();
                    resolve(val);
                };

                Swal.fire({
                    title: 'Enter Attendance PIN',
                    html: `
                        <p class="msg" style="margin-bottom:0.5rem;">${escapeHtml(internName ? `PIN for ${internName}` : 'Enter your 4‑digit PIN')}</p>
                        <div class="pin-boxes">
                            <input type="text" inputmode="numeric" maxlength="1" class="pin-box" data-idx="0" autocomplete="off" aria-label="Digit 1">
                            <input type="text" inputmode="numeric" maxlength="1" class="pin-box" data-idx="1" autocomplete="off" aria-label="Digit 2">
                            <input type="text" inputmode="numeric" maxlength="1" class="pin-box" data-idx="2" autocomplete="off" aria-label="Digit 3">
                            <input type="text" inputmode="numeric" maxlength="1" class="pin-box" data-idx="3" autocomplete="off" aria-label="Digit 4">
                        </div>
                    `,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    didOpen: () => {
                        const boxes = document.querySelectorAll('.pin-box');
                        if (boxes[0]) boxes[0].focus();

                        boxes.forEach((box, i) => {
                            box.addEventListener('input', (e) => {
                                const v = e.target.value.replace(/\D/g, '').slice(0, 1);
                                e.target.value = v;
                                if (v.length === 1) {
                                    if (i < 3) boxes[i + 1].focus();
                                    else {
                                        const pinVal = Array.from(boxes).map(b => b.value).join('');
                                        completeWithPin(pinVal);
                                    }
                                }
                            });
                            box.addEventListener('keydown', (e) => {
                                if (e.key === 'Backspace' && !e.target.value && i > 0) {
                                    boxes[i - 1].focus();
                                }
                            });
                            box.addEventListener('paste', (e) => {
                                e.preventDefault();
                                const pasted = (e.clipboardData?.getData('text') || '').replace(/\D/g, '').slice(0, 4);
                                pasted.split('').forEach((ch, j) => {
                                    if (boxes[j]) {
                                        boxes[j].value = ch;
                                    }
                                });
                                const idx = Math.min(pasted.length, 3);
                                boxes[idx].focus();
                                if (pasted.length === 4) {
                                    completeWithPin(pasted);
                                }
                            });
                        });
                    },
                }).then((result) => {
                    if (!resolved) {
                        resolved = true;
                        resolve(null);
                    }
                });
            });

            if (!pin) return;
            await postScan(qrText, pin);
        }

        function renderLatestFromPayload(items) {
            if (!Array.isArray(items) || items.length === 0) {
                elBody.innerHTML = `<tr><td colspan="3" class="small">No scans yet.</td></tr>`;
                return;
            }
            elBody.innerHTML = items.map(rowHtml).join('');
        }

        function onQrDetected(decodedText) {
            const now = Date.now();
            if (decodedText === lastText && (now - lastTextAt) < 1500) {
                return;
            }
            lastText = decodedText;
            lastTextAt = now;

            // Play beep sound
            const beepAudio = document.getElementById('beepAudio');
            beepAudio.currentTime = 0; // Reset to start
            beepAudio.play();

            setMsg("Scanning…", "");
            postScan(decodedText);
        }
        async function flipCamera() {
            isFlipped = !isFlipped;
            const qrReader = document.getElementById('qr-reader');
            if (isFlipped) {
                qrReader.classList.add('flipped');
            } else {
                qrReader.classList.remove('flipped');
            }
        }
        async function startCamera() {
            if (scanner) return;

            scanner = new Html5Qrcode("qr-reader");
            elCamState.textContent = "starting…";

            try {
                const cameras = await Html5Qrcode.getCameras();
                if (!cameras || cameras.length === 0) {
                    setMsg("No camera found on this device.", "err");
                    elCamState.textContent = "no camera";
                    scanner = null;
                    return;
                }

                const camId = cameras[cameras.length - 1].id;
                await scanner.start(
                    { deviceId: { exact: camId } },
                    {
                        fps: 10,
                        qrbox: { width: 320, height: 320 },
                        aspectRatio: 1.0,
                    },
                    onQrDetected,
                    () => {}
                );

                elCamState.textContent = "running";
                setMsg("Camera started. Ready to scan.", "ok");
            } catch (e) {
                setMsg("Camera permission denied or start failed.", "err");
                elCamState.textContent = "error";
                try { await scanner.stop(); } catch (_) {}
                scanner = null;
            }
        }

        // stopCamera is no longer used in auto‑start workflow, but kept for
        // possible future debugging. It won't touch removed button elements.
        async function stopCamera() {
            if (!scanner) return;
            try {
                await scanner.stop();
            } catch (_) {}
            scanner = null;
            elCamState.textContent = "stopped";
            setMsg("Camera stopped.", "warn");
        }

        // no buttons; start camera immediately

        refreshLatest();
        setInterval(refreshLatest, 5000);

        // camera should begin scanning as soon as scripts load
        startCamera();

        document.getElementById('flipBtn').addEventListener('click', flipCamera);

        // Lightweight server time display (derived from client clock + page load timestamp).
        const pageLoadedAt = Date.now();
        const pageLoadedIso = new Date().toISOString();
        setInterval(() => {
            const d = new Date(Date.parse(pageLoadedIso) + (Date.now() - pageLoadedAt));
            elServerTime.textContent = d.toLocaleString();
        }, 1000);
    </script>
</body>
</html>