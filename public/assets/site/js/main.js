jQuery(document).ready(function () {


    // toggle search form
    const searchTrigger = document.querySelector('.search-icon'),
        searchForm = document.querySelector('.search-form-wrapper');
    searchTrigger.addEventListener('click', function (e) {
        e.preventDefault();
        this.classList.toggle('active');
        searchForm.classList.toggle('active');
    })

    // nested nav mobile
    if (jQuery(window).width() <= 992) {
        jQuery(".menu-item-has-children").click(function () {
            const $this = jQuery(this);
            $this.children(".sub-menu").slideToggle(300);
            $this.children("a").toggleClass("icon-rotate");
            jQuery(".menu-item-has-children").not($this).children(".sub-menu").slideUp(300);
            jQuery(".menu-item-has-children").not($this).children("a").removeClass("icon-rotate");
        });
    }

    // ************************************************************************************************
    // open and close sidebar

    jQuery(".bars").on("click", function () {
        jQuery(".line1").toggleClass("rotate-line1");
        jQuery(".line2").toggleClass("hide-line2");
        jQuery(".line3").toggleClass("rotate-line3");
        jQuery(".navigation").toggleClass("open-sidebar");
        jQuery("body").toggleClass("overflow-hidden");
    });


    // ************************************************************************************************
    // show and hide to top button

    jQuery(window).on("scroll", function () {
        if (jQuery(window).scrollTop() > 100) {
            jQuery(".up-btn").addClass("show");
        }
        if (jQuery(window).scrollTop() == 0) {
            jQuery(".up-btn").removeClass("show");
        }
    });

    jQuery(".up-btn").on("click", function () {
        jQuery("html , body").animate({ scrollTop: 0 }, 0);
    });



    // ************************************************************************************************
    // swiper slider


    function initShopByCategorySwipers() {
        if (typeof Swiper === 'undefined') return;
        document.querySelectorAll('.shop-by-category-swiper').forEach(function (el) {
            if (el.swiper) return;
            new Swiper(el, {
                loop: true,
                draggable: true,
                autoplay: true,
                spaceBetween: 45,
                observer: true,
                observeParents: true,
                updateOnWindowResize: true,
                navigation: {
                    nextEl: el.querySelector(".swiper-button-next"),
                    prevEl: el.querySelector(".swiper-button-prev"),
                },
                pagination: {
                    el: el.querySelector(".swiper-pagination"),
                    clickable: true,
                },
                breakpoints: {
                    350: { slidesPerView: 2, spaceBetween: 15 },
                    500: { slidesPerView: 2, spaceBetween: 20 },
                    768: { slidesPerView: 3, spaceBetween: 24 },
                    992: { slidesPerView: 4, spaceBetween: 24 },
                    1200: { slidesPerView: 6, spaceBetween: 24 },
                },
            });

            // Prevent "stretched" slides when page was navigated without full reload.
            requestAnimationFrame(function () { try { el.swiper && el.swiper.update(); } catch (e) {} });
            setTimeout(function () { try { el.swiper && el.swiper.update(); } catch (e) {} }, 250);
        });
    }
    const salonHeroSlider = new Swiper(".salon-hero-slider .swiper", {
        loop: true,
        draggable: true,
        autoplay: true,
        slidesPerView: 1,

        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },



    });



    function initProductsSwipers() {
        if (typeof Swiper === 'undefined') return;
        document.querySelectorAll('.products-swiper').forEach(function (el) {
            if (el.swiper) return;
            new Swiper(el, {
                loop: true,
                draggable: true,
                autoplay: true,
                spaceBetween: 24,
                observer: true,
                observeParents: true,
                updateOnWindowResize: true,
                pagination: {
                    el: el.querySelector(".swiper-pagination"),
                    clickable: true,
                },
                breakpoints: {
                    350: { slidesPerView: 2, spaceBetween: 15, navigation: false },
                    500: { slidesPerView: 2, spaceBetween: 24, navigation: false },
                    768: { slidesPerView: 2, spaceBetween: 24 },
                    992: { slidesPerView: 3, spaceBetween: 24 },
                    1200: { slidesPerView: 4, spaceBetween: 24 },
                },
            });

            requestAnimationFrame(function () { try { el.swiper && el.swiper.update(); } catch (e) {} });
            setTimeout(function () { try { el.swiper && el.swiper.update(); } catch (e) {} }, 250);
        });
    }

    initShopByCategorySwipers();
    initProductsSwipers();
    document.addEventListener('livewire:navigated', function () {
        initShopByCategorySwipers();
        initProductsSwipers();
    });

    const testimonialsSwiper = new Swiper(".testimonials-swiper", {
        loop: true,
        draggable: true,
        autoplay: false,
        spaceBetween: 45,
        centeredSlides: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },

        breakpoints: {
            350: {
                slidesPerView: 1,
                spaceBetween: 15,
                navigation: false,

            },
            500: {
                slidesPerView: 1,
                spaceBetween: 20,
                navigation: false,

            },
            768: {
                slidesPerView: 1.5,
                spaceBetween: 30,
            },
            992: {
                slidesPerView: 2,
                spaceBetween: 45,
            },
            1200: {
                slidesPerView: 3,
                spaceBetween: 45,
            },
        },

    });

    const blogsSwiper = new Swiper(".blogs-swiper", {
        loop: true,
        draggable: true,
        autoplay: true,
        spaceBetween: 45,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },

        breakpoints: {
            350: {
                slidesPerView: 1,
                spaceBetween: 15,
                navigation: false,

            },
            500: {
                slidesPerView: 1,
                spaceBetween: 20,
                navigation: false,

            },
            768: {
                slidesPerView: 2,
                spaceBetween: 30,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 45,
            },
            1200: {
                slidesPerView: 3,
                spaceBetween: 45,
            },
        },

    });
    const workshopSwiper = new Swiper(".workshop-swiper", {
        loop: true,
        draggable: true,
        autoplay: true,
        spaceBetween: 45,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
            dynamicBullets: true,
        },

        breakpoints: {
            350: {
                slidesPerView: 1,
                spaceBetween: 15,
                navigation: false,

            },
            500: {
                slidesPerView: 1,
                spaceBetween: 20,
                navigation: false,

            },
            768: {
                slidesPerView: 2,
                spaceBetween: 30,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 45,
            },
            1200: {
                slidesPerView: 3,
                spaceBetween: 45,
            },
        },

    });


    /*Faq Accordion*/

    $(".faq-title").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $this = $(this);
        const $content = $this.next('.faq-content');
        const isCurrentlyActive = $this.hasClass("active");

        // Close all other FAQ items first
        $(".faq-title").not($this).removeClass("active");
        $(".faq-content").not($content).slideUp(300);

        // Toggle current item
        if (!isCurrentlyActive) {
            $this.addClass("active");
            $content.slideDown(300);
        } else {
            $this.removeClass("active");
            $content.slideUp(300);
        }

        return false;
    });

    // Footer Accordion
    $(".footer-title").on("click", function () {
        if ($(window).width() <= 767) {
            const $this = $(this);
            $this.toggleClass("active");
            $this.next("ul").slideToggle(300);
        }
    });

    $(window).on("resize", function () {
        if ($(window).width() > 767) {
            $(".footer-title").removeClass("active");
            $(".footer-title").next("ul").removeAttr("style");
        }
    });

    // User Menu Toggle for Mobile
    $(".user-menu-toggle").on("click", function () {
        if ($(window).width() <= 991) {
            const $this = $(this);
            $this.toggleClass("active");
            $this.closest(".user-menu").find(".menu-list").slideToggle(300);
        }
    });

    $(window).on("resize", function () {
        if ($(window).width() > 991) {
            $(".user-menu-toggle").removeClass("active");
            $(".menu-list").removeAttr("style");
        }
    });

    // OTP Input Logic (tmoono-style: paste, backspace, delete, arrows, hidden sync)
    // Event delegation for Livewire-dynamic OTP form
    function otpUpdateInput(container) {
        const inputs = $(container).find('input.otp-input');
        const form = $(container).closest('form');
        const hiddenInput = form.find('#otp-hidden')[0];
        const value = inputs.toArray().map(function (input) { return input.value; }).join('');
        if (hiddenInput) {
            hiddenInput.value = value;
            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    document.addEventListener('input', function (e) {
        const input = e.target;
        if (!input.matches('.otp-input')) return;
        const container = input.closest('#otp-input');
        if (!container) return;
        const inputs = $(container).find('input.otp-input');
        const index = inputs.index(input);

        if (input.value.length === 1 && !/^\d$/.test(input.value)) {
            input.value = '';
            otpUpdateInput(container);
            return;
        }

        if (input.value.length === 1 && index + 1 < inputs.length) {
            inputs.eq(index + 1).removeAttr('disabled');
            inputs[index + 1].focus();
        } else if (input.value.length === 1) {
            inputs.blur();
        }

        if (input.value.length > 1) {
            const digits = input.value.replace(/\D/g, '');
            if (!digits) {
                input.value = '';
                otpUpdateInput(container);
                return;
            }
            const chars = digits.split('');
            chars.forEach(function (ch, pos) {
                if (pos + index >= inputs.length) return;
                const targetInput = inputs[pos + index];
                targetInput.value = ch;
                $(targetInput).removeAttr('disabled');
            });
            const focusIndex = Math.min(inputs.length - 1, index + chars.length);
            inputs[focusIndex].focus();
        }
        otpUpdateInput(container);
    });

    document.addEventListener('keydown', function (e) {
        const input = e.target;
        if (!input.matches('.otp-input')) return;
        const container = input.closest('#otp-input');
        if (!container) return;
        const inputs = $(container).find('input.otp-input');
        const index = inputs.index(input);

        if (e.key === 'Backspace' && input.value === '' && index !== 0) {
            for (let pos = index; pos < inputs.length - 1; pos++) {
                inputs[pos].value = inputs[pos + 1].value;
            }
            inputs[index - 1].value = '';
            inputs[index - 1].focus();
            otpUpdateInput(container);
            return;
        }

        if (e.key === 'Delete' && index !== inputs.length - 1) {
            for (let pos = index; pos < inputs.length - 1; pos++) {
                inputs[pos].value = inputs[pos + 1].value;
            }
            inputs[inputs.length - 1].value = '';
            input.select();
            e.preventDefault();
            otpUpdateInput(container);
            return;
        }

        if (e.key === 'ArrowLeft' && index > 0) {
            e.preventDefault();
            inputs[index - 1].focus();
            inputs[index - 1].select();
            return;
        }

        if (e.key === 'ArrowRight' && index + 1 < inputs.length) {
            e.preventDefault();
            inputs[index + 1].focus();
            inputs[index + 1].select();
            return;
        }
    });

    // Favorite toggle (add/remove from favorites)
    function handleFavoriteToggle(btn) {
        if (!btn || !btn.dataset.url) return;
        const url = btn.dataset.url;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) return;
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ _token: csrfToken }),
        }).then(function (res) {
            if (res.status === 401) {
                const locale = (window.location.pathname.match(/^\/([a-z]{2})(?:\/|$)/) || [])[1] || 'ar';
                window.location.href = '/' + locale + '/login?intended=' + encodeURIComponent(window.location.href);
                return;
            }
            return res.json();
        }).then(function (data) {
            if (!data) return;
            if (data.success && data.message) {
                btn.classList.toggle('active', data.favorited);
                const modal = document.getElementById('favorite-modal');
                const msgEl = document.getElementById('favorite-modal-message');
                if (modal && msgEl) {
                    msgEl.textContent = data.message;
                    const isOnFavoritesPage = /\/favorites\/?$/.test(window.location.pathname);
                    const wasRemoved = !data.favorited;
                    modal.dataset.shouldReload = (isOnFavoritesPage && wasRemoved) ? '1' : '0';
                    const bsModal = typeof bootstrap !== 'undefined' ? bootstrap.Modal.getOrCreateInstance(modal) : null;
                    if (bsModal) bsModal.show();
                }
            }
        }).catch(function () {});
    }
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-favorite-toggle]');
        if (btn) {
            e.preventDefault();
            e.stopPropagation();
            handleFavoriteToggle(btn);
        }
    });
    document.addEventListener('keydown', function (e) {
        const btn = e.target.closest('[data-favorite-toggle]');
        if (btn && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            handleFavoriteToggle(btn);
        }
    });

    var favoriteModal = document.getElementById('favorite-modal');
    if (favoriteModal) {
        favoriteModal.addEventListener('hidden.bs.modal', function () {
            if (this.dataset.shouldReload === '1') {
                window.location.reload();
            }
        });
    }

});
// wow animation
document.addEventListener("DOMContentLoaded", function () {

    const singleElements = document.querySelectorAll('section ,h1');
    singleElements.forEach(el => {
        el.classList.add('wow', 'fadeInUp');
    });


    const staggeredItems = document.querySelectorAll('.single-faq-item,.single-blog-card,.single-product-card,.single-testimonial-card,.single-category-card');

    staggeredItems.forEach((el, index) => {
        el.classList.add('wow', 'fadeInUp');

        let delayMultiplier = index % 3;

        if (delayMultiplier > 0) {
            el.setAttribute('data-wow-delay', `${delayMultiplier * 0.2}s`);
        }
    });

    if (typeof WOW !== 'undefined') {
        new WOW({
            boxClass: 'wow',
            offset: 50,
            mobile: true,
            live: true
        }).init();
    }
});

