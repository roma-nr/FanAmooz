<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_teacher_approved();

$pageTitle = 'دوره‌های من';
$activeMenu = 'courses';
$teacherId = (int) auth_id();
$error = null;
$edit = null;

$categories = db()->query('SELECT id, name FROM course_categories WHERE is_active = 1 ORDER BY sort_order')->fetchAll();

if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM courses WHERE id = ? AND teacher_id = ?');
    $stmt->execute([(int) $_GET['edit'], $teacherId]);
    $edit = $stmt->fetch() ?: null;
}

if (is_post()) {
    if (!verify_csrf()) {
        $error = 'درخواست نامعتبر است.';
    } else {
        $action = $_POST['action'] ?? 'save';
        try {
            if ($action === 'delete') {
                $cid = (int) ($_POST['id'] ?? 0);
                if (!teacher_owns_course($teacherId, $cid)) {
                    throw new RuntimeException('دسترسی غیرمجاز.');
                }
                db()->prepare('DELETE FROM courses WHERE id = ? AND teacher_id = ?')->execute([$cid, $teacherId]);
                flash('success', 'دوره حذف شد.');
                redirect(base_url('teacher/courses.php'));
            }

            $id = (int) ($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            if ($title === '') {
                throw new RuntimeException('عنوان دوره الزامی است.');
            }
            $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
            $isPaid = isset($_POST['is_paid']) ? 1 : 0;
            $price = max(0, (float) ($_POST['price'] ?? 0));
            if (!$isPaid) $price = 0;

            $image = $edit['image'] ?? null;
            if (!empty($_FILES['image']['name'])) {
                if ($image) delete_upload($image);
                $image = handle_upload($_FILES['image'], 'courses');
            }

            $fields = [
                $title,
                $slug,
                trim($_POST['description'] ?? ''),
                $image,
                $price,
                $isPaid,
                $_POST['status'] ?? 'draft',
                (int) ($_POST['category_id'] ?: 0) ?: null,
                (float) ($_POST['min_pass_grade'] ?? 60),
                (int) ($_POST['session_count'] ?? 0),
                $_POST['start_date'] ?: null,
                $_POST['end_date'] ?: null,
                $_POST['schedule_notes'] ?: null,
            ];

            if ($id > 0) {
                if (!teacher_owns_course($teacherId, $id)) {
                    throw new RuntimeException('دسترسی غیرمجاز.');
                }
                $fields[] = $id;
                $fields[] = $teacherId;
                db()->prepare(
                    'UPDATE courses SET 
                        title=?, slug=?, description=?, image=?, price=?, is_paid=?, 
                        status=?, category_id=?, min_pass_grade=?, 
                        session_count=?, start_date=?, end_date=?, schedule_notes=?
                     WHERE id=? AND teacher_id=?'
                )->execute($fields);
                flash('success', 'دوره ویرایش شد.');
            } else {
                db()->prepare(
                    'INSERT INTO courses 
                        (teacher_id, title, slug, description, image, price, is_paid, 
                         status, category_id, min_pass_grade, 
                         session_count, start_date, end_date, schedule_notes)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                )->execute(array_merge([$teacherId], $fields));
                flash('success', 'دوره جدید ایجاد شد.');
            }
            redirect(base_url('teacher/courses.php'));
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$items = teacher_courses($teacherId);

require dirname(__DIR__) . '/includes/layout/teacher_header.php';
?>

<style>
/* اصلاح اندازه و فونت تقویم جلالی (همانند admin) */
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
jdp-container .jdp-month:hover,
jdp-container .jdp-year:hover {
    filter: none !important;
}
jdp-container .jdp-year input[type="number"]::-webkit-inner-spin-button,
jdp-container .jdp-year input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>

<h1 class="h3 text-primary mb-4">دوره‌های من</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $edit ? 'ویرایش دوره' : 'ایجاد دوره جدید' ?></h2>
                <form method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">

                    <div class="mb-2">
                        <label class="form-label">عنوان *</label>
                        <input name="title" class="form-control" required value="<?= e($edit['title'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">نامک (slug)</label>
                        <input name="slug" class="form-control" dir="ltr" value="<?= e($edit['slug'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">حوزه تخصصی</label>
                        <select name="category_id" class="form-select searchable-select" required>
                            <option value="">انتخاب کنید</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['id'] ?>" <?= (int) ($edit['category_id'] ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- تعداد جلسات + روزهای برگزاری + مدت -->
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label">تعداد جلسات</label>
                            <input type="number" name="session_count" class="form-control" min="0" value="<?= old('session_count', $edit['session_count'] ?? 0) ?>">
                        </div>
                        <div class="col-4">
                            <label class="form-label">تعداد دقیقه جلسه</label>
                            <input type="number" name="session_duration_minutes" class="form-control" min="15" value="<?= old('session_duration_minutes', $edit['session_duration_minutes'] ?? 90) ?>">
                        </div>
                        <div class="col-4">
                            <label class="form-label">روزهای برگزاری</label>
                            <input name="session_days" class="form-control" placeholder="شنبه و سه‌شنبه" value="<?= old('session_days', $edit['session_days'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- تاریخ شروع / پایان (دو فیلد مستقل) -->
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label">تاریخ شروع</label>
                            <input type="text" id="course-start-date" class="form-control" placeholder="انتخاب ..."
                                   value="<?= $edit && $edit['start_date'] ? jdate('Y/m/d', strtotime($edit['start_date'])) : '' ?>"
                                   autocomplete="off">
                            <input type="hidden" name="start_date" id="start_date" value="<?= e($edit['start_date'] ?? '') ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label">تاریخ پایان</label>
                            <input type="text" id="course-end-date" class="form-control" placeholder="انتخاب ..."
                                   value="<?= $edit && $edit['end_date'] ? jdate('Y/m/d', strtotime($edit['end_date'])) : '' ?>"
                                   autocomplete="off">
                            <input type="hidden" name="end_date" id="end_date" value="<?= e($edit['end_date'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">برنامه زمانی (روز و ساعت جلسات)</label>
                        <textarea name="schedule_notes" class="form-control" rows="2"><?= e($edit['schedule_notes'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control tinymce" rows="6"><?= e($edit['description'] ?? '') ?></textarea>
                        <small class="text-muted d-block mt-1">پیش‌نیازها، فرصت‌های شغلی، سرفصل‌های مهم و اهداف دوره را در این بخش وارد کنید.</small>
                    </div>

                    <!-- نوع دوره و قیمت -->
                    <div class="mb-3">
                        <label class="form-label">نوع دوره <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_paid" id="paidRadio" value="1" <?= ((int)($edit['is_paid'] ?? 0) === 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="paidRadio">با هزینه</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_paid" id="freeRadio" value="0" <?= ((int)($edit['is_paid'] ?? 0) === 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="freeRadio">رایگان</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 price-field" id="priceField" style="<?= ((int)($edit['is_paid'] ?? 0) === 1) ? 'display:block;' : 'display:none;' ?>">
                        <label class="form-label">هزینه (تومان) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" min="0" value="<?= e($edit['price'] ?? 0) ?>" step="1000">
                        <small class="text-muted">مبلغ را به تومان وارد کنید.</small>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label">وضعیت انتشار</label>
                            <select name="status" class="form-select searchable-select">
                                <?php foreach (['draft' => 'پیش‌نویس', 'published' => 'منتشرشده'] as $k => $v): ?>
                                    <option value="<?= $k ?>" <?= ($edit['status'] ?? 'draft') === $k ? 'selected' : '' ?>><?= e($v) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">حداقل نمره قبولی</label>
                            <input type="number" step="0.01" name="min_pass_grade" class="form-control" value="<?= e($edit['min_pass_grade'] ?? '60') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">تصویر</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <button class="btn btn-primary"><?= $edit ? 'ذخیره' : 'ایجاد' ?></button>
                    <?php if ($edit): ?><a href="courses.php" class="btn btn-link">انصراف</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="table-responsive bg-white rounded shadow-sm">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light"><tr><th>عنوان</th><th>وضعیت</th><th>جلسات</th><th>عملیات</th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?= e($item['title']) ?>
                            <br><small class="text-muted"><?= e($item['category_name'] ?? '') ?></small>
                        </td>
                        <td><span class="badge bg-secondary"><?= e(course_status_label($item['status'])) ?></span></td>
                        <td><?= (int) $item['student_count'] ?> / <?= (int) $item['session_count'] ?> جلسه</td>
                        <td class="text-nowrap">
                            <div class="btn-group btn-group-sm flex-wrap">
                            <a href="sessions.php?course_id=<?= (int) $item['id'] ?>" class="btn btn-outline-primary">جلسات</a>
                            <a href="assignments.php?course_id=<?= (int) $item['id'] ?>" class="btn btn-outline-primary">تکالیف</a>
                            <a href="chat.php?course_id=<?= (int) $item['id'] ?>" class="btn btn-outline-secondary">چت</a>
                            <a href="attendance.php?course_id=<?= (int) $item['id'] ?>" class="btn btn-outline-secondary">حضور</a>
                            <a href="grades.php?course_id=<?= (int) $item['id'] ?>" class="btn btn-outline-secondary">نمره</a>
                            <a href="?edit=<?= (int) $item['id'] ?>" class="btn btn-outline-secondary">ویرایش</a>
                            </div>
                            <form method="post" class="d-inline" onsubmit="return confirm('حذف دوره؟')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$items): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">هنوز دوره‌ای نساخته‌اید.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // بخش قیمت
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
        paidRadio.addEventListener('change', function () { if (this.checked) togglePrice(true); });
        freeRadio.addEventListener('change', function () { if (this.checked) togglePrice(false); });
        togglePrice(paidRadio.checked);
    }

    // تقویم‌های جلالی
    function zeroPad(num) { return num.toString().padStart(2, '0'); }
    function setupDatepicker(inputId, hiddenId) {
        var input = document.getElementById(inputId);
        var hidden = document.getElementById(hiddenId);
        if (!input || !hidden) return;
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
    setupDatepicker('course-start-date', 'start_date');
    setupDatepicker('course-end-date', 'end_date');
});
</script>

<?php require dirname(__DIR__) . '/includes/layout/teacher_footer.php'; ?>