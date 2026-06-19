</main>


<!-- ========== اسکریپت‌ها به ترتیب درست ========== -->
<!-- 1. jQuery (اول) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- 2. Bootstrap JS (وابسته به jQuery نیست اما بهتر است بعد از آن بیاید) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- 3. پلاگین‌های وابسته به jQuery -->
<script src="<?= e(asset_url('js/owl.carousel.min.js')) ?>"></script>
<script src="<?= e(asset_url('js/jquery.appear.js')) ?>"></script>
<script src="<?= e(asset_url('js/jquery-magnific-popup.js')) ?>"></script>
<script src="<?= e(asset_url('js/fancybox.js')) ?>"></script>
<script src="<?= e(asset_url('js/odometer.min.js')) ?>"></script>
<script src="<?= e(asset_url('js/progressbar.min.js')) ?>"></script>
<script src="<?= e(asset_url('js/tweenmax.min.js')) ?>"></script>

<!-- 4. سایر کتابخانه‌های مستقل -->
<script src="<?= e(asset_url('js/aos.js')) ?>"></script>
<script src="<?= e(asset_url('js/form-validator.min.js')) ?>"></script>
<script src="<?= e(asset_url('js/contact-form-script.js')) ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<!-- Owl Carousel JS (CDN) - قبل از main.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- 5. فایل‌های اصلی پروژه (آخر) -->
<script src="<?= e(asset_url('js/main.js')) ?>"></script>
<script src="<?= e(asset_url('js/chat.js')) ?>"></script>
</body>
</html>