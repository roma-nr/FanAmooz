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
        $cid = (int)($_POST['id'] ?? 0);
        try {
            if ($action === 'delete') {
                if (!teacher_owns_course($teacherId, $cid)) {
                    throw new RuntimeException('دسترسی غیرمجاز.');
                }
                // حذف فقط در صورت نداشتن هیچ ثبت‌نامی
                $enrollCount = (int)db()->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?")->execute([$cid])->fetchColumn();
                if ($enrollCount > 0) {
                    throw new RuntimeException('این دوره دانشجو دارد و حذف آن ممکن نیست. می‌توانید آن را غیرفعال کنید.');
                }
                db()->prepare('DELETE FROM courses WHERE id = ? AND teacher_id = ?')->execute([$cid, $teacherId]);
                flash('success', 'دوره حذف شد.');
                redirect(base_url('teacher/courses.php'));
            } elseif ($action === 'archive') {
                if (!teacher_owns_course($teacherId, $cid)) throw new RuntimeException('دسترسی غیرمجاز.');
                archive_course($cid);
                flash('success', 'دوره با موفقیت آرشیو شد.');
                redirect(base_url('teacher/courses.php'));
            } elseif ($action === 'disable') {
                if (!teacher_owns_course($teacherId, $cid)) throw new RuntimeException('دسترسی غیرمجاز.');
                $course = course_by_id($cid);
                if ($course['status'] === 'published') {
                    // بازگرداندن وجه به دانشجویان فعال
                    $activeEnrollments = db()->prepare("SELECT e.id, e.user_id, p.amount FROM enrollments e JOIN payments p ON p.enrollment_id = e.id AND p.status = 'paid' WHERE e.course_id = ? AND e.status = 'active'")->execute([$cid])->fetchAll();
                    $pdo = db();
                    $pdo->beginTransaction();
                    try {
                        foreach ($activeEnrollments as $enr) {
                            wallet_refund($enr['user_id'], (int)$enr['amount'], 'بازگشت وجه دوره: ' . $course['title']);
                        }
                        disable_course($cid);
                        $pdo->commit();
                        flash('success', 'دوره غیرفعال شد و مبالغ به کیف پول دانشجویان برگشت داده شد.');
                    } catch (Throwable $e) {
                        $pdo->rollBack();
                        throw $e;
                    }
                } else {
                    disable_course($cid);
                    flash('success', 'دوره غیرفعال شد.');
                }
                redirect(base_url('teacher/courses.php'));
            } else { // save (ویرایش یا ایجاد)
                // کدهای قبلی ایجاد/ویرایش
                $id = (int)($_POST['id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                if ($title === '') throw new RuntimeException('عنوان دوره الزامی است.');
                $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
                $isPaid = isset($_POST['is_paid']) ? 1 : 0;
                $price = max(0, (float)($_POST['price'] ?? 0));
                if (!$isPaid) $price = 0;
                $image = $edit['image'] ?? null;
                if (!empty($_FILES['image']['name'])) {
                    if ($image) delete_upload($image);
                    $image = handle_upload($_FILES['image'], 'courses');
                }
                $fields = [
                    $title, $slug, trim($_POST['description'] ?? ''), $image, $price, $isPaid,
                    $_POST['status'] ?? 'draft', (int)($_POST['category_id'] ?: 0) ?: null,
                    (float)($_POST['min_pass_grade'] ?? 60), (int)($_POST['session_count'] ?? 0),
                    $_POST['start_date'] ?: null, $_POST['end_date'] ?: null, $_POST['schedule_notes'] ?: null,
                ];
                if ($id > 0) {
                    if (!teacher_owns_course($teacherId, $id)) throw new RuntimeException('دسترسی غیرمجاز.');
                    $fields[] = $id;
                    $fields[] = $teacherId;
                    db()->prepare('UPDATE courses SET title=?, slug=?, description=?, image=?, price=?, is_paid=?, status=?, category_id=?, min_pass_grade=?, session_count=?, start_date=?, end_date=?, schedule_notes=? WHERE id=? AND teacher_id=?')->execute($fields);
                    flash('success', 'دوره ویرایش شد.');
                } else {
                    db()->prepare('INSERT INTO courses (teacher_id, title, slug, description, image, price, is_paid, status, category_id, min_pass_grade, session_count, start_date, end_date, schedule_notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute(array_merge([$teacherId], $fields));
                    flash('success', 'دوره جدید ایجاد شد.');
                }
                redirect(base_url('teacher/courses.php'));
            }
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$items = teacher_courses($teacherId);

// دریافت تعداد دانشجویان فعال برای هر دوره جهت هشدارها
$activeCounts = [];
$stmt = db()->prepare("SELECT course_id, COUNT(*) AS cnt FROM enrollments WHERE status = 'active' AND course_id IN (SELECT id FROM courses WHERE teacher_id = ?) GROUP BY course_id");
$stmt->execute([$teacherId]);
foreach ($stmt as $row) {
    $activeCounts[(int)$row['course_id']] = (int)$row['cnt'];
}

require dirname(__DIR__) . '/includes/layout/teacher_header.php';
?>

<!-- استایل‌های تقویم (بدون تغییر) -->
<style>/* ... */</style>

<h1 class="h3 text-primary mb-4">دوره‌های من</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="row g-4">
    <!-- فرم ویرایش (بدون تغییر) -->
    <div class="col-lg-5">...</div>

    <div class="col-lg-7">
        <div class="table-responsive bg-white rounded shadow-sm">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light"><tr><th>عنوان</th><th>وضعیت</th><th>جلسات</th><th>عملیات</th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): 
                    $active = $activeCounts[(int)$item['id']] ?? 0;
                    $canDelete = (int)$item['student_count'] == 0; // هیچ ثبت‌نامی نداشته باشد
                ?>
                    <tr>
                        <td>
                            <?= e($item['title']) ?>
                            <br><small class="text-muted"><?= e($item['category_name'] ?? '') ?></small>
                        </td>
                        <td><span class="badge bg-<?= $item['status'] === 'published' ? 'success' : ($item['status'] === 'archived' ? 'info' : ($item['status'] === 'disabled' ? 'danger' : 'secondary')) ?>"><?= e(course_status_label($item['status'])) ?></span></td>
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

                            <?php if ($item['status'] === 'published'): ?>
                                <!-- آرشیو -->
                                <form method="post" class="d-inline" onsubmit="return confirm('آرشیو شدن این دوره موجب تکمیل ثبت‌نام‌ها و واریز درآمد به کیف پول شما می‌شود. ادامه می‌دهید؟')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="archive">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button class="btn btn-sm btn-outline-info">آرشیو</button>
                                </form>
                                <!-- غیرفعال‌سازی -->
                                <form method="post" class="d-inline" onsubmit="return confirm('<?= $active > 0 ? 'توجه! این دوره ' . $active . ' دانشجوی فعال دارد. در صورت غیرفعال‌سازی، شهریه به کیف پول آن‌ها بازگردانده می‌شود. ادامه می‌دهید؟' : 'آیا از غیرفعال‌سازی این دوره اطمینان دارید؟' ?>')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="disable">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button class="btn btn-sm btn-outline-warning">غیرفعال‌سازی</button>
                                </form>
                            <?php elseif ($item['status'] === 'archived'): ?>
                                <!-- غیرفعال‌سازی از آرشیو -->
                                <form method="post" class="d-inline" onsubmit="return confirm('این دوره آرشیو شده است. با غیرفعال‌سازی، دانشجویان همچنان به محتوا دسترسی خواهند داشت ولی ثبت‌نام جدید ممکن نیست. ادامه می‌دهید؟')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="disable">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button class="btn btn-sm btn-outline-warning">غیرفعال‌سازی</button>
                                </form>
                            <?php endif; ?>

                            <!-- حذف (فقط در صورت نداشتن دانشجو) -->
                            <?php if ($canDelete): ?>
                            <form method="post" class="d-inline" onsubmit="return confirm('این دوره هیچ دانشجویی ندارد. حذف شود؟')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// کدهای جاوااسکریپت قبلی (قیمت، تقویم)
</script>

<?php require dirname(__DIR__) . '/includes/layout/teacher_footer.php'; ?>