// ==========================================
// Open Media Modal
// ==========================================
function openMediaModal(type, src) {
    const modalBody = document.getElementById('mediaModalBody');

    // Detect Content Type
    if (type === 'image') {
        modalBody.innerHTML = `<img src="${src}" class="img-fluid rounded shadow-lg" style="max-height: 80vh; object-fit: contain;">`;
    } else if (type === 'video') {
        modalBody.innerHTML = `<video src="${src}" class="w-100 rounded shadow-lg" style="max-height: 80vh;" controls autoplay></video>`;
    }

    const modalElement = document.getElementById('mediaModal');
    const mediaModal = bootstrap.Modal.getOrCreateInstance(modalElement);
    mediaModal.show();
}

// Clean the modal and stop the video when it is closed (with confirmation that it exists first)
const mediaModalElement = document.getElementById('mediaModal');
if (mediaModalElement) {
    mediaModalElement.addEventListener('hidden.bs.modal', function () {
        const modalBody = document.getElementById('mediaModalBody');
        if (modalBody) {
            modalBody.innerHTML = '';
        }
    });
}

// ==========================================
// Play and Stop Audio (Dynamically without ID)
// ==========================================
function toggleAudio(btn) {
    // Search for the audio file for this specific button
    let audio = btn.parentElement.querySelector('audio');
    let icon = btn.querySelector('i');

    // Stop any other audio playing on the page so they don't interfere
    document.querySelectorAll('audio').forEach(a => {
        if (a !== audio) {
            a.pause();
            let otherBtn = a.parentElement.querySelector('.play-audio-btn i');
            if (otherBtn) {
                otherBtn.classList.remove('fa-pause');
                otherBtn.classList.add('fa-play');
            }
        }
    });

    // Play or Stop Current Audio
    if (audio.paused) {
        audio.play();
        icon.classList.remove('fa-play');
        icon.classList.add('fa-pause');
    } else {
        audio.pause();
        icon.classList.remove('fa-pause');
        icon.classList.add('fa-play');
    }
}

