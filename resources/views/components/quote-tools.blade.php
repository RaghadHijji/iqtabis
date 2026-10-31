{{-- Tools every quote button uses, on any page:
     [data-copy]  copies the quote with who said it
     [data-image] saves the quote as a picture for stories (1080 × 1920) --}}
<script>
    (() => {
        document.addEventListener('click', async (e) => {
            const button = e.target.closest('[data-copy]');
            if (!button) return;
            const author = button.dataset.author ? `\n— ${button.dataset.author}` : '';
            await navigator.clipboard.writeText(button.dataset.copy + author);
            const old = button.innerHTML;
            button.textContent = '✓';
            setTimeout(() => (button.innerHTML = old), 1200);
        });

        const painting = new Image();
        painting.src = @json(asset('images/athens.jpg'));
        // load the fonts early, so the picture is ready right after the tap (phones only allow sharing straight after a tap)
        ['700 64px Amiri', '700 72px "Aref Ruqaa"', '500 36px Tajawal'].forEach((f) => document.fonts.load(f));

        // phones: no download, so show the picture to press and hold (or tap Save)
        const showPicture = (url) => {
            const box = document.createElement('div');
            box.style.cssText = 'position:fixed;inset:0;z-index:2147483647;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1rem;padding:1.5rem;background:rgba(0,0,0,.88)';
            box.innerHTML = `<img src="${url}" alt="" style="max-height:72vh;max-width:100%;border-radius:.75rem">
                <p style="color:#fff;font-size:.9rem;text-align:center">${@json(__('Press and hold the picture to save it.'))}</p>
                <button type="button" class="btn-primary px-6">${@json(__('Close'))}</button>`;
            box.addEventListener('click', (e) => (e.target === box || e.target.tagName === 'BUTTON') && box.remove());
            document.body.append(box);
        };

        // split the text into lines that fit the width
        const wrap = (ctx, text, width) => {
            const lines = [];
            let line = '';
            text.split(/\s+/).forEach((word) => {
                const test = line ? `${line} ${word}` : word;
                if (ctx.measureText(test).width > width && line) {
                    lines.push(line);
                    line = word;
                } else {
                    line = test;
                }
            });
            if (line) lines.push(line);
            return lines;
        };

        document.addEventListener('click', async (e) => {
            const button = e.target.closest('[data-image]');
            if (!button) return;

            const { text, author = '', category = '' } = button.dataset;
            const arabic = /[؀-ۿ]/.test(text);
            await Promise.all(['700 64px Amiri', '700 72px "Aref Ruqaa"', '500 36px Tajawal'].map((f) => document.fonts.load(f)));

            const canvas = document.createElement('canvas');
            canvas.width = 1080;
            canvas.height = 1920; // story size (9:16)
            const ctx = canvas.getContext('2d');
            const W = canvas.width, H = canvas.height;

            // the site's own background behind: "The School of Athens", warm and golden by day, dark brown at night
            const night = document.documentElement.classList.contains('dark');
            ctx.fillStyle = night ? '#1C120B' : '#EADBC0';
            ctx.fillRect(0, 0, W, H);
            if (!painting.complete) await new Promise((done) => (painting.onload = painting.onerror = done));
            if (painting.naturalWidth) {
                const scale = Math.max(W / painting.naturalWidth, H / painting.naturalHeight);
                const pw = painting.naturalWidth * scale, ph = painting.naturalHeight * scale;
                // a little blurred, like a photo taken close to the sheet, so the paper stands out from it
                ctx.filter = night ? 'sepia(.7) saturate(1.35) brightness(.85) contrast(1.1) blur(2px)' : 'sepia(.65) saturate(1.3) brightness(1) contrast(1.05) blur(2px)';
                ctx.drawImage(painting, (W - pw) / 2, (H - ph) / 2 - 80, pw, ph);
                ctx.filter = 'none';
            }
            const shade = ctx.createLinearGradient(0, 0, 0, H);
            // a warm golden light from above that fades into brown at the bottom
            shade.addColorStop(0, night ? 'rgba(40, 24, 12, .45)' : 'rgba(70, 45, 20, .3)');
            shade.addColorStop(0.55, night ? 'rgba(28, 18, 11, .55)' : 'rgba(60, 38, 18, .4)');
            shade.addColorStop(1, night ? 'rgba(15, 9, 5, .85)' : 'rgba(40, 24, 10, .72)');
            ctx.fillStyle = shade;
            ctx.fillRect(0, 0, W, H);
            const edges = ctx.createRadialGradient(W / 2, H / 2, H * 0.25, W / 2, H / 2, H * 0.75);
            edges.addColorStop(0, 'rgba(0, 0, 0, 0)');
            edges.addColorStop(1, night ? 'rgba(10, 6, 3, .9)' : 'rgba(70, 40, 15, .65)');
            ctx.fillStyle = edges;
            ctx.fillRect(0, 0, W, H);

            // a soft dark glow in the middle, so the light words stand out on the painting
            const scrim = ctx.createRadialGradient(W / 2, H * 0.47, 60, W / 2, H * 0.47, W * 0.85);
            scrim.addColorStop(0, night ? 'rgba(15, 9, 5, .55)' : 'rgba(30, 18, 9, .6)');
            scrim.addColorStop(1, 'rgba(15, 9, 5, 0)');
            ctx.fillStyle = scrim;
            ctx.fillRect(0, 0, W, H);

            ctx.textAlign = 'center';
            ctx.direction = arabic ? 'rtl' : 'ltr';

            // the quote: smaller letters for longer quotes
            const size = text.length > 110 ? 56 : text.length > 60 ? 66 : 78;
            ctx.font = `700 ${size}px ${arabic ? 'Amiri' : 'Georgia'}`;
            const lines = wrap(ctx, text, 860);
            const lineHeight = size * 1.6;
            let y = H / 2 - ((lines.length - 1) * lineHeight) / 2 - 40;

            ctx.fillStyle = '#E8CFAE';
            ctx.font = '700 180px Georgia';
            ctx.fillText('❝', W / 2, y - lineHeight - 10);

            ctx.fillStyle = '#ffffff';
            ctx.font = `700 ${size}px ${arabic ? 'Amiri' : 'Georgia'}`;
            ctx.shadowColor = 'rgba(0, 0, 0, .5)';
            ctx.shadowBlur = 20;
            lines.forEach((line, i) => ctx.fillText(line, W / 2, y + i * lineHeight));
            ctx.shadowBlur = 0;

            y += lines.length * lineHeight + 10;
            ctx.font = '500 38px Tajawal';
            ctx.fillStyle = '#E8CFAE';
            if (author) ctx.fillText(`— ${author}`, W / 2, y);
            if (category) {
                ctx.fillStyle = 'rgba(255, 255, 255, .75)';
                ctx.font = '500 30px Tajawal';
                ctx.fillText(category, W / 2, y + (author ? 60 : 0));
            }

            // the site name at the bottom
            ctx.direction = 'rtl';
            ctx.fillStyle = '#ffffff';
            ctx.shadowColor = 'rgba(0, 0, 0, .5)';
            ctx.shadowBlur = 16;
            ctx.font = '700 72px "Aref Ruqaa"';
            ctx.fillText('اقتبس', W / 2, H - 300); // above the story reply bar
            ctx.font = '500 26px Tajawal';
            ctx.fillStyle = 'rgba(255, 255, 255, .8)';
            ctx.fillText(@json(__('A quote for every moment')) + ' · ' + location.host, W / 2, H - 250);
            ctx.shadowBlur = 0;

            const url = canvas.toDataURL('image/png');
            const phone = matchMedia('(pointer: coarse)').matches;

            // phones: open the share sheet ("Save Image", Instagram, WhatsApp...)
            if (phone && navigator.canShare) {
                const bytes = Uint8Array.from(atob(url.split(',')[1]), (ch) => ch.charCodeAt(0));
                const file = new File([bytes], 'iqtabis-quote.png', { type: 'image/png' });
                if (navigator.canShare({ files: [file] })) {
                    try {
                        await navigator.share({ files: [file] });
                        return;
                    } catch (error) {
                        if (error.name === 'AbortError') return; // closed the share sheet
                    }
                }
            }
            if (phone) return showPicture(url);

            const link = document.createElement('a');
            link.download = 'iqtabis-quote.png';
            link.href = url;
            link.click();
        });
    })();
</script>
