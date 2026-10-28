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

        const photo = new Image();
        photo.src = @json(asset('images/hero.jpg'));

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
            if (!photo.complete) await new Promise((done) => (photo.onload = photo.onerror = done));

            const canvas = document.createElement('canvas');
            canvas.width = 1080;
            canvas.height = 1920; // story size (9:16)
            const ctx = canvas.getContext('2d');

            // the arches photo, cut to fill the picture, darker so the words stand out
            if (photo.naturalWidth) {
                const scale = Math.max(canvas.width / photo.naturalWidth, canvas.height / photo.naturalHeight);
                const w = photo.naturalWidth * scale;
                const h = photo.naturalHeight * scale;
                ctx.drawImage(photo, (canvas.width - w) / 2, (canvas.height - h) / 2, w, h);
            }
            const shade = ctx.createLinearGradient(0, 0, 0, canvas.height);
            shade.addColorStop(0, 'rgba(10, 30, 40, .55)');
            shade.addColorStop(1, 'rgba(10, 30, 40, .85)');
            ctx.fillStyle = shade;
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.textAlign = 'center';
            ctx.direction = arabic ? 'rtl' : 'ltr';

            // the quote: smaller letters for longer quotes
            const size = text.length > 110 ? 56 : text.length > 60 ? 66 : 78;
            ctx.font = `700 ${size}px ${arabic ? 'Amiri' : 'Georgia'}`;
            const lines = wrap(ctx, text, 860);
            const lineHeight = size * 1.6;
            let y = canvas.height / 2 - ((lines.length - 1) * lineHeight) / 2 - 40;

            ctx.fillStyle = '#E8CFAE';
            ctx.font = '700 180px Georgia';
            ctx.fillText('❝', canvas.width / 2, y - lineHeight - 10);

            ctx.fillStyle = '#ffffff';
            ctx.font = `700 ${size}px ${arabic ? 'Amiri' : 'Georgia'}`;
            ctx.shadowColor = 'rgba(0, 0, 0, .35)';
            ctx.shadowBlur = 18;
            lines.forEach((line, i) => ctx.fillText(line, canvas.width / 2, y + i * lineHeight));
            ctx.shadowBlur = 0;

            y += lines.length * lineHeight + 10;
            ctx.font = '500 38px Tajawal';
            ctx.fillStyle = '#E8CFAE';
            if (author) ctx.fillText(`— ${author}`, canvas.width / 2, y);
            if (category) {
                ctx.fillStyle = 'rgba(255, 255, 255, .7)';
                ctx.font = '500 30px Tajawal';
                ctx.fillText(category, canvas.width / 2, y + (author ? 60 : 0));
            }

            // the site name at the bottom
            ctx.direction = 'rtl';
            ctx.fillStyle = '#ffffff';
            ctx.font = '700 72px "Aref Ruqaa"';
            ctx.fillText('اقتبس', canvas.width / 2, canvas.height - 300); // above the story reply bar
            ctx.font = '500 26px Tajawal';
            ctx.fillStyle = 'rgba(255, 255, 255, .75)';
            ctx.fillText(@json(__('A quote for every moment')) + ' · ' + location.host, canvas.width / 2, canvas.height - 250);

            const link = document.createElement('a');
            link.download = 'iqtabis-quote.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    })();
</script>
