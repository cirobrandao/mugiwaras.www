<?php
use App\Core\View;
ob_start();
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-warning mb-3"><?= View::e((string)$error) ?></div>
<?php else: ?>
    <div class="epub-reader">
        <header class="epub-topbar">
            <button id="prev" class="btn btn-sm btn-outline-secondary" type="button">◀</button>
            <div id="bookTitle" class="epub-title">
                <?= View::e((string)($content['title'] ?? $content['original_name'] ?? 'Carregando…')) ?>
            </div>
            <button id="next" class="btn btn-sm btn-outline-secondary" type="button">▶</button>
        </header>

        <div id="epubStatus" class="epub-status text-muted">Carregando ePub...</div>

        <main class="epub-main">
            <div id="viewer" class="epub-viewer"></div>
        </main>

        <footer class="epub-bottombar">
            <input id="slider" type="range" min="0" max="100" value="0" />
            <span id="progress">0%</span>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/epubjs@0.3.93/dist/epub.min.js" integrity="sha384-JfQ3l9BFtV6E+uQEN8p2Tqv0T0qlz1Zk1h4SM6oKfFZHG4lSgnfyy9lRdwH2c0cZ" crossorigin="anonymous"></script>
    <script>
    (async () => {
        const fileUrl = "<?= View::e((string)($fileUrl ?? '')) ?>";
        const titleText = "<?= View::e((string)($content['title'] ?? $content['original_name'] ?? 'ePub')) ?>";

        const titleEl = document.getElementById("bookTitle");
        const statusEl = document.getElementById("epubStatus");
        const prevBtn = document.getElementById("prev");
        const nextBtn = document.getElementById("next");
        const slider = document.getElementById("slider");
        const progress = document.getElementById("progress");

        const setStatus = (text, isError = false) => {
            if (!statusEl) return;
            statusEl.textContent = text;
            statusEl.classList.toggle("text-danger", isError);
            statusEl.classList.toggle("text-muted", !isError);
        };

        if (!fileUrl) {
            if (titleEl) titleEl.textContent = "Erro ao carregar";
            setStatus("Arquivo não disponível.", true);
            return;
        }

        if (titleEl && titleText) titleEl.textContent = titleText;
        setStatus("Carregando ePub...");

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000);
            const response = await fetch(fileUrl, { credentials: "same-origin", signal: controller.signal });
            clearTimeout(timeoutId);
            if (!response.ok) {
                throw new Error("Falha ao carregar o ePub (" + response.status + ")");
            }
            setStatus("Processando arquivo...");
            const buffer = await response.arrayBuffer();
            setStatus("Inicializando ePub...");

            const book = new ePub(buffer);
            const rendition = book.renderTo("viewer", {
                width: "100%",
                height: "100%",
                flow: "paginated"
            });

            rendition.themes.register("app", {
                body: {
                    "font-family": "Georgia, serif",
                    "line-height": "1.6"
                }
            });
            rendition.themes.select("app");

            setStatus("Renderizando...");
            await rendition.display();

            if (prevBtn) prevBtn.addEventListener("click", () => rendition.prev());
            if (nextBtn) nextBtn.addEventListener("click", () => rendition.next());

            await book.ready;
            await book.locations.generate(1200);

            const updateProgress = () => {
                const cfi = rendition.currentLocation()?.start?.cfi;
                if (!cfi) return;
                const pct = book.locations.percentageFromCfi(cfi);
                const v = Math.max(0, Math.min(100, Math.round(pct * 100)));
                if (slider) slider.value = v;
                if (progress) progress.textContent = `${v}%`;
                localStorage.setItem(`epub:${fileUrl}:cfi`, cfi);
            };

            rendition.on("relocated", updateProgress);

            const saved = localStorage.getItem(`epub:${fileUrl}:cfi`);
            if (saved) {
                await rendition.display(saved);
            } else {
                updateProgress();
            }

            if (slider) {
                slider.addEventListener("input", () => {
                    const pct = Number(slider.value) / 100;
                    const cfi = book.locations.cfiFromPercentage(pct);
                    if (cfi) rendition.display(cfi);
                });
            }

            window.addEventListener("keydown", (e) => {
                if (e.key === "ArrowLeft") rendition.prev();
                if (e.key === "ArrowRight") rendition.next();
            });

            setStatus("");
        } catch (err) {
            if (titleEl) titleEl.textContent = "Erro ao carregar";
            if (err?.name === "AbortError") {
                setStatus("Timeout ao carregar ePub.", true);
            } else {
                setStatus(err?.message || "Erro ao carregar ePub.", true);
            }
        }
    })();
    </script>
<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layout.php'; ?>
