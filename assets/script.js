/**
 * Modern JavaScript - Multi-Product Management System
 * LAB 5 Quiz Project V2
 */

// Image Preview
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const file = input.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            preview.style.display = 'block';

            // Animate preview
            preview.style.animation = 'fadeIn 0.5s ease';
        };

        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
        preview.style.display = 'none';
    }
}

// Drag and Drop Upload
function initDragDrop() {
    const fileUpload = document.querySelector('.file-upload');
    if (!fileUpload) return;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        fileUpload.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        fileUpload.addEventListener(eventName, () => {
            fileUpload.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        fileUpload.addEventListener(eventName, () => {
            fileUpload.classList.remove('dragover');
        }, false);
    });

    fileUpload.addEventListener('drop', function (e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        const input = document.getElementById('image');

        if (input && files.length > 0) {
            input.files = files;
            previewImage(input);
        }
    }, false);
}

// Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast alert alert-${type}`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'toastSlide 0.5s ease reverse';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 500);
    }, 3000);
}

// Form Validation
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = 'var(--danger)';
            isValid = false;
        } else {
            field.style.borderColor = 'var(--border-color)';
        }
    });

    return isValid;
}

// Delete Confirmation Modal
function confirmDelete(productName) {
    return new Promise((resolve) => {
        // Create modal backdrop
        const modal = document.createElement('div');
        modal.className = 'delete-modal-backdrop';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            animation: fadeIn 0.3s ease;
        `;

        // Create modal content
        const modalContent = document.createElement('div');
        modalContent.style.cssText = `
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 32px;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: scaleIn 0.3s ease;
            border: 1px solid var(--border-color);
        `;

        modalContent.innerHTML = `
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="font-size: 4em; margin-bottom: 16px;">🗑️</div>
                <h3 style="margin: 0 0 12px 0; font-size: 1.5em; color: var(--text-primary);">Konfirmasi Hapus</h3>
                <p style="margin: 0; color: var(--text-muted); line-height: 1.6;">
                    Yakin ingin menghapus produk<br>
                    <strong style="color: var(--text-primary);">"${productName}"</strong>?
                </p>
                <p style="margin: 12px 0 0 0; color: var(--danger); font-size: 0.9em;">
                    ⚠️ Gambar produk juga akan dihapus dan tidak dapat dikembalikan.
                </p>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button class="modal-cancel-btn" style="
                    flex: 1;
                    padding: 12px 24px;
                    background: var(--bg-secondary);
                    color: var(--text-primary);
                    border: 1px solid var(--border-color);
                    border-radius: var(--radius-md);
                    cursor: pointer;
                    font-size: 1em;
                    font-weight: 600;
                    transition: all 0.3s ease;
                ">
                    ❌ Batal
                </button>
                <button class="modal-delete-btn" style="
                    flex: 1;
                    padding: 12px 24px;
                    background: var(--danger);
                    color: white;
                    border: none;
                    border-radius: var(--radius-md);
                    cursor: pointer;
                    font-size: 1em;
                    font-weight: 600;
                    transition: all 0.3s ease;
                ">
                    🗑️ Hapus
                </button>
            </div>
        `;

        modal.appendChild(modalContent);
        document.body.appendChild(modal);

        // Add hover effects
        const cancelBtn = modalContent.querySelector('.modal-cancel-btn');
        const deleteBtn = modalContent.querySelector('.modal-delete-btn');

        cancelBtn.addEventListener('mouseenter', () => {
            cancelBtn.style.background = 'var(--bg-hover)';
            cancelBtn.style.transform = 'translateY(-2px)';
        });
        cancelBtn.addEventListener('mouseleave', () => {
            cancelBtn.style.background = 'var(--bg-secondary)';
            cancelBtn.style.transform = 'translateY(0)';
        });

        deleteBtn.addEventListener('mouseenter', () => {
            deleteBtn.style.background = '#dc2626';
            deleteBtn.style.transform = 'translateY(-2px)';
            deleteBtn.style.boxShadow = '0 10px 25px -5px rgba(220, 38, 38, 0.4)';
        });
        deleteBtn.addEventListener('mouseleave', () => {
            deleteBtn.style.background = 'var(--danger)';
            deleteBtn.style.transform = 'translateY(0)';
            deleteBtn.style.boxShadow = 'none';
        });

        // Handle cancel
        const closeModal = (result) => {
            modal.style.animation = 'fadeIn 0.3s ease reverse';
            modalContent.style.animation = 'scaleIn 0.3s ease reverse';
            setTimeout(() => {
                document.body.removeChild(modal);
                resolve(result);
            }, 300);
        };

        cancelBtn.addEventListener('click', () => closeModal(false));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(false);
        });

        // Handle delete
        deleteBtn.addEventListener('click', () => closeModal(true));

        // Handle ESC key
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                closeModal(false);
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);
    });
}

// Search and Filter
function filterProducts() {
    const searchInput = document.getElementById('search');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');

    if (!searchInput || !categoryFilter || !sortFilter) return;

    // Build URL parameters
    const params = new URLSearchParams(window.location.search);

    // Update search parameter
    const searchTerm = searchInput.value.trim();
    if (searchTerm) {
        params.set('search', searchTerm);
    } else {
        params.delete('search');
    }

    // Update category parameter
    const selectedCategory = categoryFilter.value;
    if (selectedCategory) {
        params.set('category_id', selectedCategory);
    } else {
        params.delete('category_id');
    }

    // Update sort parameter
    const selectedSort = sortFilter.value;
    if (selectedSort && selectedSort !== 'created_at_desc') {
        params.set('sort', selectedSort);
    } else {
        params.delete('sort');
    }

    // Remove stock filter if it exists
    params.delete('stock');

    // Update URL without reloading
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.location.href = newUrl;
}

// Image Lightbox
function openLightbox(imageSrc) {
    const lightbox = document.createElement('div');
    lightbox.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        cursor: pointer;
        animation: fadeIn 0.3s ease;
    `;

    const img = document.createElement('img');
    img.src = imageSrc;
    img.style.cssText = `
        max-width: 90%;
        max-height: 90%;
        border-radius: 12px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    `;

    lightbox.appendChild(img);
    document.body.appendChild(lightbox);

    lightbox.addEventListener('click', () => {
        lightbox.style.animation = 'fadeIn 0.3s ease reverse';
        setTimeout(() => {
            document.body.removeChild(lightbox);
        }, 300);
    });
}

// Lazy Load Images
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Animate Cards on Scroll
function animateOnScroll() {
    const cards = document.querySelectorAll('.card');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                entry.target.style.setProperty('--card-index', index);
                entry.target.style.animation = `scaleIn 0.5s ease-out ${index * 0.1}s both`;
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => observer.observe(card));
}

// Initialize on DOM Load
document.addEventListener('DOMContentLoaded', function () {
    // Initialize drag and drop
    initDragDrop();

    // Lazy load images
    lazyLoadImages();

    // Animate cards
    animateOnScroll();

    // Add click handlers for product images
    document.querySelectorAll('.product-image').forEach(img => {
        img.addEventListener('click', function () {
            openLightbox(this.src);
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showToast('Mohon isi semua field yang diperlukan', 'danger');
            }
        });
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'slideInRight 0.5s ease reverse';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });
});

// Price formatting
function formatPrice(price) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
}

// Export functions for global use
window.previewImage = previewImage;
window.confirmDelete = confirmDelete;
window.filterProducts = filterProducts;
window.openLightbox = openLightbox;
window.showToast = showToast;
