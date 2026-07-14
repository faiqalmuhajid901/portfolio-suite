document.addEventListener('alpine:init', () => {
    window.Alpine.data('projectImageUploader', (config) => ({
        uploading: false,
        progress: 0,
        error: '',
        preview: config.existingImage || null,
        originalPreview: config.existingImage || null,
        objectUrl: null,
        hasNewUpload: false,

        resetUploader() {
            if (this.objectUrl) {
                URL.revokeObjectURL(this.objectUrl);
            }

            this.uploading = false;
            this.progress = 0;
            this.error = '';
            this.objectUrl = null;
            this.preview = this.originalPreview;
            this.hasNewUpload = false;

            if (this.$refs.imageInput) {
                this.$refs.imageInput.value = '';
            }
        },

        firstError(payload, fallback) {
            if (payload?.message) {
                return payload.message;
            }

            if (payload?.errors) {
                const first = Object.values(payload.errors).flat()[0];

                if (first) {
                    return first;
                }
            }

            return fallback;
        },

        validateFile(file) {
            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'image/bmp',
                'image/x-ms-bmp',
            ];

            if (!allowedTypes.includes(file.type)) {
                throw new Error(
                    'Format gambar harus JPG, JPEG, PNG, GIF, WEBP, atau BMP.'
                );
            }

            if (file.size > 4 * 1024 * 1024) {
                throw new Error('Ukuran gambar maksimal 4 MB.');
            }
        },

        async requestSignedUrl(file) {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            if (!csrfToken) {
                throw new Error('CSRF token tidak ditemukan.');
            }

            const response = await fetch(config.signUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    name: file.name,
                    type: file.type,
                    size: file.size,
                }),
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(
                    this.firstError(
                        payload,
                        `Gagal meminta signed URL (${response.status}).`
                    )
                );
            }

            if (!payload.signed_url || !payload.path) {
                throw new Error(
                    'Respons signed upload URL dari server tidak lengkap.'
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

                    let message = `Upload Supabase gagal (${xhr.status}).`;

                    try {
                        const payload = JSON.parse(xhr.responseText);
                        message = payload.message || message;
                    } catch {
                        if (xhr.responseText) {
                            message = xhr.responseText;
                        }
                    }

                    reject(new Error(message));
                };

                xhr.onerror = () => {
                    reject(
                        new Error(
                            'Koneksi browser ke Supabase gagal. Periksa CORS dan koneksi.'
                        )
                    );
                };

                const body = new FormData();
                body.append('cacheControl', '3600');
                body.append('', file);

                xhr.send(body);
            });
        },

        async chooseImage(event) {
            const input = event.target;
            const file = input.files?.[0];

            this.error = '';
            this.progress = 0;

            if (!file) {
                return;
            }

            try {
                this.validateFile(file);

                if (this.objectUrl) {
                    URL.revokeObjectURL(this.objectUrl);
                }

                this.objectUrl = URL.createObjectURL(file);
                this.preview = this.objectUrl;
                this.uploading = true;

                const signed = await this.requestSignedUrl(file);

                await this.uploadToSupabase(signed.signed_url, file);
                await this.$wire.setUploadedImage(signed.path);

                if (this.objectUrl) {
                    URL.revokeObjectURL(this.objectUrl);
                    this.objectUrl = null;
                }

                this.preview = signed.public_url;
                this.hasNewUpload = true;
                this.progress = 100;
            } catch (exception) {
                if (this.objectUrl) {
                    URL.revokeObjectURL(this.objectUrl);
                    this.objectUrl = null;
                }

                this.preview = this.originalPreview;
                this.hasNewUpload = false;
                this.error =
                    exception?.message || 'Upload gambar gagal.';
                input.value = '';
            } finally {
                this.uploading = false;
            }
        },

        async cancelNewImage() {
            if (this.uploading) {
                return;
            }

            try {
                await this.$wire.removeUploadedImage();
            } finally {
                this.resetUploader();
            }
        },
    }));
});
