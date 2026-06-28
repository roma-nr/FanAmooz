<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'مدیریت دوره‌ها';
$activeMenu = 'courses';
$errors = [];
$edit = null;

$categories = db()->query('SELECT id, name FROM course_categories WHERE is_active = 1 ORDER BY sort_order')->fetchAll();
$teachers = db()->query("SELECT id, full_name FROM users WHERE role = 'teacher' AND teacher_status = 'approved' ORDER BY full_name")->fetchAll();
$provinces = db()->query('SELECT id, name FROM provinces WHERE is_active = 1 ORDER BY sort_order, name')->fetchAll();

if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM courses WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

// ========== پردازش فرم ==========
if (is_post()) {
    if (!verify_csrf()) {
        $errors['general'] = 'درخواست نامعتبر است.';
    } else {
        $action = $_POST['action'] ?? 'save';
        try {
            if ($action === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                if ($id > 0) {
                    db()->prepare('DELETE FROM courses WHERE id = ?')->execute([$id]);
                    flash('success', 'دوره حذف شد.');
                }
                redirect(base_url('admin/courses.php'));
            }

            // دریافت داده‌ها
            $id = (int) ($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
            $description = trim($_POST['description'] ?? '');
            $teacherId = (int) ($_POST['teacher_id'] ?? 0);
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $provinceId = (int) ($_POST['province_id'] ?? 0) ?: null;
            $institutionId = (int) ($_POST['institution_id'] ?? 0) ?: null;
            $sessionCount = max(0, (int) ($_POST['session_count'] ?? 0));
            $startDate = trim($_POST['start_date'] ?? '') ?: null;
            $endDate = trim($_POST['end_date'] ?? '') ?: null;
            $scheduleNotes = trim($_POST['schedule_notes'] ?? '') ?: null;
            $isPaid = isset($_POST['is_paid']) ? 1 : 0;
            $price = (float) ($_POST['price'] ?? 0);
            $minPassGrade = (float) ($_POST['min_pass_grade'] ?? 60);
            $status = $_POST['status'] ?? 'draft';
            $featured = isset($_POST['featured_on_home']) ? 1 : 0;
            $homeSortOrder = (int) ($_POST['home_sort_order'] ?? 0);
            
            // تصویر
            $image = $edit['image'] ?? null;
            if (!empty($_FILES['image']['name'])) {
                if ($image) delete_upload($image);
                $image = handle_upload($_FILES['image'], 'courses');
            }

            // اعتبارسنجی
            if ($title === '') {
                $errors['title'] = 'عنوان دوره الزامی است.';
            }
            if ($teacherId <= 0) {
                $errors['teacher_id'] = 'انتخاب استاد الزامی است.';
            }
            if ($categoryId <= 0) {
                $errors['category_id'] = 'انتخاب حوزه تخصصی الزامی است.';
            }
            if ($isPaid && $price <= 0) {
                $errors['price'] = 'هزینه دوره را وارد کنید.';
            }

            // اگر خطایی نبود، ذخیره کن
            if (empty($errors)) {
                $pdo = db();
                $fields = [
                    $title, $slug, $description, $image, $price, $isPaid,
                    $status, $featured, $homeSortOrder,
                    $categoryId, $minPassGrade, $sessionCount, $startDate,
                    $endDate, $scheduleNotes, $teacherId, $provinceId, $institutionId
                ];

                if ($id > 0) {
                    // ویرایش
                    $fields[] = $id;
                    $sql = "UPDATE courses SET 
                            title=?, slug=?, description=?, image=?, price=?, is_paid=?, 
                            status=?, featured_on_home=?, home_sort_order=?,
                            category_id=?, min_pass_grade=?, session_count=?, start_date=?,
                            end_date=?, schedule_notes=?, teacher_id=?, province_id=?, institution_id=?
                            WHERE id=?";
                    $pdo->prepare($sql)->execute($fields);
                    flash('success', 'دوره با موفقیت ویرایش شد.');
                } else {
                    // افزودن جدید
                    $sql = "INSERT INTO courses (
                            title, slug, description, image, price, is_paid, 
                            status, featured_on_home, home_sort_order,
                            category_id, min_pass_grade, session_count, start_date,
                            end_date, schedule_notes, teacher_id, province_id, institution_id
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute($fields);
                    flash('success', 'دوره جدید با موفقیت ایجاد شد.');
                }
                redirect(base_url('admin/courses.php'));
            }
        } catch (PDOException $e) {
            $errors['general'] = 'خطای دیتابیس: ' . $e->getMessage();
        } catch (Throwable $e) {
            $errors['general'] = 'خطا: ' . $e->getMessage();
        }
    }
}

// ========== دریافت لیست دوره‌ها ==========
$items = db()->query("
    SELECT c.*, cat.name AS category_name, u.full_name AS teacher_name
    FROM courses c
    LEFT JOIN course_categories cat ON cat.id = c.category_id
    LEFT JOIN users u ON u.id = c.teacher_id
    ORDER BY c.id DESC
")->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<style>
/* اصلاح اندازه و فونت تقویم جلالی */
jdp-container {
    font-size: 12px !important;
}
jdp-container .jdp-day,
jdp-container .jdp-day-name {
    height: 28px !important;
    line-height: 28px !important;
}
jdp-container .jdp-month select,
jdp-container .jdp-year select,
jdp-container .jdp-time select {
    font-size: 12px !important;
    padding: 2px 4px !important;
    transition: none !important;
}
/* حذف افکت hover که چشمک می‌زند */
jdp-container .jdp-month:hover,
jdp-container .jdp-year:hover {
    filter: none !important;
}
/* مخفی کردن اسپینر input عددی سال */
jdp-container .jdp-year input[type="number"]::-webkit-inner-spin-button,
jdp-container .jdp-year input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.ts-control{ background-position: left .75rem center !important; }
</style>

<h1 class="h3 mb-4">مدیریت دوره‌ها</h1>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger"><?= e($errors['general']) ?></div>
<?php endif; ?>
<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- فرم افزودن/ویرایش -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $edit ? 'ویرایش دوره' : 'افزودن دوره جدید' ?></h2>
                <form method="post" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">

                    <!-- عنوان -->
                    <div class="mb-3">
                        <label class="form-label">عنوان <span class="text-danger">*</span></label>
                        <input name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                               value="<?= old('title', $edit['title'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['title'] ?? '' ?></div>
                    </div>

                    <!-- استاد -->
                    <div class="mb-3">
                        <label class="form-label">استاد <span class="text-danger">*</span></label>
                        <select name="teacher_id" class="form-select searchable-select <?= isset($errors['teacher_id']) ? 'is-invalid' : '' ?>">
                            <option value="">انتخاب استاد</option>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= (int) $t['id'] ?>" 
                                    <?= old('teacher_id', $edit['teacher_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                                    <?= e($t['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= $errors['teacher_id'] ?? '' ?></div>
                    </div>

                    <!-- دسته‌بندی -->
                    <div class="mb-3">
                        <label class="form-label">حوزه تخصصی <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select searchable-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>">
                            <option value="">انتخاب حوزه</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['id'] ?>" 
                                    <?= old('category_id', $edit['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= $errors['category_id'] ?? '' ?></div>
                    </div>

                    <!-- رادیو باتن نوع دوره -->
                    <div class="mb-3">
                        <label class="form-label">نوع دوره <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_paid" id="paidRadio" value="1" 
                                    <?= ((int)($edit['is_paid'] ?? 0) === 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="paidRadio">با هزینه</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_paid" id="freeRadio" value="0" 
                                    <?= ((int)($edit['is_paid'] ?? 0) === 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="freeRadio">رایگان</label>
                            </div>
                        </div>
                    </div>

                    <!-- قیمت (با انیمیشن) -->
                    <div class="mb-3 price-field" id="priceField" style="<?= ((int)($edit['is_paid'] ?? 0) === 1) ? 'display:block;' : 'display:none;' ?>">
                        <label class="form-label">هزینه (تومان) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                               min="0" value="<?= old('price', $edit['price'] ?? 0) ?>" step="1000">
                        <div class="invalid-feedback"><?= $errors['price'] ?? '' ?></div>
                        <small class="text-muted">مبلغ را به تومان وارد کنید.</small>
                    </div>

                    <!-- Slug -->
                    <div class="mb-3">
                        <label class="form-label">نامک (slug)</label>
                        <input name="slug" class="form-control" value="<?= old('slug', $edit['slug'] ?? '') ?>" dir="ltr">
                    </div>

                    <!-- توضیحات -->
                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control tinymce" rows="4"><?= old('description', $edit['description'] ?? '') ?></textarea>
                    </div>

                    <!-- تاریخ‌ها: دو فیلد مجزا (شروع / پایان) -->
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label">تعداد جلسات</label>
                            <input type="number" name="session_count" class="form-control" min="0" 
                                   value="<?= old('session_count', $edit['session_count'] ?? 0) ?>">
                        </div>
                        <div class="col-4">
                            <label class="form-label">تاریخ شروع <span class="text-danger">*</span></label>
                            <input type="text" id="course-start-date" class="form-control" placeholder="انتخاب ..."
                                   value="<?= $edit && $edit['start_date'] ? jdate('Y/m/d', strtotime($edit['start_date'])) : '' ?>"
                                   autocomplete="off">
                            <input type="hidden" name="start_date" id="start_date" value="<?= e($edit['start_date'] ?? '') ?>">
                        </div>
                        <div class="col-4">
                            <label class="form-label">تاریخ پایان <span class="text-danger">*</span></label>
                            <input type="text" id="course-end-date" class="form-control" placeholder="انتخاب ..."
                                   value="<?= $edit && $edit['end_date'] ? jdate('Y/m/d', strtotime($edit['end_date'])) : '' ?>"
                                   autocomplete="off">
                            <input type="hidden" name="end_date" id="end_date" value="<?= e($edit['end_date'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- برنامه زمانی -->
                    <div class="mb-3 mt-2">
                        <label class="form-label">برنامه زمانی (روز و ساعت جلسات)</label>
                        <textarea name="schedule_notes" class="form-control" rows="2" 
                                  placeholder="مثلاً شنبه و سه‌شنبه ۱۶ تا ۱۸"><?= old('schedule_notes', $edit['schedule_notes'] ?? '') ?></textarea>
                    </div>

                    <!-- وضعیت و حداقل نمره -->
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">وضعیت</label>
                            <select name="status" class="form-select">
                                <option value="draft" <?= old('status', $edit['status'] ?? 'draft') == 'draft' ? 'selected' : '' ?>>پیش‌نویس</option>
                                <option value="published" <?= old('status', $edit['status'] ?? 'draft') == 'published' ? 'selected' : '' ?>>منتشرشده</option>
                                <option value="archived" <?= old('status', $edit['status'] ?? 'draft') == 'archived' ? 'selected' : '' ?>>بایگانی</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">حداقل نمره قبولی</label>
                            <input type="number" step="0.01" name="min_pass_grade" class="form-control" 
                                   value="<?= old('min_pass_grade', $edit['min_pass_grade'] ?? 60) ?>">
                        </div>
                    </div>

                    <!-- نمایش در صفحه اصلی -->
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="featured_on_home" value="1" id="fh" 
                            <?= ((int)($edit['featured_on_home'] ?? 0) === 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="fh">نمایش در صفحه اصلی</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ترتیب در صفحه اصلی</label>
                        <input type="number" name="home_sort_order" class="form-control" 
                               value="<?= old('home_sort_order', $edit['home_sort_order'] ?? 0) ?>">
                    </div>

                    <!-- تصویر -->
                    <div class="mb-3">
                        <label class="form-label">تصویر دوره</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <?php if ($edit && $edit['image']): ?>
                            <div class="mt-1"><img src="<?= e(upload_url($edit['image'])) ?>" style="max-height:60px;" alt=""></div>
                        <?php endif; ?>
                    </div>

                    <!-- دکمه‌ها -->
                    <button class="btn btn-primary"><?= $edit ? 'ذخیره' : 'افزودن' ?></button>
                    <?php if ($edit): ?>
                        <a href="courses.php" class="btn btn-link">انصراف</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- لیست دوره‌ها -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>عنوان</th><th>استاد</th><th>هزینه</th><th>وضعیت</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['title']) ?><br><small class="text-muted"><?= e($item['category_name'] ?? '') ?></small></td>
                        <td><?= e($item['teacher_name'] ?? '---') ?></td>
                        <td><?= (int)$item['is_paid'] ? e(format_price($item['price'])) : 'رایگان' ?></td>
                        <td><span class="badge bg-secondary"><?= e($item['status']) ?></span></td>
                        <td class="text-nowrap">
                            <a href="?edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">ویرایش</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('حذف دوره؟')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------- بخش قیمت ----------
    const paidRadio = document.getElementById('paidRadio');
    const freeRadio = document.getElementById('freeRadio');
    const priceField = document.getElementById('priceField');

    function togglePrice(show) {
        if (show) {
            priceField.style.display = 'block';
            priceField.style.opacity = '0';
            setTimeout(() => { priceField.style.opacity = '1'; }, 50);
        } else {
            priceField.style.opacity = '0';
            setTimeout(() => { priceField.style.display = 'none'; }, 300);
        }
    }

    if (paidRadio && freeRadio) {
        paidRadio.addEventListener('change', function() { if (this.checked) togglePrice(true); });
        freeRadio.addEventListener('change', function() { if (this.checked) togglePrice(false); });
        togglePrice(paidRadio.checked);
    }

    // ---------- تقویم‌های جلالی (دو فیلد مستقل) ----------
    function zeroPad(num) {
        return num.toString().padStart(2, '0');
    }

    // تابع کمکی برای تنظیم یک Datepicker و ذخیره در hidden input
    function setupDatepicker(inputId, hiddenId) {
        var input = document.getElementById(inputId);
        var hidden = document.getElementById(hiddenId);
        if (!input || !hidden) return;

        // مقدار اولیه
        var initialDate = null;
        if (hidden.value) {
            // تبدیل میلادی به جلالی برای نمایش (با jdate در PHP قبلاً پر شده، ولی ما اینجا مستقیم از hidden می‌خوانیم)
            // hidden.value میلادی است، ولی jalaliDatepicker خودش آن را تشخیص می‌دهد و در input نمایش می‌دهد.
            // بنابراین فقط hidden.value را به عنوان مقدار اولیه به input می‌دهیم.
            // اما jalaliDatepicker ورودی را به صورت جلالی نمایش می‌دهد، پس باید از طریق setValue انجام دهیم.
            // روش بهتر: input را با مقدار جلالی (که با jdate در value نوشته شده) پر می‌کنیم، hidden هم میلادی دارد.
            // در HTML ما value جلالی را با jdate گذاشته‌ایم، و hidden میلادی. پس خوب است.
        }

        jalaliDatepicker.startWatch({
            selector: '#' + inputId,
            type: 'single',
            onChange: function(e) {
                if (e._date) {
                    var g = e._date.toGregorian();
                    hidden.value = g.year + '-' + zeroPad(g.month) + '-' + zeroPad(g.day);
                } else {
                    hidden.value = '';
                }
            }
        });
    }

    // راه‌اندازی برای شروع و پایان
    setupDatepicker('course-start-date', 'start_date');
    setupDatepicker('course-end-date', 'end_date');
});
</script>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>