// Return the icon to the Play shape when the sound ends
function resetAudio(audio) {
    let icon = audio.parentElement.querySelector('.play-audio-btn i');
    if (icon) {
        icon.classList.remove('fa-pause');
        icon.classList.add('fa-play');
    }
}

// ==========================================
// Category Filters Logic 
// ==========================================
document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll('.category-filters button');

    if (filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Remove active styling from all buttons
                filterButtons.forEach(btn => {
                    btn.classList.remove('active');
                });

                // Add active styling to clicked button
                this.classList.add('active');

                // Get filter value
                const filterValue = this.getAttribute('data-filter');

                // Find nearest services list to this filter group (allows multiple chairs to have separate filters if needed, or global)
                const servicesContainer = this.closest('.tab-pane') || document;
                const serviceCards = servicesContainer.querySelectorAll('.services-list > div.service-card');

                serviceCards.forEach((card) => {
                    const category = card.getAttribute('data-category');

                    if (filterValue === 'all' || filterValue === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }
});

// ==========================================
// Optional Products Logic
// ==========================================
document.addEventListener("DOMContentLoaded", function () {
    const qtyButtons = document.querySelectorAll('.product-qty-btn');

    qtyButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            // Find the closest service card to scope the price updates
            const serviceCard = this.closest('.service-card');
            if (!serviceCard) return;

            const isAdd = this.classList.contains('add-btn');
            const itemContainer = this.closest('.d-flex.align-items-center.justify-content-end');
            const qtyDisplay = itemContainer.querySelector('.product-qty-display');

            if (!qtyDisplay) return; // Safeguard if not found

            let currentQty = parseInt(qtyDisplay.innerText) || 0;
            const productPrice = parseInt(this.getAttribute('data-price')) || 0;

            // Handle Logic (Max 1)
            let qtyChanged = false;
            let priceChange = 0;

            if (isAdd && currentQty < 1) {
                currentQty++;
                priceChange = productPrice;
                qtyChanged = true;

                // Styling updates
                this.classList.remove('bg-blue');
                this.classList.add('bg-secondary'); // Disable add styling visually

                const removeBtn = itemContainer.querySelector('.remove-btn');
                if (removeBtn) {
                    removeBtn.classList.remove('bg-secondary');
                    removeBtn.classList.add('bg-blue'); // Enable remove styling visually
                }
            } else if (!isAdd && currentQty > 0) {
                currentQty--;
                priceChange = -productPrice;
                qtyChanged = true;

                // Styling updates
                this.classList.remove('bg-blue');
                this.classList.add('bg-secondary'); // Disable remove styling visually

                const addBtn = itemContainer.querySelector('.add-btn');
                if (addBtn) {
                    addBtn.classList.remove('bg-secondary');
                    addBtn.classList.add('bg-blue'); // Enable add styling visually
                }
            }

            if (qtyChanged) {
                // Update specific product display
                qtyDisplay.innerText = currentQty;

                // Update Master Service Price
                const priceElement = serviceCard.querySelector('.service-price');
                if (priceElement) {
                    // Check if base price is stored, if not store it
                    let basePrice = parseInt(priceElement.getAttribute('data-base'));
                    if (isNaN(basePrice)) {
                        basePrice = parseInt(priceElement.innerText);
                        priceElement.setAttribute('data-base', basePrice);
                    }

                    // Calculate current total from base + all active products in this service card
                    let totalExtras = 0;
                    const allQtyDisplays = serviceCard.querySelectorAll('.product-qty-display');
                    allQtyDisplays.forEach(display => {
                        const qty = parseInt(display.innerText) || 0;
                        if (qty > 0) {
                            // Find corresponding add button to get its price
                            const btn = display.closest('.d-flex').querySelector('.add-btn');
                            if (btn) {
                                totalExtras += parseInt(btn.getAttribute('data-price')) || 0;
                            }
                        }
                    });

                    const newTotal = basePrice + totalExtras;

                    // Keep the SA icon and store current total for grand total calculation
                    const iconHtml = priceElement.innerHTML.match(/<i[^>]*>[\s\S]*?<\/i>/);
                    priceElement.innerHTML = `${formatMoneyMinorUnits(newTotal)} ${iconHtml ? iconHtml[0] : ''}`;
                    priceElement.setAttribute('data-current', newTotal);
                }

                // Update grand total globally
                if (typeof updateGrandTotal === 'function') {
                    updateGrandTotal();
                }
            }
        });
    });

    // Money amounts in data-* attributes are minor units (e.g. halalas); display as decimal SAR.
    function formatMoneyMinorUnits(amount) {
        const n = parseInt(amount, 10);
        if (isNaN(n)) return '0.00';
        return (n / 100).toFixed(2);
    }

    // --- Grand Total Calculation Logic ---
    function updateGrandTotal() {
        let grandTotal = 0;
        const checkedServices = document.querySelectorAll('.service-checkbox:checked');
        checkedServices.forEach(checkbox => {
            const card = checkbox.nextElementSibling;
            if (card && card.classList.contains('service-card')) {
                const priceElement = card.querySelector('.service-price');
                if (priceElement) {
                    const current = parseInt(priceElement.getAttribute('data-current'), 10) || parseInt(priceElement.getAttribute('data-base'), 10) || 0;
                    grandTotal += current;
                }
            }
        });

        const grandTotalElement = document.querySelector('.booking-total');
        if (grandTotalElement) {
            const iconHtml = grandTotalElement.innerHTML.match(/<i[^>]*>[\s\S]*?<\/i>/);
            grandTotalElement.innerHTML = `${formatMoneyMinorUnits(grandTotal)} ${iconHtml ? iconHtml[0] : '<i class="icon-saudi_riyal"></i>'}`;
        }

        // Provider booking bar: show when services selected, populate add-to-cart form
        const bookingBar = document.getElementById('provider-booking-bar');
        if (bookingBar) {
            const totalEl = bookingBar.querySelector('[data-booking-total]');
            const seatInput = document.getElementById('provider-cart-seat-id');
            const servicesContainer = document.getElementById('provider-cart-services-inputs');
            const fees = parseFloat(bookingBar.dataset.reservationFees) || 0;

            if (checkedServices.length > 0) {
                bookingBar.classList.remove('d-none');
                if (totalEl) {
                    const icon = totalEl.innerHTML.match(/<i[^>]*>[\s\S]*?<\/i>/);
                    totalEl.innerHTML = formatMoneyMinorUnits(grandTotal) + (icon ? ' ' + icon[0] : ' <i class="icon-saudi_riyal"></i>');
                }
                let primarySeatId = null;
                const servicesData = [];
                checkedServices.forEach(function (cb) {
                    const card = cb.nextElementSibling;
                    if (card && card.classList.contains('service-card')) {
                        const sid = card.getAttribute('data-service-id');
                        const chairWrap = card.closest('[data-chair-id]');
                        const seatId = chairWrap ? chairWrap.getAttribute('data-chair-id') : null;
                        if (!primarySeatId) primarySeatId = seatId;
                        if (sid && seatId === primarySeatId) {
                            const products = [];
                            const productItems = card.querySelectorAll('.product-item');
                            productItems.forEach(function (prodEl) {
                                const qtyDisplay = prodEl.querySelector('.product-qty-display');
                                const addBtn = prodEl.querySelector('.add-btn');
                                const qty = parseInt(qtyDisplay?.innerText || '0', 10) || 0;
                                const pid = addBtn?.getAttribute('data-product-id');
                                if (pid && qty > 0) {
                                    products.push({ id: pid, quantity: qty });
                                }
                            });
                            servicesData.push({ id: sid, products: products });
                        }
                    }
                });
                const seatId = primarySeatId;
                if (seatInput) seatInput.value = seatId || '';
                if (servicesContainer) {
                    servicesContainer.innerHTML = '';
                    servicesData.forEach(function (s, i) {
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'services[' + i + '][id]';
                        idInput.value = s.id;
                        servicesContainer.appendChild(idInput);
                        (s.products || []).forEach(function (p, j) {
                            const pidInput = document.createElement('input');
                            pidInput.type = 'hidden';
                            pidInput.name = 'services[' + i + '][products][' + j + '][id]';
                            pidInput.value = p.id;
                            servicesContainer.appendChild(pidInput);
                            const qtyInput = document.createElement('input');
                            qtyInput.type = 'hidden';
                            qtyInput.name = 'services[' + i + '][products][' + j + '][quantity]';
                            qtyInput.value = p.quantity || 1;
                            servicesContainer.appendChild(qtyInput);
                        });
                    });
                }
            } else {
                bookingBar.classList.add('d-none');
                if (seatInput) seatInput.value = '';
                if (servicesContainer) servicesContainer.innerHTML = '';
            }
        }
    }

    // Attach change event to all service checkboxes to recalculate grand total on select/deselect
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
    serviceCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateGrandTotal);
    });

    // When guest clicks Book, redirect to login instead of submitting (preserves intended URL)
    const addToCartForm = document.getElementById('provider-add-to-cart-form');
    if (addToCartForm) {
        const bookingBar = document.getElementById('provider-booking-bar');
        addToCartForm.addEventListener('submit', function (e) {
            if (bookingBar && bookingBar.dataset.isGuest === '1') {
                e.preventDefault();
                const loginUrl = bookingBar.dataset.loginUrl || (bookingBar.dataset.addToCartUrl || '').replace(/\/cart\/add.*/, '/login');
                window.location.href = loginUrl;
            }
        });
    }

    // Initialize grand total when page loads
    updateGrandTotal();

    // --- Clear previous seat selections when switching seat tabs ---
    const chairTabTriggers = document.querySelectorAll('.chair-tabs [data-bs-toggle="tab"]');
    chairTabTriggers.forEach(function (trigger) {
        trigger.addEventListener('shown.bs.tab', function (e) {
            const previousTab = e.relatedTarget;
            if (!previousTab || !previousTab.getAttribute('data-bs-target')) return;
            const previousPane = document.querySelector(previousTab.getAttribute('data-bs-target'));
            if (!previousPane) return;
            // Uncheck all service checkboxes in the previous tab
            previousPane.querySelectorAll('.service-checkbox:checked').forEach(function (cb) {
                cb.checked = false;
            });
            // Reset product quantities to 0 in the previous tab
            previousPane.querySelectorAll('.product-qty-display').forEach(function (el) {
                el.innerText = '0';
            });
            previousPane.querySelectorAll('.product-qty-btn.add-btn').forEach(function (btn) {
                btn.classList.remove('bg-secondary');
                btn.classList.add('bg-blue');
            });
            previousPane.querySelectorAll('.product-qty-btn.remove-btn').forEach(function (btn) {
                btn.classList.remove('bg-blue');
                btn.classList.add('bg-secondary');
            });
            // Reset service prices to base (products were cleared so price = base)
            previousPane.querySelectorAll('.service-price').forEach(function (el) {
                const base = el.getAttribute('data-base');
                if (base) {
                    el.setAttribute('data-current', base);
                    const iconHtml = el.innerHTML.match(/<i[^>]*>[\s\S]*?<\/i>/);
                    el.innerHTML = formatMoneyMinorUnits(base) + ' ' + (iconHtml ? iconHtml[0] : '<i class="icon-saudi_riyal"></i>');
                }
            });
            updateGrandTotal();
        });
    });

    // --- Accordion Plus/Minus Icon Toggle ---
    const accordions = document.querySelectorAll('.collapse');
    accordions.forEach(acc => {
        acc.addEventListener('show.bs.collapse', function () {
            const toggleBtn = document.querySelector(`[data-bs-target="#${this.id}"]`);
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('.accordion-icon');
                if (icon) {
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-minus');
                }
            }
        });
        acc.addEventListener('hide.bs.collapse', function () {
            const toggleBtn = document.querySelector(`[data-bs-target="#${this.id}"]`);
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('.accordion-icon');
                if (icon) {
                    icon.classList.remove('fa-minus');
                    icon.classList.add('fa-plus');
                }
            }
        });
    });

    // ==========================================
    // Pricing Toggle Logic (static pages: two amounts in .annual-price / .quarterly-price)
    // Skip when tabs live inside a Livewire component (e.g. join flow) — otherwise this hides .annual-price
    // for non-first tabs while the server renders one price in .annual-price only.
    // ==========================================
    const pricingTabs = document.querySelectorAll('.desc-tabs .nav-link');
    const descTabsEl = document.querySelector('.desc-tabs');
    const skipStaticPricingToggle = descTabsEl && descTabsEl.closest('[wire\\:id]');
    if (pricingTabs.length > 0 && !skipStaticPricingToggle) {
        // Init state: hide quarterly
        document.querySelectorAll('.package-price').forEach(priceContainer => {
            const annualPrice = priceContainer.querySelector('.annual-price');
            const quarterlyPrice = priceContainer.querySelector('.quarterly-price');
            if (annualPrice && quarterlyPrice) {
                quarterlyPrice.style.display = 'none';
            }
        });

        pricingTabs.forEach((tab, index) => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                // Remove active class from all tabs
                pricingTabs.forEach(t => t.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');

                // Toggle prices based on index (0 = Annual, 1 = Quarterly)
                const isAnnual = index === 0;

                document.querySelectorAll('.package-price').forEach(priceContainer => {
                    const annualPrice = priceContainer.querySelector('.annual-price');
                    const quarterlyPrice = priceContainer.querySelector('.quarterly-price');

                    if (annualPrice && quarterlyPrice) {
                        if (isAnnual) {
                            annualPrice.style.display = 'block';
                            quarterlyPrice.style.display = 'none';
                        } else {
                            annualPrice.style.display = 'none';
                            quarterlyPrice.style.display = 'block';
                        }
                    }
                });
            });
        });
    }

});