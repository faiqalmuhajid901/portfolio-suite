document.addEventListener('alpine:init', () => {
    Alpine.data('directUploader', (config) => ({
        uploading: false,
        progress: 0,
        error: '',
        preview: config.existingUrl || null,
        objectUrl: null,

        async chooseFile(event) {
            const input = event.target;
            const file = input.files?.[0];

            this.error = '';
            this.progress = 0;

            if (!file) {
                return;
            }

            try {
                if (file.size > config.maxSize) {
                    throw new Error(config.maxSizeMessage);
                }

                if (
                    Array.isArray(config.allowedTypes)
                    && !config.allowedTypes.includes(file.type)
                ) {
                    throw new Error('Format file tidak didukung.');
                }

                if (file.type.startsWith('image/')) {
                    this.objectUrl = URL.createObjectURL(file);
                    this.preview = this.objectUrl;
                }

                this.uploading = true;

                const signed = await this.requestSignedUrl(file);

                await this.uploadToSupabase(
                    signed.signed_url,
                    file
                );

                await this.$wire.call(
                    config.livewireMethod,
                    signed.path,
                    signed.public_url
                );

                if (this.objectUrl) {
                    URL.revokeObjectURL(this.objectUrl);
                    this.objectUrl = null;
                }

                if (file.type.startsWith('image/')) {
                    this.preview = signed.public_url;
                }

                this.progress = 100;
            } catch (error) {
                this.error = error?.message || 'Upload file gagal.';
                input.value = '';
            } finally {
                this.uploading = false;
            }
        },

        async requestSignedUrl(file) {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            const response = await fetch(config.signUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    target: config.target,
                    name: file.name,
                    type: file.type,
                    size: file.size,
                }),
            });

            const payload = await response
                .json()
                .catch(() => ({}));

            if (!response.ok) {
                throw new Error(
                    payload.message
                    || `Gagal meminta signed URL (${response.status}).`
                );
            }

            return payload;
        },

        uploadToSupabase(signedUrl, file) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();

                xhr.open('PUT', signedUrl, true);
                xhr.setRequestHeader('x-upsert', 'false');

                xhr.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        this.progress = Math.round(
                            (event.loaded / event.total) * 100
                        );
                    }
                };

                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve();
                        return;
                    }

                    reject(
                        new Error(
                            `Upload Supabase gagal (${xhr.status}).`
                        )
                    );
                };

                xhr.onerror = () => {
                    reject(
                        new Error(
                            'Koneksi browser ke Supabase gagal.'
                        )
                    );
                };

                const body = new FormData();

                body.append('cacheControl', '3600');
                body.append('', file);

                xhr.send(body);
            });
        },
    }));
});
