/**
 * ============================================
 * main.js - فایل اصلی جاوااسکریپت پروژه فن‌آموز
 * ============================================
 * نسخه ۲.۰ - با پشتیبانی کامل از Tom Select در تمام شرایط
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================
    // بخش ۱: Tom Select (با MutationObserver)
    // ============================================

    /**
     * تابع فعال‌سازی Tom Select روی یک المان
     */
    // ============================================
// Tom Select - نسخه نهایی تضمینی
// ============================================



// اجرا در DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    // اجرای اولیه
    if (typeof TomSelect !== 'undefined') {
        initTomSelects();
        // اجرای مجدد با تأخیر برای المان‌های داینامیک
        setTimeout(initTomSelects, 300);
        setTimeout(initTomSelects, 1000);
    }
});

// برای مودال‌های Bootstrap
document.addEventListener('shown.bs.modal', function(e) {
    initTomSelects(e.target);
});

// برای هر تغییر در DOM (MutationObserver)
if (window.MutationObserver) {
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) {
                    if (node.matches && node.matches('select.searchable-select, select.form-select[data-searchable], select:not([data-no-search])')) {
                        initTomSelects(node.parentNode);
                    }
                    if (node.querySelectorAll) {
                        var inside = node.querySelectorAll('select.searchable-select, select.form-select[data-searchable], select:not([data-no-search])');
                        if (inside.length) {
                            initTomSelects(node);
                        }
                    }
                }
            });
        });
    });
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

    /**
     * تابع اسکن و فعال‌سازی تمام selectهای قابل جستجو
     */
    function initTomSelects(container) {
        container = container || document;
        var selectors = container.querySelectorAll('select.searchable-select, select.form-select[data-searchable], select:not([data-no-search])');
        selectors.forEach(function (el) {
            applyTomSelect(el);
        });
    }

    // اجرای اولیه
    if (typeof TomSelect !== 'undefined') {
        initTomSelects();
    }

    // مشاهده‌گر تغییرات DOM (برای selectهایی که بعداً اضافه می‌شوند)
    if (window.MutationObserver) {
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    // اگر المان اضافه شده یک select باشد
                    if (node.nodeType === 1 && node.matches && node.matches('select.searchable-select, select.form-select[data-searchable], select:not([data-no-search])')) {
                        applyTomSelect(node);
                    }
                    // اگر داخل آن selectهایی وجود دارد
                    if (node.querySelectorAll) {
                        var insideSelects = node.querySelectorAll('select.searchable-select, select.form-select[data-searchable], select:not([data-no-search])');
                        insideSelects.forEach(function (el) {
                            applyTomSelect(el);
                        });
                    }
                });
            });
        });
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // برای مودال‌های Bootstrap که با نمایش فعال می‌شوند
    document.addEventListener('shown.bs.modal', function (event) {
        initTomSelects(event.target);
    });

    // ============================================
    // بخش ۲: سایر کدهای عمومی
    // ============================================

    // ---------- بستن خودکار اعلان‌ها ----------
    document.querySelectorAll('.alert-dismissible').forEach(function (el) {
        setTimeout(function () {
            var btn = el.querySelector('.btn-close');
            if (btn) btn.click();
        }, 6000);
    });

    // ---------- مدیریت استان و دانشکده (AJAX) ----------
    var provinceEl = document.getElementById('province_id');
    var institutionEl = document.getElementById('institution_id');

    if (provinceEl && institutionEl && provinceEl.dataset.ajaxInstitutions !== '0') {
        provinceEl.addEventListener('change', function () {
            var apiUrl = provinceEl.dataset.institutionsUrl || '';
            if (!apiUrl) return;

            institutionEl.innerHTML = '<option value="">در حال بارگذاری...</option>';
            institutionEl.disabled = true;

            if (institutionEl.tomselect) {
                institutionEl.tomselect.destroy();
            }

            if (!this.value) {
                institutionEl.innerHTML = '<option value="">ابتدا استان را انتخاب کنید</option>';
                institutionEl.disabled = false;
                return;
            }

            fetch(apiUrl + '?province_id=' + encodeURIComponent(this.value))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    institutionEl.innerHTML = '<option value="">انتخاب دانشکده</option>';
                    data.forEach(function (i) {
                        var opt = document.createElement('option');
                        opt.value = i.id;
                        opt.textContent = i.name;
                        institutionEl.appendChild(opt);
                    });
                    institutionEl.disabled = false;
                    applyTomSelect(institutionEl);
                })
                .catch(function () {
                    institutionEl.innerHTML = '<option value="">خطا در بارگذاری</option>';
                    institutionEl.disabled = false;
                });
        });
    }

    // ---------- دکمه چشم ویدئویی ----------
    var eyeVideo = document.getElementById('eyeVideo');
    var eyeContainer = document.querySelector('.btn-eye-wrapper');
    var isOpen = false;

    if (eyeVideo && eyeContainer) {
        var videoSrc = eyeVideo.querySelector('source');

        function playEyeVideo(videoPath, onEndCallback) {
            videoSrc.src = videoPath;
            eyeVideo.load();
            eyeVideo.play();
            eyeVideo.onended = function () {
                if (onEndCallback) onEndCallback();
                eyeVideo.currentTime = eyeVideo.duration - 0.05;
            };
        }

        eyeContainer.addEventListener('click', function (e) {
            e.preventDefault();
            var passwordInput = document.getElementById('password');
            if (!passwordInput) return;

            if (!isOpen) {
                playEyeVideo(eyeVideo.getAttribute('data-open-video'), function () {
                    passwordInput.type = 'text';
                    isOpen = true;
                });
            } else {
                playEyeVideo(eyeVideo.getAttribute('data-close-video'), function () {
                    passwordInput.type = 'password';
                    isOpen = false;
                });
            }
        });
    }

    // ---------- دکمه نمایش رمز (ساده) ----------
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;
            var icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            }
        });
    });

    // ============================================
    // بخش ۳: کدهای jQuery
    // ============================================

    if (typeof jQuery !== 'undefined') {
        (function ($) {
            "use strict";

            // ---------- Preloader ----------
            $(window).on('load', function () {
                $('.js-preloader').delay(500).fadeOut(500);
            });

            // ---------- جستجو ----------
            $('.searchbtn').on('click', function () {
                $('.search-area').toggleClass('open');
            });
            $('.close-searchbox').on('click', function () {
                $('.search-area').removeClass('open');
            });

            // ---------- منوی زبان ----------
            $(".language-option").each(function () {
                var each = $(this);
                each.find(".lang-name").html(each.find(".language-dropdown-menu a:nth-child(1)").text());
                var allOptions = $(".language-dropdown-menu").children('a');
                each.find(".language-dropdown-menu").on("click", "a", function () {
                    allOptions.removeClass('selected');
                    $(this).addClass('selected');
                    $(this).closest(".language-option").find(".lang-name").html($(this).text());
                });
            });

            $('.user-option').on('click', function () {
                $('.user-menuitem').toggleClass('open');
            });

            // ---------- شمارنده ----------
            $(".odometer").appear(function () {
                var odo = $(".odometer");
                odo.each(function () {
                    var countNumber = $(this).attr("data-count");
                    $(this).html(countNumber);
                });
            });

            // ---------- نوار پیشرفت ----------
            $(window).scroll(function () {
                $('.progress-bar').each(function () {
                    $(this).find('.progress-content').animate({
                        width: $(this).attr('data-percentage')
                    }, 2000);

                    $(this).find('.progress-number-mark').animate({ right: $(this).attr('data-percentage') }, {
                        duration: 2000,
                        step: function (now) {
                            var data = Math.round(now);
                            $(this).find('.percent').html(data + '%');
                        }
                    });
                });
            });

// ============================================
// فعال‌سازی swipe برای کروسل‌های بوت‌استرپ
// ============================================
document.querySelectorAll('.carousel').forEach(function(carousel) {
    var startX, startY;
    var isSwiping = false;

    // رویدادهای لمسی (موبایل)
    carousel.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        isSwiping = false;
    }, { passive: true });

    carousel.addEventListener('touchmove', function(e) {
        if (!startX || !startY) return;
        var diffX = e.touches[0].clientX - startX;
        var diffY = e.touches[0].clientY - startY;
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 30) {
            isSwiping = true;
            e.preventDefault();
            var bsCarousel = bootstrap.Carousel.getInstance(carousel);
            if (bsCarousel) {
                if (diffX > 0) {
                    bsCarousel.prev();
                } else {
                    bsCarousel.next();
                }
            }
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        }
    }, { passive: false });

    carousel.addEventListener('touchend', function() {
        startX = startY = null;
    }, { passive: true });

    // رویدادهای موس (دسکتاپ - درگ با کلیک چپ)
    var mouseDown = false;
    var mouseStartX = 0;

    carousel.addEventListener('mousedown', function(e) {
        if (e.button === 0) {
            mouseDown = true;
            mouseStartX = e.clientX;
        }
    });

    carousel.addEventListener('mousemove', function(e) {
        if (!mouseDown) return;
        var diffX = e.clientX - mouseStartX;
        if (Math.abs(diffX) > 30) {
            mouseDown = false;
            var bsCarousel = bootstrap.Carousel.getInstance(carousel);
            if (bsCarousel) {
                if (diffX > 0) {
                    bsCarousel.prev();
                } else {
                    bsCarousel.next();
                }
            }
        }
    });

    carousel.addEventListener('mouseup', function() {
        mouseDown = false;
    });

    carousel.addEventListener('mouseleave', function() {
        mouseDown = false;
    });
});

            // ---------- هدر موبایل ----------
            $('.mobile-top-bar').on('click', function () {
                $('.header-top-right').addClass('open');
            });
            $('.close-header-top').on('click', function () {
                $('.header-top-right').removeClass('open');
            });

            // ---------- هدر چسبنده ----------
            var wind = $(window);
            var sticky = $('.header-wrap');
            wind.on('scroll', function () {
                var scroll = wind.scrollTop();
                if (scroll < 100) {
                    sticky.removeClass('sticky');
                } else {
                    sticky.addClass('sticky');
                }
            });

            // ---------- منوی موبایل ----------
            $(window).on('resize', function () {
                if ($(window).width() <= 1199) {
                    $('.collapse.navbar-collapse').removeClass('collapse');
                } else {
                    $('.navbar-collapse').addClass('collapse');
                }
            });

            $('.mobile-menu a').on('click', function () {
                $('.main-menu-wrap').addClass('open');
                $('.collapse.navbar-collapse').removeClass('collapse');
            });

            $('.mobile_menu a').on('click', function () {
                $(this).parent().toggleClass('open');
                $('.main-menu-wrap').toggleClass('open');
            });

            $('.menu-close').on('click', function () {
                $('.main-menu-wrap').removeClass('open');
            });

            $('.mobile-top-bar').on('click', function () {
                $('.header-top').addClass('open');
            });

            $('.close-header-top button').on('click', function () {
                $('.header-top').removeClass('open');
            });

            // ---------- منوی کشویی ----------
            var $offcanvasNav = $('.navbar-nav');
            var $offcanvasNavSubMenu = $offcanvasNav.find('.dropdown-menu');

            $offcanvasNavSubMenu.parent().prepend('<span class="menu-expand"><i class="ri-arrow-down-s-line"></i></span>');
            $offcanvasNavSubMenu.slideUp();

            $offcanvasNav.on('click', 'li a, li .menu-expand', function (e) {
                var $this = $(this);
                if (($this.attr('href') === '#' || $this.hasClass('menu-expand'))) {
                    e.preventDefault();
                    if ($this.siblings('ul:visible').length) {
                        $this.siblings('ul').slideUp('slow');
                    } else {
                        $this.closest('li').siblings('li').find('ul:visible').slideUp('slow');
                        $this.siblings('ul').slideDown('slow');
                    }
                }
                if ($this.is('a') || $this.is('span') || $this.attr('class').match(/\b(menu-expand)\b/)) {
                    $this.parent().toggleClass('menu-open');
                } else if ($this.is('li') && $this.attr('class').match(/\b('dropdown-menu')\b/)) {
                    $this.toggleClass('menu-open');
                }
            });

            // ---------- اسکرول انیمیشن ----------
            if (typeof AOS !== 'undefined') {
                AOS.init();
            }

            // ---------- دکمه بازگشت به بالا ----------
            function BackToTop() {
                $('.back-to-top').on('click', function () {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 100);
                    return false;
                });

                $(document).scroll(function () {
                    var y = $(this).scrollTop();
                    if (y > 600) {
                        $('.back-to-top').fadeIn();
                        $('.back-to-top').addClass('open');
                    } else {
                        $('.back-to-top').fadeOut();
                        $('.back-to-top').removeClass('open');
                    }
                });
            }
            BackToTop();

        })(jQuery);
    }
	// تابع یک‌دست برای اعمال Tom Select
