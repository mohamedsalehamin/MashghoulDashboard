jQuery(function () {
    "use strict";

    // ************************ Main Slider ************************
    //     var rtlVal = false;
    //     jQuery('html').attr("dir") == "ltr" ? rtlVal = false : rtlVal = true;
    //     //
    //     jQuery('.main-slider').owlCarousel({
    //         rtl: rtlVal,
    //         margin: 0,
    //         smartSpeed: 1500,
    //         autoplayTimeout:3000,
    //         nav: false,
    //         dots: false,
    //         loop: true,
    //         autoplay: true,
    //         mouseDrag: true,
    //         touchDrag: true,
    //         rewind: true,
    //         animateOut: 'animate__zoomOut',
    //         animateIn: 'animate__zoomIn',
    //         responsive: {
    //             0: {
    //                 items: 1
    //             },
    //             768: {
    //                 items: 1
    //             },
    //             992: {
    //                 items: 1
    //             }
    //         },
    //     });


    // ************************ for language ************************
    let currentLanguage = 'ar';
    //
    // jQuery('#language-switcher').click(function (e) {
    //     e.preventDefault();
    //
    //     const currentPage = window.location.pathname;
    //     const isArabic = currentPage.includes('_en');
    //
    //     if (isArabic) {
    //         const arabicPage = currentPage.replace('_en', '');
    //         window.location.href = arabicPage;
    //     } else {
    //         const parts = currentPage.split('/');
    //         const pageName = parts.pop();
    //         const englishPage = pageName.includes('.html') ? pageName.replace('.html', '_en.html') : `${pageName}_en.html`;
    //         window.location.href = `${parts.join('/')}/${englishPage}`;
    //     }
    // });


    // ************************ fixed menu ************************
    jQuery(window).scroll(function () {

        if (jQuery(window).scrollTop() > 30) {
            jQuery('header').addClass('stick');
        } else {
            jQuery('header').removeClass('stick');
        }
    });

    // ************************ smooth scroll ************************
    // Smooth Scroll to Section
    // Smooth Scroll to Section
    // jQuery('.link a').on('click', function (event) {
    //     event.preventDefault();
    //
    //     jQuery('html, body').animate({
    //         scrollTop: jQuery(jQuery(this).attr('href')).offset().top - 90
    //     }, 1000);
    // });

    jQuery('.link a').on('click', function (event) {
        event.preventDefault();

        const href = jQuery(this).attr('href');
        const isEnglish = href.includes('_en');
        const targetSelector = href.split('#')[1];
        const target = targetSelector ? jQuery(`#${targetSelector}`) : null;

        if (target && target.length) {
            const stickHeight = jQuery("header").length ? jQuery("header").height() : 0;
            const paddingTop = parseFloat(jQuery("header").css('padding-top')) || 0;
            const paddingBottom = parseFloat(jQuery("header").css('padding-bottom')) || 0;

            const offset = jQuery(window).scrollTop() < 1
                ? -(stickHeight + paddingTop + paddingBottom) * 2
                : -(3 + stickHeight + paddingTop + paddingBottom);

            const newScrollPosition = target.offset().top + offset;

            jQuery('html, body').animate({ scrollTop: newScrollPosition }, 600); // Smooth scroll
        } else {
            window.location.href = href; // Navigate to external or different page
        }
    });



// Scroll Event to Change Navbar Style and Active Class
    jQuery(window).on('scroll', function () {
        var navbar = jQuery("header"),
            wst = jQuery(window).scrollTop();

        // Changing the Navbar Style on Scroll
        if (wst > 0) {
            navbar.addClass("stick");
        } else {
            navbar.removeClass("stick");
        }

        // Sync Active Class to the Nav Links Based on Scroll Position
        jQuery(".block").each(function () {
            var blockOffset = jQuery(this).offset().top - 100;

            // If the scroll position is greater than the block offset, add active class
            if (wst > blockOffset) {
                var blockId = jQuery(this).attr("id");


                jQuery(function () {
                    const dir = jQuery('html').attr('dir'); // Detect the direction (rtl or ltr)
                    const pagePrefix = dir === 'rtl' ? 'index.html' : 'index_en.html';

                    jQuery(".link a[href='" + pagePrefix + "#" + blockId + "']").addClass("active")
                        .parent().siblings().children().removeClass("active");
                });

            }
        });
        if (wst === 0) {
            jQuery(".link a").removeClass("active");
        }

    });


    // ************************ Side menu ************************
    function addSidebar() {
        jQuery(".open-me label").addClass("active");

        var direction = jQuery("html").attr("dir");
        if (direction === "rtl") {
            jQuery("main").addClass("helpMoveRTL");
        } else {
            jQuery("main").addClass("helpMoveLTR");
        }

        jQuery(".menu-item").addClass("special");
        jQuery(".sidebar").addClass("noo");
    }

    function removeSidebar() {
        jQuery(".open-me label").removeClass("active");

        var direction = jQuery("html").attr("dir");
        if (direction === "rtl") {
            jQuery("main").removeClass("helpMoveRTL");
        } else {
            jQuery("main").removeClass("helpMoveLTR");
        }

        jQuery(".menu-item").removeClass("special");
        jQuery(".sidebar").removeClass("noo");
    }

    function toggleSubmenu() {
        if (jQuery(this).hasClass("open")) {
            jQuery(this)
                .siblings(".sub-menu")
                .animate({height: "hide", opacity: "hide"}, "slow");
            jQuery(this)
                .removeClass("open fa-solid fa-minus")
                .addClass("fa-solid fa-plus");
        } else {
            jQuery(this).parent().siblings(".menu-item").find(".sub-menu").animate(
                {
                    height: "hide",
                    opacity: "hide",
                },
                "slow",
            );
            jQuery(this)
                .parent()
                .siblings(".menu-item")
                .find("i")
                .removeClass("open fa-solid fa-minus")
                .addClass("fa-solid fa-plus");
            jQuery(this)
                .siblings(".sub-menu")
                .animate({height: "show", opacity: "show"}, "slow");
            jQuery(this)
                .removeClass("fa-solid fa-plus")
                .addClass("open fa-solid fa-minus");
        }
    }

    jQuery(".open-me label").on("change", function () {
        addSidebar();
    });
    jQuery(".close-me label").on("change", function () {
        removeSidebar();
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode === 27) {
            removeSidebar();
        }
    });
    jQuery(".dd-trigger").on("click", toggleSubmenu);


    // ************************ FAQ ************************
    jQuery('.faq-item').click(function () {
        jQuery('.faq-item').not(this).removeClass('active').find('.answer').slideUp();
        jQuery('.faq-item').not(this).find('.icon').removeClass('fas fa-minus').addClass('fas fa-plus');

        jQuery(this).toggleClass('active');
        jQuery(this).find('.answer').stop(true, true).slideToggle();

        var icon = jQuery(this).find('.icon');
        if (icon.hasClass('fa-plus')) {
            icon.removeClass('fa-plus').addClass('fa-minus');
        } else {
            icon.removeClass('fa-minus').addClass('fa-plus');
        }
    });

    // ************************ for form (focus) ************************
    jQuery(".form-control")
        .on("focus", function () {
            const inputId = jQuery(this).attr("id");
            jQuery("label[for='" + inputId + "']").addClass("active_form");
        })
        .on("blur", function () {
            const inputId = jQuery(this).attr("id");
            jQuery("label[for='" + inputId + "']").removeClass("active_form");
        });
    // INPUT FOCUS ANIMATION
    jQuery("input, textarea").focus(function () {
        jQuery(this).closest(".form-group").addClass("focus-visible");
    });
    jQuery("input, textarea").each(function () {
        if (jQuery(this).val() !== "") {
            jQuery(this).closest(".form-group").addClass("focus-visible");
        }
    });
    jQuery("input, textarea").focusout(function () {
        if (jQuery(this).val() === "") {
            jQuery(this).closest(".form-group").removeClass("focus-visible");
        }
    });

    // ************************ SCROLL TO TOP BUTTON ************************
    var btnHome = jQuery('.footer-home #to_top');
    var btnInside = jQuery('.footer-inside #to_top');

// Function to show or hide button based on scroll position
    jQuery(window).scroll(function () {
        if (jQuery(window).scrollTop() > 300) {
            jQuery(btnHome).addClass('show');
        } else {
            jQuery(btnHome).removeClass('show');
        }
        if (jQuery(window).scrollTop() > 0) {
            jQuery(btnInside).addClass('show');
        } else {
            jQuery(btnInside).removeClass('show');
        }
    });

// Scroll to top when the button is clicked
    jQuery(btnHome).on('click', function (e) {
        e.preventDefault();
        jQuery('html, body').animate({scrollTop: 0}, 300);
    });

    jQuery(btnInside).on('click', function (e) {
        e.preventDefault();
        jQuery('html, body').animate({scrollTop: 0}, 300);
    });

    // ************************ custom scroll animation for elements ************************
    if (jQuery(window).width() > 768) {
        jQuery(window).on("scroll", function () {
            var windowHeight = jQuery(window).height();
            var scrollTop = jQuery(window).scrollTop();

            jQuery(".features .features_item [class*='col-']:first-child").each(function () {
                var offsetTop = jQuery(this).offset().top;
                var elementHeight = jQuery(this).outerHeight();

                if (scrollTop + windowHeight > offsetTop && scrollTop < offsetTop + elementHeight) {
                    jQuery(this).addClass("active");
                } else {
                    jQuery(this).removeClass("active");
                }
            });
        });
    }

    if (jQuery(window).width() > 768) {
        jQuery(window).on("scroll", function () {
            var windowHeight = jQuery(window).height();
            var scrollTop = jQuery(window).scrollTop();

            jQuery(".features .features_item [class*='col-']:last-child").each(function () {
                var offsetTop = jQuery(this).offset().top;
                var elementHeight = jQuery(this).outerHeight();

                if (scrollTop + windowHeight > offsetTop && scrollTop < offsetTop + elementHeight) {
                    jQuery(this).addClass("active");
                } else {
                    jQuery(this).removeClass("active");
                }
            });
        });
    }


    //********************  Shopping Progress ********************//
    const prevBtns = jQuery(".btn-prev button");
    const nextBtns = jQuery(".btn-next button");
    const progress = jQuery("#progress");
    const shoppingSteps = jQuery(".shopping-step");
    const progressSteps = jQuery(".progress-step");

    let shoppingStepsNum = 0;

    nextBtns.each(function () {
        jQuery(this).on("click", function () {
            shoppingStepsNum++;
            updateshoppingSteps();
            updateProgressbar();
        });
    });

    prevBtns.each(function () {
        jQuery(this).on("click", function () {
            shoppingStepsNum--;
            updateshoppingSteps();
            updateProgressbar();
        });
    });

    function updateshoppingSteps() {
        shoppingSteps.each(function () {
            jQuery(this).slideUp();
        });

        jQuery(shoppingSteps[shoppingStepsNum]).slideDown();
    }

    // ************************ for checking ************************
    jQuery("#terms").on("change", function () {
        if (jQuery(this).is(":checked")) {
            jQuery("#nextButton").prop("disabled", false);
        } else {
            jQuery("#nextButton").prop("disabled", true);
        }
    });
    function updateProgressbar() {
        progressSteps.each(function (idx) {
            if (idx < shoppingStepsNum + 1) {
                jQuery(this).addClass("progress-step-active");
            } else {
                jQuery(this).removeClass("progress-step-active");
            }
        });

        const progressActive = jQuery(".progress-step-active");
        progress.css("width", ((progressActive.length - 1) / (progressSteps.length - 1)) * 100 + "%");
    }

// Initialize by hiding all steps except the first one
    jQuery(document).ready(function () {
        shoppingSteps.hide();
        jQuery(shoppingSteps[0]).show();
    });


// ************************ popUp ************************
//     jQuery('.join_Us').on('click', function (e) {
//         e.preventDefault();
//         jQuery('#joinServiceModal').fadeIn();
//     });
//
//     jQuery('#closeModal').on('click', function () {
//         jQuery('#joinServiceModal').fadeOut();
//     });
//
//     jQuery('#joinServiceForm').on('submit', function (e) {
//         e.preventDefault();
//
//         jQuery('#joinServiceForm').fadeOut(400, function () {
//             jQuery('#successMessage').fadeIn(400);
//             setTimeout(function () {
//                 jQuery('#joinServiceModal').fadeOut(400);
//             }, 2500);
//         });
//     });
//
//     jQuery(document).on('keydown', function (e) {
//         if (e.key === "Escape" || e.keyCode === 27) {
//             jQuery('#joinServiceModal').fadeOut(400);
//         }
//     });


    const sr = ScrollrevealMin({
        distance: '50px',
        duration: 1000,
        easing: 'cubic-bezier(0.215, 0.61, 0.355, 1)',
        interval: 150,
        reset: false,
    });

    // Apply animation to elements revealing from the top
    sr.reveal('.sec-tit, .about-img, .features-content, .ready-tit, .slid-tit, .slid-desc', {
        origin: 'top',
        scale: 1,
        opacity: 0,
        delay: 200,
        distance: '50px',
    });

    // Apply the animation to elements revealing from the bottom
    sr.reveal('.about-content, .feature-image-container, .ready-para, .image-slider, .btns-url', {
        origin: 'bottom',
        scale: 1,
        opacity: 0,
        delay: 200,
    });

    // Apply a different animation to elements revealing from the right
    // sr.reveal('.faq-item', {
    //   origin: 'right',
    //   distance: '100px',
    //   scale: 1.2,
    //   opacity: 0,
    //   delay: 300,
    // });


    // Callback for when an element is revealed
    sr.reveal('.sec-tit', {
        afterReveal: (el) => {
            el.classList.add('revealed');
        },
    });
});

//wow
wow = new WOW(
    {
        boxClass: 'wow',      // default
        animateClass: 'animated', // default
        offset: 0,          // default
        mobile: false,
        live: true        // default
    }
)
new WOW().init();

jQuery(".nav-list li ").addClass("link");
jQuery(".foot-menu li ").addClass("link");
