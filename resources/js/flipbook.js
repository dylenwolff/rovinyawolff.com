import { PageFlip } from 'page-flip';
import * as pdfjsLib from 'pdfjs-dist';

pdfjsLib.GlobalWorkerOptions.workerSrc = '/pdf.worker.min.js';

const root = document.querySelector('[data-flipbook]');

if (root) {
    const viewport = root.querySelector('[data-flipbook-viewport]');
    const zoomSurface = root.querySelector('[data-flipbook-zoom]');
    const stage = root.querySelector('[data-flipbook-stage]');
    const status = root.querySelector('[data-flipbook-status]');
    const pageLabel = root.querySelector('[data-flip-page]');
    const thumbnailPanel = root.querySelector('[data-flip-thumbnails]');
    const thumbnailGrid = root.querySelector('[data-flip-thumbnail-grid]');
    const setStatus = (message) => { if (status) status.textContent = message; };

    (async () => {
        try {
            const pdfUrl = root.getAttribute('data-pdf-url');
            if (!pdfUrl) throw new Error('Missing PDF URL for the flipbook.');
            const pdf = await pdfjsLib.getDocument({ url: pdfUrl }).promise;
            const firstPage = await pdf.getPage(1);
            const base = firstPage.getViewport({ scale: 1 });
            const ratio = base.height / base.width;
            const isMobile = window.matchMedia('(max-width: 767px)').matches;
            const isSpread = !isMobile && viewport.clientWidth > 800;
            const viewportTop = viewport.getBoundingClientRect().top;
            const availableHeight = Math.max(340, window.innerHeight - viewportTop - 84);
            const mobilePeek = Math.max(34, viewport.clientWidth * 0.13);
            const widthFromStage = isMobile ? viewport.clientWidth * 0.86 : viewport.clientWidth / (isSpread ? 2.18 : 1.08);
            const widthFromHeight = availableHeight / ratio;
            const width = Math.max(230, Math.floor(isMobile
                ? Math.min(520, widthFromStage)
                : Math.min(520, widthFromStage, widthFromHeight)));
            const height = Math.round(width * ratio);
            const pages = [];
            const renderJobs = new Map();
            let zoom = 1;
            let thumbnailsReady = false;
            let book = null;
            let mobileIndex = 0;
            let mobileBusy = false;

            stage.style.width = `${isSpread || isMobile ? width * 2 : width}px`;
            stage.style.height = `${height}px`;
            stage.style.touchAction = isMobile ? 'pan-y' : 'none';
            zoomSurface.style.width = isMobile ? `${viewport.clientWidth}px` : stage.style.width;
            zoomSurface.style.height = stage.style.height;

            for (let number = 1; number <= pdf.numPages; number += 1) {
                const element = document.createElement('div');
                element.className = 'flipbook-page';
                element.dataset.density = number === 1 ? 'hard' : 'soft';
                element.style.width = `${width}px`;
                element.style.height = `${height}px`;
                const loader = document.createElement('span');
                loader.className = 'flipbook-page-loader';
                loader.setAttribute('aria-hidden', 'true');
                const canvas = document.createElement('canvas');
                canvas.setAttribute('aria-label', `Publication page ${number}`);
                element.append(loader, canvas);
                pages.push(element);
            }

            const renderPage = (index) => {
                if (index < 0 || index >= pdf.numPages) return Promise.resolve();
                if (renderJobs.has(index)) return renderJobs.get(index);
                const job = (async () => {
                    const page = index === 0 ? firstPage : await pdf.getPage(index + 1);
                    const natural = page.getViewport({ scale: 1 });
                    const pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
                    const targetWidth = Math.min(2600, width * 2 * pixelRatio);
                    const rendered = page.getViewport({ scale: targetWidth / natural.width });
                    const canvas = pages[index].querySelector('canvas');
                    canvas.width = Math.ceil(rendered.width);
                    canvas.height = Math.ceil(rendered.height);
                    const context = canvas.getContext('2d');
                    context.imageSmoothingEnabled = true;
                    context.imageSmoothingQuality = 'high';
                    await page.render({ canvasContext: context, viewport: rendered }).promise;
                    pages[index].classList.add('is-rendered');
                })();
                renderJobs.set(index, job);
                return job;
            };

            const renderAround = (index) => {
                const radius = isMobile ? 3 : 4;
                for (let offset = -radius; offset <= radius; offset += 1) renderPage(index + offset);
            };

            const updateControls = (index) => {
                const page = index + 1;
                const side = page === 1 ? 'Cover' : (page % 2 === 0 ? 'Left page' : 'Right page');
                if (pageLabel) pageLabel.textContent = isMobile ? `${side} · ${page} / ${pdf.numPages}` : `${page} / ${pdf.numPages}`;
                root.querySelector('[data-flip-prev]')?.toggleAttribute('disabled', page <= 1);
                root.querySelector('[data-flip-next]')?.toggleAttribute('disabled', page >= pdf.numPages);
            };

            const mobilePosition = (index) => {
                if (index === 0) return (viewport.clientWidth - width) / 2;
                return index % 2 === 1 ? 0 : -(width - mobilePeek);
            };

            const mountMobileSpread = (index, fromRight = false) => {
                stage.replaceChildren();
                stage.className = 'flipbook-stage is-mobile-spread';
                if (index === 0) {
                    stage.appendChild(pages[0]);
                    stage.classList.add('is-cover');
                } else {
                    const spreadStart = index % 2 === 1 ? index : index - 1;
                    stage.appendChild(pages[spreadStart]);
                    if (pages[spreadStart + 1]) stage.appendChild(pages[spreadStart + 1]);
                }
                stage.style.setProperty('--mobile-book-x', `${fromRight ? viewport.clientWidth : mobilePosition(index)}px`);
                if (fromRight) requestAnimationFrame(() => requestAnimationFrame(() => stage.style.setProperty('--mobile-book-x', `${mobilePosition(index)}px`)));
                renderAround(index);
                updateControls(index);
            };

            const goMobile = (nextIndex) => {
                nextIndex = Math.max(0, Math.min(pdf.numPages - 1, nextIndex));
                if (nextIndex === mobileIndex || mobileBusy) return;
                const oldSpread = mobileIndex === 0 ? -1 : Math.floor((mobileIndex - 1) / 2);
                const newSpread = nextIndex === 0 ? -1 : Math.floor((nextIndex - 1) / 2);
                if (oldSpread === newSpread) {
                    mobileIndex = nextIndex;
                    stage.style.setProperty('--mobile-book-x', `${mobilePosition(mobileIndex)}px`);
                    renderAround(mobileIndex);
                    updateControls(mobileIndex);
                    return;
                }
                mobileBusy = true;
                stage.classList.add('is-fading-out');
                window.setTimeout(() => {
                    mobileIndex = nextIndex;
                    mountMobileSpread(mobileIndex);
                    stage.classList.add('is-fading-in');
                    stage.getBoundingClientRect();
                    requestAnimationFrame(() => requestAnimationFrame(() => {
                        stage.classList.remove('is-fading-in');
                    }));
                    window.setTimeout(() => { mobileBusy = false; }, 320);
                }, 260);
            };

            setStatus('Opening publication…');
            await Promise.all([renderPage(0), renderPage(1), renderPage(2)]);

            if (isMobile) {
                root.classList.add('uses-mobile-spreads');
                mountMobileSpread(0);
                let pointerStart = null;
                viewport.addEventListener('pointerdown', (event) => { pointerStart = event.clientX; });
                viewport.addEventListener('pointerup', (event) => {
                    if (pointerStart === null) return;
                    const delta = event.clientX - pointerStart;
                    pointerStart = null;
                    if (Math.abs(delta) >= 42) goMobile(mobileIndex + (delta < 0 ? 1 : -1));
                });
            } else {
                book = new PageFlip(stage, { width, height, size: 'fixed', showCover: true, usePortrait: true, mobileScrollSupport: false, maxShadowOpacity: 0.55, flippingTime: 850, drawShadow: true, autoSize: false, clickEventForward: true, swipeDistance: 24 });
                book.loadFromHTML(pages);
                book.on('flip', () => {
                    const index = book.getCurrentPageIndex();
                    renderAround(index);
                    updateControls(index);
                });
            }

            const currentIndex = () => isMobile ? mobileIndex : book.getCurrentPageIndex();
            const goTo = (index) => isMobile ? goMobile(index) : book.flip(index);
            const previous = () => isMobile ? goMobile(mobileIndex - 1) : book.flipPrev();
            const next = () => isMobile ? goMobile(mobileIndex + 1) : book.flipNext();

            const applyZoom = (nextZoom) => {
                zoom = Math.max(1, Math.min(2, nextZoom));
                zoomSurface.style.setProperty('--flipbook-zoom', zoom);
                zoomSurface.classList.toggle('is-zoomed', zoom > 1);
                root.querySelector('[data-flip-zoom-out]')?.toggleAttribute('disabled', zoom === 1);
                root.querySelector('[data-flip-zoom-in]')?.toggleAttribute('disabled', zoom === 2);
                root.querySelector('[data-flip-zoom-value]').textContent = `${Math.round(zoom * 100)}%`;
                setStatus(zoom > 1 ? 'Drag the page area to inspect the design.' : (isMobile ? 'Swipe left and right across each two-page spread.' : 'Drag a page corner or use the arrows.'));
            };

            const buildThumbnails = async () => {
                if (thumbnailsReady) return;
                thumbnailsReady = true;
                for (let index = 0; index < pdf.numPages; index += 1) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'flipbook-thumbnail';
                    button.innerHTML = `<span class="flipbook-thumbnail-placeholder"></span><small>${index + 1}</small>`;
                    thumbnailGrid.appendChild(button);
                    button.addEventListener('click', () => { goTo(index); thumbnailPanel.hidden = true; });
                    window.setTimeout(async () => {
                        const page = index === 0 ? firstPage : await pdf.getPage(index + 1);
                        const natural = page.getViewport({ scale: 1 });
                        const thumbViewport = page.getViewport({ scale: 116 / natural.width });
                        const canvas = document.createElement('canvas');
                        canvas.width = Math.ceil(thumbViewport.width);
                        canvas.height = Math.ceil(thumbViewport.height);
                        await page.render({ canvasContext: canvas.getContext('2d'), viewport: thumbViewport }).promise;
                        button.querySelector('.flipbook-thumbnail-placeholder')?.replaceWith(canvas);
                    }, index * 18);
                }
            };

            root.querySelector('[data-flip-prev]')?.addEventListener('click', previous);
            root.querySelector('[data-flip-next]')?.addEventListener('click', next);
            root.querySelector('[data-flip-zoom-out]')?.addEventListener('click', () => applyZoom(zoom - 0.25));
            root.querySelector('[data-flip-zoom-in]')?.addEventListener('click', () => applyZoom(zoom + 0.25));
            root.querySelector('[data-flip-reset]')?.addEventListener('click', () => applyZoom(1));
            root.querySelector('[data-flip-grid]')?.addEventListener('click', async () => { await buildThumbnails(); thumbnailPanel.hidden = !thumbnailPanel.hidden; });
            root.querySelector('[data-flip-thumbnail-close]')?.addEventListener('click', () => { thumbnailPanel.hidden = true; });
            root.querySelectorAll('[data-flip-fullscreen]').forEach((button) => button.addEventListener('click', async () => {
                if (document.fullscreenElement) await document.exitFullscreen(); else await root.requestFullscreen?.();
            }));
            document.addEventListener('keydown', (event) => {
                if (!root.matches(':hover') && !document.fullscreenElement) return;
                if (event.key === 'ArrowLeft') previous();
                if (event.key === 'ArrowRight') next();
                if (event.key === '+' || event.key === '=') applyZoom(zoom + 0.25);
                if (event.key === '-') applyZoom(zoom - 0.25);
                if (event.key === '0') applyZoom(1);
                if (event.key === 'Escape') thumbnailPanel.hidden = true;
            });
            renderAround(currentIndex());
            applyZoom(1);
            setStatus(isMobile ? 'Swipe: left page, slide right, then turn to the next spread.' : 'Drag a page corner, swipe, or use the arrows.');
            updateControls(currentIndex());
            root.classList.add('is-ready');
        } catch (error) {
            console.error(error);
            setStatus('The interactive preview could not be opened.');
            const fallbackUrl = root.getAttribute('data-pdf-url') || '#';
            stage.innerHTML = `<a class="button-primary mt-5" target="_blank" rel="noreferrer" href="${fallbackUrl}">Open PDF</a>`;
        }
    })();
}
