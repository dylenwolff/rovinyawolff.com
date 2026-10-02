import { PageFlip } from 'page-flip';
import * as pdfjsLib from 'pdfjs-dist';

pdfjsLib.GlobalWorkerOptions.workerSrc = '/pdf.worker.min.js';
const root = document.querySelector('[data-flipbook]');
const stage = root.querySelector('[data-flipbook-stage]');
const status = root.querySelector('[data-flipbook-status]');
const pageLabel = root.querySelector('[data-flip-page]');
const setStatus = (message) => { if (status) status.textContent = message; };
stage.style.overflow = 'visible';

(async () => {
    try {
        const pdfUrl = root?.getAttribute('data-pdf-url');
        if (!pdfUrl) throw new Error('Missing PDF URL for the flipbook.');
        const pdf = await pdfjsLib.getDocument({ url: pdfUrl }).promise;
        const firstPage = await pdf.getPage(1);
        const base = firstPage.getViewport({ scale: 1 });
        const ratio = base.height / base.width;
        const isMobile = window.matchMedia('(max-width: 767px)').matches;
        const isSpread = !isMobile && stage.clientWidth > 800;
        const widthFromStage = stage.clientWidth / (isSpread ? 2.15 : 1.08);
        const stageTop = stage.getBoundingClientRect().top;
        const availableHeight = Math.max(320, window.innerHeight - stageTop - 72);
        const widthFromHeight = availableHeight / ratio;
        const width = Math.floor(Math.min(520, widthFromStage, widthFromHeight));
        const height = Math.round(width * ratio);
        if (isMobile) {
            stage.style.width = `${width}px`;
            stage.style.maxWidth = `${width}px`;
            stage.style.touchAction = 'pan-y';
        }
        const pages = [];
        const renderJobs = new Map();
        for (let number = 1; number <= pdf.numPages; number += 1) {
            const element = document.createElement('div');
            element.className = 'flipbook-page';
            const canvas = document.createElement('canvas');
            canvas.setAttribute('aria-label', `Publication page ${number}`);
            element.appendChild(canvas);
            pages.push(element);
        }

        const renderPage = (index) => {
            if (index < 0 || index >= pdf.numPages) return Promise.resolve();
            if (renderJobs.has(index)) return renderJobs.get(index);
            const job = (async () => {
                const page = index === 0 ? firstPage : await pdf.getPage(index + 1);
                const natural = page.getViewport({ scale: 1 });
                const scale = Math.min(2, (width * devicePixelRatio) / natural.width);
                const viewport = page.getViewport({ scale });
                const canvas = pages[index].querySelector('canvas');
                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);
                await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
            })();
            renderJobs.set(index, job);
            return job;
        };

        const renderAround = (index) => {
            const radius = isMobile ? 2 : 3;
            for (let offset = -radius; offset <= radius; offset += 1) renderPage(index + offset);
        };

        setStatus('Preparing the first page…');
        await Promise.all([renderPage(0), renderPage(1), renderPage(2)]);
        const book = new PageFlip(stage, { width, height, size: 'fixed', showCover: true, usePortrait: true, mobileScrollSupport: false, maxShadowOpacity: .35, autoSize: true });
        book.loadFromHTML(pages);
        const update = () => {
            if (!pageLabel) return;
            const page = book.getCurrentPageIndex() + 1;
            const side = page === 1 ? 'Cover' : (page % 2 === 0 ? 'Left page' : 'Right page');
            pageLabel.textContent = isMobile ? `${side} · ${page} of ${pdf.numPages}` : `Page ${page} of ${pdf.numPages}`;
        };
        book.on('flip', () => {
            const index = book.getCurrentPageIndex();
            renderAround(index);
            update();
        });
        root.querySelector('[data-flip-prev]')?.addEventListener('click', () => book.flipPrev());
        root.querySelector('[data-flip-next]')?.addEventListener('click', () => book.flipNext());
        root.querySelector('[data-flip-fullscreen]')?.addEventListener('click', () => root.requestFullscreen?.());
        renderAround(0);
        setStatus(isMobile ? 'Swipe to read each left and right page in order.' : 'Drag a page, swipe, or use the buttons to browse.'); update();
    } catch (error) {
        console.error(error); setStatus('The interactive preview could not be opened.');
        const fallbackUrl = root?.getAttribute('data-pdf-url') || '#';
        stage.innerHTML = `<a class="button-primary mt-5" target="_blank" rel="noreferrer" href="${fallbackUrl}">Open PDF</a>`;
    }
})();
