$(window).on("load", function () {
    $("body").removeClass("overflow");
});
$(document).ready(function () {
    /************************************ Fixed Header ************************************/
    $(window).scroll(function () {
        $(this).scrollTop() >= 150
            ? $("header:not(.auth-header,.success-header)").addClass("fixed")
            : $("header:not(.auth-header,.success-header)").removeClass("fixed ");
    });
    /************************************ Side Menu ************************************/
    $(".menu-btn").on("click", (e) => {
        if (e.isDefaultPrevented()) return;
        e.preventDefault();
        e.stopPropagation();
        $(".overlay").fadeIn(300);
        $(".header-nav").addClass("active");
        $("body").addClass("overflow");
    });

    $(".menu-close,.overlay").on("click", (e) => {
        if (e.isDefaultPrevented()) return;
        e.preventDefault();
        e.stopPropagation();
        $(".overlay").fadeOut(300);
        $(".header-nav").removeClass("active");
        $("body").removeClass("overflow");
    });

    $(".nav-lang .lang-head").click(function (e) {
        $(this).siblings(".lang-list").slideToggle();
        $(this).toggleClass("active");
    });
    /************************************ Footer Accordion ************************************/
    if ($(window).width() <= 767) {
        $(".footer-title.acc-title").click(function () {
            $(".footer-title.acc-title").not(this).removeClass("active");
            $(this).toggleClass("active");
            if ($(this).siblings().css("display") == "none") {
                $(this).siblings().slideDown(500);
            } else {
                $(this).siblings().slideUp(500);
            }
            $(".footer-title.acc-title").not(this).siblings().slideUp(500);
        });
    }
    /************************************ Main Slider ************************************/
    var mainSwiper = new Swiper(".main-slider .swiper", {
        a11y: {
            enabled: false,
        },
        spaceBetween: 10,
        loop: false,
        a11y: {
            enabled: false,
        },
        // autoplay: {
        //   delay: 5000,
        // },
        // effect: "fade",
        pagination: {
            el: ".main-slider .main-pagination",
            clickable: true,
        },
        on: {
            init: function (swiper) {
                lazyLoad();
            },
        },
    });

    /************************************ Specialties Slider ************************************/
    var specialtiesSwiper = new Swiper(".specialties-slider .swiper", {
        a11y: {
            enabled: false,
        },
        spaceBetween: 10,
        breakpoints: {
            0: {
                slidesPerView: 3,
                spaceBetween: 10,
                loop: false,
            },
            480: {
                slidesPerView: 4,
                spaceBetween: 10,
                loop: false,
            },
            767: {
                slidesPerView: 6,
                spaceBetween: 15,
                loop: false,
            },
            992: {
                slidesPerView: 8,
                spaceBetween: 15,
                loop: false,
            },
            1199: {
                slidesPerView: 10,
                spaceBetween: 15,
                loop: false,
            },
        },
        pagination: {
            el: ".specialties-slider .slider-pagination",
            clickable: true,
        },
        on: {
            init: function (swiper) {
                lazyLoad();
            },
        },
    });

    /************************************ Doctors Slider ************************************/
    var doctorsSwiper = new Swiper(".doctors-slider .swiper", {
        a11y: {
            enabled: false,
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: false,
                grid: false,
            },
            992: {
                slidesPerView: 2,
                spaceBetween: 15,
                loop: false,
            },
            1199: {
                slidesPerView: 2,
                spaceBetween: 30,
                loop: false,
                grid: {
                    rows: 2,
                    fill: "rows",
                },
            },
        },
        pagination: {
            el: ".doctors-slider .slider-pagination",
            clickable: true,
        },
        on: {
            init: function (swiper) {
                lazyLoad();
            },
        },
    });

    /************************************ Labs Slider ************************************/
    var labsSwiper = new Swiper(".labs-slider .swiper", {
        a11y: {
            enabled: false,
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: false,
                grid: false,
            },
            992: {
                slidesPerView: 2,
                spaceBetween: 15,
                loop: false,
            },
            1199: {
                slidesPerView: 2,
                spaceBetween: 30,
                loop: false,
                grid: {
                    rows: 2,
                    fill: "rows",
                },
            },
        },
        pagination: {
            el: ".labs-slider .slider-pagination",
            clickable: true,
        },
        on: {
            init: function (swiper) {
                lazyLoad();
            },
        },
    });

    /************************************ blog Slider ************************************/
    var blogSwiper = new Swiper(".blog-slider .swiper", {
        a11y: {
            enabled: false,
        },
        loop: false,
        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 15,
            },
            480: {
                slidesPerView: 2,
                spaceBetween: 15,
            },
            767: {
                slidesPerView: 3,
                spaceBetween: 15,
            },
            992: {
                slidesPerView: 4,
                spaceBetween: 15,
            },
            1199: {
                slidesPerView: 4,
                spaceBetween: 31,
            },
        },
        pagination: {
            el: ".blog-slider .slider-pagination",
            clickable: true,
        },
        on: {
            init: function (swiper) {
                lazyLoad();
            },
        },
    });

    /************************************ Filters ************************************/
    $(".filter-trigger").click(function (e) {
        $(this).parents(".archive-sidebar").toggleClass("active");
    });
    $(".filter-btn").click(function (e) {
        $(".filter-btn").removeClass("active");
        $(this).addClass("active");
        let filter = $(this).attr("id");
        if (filter == "all") {
            $("[data-filter]").show();
        } else {
            $("[data-filter]").hide();
            $("[data-filter=" + filter + "]").show();
        }
    });
    $(".filter-item .filter-title").click(function (e) {
        $(this).siblings(".filter-values").slideToggle();
        $(this).toggleClass("active");
    });

    $(".search-filter .search-select").change(function (e) {
        let filter = $(this).val();

        if (filter == "all") {
            $(".reservation-content").show();
        } else {
            $(".reservation-content").hide();
            $(".reservation-content[data-filter=" + filter + "]").show();
        }
    });

    $(".categories-title").click(function (e) {
        $(this).siblings(".categories-list").slideToggle();
        $(this).toggleClass("active");
    });
    /************************************ Account Nav ************************************/
    $(".accountNav-trigger").click(function (e) {
        $(this).parents(".account-nav").toggleClass("active");
    });
    $(".main-link.has-list").click(function (e) {
        $(this).toggleClass("active").siblings(".sub-list").slideToggle();
    });
    /************************************ Form ************************************/

    $(".password-toggle").click(function (e) {
        if (e.isDefaultPrevented()) return;
        e.preventDefault();
        e.stopPropagation();
        let eye = $(this);
        let input = $(this).parents(".password-content").find("input.form-control");
        if ($(input).attr("type") == "password") {
            $(input).attr("type", "text");
            eye.addClass("active");
        } else {
            $(input).attr("type", "password");
            eye.removeClass("active");
        }
    });





    /************************************ OTP ************************************/
    const inputs = $("#otp-input input");

    inputs.on("input", function () {
        const index = inputs.index(this);

        // handling normal input
        if (this.value.length == 1 && index + 1 < inputs.length) {
            $(inputs[index + 1]).removeAttr("disabled");
            inputs[index + 1].focus();
        } else {
            inputs.blur();
            $(".otp-content .main-btn").removeAttr("disabled");
        }

        // if a value is pasted, put each character to each of the next input
        if (this.value.length > 1) {
            // sanitize input
            if (isNaN(this.value)) {
                this.value = "";
                updateInput();
                return;
            }

            // split characters to array
            const chars = this.value.split("");

            $.each(chars, function (pos) {
                // if length exceeded the number of inputs, stop
                if (pos + index >= inputs.length) return false;

                // paste value
                let targetInput = inputs[pos + index];
                targetInput.value = chars[pos];
            });

            // focus the input next to the last pasted character
            let focusIndex = Math.min(inputs.length - 1, index + chars.length);
            inputs[focusIndex].focus();
        }
        updateInput();
    });

    inputs.on("keydown", function (e) {
        const index = inputs.index(this);

        // backspace button
        if (e.keyCode == 8 && this.value == "" && index != 0) {
            // shift next values towards the left
            for (let pos = index; pos < inputs.length - 1; pos++) {
                inputs[pos].value = inputs[pos + 1].value;
            }

            // clear previous box and focus on it
            inputs[index - 1].value = "";
            inputs[index - 1].focus();
            updateInput();
            return;
        }

        // delete button
        if (e.keyCode == 46 && index != inputs.length - 1) {
            // shift next values towards the left
            for (let pos = index; pos < inputs.length - 1; pos++) {
                inputs[pos].value = inputs[pos + 1].value;
            }

            // clear the last box
            inputs[inputs.length - 1].value = "";
            this.select();
            e.preventDefault();
            updateInput();
            return;
        }

        // left button
        if (e.keyCode == 37) {
            if (index > 0) {
                e.preventDefault();
                inputs[index - 1].focus();
                inputs[index - 1].select();
            }
            return;
        }

        // right button
        if (e.keyCode == 39) {
            if (index + 1 < inputs.length) {
                e.preventDefault();
                inputs[index + 1].focus();
                inputs[index + 1].select();
            }
            return;
        }
    });

    function updateInput() {
        let inputValue = inputs.toArray().reduce(function (otp, input) {
            otp += input.value.length ? input.value : " ";
            return otp;
        }, "");
        $("input[name=otp]").val(inputValue);
    }
});
