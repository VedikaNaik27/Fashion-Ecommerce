/**
 * AURA Fashion Atelier - Frontend Client Script
 * Handles responsive mobile navigation, UI dropdowns, toast auto-dismissal,
 * cart quantity steppers, AI stylist interaction, and Virtual Try-On canvas studio.
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Menu Drawer Navigation
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileCloseBtn = document.getElementById('mobileCloseBtn');
  const mobileDrawer = document.getElementById('mobileDrawer');
  const mobileBackdrop = document.getElementById('mobileBackdrop');

  function openMobileNav() {
    if (mobileDrawer) mobileDrawer.classList.add('open');
    if (mobileBackdrop) mobileBackdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeMobileNav() {
    if (mobileDrawer) mobileDrawer.classList.remove('open');
    if (mobileBackdrop) mobileBackdrop.classList.remove('open');
    document.body.style.overflow = '';
  }

  if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMobileNav);
  if (mobileCloseBtn) mobileCloseBtn.addEventListener('click', closeMobileNav);
  if (mobileBackdrop) mobileBackdrop.addEventListener('click', closeMobileNav);

  // 2. User Account Dropdown
  const userMenuToggle = document.getElementById('userMenuToggle');
  const userDropdownMenu = document.getElementById('userDropdownMenu');

  if (userMenuToggle && userDropdownMenu) {
    userMenuToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      userDropdownMenu.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
      if (!userDropdownMenu.contains(e.target) && e.target !== userMenuToggle) {
        userDropdownMenu.classList.remove('active');
      }
    });
  }

  // 3. Auto-Dismiss Flash Toasts after 4.5 seconds
  const toastContainer = document.getElementById('toastContainer');
  if (toastContainer) {
    const alerts = toastContainer.querySelectorAll('.toast-alert');
    alerts.forEach((alert) => {
      setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(20px)';
        setTimeout(() => alert.remove(), 300);
      }, 4500);
    });
  }

  // 4. Quantity Stepper Controls
  document.querySelectorAll('.quantity-stepper').forEach((stepper) => {
    const input = stepper.querySelector('input[type="number"]');
    const minusBtn = stepper.querySelector('.btn-qty-minus');
    const plusBtn = stepper.querySelector('.btn-qty-plus');

    if (input && minusBtn && plusBtn) {
      minusBtn.addEventListener('click', (e) => {
        e.preventDefault();
        let val = parseInt(input.value, 10) || 1;
        if (val > 1) {
          input.value = val - 1;
          input.dispatchEvent(new Event('change'));
        }
      });

      plusBtn.addEventListener('click', (e) => {
        e.preventDefault();
        let val = parseInt(input.value, 10) || 1;
        input.value = val + 1;
        input.dispatchEvent(new Event('change'));
      });
    }
  });

  // 5. Cart Demo Promo Code Handler
  const promoApplyBtn = document.getElementById('applyPromoBtn');
  const promoInput = document.getElementById('promoCodeInput');
  const promoFeedback = document.getElementById('promoFeedback');
  const discountRow = document.getElementById('discountRow');
  const discountAmount = document.getElementById('discountAmount');
  const grandTotalDisplay = document.getElementById('grandTotalDisplay');

  if (promoApplyBtn && promoInput) {
    promoApplyBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const code = promoInput.value.trim().toUpperCase();
      if (code === 'AURA10') {
        if (promoFeedback) {
          promoFeedback.textContent = 'Promo code AURA10 applied! (10% demonstration discount)';
          promoFeedback.style.color = 'var(--color-success)';
        }
        if (discountRow) discountRow.style.display = 'flex';
        
        // Recalculate discount
        const subtotalElem = document.getElementById('subtotalValue');
        if (subtotalElem && discountAmount && grandTotalDisplay) {
          const subtotal = parseFloat(subtotalElem.dataset.subtotal) || 0;
          const shipping = parseFloat(subtotalElem.dataset.shipping) || 0;
          const discount = subtotal * 0.10;
          const newTotal = Math.max(0, subtotal - discount + shipping);
          
          discountAmount.textContent = '-₹' + discount.toFixed(2);
          grandTotalDisplay.textContent = '₹' + newTotal.toFixed(2);
        }
      } else if (code === '') {
        if (promoFeedback) {
          promoFeedback.textContent = 'Please enter a coupon code.';
          promoFeedback.style.color = 'var(--color-error)';
        }
      } else {
        if (promoFeedback) {
          promoFeedback.textContent = 'Invalid promo code. Try AURA10 for this demo.';
          promoFeedback.style.color = 'var(--color-error)';
        }
      }
    });
  }

  // 6. Virtual Try-On Studio Engine
  const modelOptions = document.querySelectorAll('.model-option-card');
  const canvasModelLayer = document.getElementById('canvasModelLayer');
  const canvasGarmentLayer = document.getElementById('canvasGarmentLayer');
  const garmentThumbBtns = document.querySelectorAll('.garment-thumb-btn');
  const tryonFileInput = document.getElementById('tryonFileInput');
  const tryonUploadTrigger = document.getElementById('tryonUploadTrigger');

  // Sliders
  const garmentScaleSlider = document.getElementById('garmentScale');
  const garmentYSlider = document.getElementById('garmentY');
  const garmentOpacitySlider = document.getElementById('garmentOpacity');
  const resetCanvasBtn = document.getElementById('resetCanvasBtn');

  // Switch Model Silhouette
  if (modelOptions.length > 0 && canvasModelLayer) {
    modelOptions.forEach((card) => {
      card.addEventListener('click', () => {
        modelOptions.forEach((c) => c.classList.remove('active'));
        card.classList.add('active');
        const modelSrc = card.getAttribute('data-model-src');
        if (modelSrc) {
          canvasModelLayer.src = modelSrc;
        }
      });
    });
  }

  // Choose Garment from Scroller
  if (garmentThumbBtns.length > 0 && canvasGarmentLayer) {
    garmentThumbBtns.forEach((btn) => {
      btn.addEventListener('click', () => {
        garmentThumbBtns.forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        const garmentSrc = btn.getAttribute('data-garment-src');
        const garmentName = btn.getAttribute('data-garment-name');
        const garmentId = btn.getAttribute('data-garment-id');
        
        if (garmentSrc) {
          canvasGarmentLayer.src = garmentSrc;
          canvasGarmentLayer.style.display = 'block';
        }

        const selectedNameLabel = document.getElementById('selectedGarmentName');
        if (selectedNameLabel) selectedNameLabel.textContent = garmentName || 'Selected Garment';

        const tryonProductLink = document.getElementById('tryonProductLink');
        if (tryonProductLink && garmentId) {
          tryonProductLink.href = 'product_details.php?id=' + encodeURIComponent(garmentId);
          tryonProductLink.style.display = 'inline-flex';
        }
      });
    });
  }

  // User Custom Photo Upload Preview
  if (tryonUploadTrigger && tryonFileInput) {
    tryonUploadTrigger.addEventListener('click', () => {
      tryonFileInput.click();
    });

    tryonFileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file && canvasModelLayer) {
        if (!file.type.startsWith('image/')) {
          alert('Please select a valid image file (JPG, PNG, or WebP).');
          return;
        }
        const reader = new FileReader();
        reader.onload = (event) => {
          canvasModelLayer.src = event.target.result;
          modelOptions.forEach((c) => c.classList.remove('active'));
          const uploadStatus = document.getElementById('uploadStatusText');
          if (uploadStatus) {
            uploadStatus.textContent = 'Custom photo loaded: ' + file.name;
          }
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Garment Adjustments (Scale, Position, Opacity)
  function updateGarmentTransform() {
    if (!canvasGarmentLayer) return;
    const scale = (garmentScaleSlider ? parseInt(garmentScaleSlider.value, 10) : 100) / 100;
    const yPos = garmentYSlider ? parseInt(garmentYSlider.value, 10) : 0;
    const opacity = (garmentOpacitySlider ? parseInt(garmentOpacitySlider.value, 10) : 100) / 100;

    canvasGarmentLayer.style.transform = `translateY(${yPos}px) scale(${scale})`;
    canvasGarmentLayer.style.opacity = opacity;

    const scaleValDisp = document.getElementById('scaleValDisplay');
    const yValDisp = document.getElementById('yValDisplay');
    const opacityValDisp = document.getElementById('opacityValDisplay');

    if (scaleValDisp && garmentScaleSlider) scaleValDisp.textContent = garmentScaleSlider.value + '%';
    if (yValDisp && garmentYSlider) yValDisp.textContent = garmentYSlider.value + 'px';
    if (opacityValDisp && garmentOpacitySlider) opacityValDisp.textContent = garmentOpacitySlider.value + '%';
  }

  if (garmentScaleSlider) garmentScaleSlider.addEventListener('input', updateGarmentTransform);
  if (garmentYSlider) garmentYSlider.addEventListener('input', updateGarmentTransform);
  if (garmentOpacitySlider) garmentOpacitySlider.addEventListener('input', updateGarmentTransform);

  if (resetCanvasBtn) {
    resetCanvasBtn.addEventListener('click', () => {
      if (garmentScaleSlider) garmentScaleSlider.value = 100;
      if (garmentYSlider) garmentYSlider.value = 0;
      if (garmentOpacitySlider) garmentOpacitySlider.value = 100;
      updateGarmentTransform();
    });
  }
});
