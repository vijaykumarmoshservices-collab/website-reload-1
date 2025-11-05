<!DOCTYPE html>
<html lang="en">
<head>
    <title>🌐 Websites Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { margin: 0; height: 100%; background: #f5f6fa; overflow-x: hidden; }
        .header { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: #2563eb; color: white; }
        iframe { width: 95%; height: 80vh; border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .iframe-wrapper { padding-top: 130px; display: flex; justify-content: center; }
        button { transition: 0.2s; }
        button.active { background: #10b981 !important; }
    </style>
</head>

<body>
    <div class="header shadow-lg p-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">🌐 Websites Dashboard</h1>
            <div class="controls flex flex-wrap justify-end items-center gap-2">
                <span class="text-blue-100">Interval:</span>
                <button data-time="5" class="time-btn bg-blue-700 hover:bg-blue-800 text-white px-3 py-1 rounded-lg">5s</button>
                <button data-time="10" class="time-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg">10s</button>
                <button data-time="30" class="time-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg">30s</button>
                <button data-time="60" class="time-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg active">60s</button>
                <button id="next" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg">Next ➡️</button>
                <button id="pause" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg">⏸️ Pause</button>
            </div>
        </div>
    </div>

    <div class="iframe-wrapper">
        <iframe id="rotator" src=""></iframe>
    </div>

    <script>
        const sites = @json($websites);
        const frame = document.getElementById('rotator');
        const buttons = document.querySelectorAll('.time-btn');
        const nextBtn = document.getElementById('next');
        const pauseBtn = document.getElementById('pause');

        let index = 0;
        let delay = 60000;
        let interval = null;
        let paused = false;
        let autoPaused = false;

        // ✅ Load a site
        function loadSite(i) {
            index = i % sites.length;
            const current = sites[index];
            const viewerUrl = `/viewer?url=${encodeURIComponent(current)}`;
            frame.src = viewerUrl;
            document.title = "Viewing: " + current;
            localStorage.setItem("last_site_index", index);
            localStorage.setItem("last_iframe_url", viewerUrl);
        }

        // ✅ Load next site
        function nextSite() {
            if (paused || autoPaused) return;
            index = (index + 1) % sites.length;
            loadSite(index);
        }

        // ✅ Start auto rotation
        function startRotation(ms) {
            if (interval) clearInterval(interval);
            delay = ms;
            interval = setInterval(nextSite, delay);
        }

        // ✅ Time buttons
        buttons.forEach(btn => {
            btn.addEventListener("click", function() {
                buttons.forEach(b => b.classList.remove("active"));
                this.classList.add("active");
                startRotation(parseInt(this.dataset.time) * 1000);
            });
        });

        // ✅ Manual Next
        nextBtn.addEventListener("click", function() {
            loadSite(index + 1);
        });

        // ✅ Manual Pause / Resume
        pauseBtn.addEventListener("click", function() {
            paused = !paused;
            updatePauseButton();
            if (!paused && !autoPaused) startRotation(delay);
        });

        function updatePauseButton() {
            if (paused || autoPaused) {
                clearInterval(interval);
                pauseBtn.textContent = "▶️ Resume";
                pauseBtn.classList.remove("bg-yellow-500");
                pauseBtn.classList.add("bg-green-500");
            } else {
                pauseBtn.textContent = "⏸️ Pause";
                pauseBtn.classList.remove("bg-green-500");
                pauseBtn.classList.add("bg-yellow-500");
            }
        }

        // ✅ Auto-pause when clicking or focusing the iframe
        frame.addEventListener("focus", () => {
            if (!paused) {
                autoPaused = true;
                updatePauseButton();
            }
        });

        // ✅ Resume rotation when iframe loses focus
        window.addEventListener("focus", () => {
            if (autoPaused && !paused) {
                autoPaused = false;
                startRotation(delay);
                updatePauseButton();
            }
        });

        // ✅ Save last session
        window.addEventListener("beforeunload", () => {
            localStorage.setItem("last_site_index", index);
            localStorage.setItem("last_iframe_url", frame.src);
        });

        // ✅ Restore same page
        document.addEventListener("DOMContentLoaded", function() {
            const savedIndex = localStorage.getItem("last_site_index");
            const savedUrl = localStorage.getItem("last_iframe_url");
            if (savedUrl) {
                frame.src = savedUrl;
                index = savedIndex ? parseInt(savedIndex) : 0;
            } else {
                loadSite(index);
            }
            startRotation(delay);
        });
    </script>
</body>
</html>
