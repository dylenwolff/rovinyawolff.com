import { PageFlip } from 'page-flip';
import * as pdfjsLib from 'pdfjs-dist';

pdfjsLib.GlobalWorkerOptions.workerSrc = '/pdf.worker.min.js';
const root = document.querySelector('[data-flipbook]');
const stage = root.querySelector('[data-flipbook-stage]');
const status = root.querySelector('[data-flipbook-status]');
const pageLabel = root.querySelector('[data-flip-page]');
const setStatus = (message) => { if (status) status.textContent = message; };

(async () => {
    try {
        const pdf = await pdfjsLib.getDocument(root.dataset.pdfUrl).promise;
        const firstPage = await pdf.getPage(1);
        const base = firstPage.getViewport({ scale: 1 });
        const ratio = base.height / base.width;
        const width = Math.min(520, Math.max(280, Math.floor(stage.clientWidth / (stage.clientWidth > 800 ? 2.15 : 1.08))));
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
        const book = new PageFlip(stage, { width, height: Math.round(width * ratio), size: 'stretch', minWidth: 280, maxWidth: 620, minHeight: Math.round(280 * ratio), maxHeight: Math.round(620 * ratio), showCover: true, usePortrait: true, mobileScrollSupport: false, maxShadowOpacity: .35 });
        book.loadFromHTML(pages);
        const update = () => { if (pageLabel) pageLabel.textContent = `Page ${book.getCurrentPageIndex() + 1} of ${pdf.numPages}`; };
        book.on('flip', update);
        root.querySelector('[data-flip-prev]')?.addEventListener('click', () => book.flipPrev());
        root.querySelector('[data-flip-next]')?.addEventListener('click', () => book.flipNext());
        root.querySelector('[data-flip-fullscreen]')?.addEventListener('click', () => root.requestFullscreen?.());
        setStatus('Drag a page, swipe, or use the buttons to browse.'); update();
    } catch (error) {
        console.error(error); setStatus('The interactive preview could not be opened.');
        stage.innerHTML = `<a class="button-primary mt-5" target="_blank" rel="noreferrer" href="${root.dataset.pdfUrl}">Open PDF</a>`;
    }
})();