function applyTomSelect(el) {
    if (!el || el.tomselect || el.disabled) return;
    try {
        new TomSelect(el, {
            create: false,
            allowEmptyOption: true,
            maxOptions: 500,
            placeholder: el.getAttribute('data-placeholder') || 'جستجو و انتخاب...',
            render: {
                no_results: function() {
                    return '<div class="no-results px-2 py-1">موردی یافت نشد</div>';
                }
            }
        });
    } catch(e) {
        console.warn('خطا در TomSelect:', e);
    }
}

// راه‌اندازی اولیه
function initTomSelects(scope) {
    scope = scope || document;
    scope.querySelectorAll('select.searchable-select, select.form-select[data-searchable], select:not([data-no-search])').forEach(applyTomSelect);
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof TomSelect !== 'undefined') {
        initTomSelects();
        // چندبار تلاش برای المان‌های داینامیک
        setTimeout(initTomSelects, 300);
        setTimeout(initTomSelects, 1000);
    }
});

// MutationObserver برای selectهای اضافه‌شده بعدی
if (window.MutationObserver) {
    new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) {
                    if (node.matches && node.matches('select.searchable-select, select:not([data-no-search])')) {
                        applyTomSelect(node);
                    }
                    if (node.querySelectorAll) {
                        node.querySelectorAll('select.searchable-select, select:not([data-no-search])').forEach(applyTomSelect);
                    }
                }
            });
        });
    }).observe(document.body, { childList: true, subtree: true });
}

// پشتیبانی از مودال‌های بوت‌استرپ
document.addEventListener('shown.bs.modal', function(e) {
    initTomSelects(e.target);
});



}); // پایان DOMContentLoaded