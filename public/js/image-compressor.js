/**
 * Client-Side Image Compressor
 * 
 * Compresses images in the browser BEFORE uploading to the server.
 * Prevents PHP GD "out of memory" errors when processing high-resolution
 * camera photos from mobile devices (12-108 MP).
 *
 * Usage:
 *   // Auto-init: Add data-compress attribute to any file input
 *   <input type="file" name="photo" accept="image/*" data-compress>
 *
 *   // With custom options:
 *   <input type="file" name="photo" accept="image/*"
 *          data-compress
 *          data-compress-max-width="1200"
 *          data-compress-max-height="1200"
 *          data-compress-quality="0.7">
 *
 *   // Manual JS init:
 *   ImageCompressor.compress(file, { maxWidth: 1200, quality: 0.7 })
 *     .then(compressedFile => { ... });
 */
(function() {
    'use strict';

    var ImageCompressor = {

        // Default options
        defaults: {
            maxWidth: 1200,
            maxHeight: 1200,
            quality: 0.7,
            mimeType: 'image/jpeg' // output format for non-PNG
        },

        /**
         * Compress a single File/Blob image.
         * Returns a Promise that resolves to a compressed File.
         */
        compress: function(file, options) {
            var opts = Object.assign({}, this.defaults, options || {});

            return new Promise(function(resolve, reject) {
                // Skip non-image files (e.g. PDF)
                if (!file || !file.type.match(/^image\/(jpeg|png|webp|gif)$/i)) {
                    resolve(file);
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = new Image();
                    img.onload = function() {
                        try {
                            var result = ImageCompressor._resizeAndCompress(img, file, opts);
                            resolve(result);
                        } catch (err) {
                            console.warn('[ImageCompressor] Compression failed, using original:', err);
                            resolve(file);
                        }
                    };
                    img.onerror = function() {
                        console.warn('[ImageCompressor] Could not load image, using original.');
                        resolve(file);
                    };
                    img.src = e.target.result;
                };
                reader.onerror = function() {
                    resolve(file);
                };
                reader.readAsDataURL(file);
            });
        },

        /**
         * Internal: resize image on canvas and return compressed File.
         */
        _resizeAndCompress: function(img, originalFile, opts) {
            var width = img.naturalWidth || img.width;
            var height = img.naturalHeight || img.height;

            // Calculate new dimensions maintaining aspect ratio
            if (width > opts.maxWidth) {
                height = Math.round(height * (opts.maxWidth / width));
                width = opts.maxWidth;
            }
            if (height > opts.maxHeight) {
                width = Math.round(width * (opts.maxHeight / height));
                height = opts.maxHeight;
            }

            // Create canvas
            var canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            var ctx = canvas.getContext('2d');

            // Handle EXIF orientation is done automatically by modern browsers
            // when using drawImage with an HTMLImageElement from a data URL.
            ctx.drawImage(img, 0, 0, width, height);

            // Determine output type
            var isPng = originalFile.type === 'image/png';
            var outputType = isPng ? 'image/png' : opts.mimeType;
            var quality = isPng ? undefined : opts.quality;

            // Convert canvas to blob synchronously via dataURL
            var dataUrl = canvas.toDataURL(outputType, quality);
            var blob = ImageCompressor._dataUrlToBlob(dataUrl);

            // Create a new File with the original name
            var ext = isPng ? '.png' : '.jpg';
            var fileName = originalFile.name.replace(/\.[^.]+$/, '') + '_compressed' + ext;

            var compressedFile;
            try {
                compressedFile = new File([blob], fileName, {
                    type: outputType,
                    lastModified: Date.now()
                });
            } catch (e) {
                // Fallback for older browsers that don't support File constructor
                compressedFile = blob;
                compressedFile.name = fileName;
                compressedFile.lastModified = Date.now();
            }

            var originalKB = Math.round(originalFile.size / 1024);
            var compressedKB = Math.round(compressedFile.size / 1024);
            console.log(
                '[ImageCompressor] ' + img.naturalWidth + 'x' + img.naturalHeight + 
                ' → ' + width + 'x' + height + 
                ' | ' + originalKB + 'KB → ' + compressedKB + 'KB' +
                ' (' + Math.round((1 - compressedFile.size / originalFile.size) * 100) + '% smaller)'
            );

            // Clean up canvas to free memory
            canvas.width = 0;
            canvas.height = 0;

            return compressedFile;
        },

        /**
         * Convert a data URL to a Blob.
         */
        _dataUrlToBlob: function(dataUrl) {
            var parts = dataUrl.split(',');
            var mime = parts[0].match(/:(.*?);/)[1];
            var bstr = atob(parts[1]);
            var n = bstr.length;
            var u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new Blob([u8arr], { type: mime });
        },

        /**
         * Attach compression to a file input element.
         * Replaces the selected files with compressed versions.
         */
        attachToInput: function(input, options) {
            if (input._compressorAttached) return;
            input._compressorAttached = true;

            var self = this;
            var opts = Object.assign({}, this.defaults, options || {});

            input.addEventListener('change', function(e) {
                var files = Array.from(input.files);
                if (!files.length) return;

                // Only process image files
                var imageFiles = files.filter(function(f) {
                    return f.type.match(/^image\/(jpeg|png|webp|gif)$/i);
                });

                if (!imageFiles.length) return;

                // Show a subtle processing indicator
                var indicator = ImageCompressor._showIndicator(input);

                var promises = files.map(function(file) {
                    if (file.type.match(/^image\/(jpeg|png|webp|gif)$/i)) {
                        return self.compress(file, opts);
                    }
                    return Promise.resolve(file);
                });

                Promise.all(promises).then(function(compressedFiles) {
                    // Replace files in the input using DataTransfer
                    try {
                        var dt = new DataTransfer();
                        compressedFiles.forEach(function(f) {
                            dt.items.add(f);
                        });
                        input.files = dt.files;
                    } catch (err) {
                        // DataTransfer not supported (very old browsers)
                        // Store compressed files for form submission interception
                        console.warn('[ImageCompressor] DataTransfer not supported, fallback mode.');
                        input._compressedFiles = compressedFiles;
                    }

                    ImageCompressor._hideIndicator(indicator);
                }).catch(function(err) {
                    console.error('[ImageCompressor] Error:', err);
                    ImageCompressor._hideIndicator(indicator);
                });
            });
        },

        /**
         * Show a small "Mengompresi..." indicator near the input.
         */
        _showIndicator: function(input) {
            var el = document.createElement('div');
            el.className = 'image-compress-indicator';
            el.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Mengompresi foto...';
            el.style.cssText = 'display:inline-flex;align-items:center;margin-top:6px;padding:6px 14px;' +
                'font-size:0.82rem;color:#0d6efd;background:#e8f0fe;border-radius:6px;' +
                'animation:fadeIn 0.2s ease;';
            
            if (input.parentNode) {
                input.parentNode.insertBefore(el, input.nextSibling);
            }
            return el;
        },

        _hideIndicator: function(el) {
            if (el && el.parentNode) {
                el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#198754" viewBox="0 0 16 16" style="margin-right:6px"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>Foto berhasil dikompresi!';
                el.style.color = '#198754';
                el.style.background = '#d1e7dd';
                setTimeout(function() {
                    if (el.parentNode) {
                        el.style.transition = 'opacity 0.5s';
                        el.style.opacity = '0';
                        setTimeout(function() {
                            if (el.parentNode) el.parentNode.removeChild(el);
                        }, 500);
                    }
                }, 3000);
            }
        },

        /**
         * Auto-initialize all inputs with [data-compress] attribute.
         */
        init: function() {
            var self = this;
            document.querySelectorAll('input[type="file"][data-compress]').forEach(function(input) {
                var opts = {};
                if (input.dataset.compressMaxWidth) opts.maxWidth = parseInt(input.dataset.compressMaxWidth);
                if (input.dataset.compressMaxHeight) opts.maxHeight = parseInt(input.dataset.compressMaxHeight);
                if (input.dataset.compressQuality) opts.quality = parseFloat(input.dataset.compressQuality);
                self.attachToInput(input, opts);
            });
        }
    };

    // Expose globally
    window.ImageCompressor = ImageCompressor;

    // Auto-init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { ImageCompressor.init(); });
    } else {
        ImageCompressor.init();
    }

    // Also observe for dynamically added inputs
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function(mutations) {
            var shouldInit = false;
            mutations.forEach(function(m) {
                if (m.addedNodes.length) shouldInit = true;
            });
            if (shouldInit) ImageCompressor.init();
        });
        
        var startObserving = function() {
            observer.observe(document.body, { childList: true, subtree: true });
        };
        
        if (document.body) {
            startObserving();
        } else {
            document.addEventListener('DOMContentLoaded', startObserving);
        }
    }
})();
