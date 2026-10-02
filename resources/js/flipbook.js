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
        const isSpread = stage.clientWidth > 800;
        const widthFromStage = stage.clientWidth / (isSpread ? 2.15 : 1.08);
        const availableHeight = Math.max(420, window.innerHeight - 190);
        const widthFromHeight = availableHeight / ratio;
        const width = Math.floor(Math.min(520, widthFromStage, widthFromHeight));
        const height = Math.round(width * ratio);
        const pages = [];
        for (let number = 1; number <= pdf.numPages; number += 1) {
            setStatus(`Preparing page ${number} of ${pdf.numPages}…`);
            const page = number === 1 ? firstPage : await pdf.getPage(number);
            const viewport = page.getViewport({ scale: Math.min(2, (width * devicePixelRatio) / base.width) });
            const element = document.createElement('div');
            element.className = 'flipbook-page';
            const canvas = document.createElement('canvas');
            canvas.width = viewport.width; canvas.height = viewport.height;
            element.appendChild(canvas);
            await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
            pages.push(element);
        }
        const book = new PageFlip(stage, { width, height, size: 'fixed', showCover: true, usePortrait: true, mobileScrollSupport: false, maxShadowOpacity: .35, autoSize: true });
        book.loadFromHTML(pages);
        const update = () => { if (pageLabel) pageLabel.textContent = `Page ${book.getCurrentPageIndex() + 1} of ${pdf.numPages}`; };
        book.on('flip', update);
        root.querySelector('[data-flip-prev]')?.addEventListener('click', () => book.flipPrev());
        root.querySelector('[data-flip-next]')?.addEventListener('click', () => book.flipNext());
        root.querySelector('[data-flip-fullscreen]')?.addEventListener('click', () => root.requestFullscreen?.());
        setStatus('Drag a page, swipe, or use the buttons to browse.'); update();
    } catch (error) {
        console.error(error); setStatus('The interactive preview could not be opened.');
        const fallbackUrl = root?.getAttribute('data-pdf-url') || '#';
        stage.innerHTML = `<a class="button-primary mt-5" target="_blank" rel="noreferrer" href="${fallbackUrl}">Open PDF</a>`;
    }
})();
