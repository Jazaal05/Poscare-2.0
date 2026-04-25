/**
 * ============================================================================
 * INPUT VALIDATOR - Frontend Validation untuk PosCare
 * ============================================================================
 * File ini berisi fungsi-fungsi untuk memvalidasi dan sanitasi input di frontend
 * Hanya mengizinkan huruf, angka, dan karakter yang diperlukan
 * TIDAK mengizinkan emoji dan simbol khusus
 */

const InputValidator = {
    // Regex patterns untuk validasi
    patterns: {
        alphanumericSpace: /^[a-zA-Z0-9\s\u00C0-\u017F]+$/,
        alphanumericBasic: /^[a-zA-Z0-9\s\u00C0-\u017F.,\-\/]+$/,
        nik: /^[0-9]{16}$/,
        phone: /^[0-9+]+$/,
        decimal: /^[0-9]+(\.[0-9]+)?$/,
        integer: /^[0-9]+$/,
        date: /^\d{4}-\d{2}-\d{2}$/,
        time: /^\d{2}:\d{2}(:\d{2})?$/,
        rtrw: /^[0-9\/]+$/,
        email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        url: /^https?:\/\/.+/
    },
    
    // Pattern untuk mendeteksi emoji
    emojiPattern: /[\u{1F600}-\u{1F64F}]|[\u{1F300}-\u{1F5FF}]|[\u{1F680}-\u{1F6FF}]|[\u{1F1E0}-\u{1F1FF}]|[\u{2600}-\u{26FF}]|[\u{2700}-\u{27BF}]|[\u{1F900}-\u{1F9FF}]|[\u{1FA00}-\u{1FAFF}]|[\u{1FA70}-\u{1FAFF}]|[\u{FE00}-\u{FE0F}]|[\u{231A}-\u{231B}]|[\u{23E9}-\u{23FA}]|[\u{25AA}-\u{25AB}]|[\u{25B6}]|[\u{25C0}]|[\u{25FB}-\u{25FE}]|[\u{2614}-\u{2615}]|[\u{2648}-\u{2653}]|[\u{267F}]|[\u{2693}]|[\u{26A1}]|[\u{26AA}-\u{26AB}]|[\u{26BD}-\u{26BE}]|[\u{26C4}-\u{26C5}]|[\u{26CE}]|[\u{26D4}]|[\u{26EA}]|[\u{26F2}-\u{26F5}]|[\u{26FA}]|[\u{26FD}]/gu,
    
    // Pattern untuk karakter spesial yang tidak diizinkan
    specialCharsPattern: /[!@#$%^&*()_+=\[\]{}|\\:;"'<>?`~]/g,
    
    // Fungsi sanitasi
    sanitize: {
        /**
         * Sanitize string - hanya huruf, angka, dan spasi
         * Untuk: nama kegiatan, nama anak, nama orang tua, dll
         */
        alphanumericSpace: function(input) {
            if (!input) return '';
            let clean = InputValidator.sanitize.removeEmoji(input);
            clean = clean.replace(/[^a-zA-Z0-9\s\u00C0-\u017F]/g, '');
            clean = clean.replace(/\s+/g, ' ');
            return clean.trim();
        },
        
        /**
         * Sanitize string - hanya huruf, angka, spasi, dan tanda baca dasar (. , - /)
         * Untuk: alamat, keterangan, lokasi
         */
        alphanumericBasic: function(input) {
            if (!input) return '';
            let clean = InputValidator.sanitize.removeEmoji(input);
            clean = clean.replace(/[^a-zA-Z0-9\s\u00C0-\u017F.,\-\/]/g, '');
            clean = clean.replace(/\s+/g, ' ');
            return clean.trim();
        },
        
        /**
         * Sanitize NIK - hanya angka, maks 16 digit
         */
        nik: function(input) {
            if (!input) return '';
            return input.replace(/[^0-9]/g, '').substring(0, 16);
        },
        
        /**
         * Sanitize nomor telepon - hanya angka dan +
         */
        phone: function(input) {
            if (!input) return '';
            return input.replace(/[^0-9+]/g, '');
        },
        
        /**
         * Sanitize angka desimal (untuk berat badan, tinggi badan)
         */
        decimal: function(input) {
            if (!input) return '';
            let clean = input.replace(/[^0-9.]/g, '');
            const parts = clean.split('.');
            if (parts.length > 2) {
                clean = parts[0] + '.' + parts.slice(1).join('');
            }
            return clean;
        },
        
        /**
         * Sanitize integer - hanya angka
         */
        integer: function(input) {
            if (!input && input !== '0' && input !== 0) return '';
            return String(input).replace(/[^0-9]/g, '');
        },
        
        /**
         * Hapus emoji dari string
         */
        removeEmoji: function(input) {
            if (!input) return '';
            return input.replace(InputValidator.emojiPattern, '');
        },
        
        /**
         * Sanitize RT/RW - hanya angka dan /
         */
        rtrw: function(input) {
            if (!input) return '';
            return input.replace(/[^0-9\/]/g, '');
        },
        
        /**
         * Sanitize gender - hanya L atau P
         */
        gender: function(input) {
            if (!input) return '';
            const clean = input.toUpperCase().trim();
            return ['L', 'P'].includes(clean) ? clean : '';
        },
        
        /**
         * Sanitize untuk textarea/konten panjang - izinkan newline
         */
        textareaBasic: function(input) {
            if (!input) return '';
            let clean = InputValidator.sanitize.removeEmoji(input);
            clean = clean.replace(/[^a-zA-Z0-9\s\u00C0-\u017F.,\-\/\n\r]/g, '');
            return clean.trim();
        }
    },
    
    /**
     * Cek apakah string mengandung emoji
     */
    hasEmoji: function(input) {
        if (!input) return false;
        return InputValidator.emojiPattern.test(input);
    },
    
    /**
     * Cek apakah string mengandung karakter spesial yang tidak diizinkan
     */
    hasSpecialChars: function(input) {
        if (!input) return false;
        return InputValidator.specialCharsPattern.test(input);
    },
    
    /**
     * Validasi input berdasarkan tipe
     */
    validate: function(input, type) {
        if (!input) return true;
        const pattern = this.patterns[type];
        if (!pattern) return true;
        return pattern.test(input);
    },
    
    /**
     * Tampilkan pesan error di bawah input
     */
    showError: function(inputElement, message) {
        // Hapus error sebelumnya
        this.clearError(inputElement);
        
        // Buat element error
        const errorEl = document.createElement('span');
        errorEl.className = 'input-validator-error';
        errorEl.textContent = message;
        errorEl.style.cssText = 'color: #EF4444; font-size: 12px; display: block; margin-top: 4px;';
        
        // Tambahkan setelah input
        inputElement.parentNode.insertBefore(errorEl, inputElement.nextSibling);
        inputElement.style.borderColor = '#EF4444';
        
        // Hapus error setelah 3 detik
        setTimeout(function() {
            InputValidator.clearError(inputElement);
        }, 3000);
    },
    
    /**
     * Hapus pesan error
     */
    clearError: function(inputElement) {
        const errorEl = inputElement.parentNode.querySelector('.input-validator-error');
        if (errorEl) {
            errorEl.remove();
        }
        inputElement.style.borderColor = '';
    },
    
    /**
     * Attach validasi real-time ke input element
     */
    attachToInput: function(inputElement, type, options) {
        if (!inputElement) return;
        
        options = options || {};
        const allowEmpty = options.allowEmpty !== false;
        const maxLength = options.maxLength || null;
        const showError = options.showError !== false;
        const errorMessage = options.errorMessage || 'Input tidak valid. Hanya huruf dan angka yang diizinkan.';
        const self = this;
        
        // Input event - sanitize saat user mengetik
        inputElement.addEventListener('input', function(e) {
            let value = this.value;
            let changed = false;
            
            // Hapus emoji terlebih dahulu
            if (self.hasEmoji(value)) {
                value = self.sanitize.removeEmoji(value);
                changed = true;
            }
            
            // Terapkan sanitasi sesuai tipe
            if (self.sanitize[type]) {
                const sanitized = self.sanitize[type](value);
                if (sanitized !== value) {
                    value = sanitized;
                    changed = true;
                }
            }
            
            // Update nilai jika berubah
            if (changed) {
                this.value = value;
                if (showError) {
                    self.showError(this, errorMessage);
                }
            }
            
            // Cek max length
            if (maxLength && this.value.length > maxLength) {
                this.value = this.value.substring(0, maxLength);
            }
        });
        
        // Paste event - sanitize konten yang di-paste
        inputElement.addEventListener('paste', function(e) {
            e.preventDefault();
            let pastedText = (e.clipboardData || window.clipboardData).getData('text');
            
            // Hapus emoji
            pastedText = self.sanitize.removeEmoji(pastedText);
            
            // Terapkan sanitasi sesuai tipe
            if (self.sanitize[type]) {
                pastedText = self.sanitize[type](pastedText);
            }
            
            // Insert di posisi cursor
            const start = this.selectionStart;
            const end = this.selectionEnd;
            const currentValue = this.value;
            this.value = currentValue.substring(0, start) + pastedText + currentValue.substring(end);
            this.selectionStart = this.selectionEnd = start + pastedText.length;
            
            // Trigger input event
            this.dispatchEvent(new Event('input', { bubbles: true }));
        });
        
        // Blur event - validasi akhir
        inputElement.addEventListener('blur', function(e) {
            if (!allowEmpty && !this.value.trim()) {
                self.showError(this, 'Field ini wajib diisi');
            } else {
                self.clearError(this);
            }
        });
    },
    
    /**
     * Inisialisasi semua input dengan atribut data-validate
     * Contoh: <input type="text" data-validate="alphanumericSpace">
     */
    initAll: function() {
        const self = this;
        document.querySelectorAll('[data-validate]').forEach(function(input) {
            const type = input.dataset.validate;
            const options = {
                allowEmpty: input.dataset.allowEmpty !== 'false',
                maxLength: input.dataset.maxLength ? parseInt(input.dataset.maxLength) : null,
                showError: input.dataset.showError !== 'false',
                errorMessage: input.dataset.errorMessage || 'Input tidak valid. Hanya huruf dan angka yang diizinkan.'
            };
            self.attachToInput(input, type, options);
        });
    },
    
    /**
     * Sanitasi semua input dalam form sebelum submit
     */
    sanitizeForm: function(formElement, rules) {
        const self = this;
        Object.keys(rules).forEach(function(fieldName) {
            const type = rules[fieldName];
            const input = formElement.querySelector('[name="' + fieldName + '"]');
            if (input && self.sanitize[type]) {
                input.value = self.sanitize[type](input.value);
            }
        });
    },
    
    /**
     * Validasi form sebelum submit
     * @return {boolean} true jika valid, false jika ada error
     */
    validateForm: function(formElement, rules) {
        const self = this;
        let isValid = true;
        
        Object.keys(rules).forEach(function(fieldName) {
            const rule = rules[fieldName];
            const input = formElement.querySelector('[name="' + fieldName + '"]');
            
            if (input) {
                const value = input.value.trim();
                
                // Cek required
                if (rule.required && !value) {
                    self.showError(input, rule.errorMessage || 'Field ini wajib diisi');
                    isValid = false;
                    return;
                }
                
                // Cek emoji
                if (value && self.hasEmoji(value)) {
                    self.showError(input, 'Emoji tidak diizinkan');
                    isValid = false;
                    return;
                }
                
                // Cek pattern
                if (value && rule.type && !self.validate(value, rule.type)) {
                    self.showError(input, rule.errorMessage || 'Format tidak valid');
                    isValid = false;
                }
            }
        });
        
        return isValid;
    }
};

// Auto-initialize saat DOM ready
document.addEventListener('DOMContentLoaded', function() {
    InputValidator.initAll();
});

// Export untuk module system jika diperlukan
if (typeof module !== 'undefined' && module.exports) {
    module.exports = InputValidator;
}
