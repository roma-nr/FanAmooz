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
        $cid = (int)($_POST['id'] ?? 0);
        try {
            if ($action === 'delete') {
                $stmt = db()->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?");
                $stmt->execute([$cid]);
                $enrollCount = (int)$stmt->fetchColumn();
                if ($enrollCount > 0) {
                    throw new RuntimeException('این دوره دانشجو دارد و حذف آن ممکن نیست. می‌توانید آن را غیرفعال کنید.');
                }
                db()->prepare('DELETE FROM courses WHERE id = ?')->execute([$cid]);
                flash('success', 'دوره حذف شد.');
                redirect(base_url('admin/courses.php'));
            } elseif ($action === 'archive') {
                $course = course_by_id($cid);
                if (!$course || $course['status'] !== 'published') {
                    throw new RuntimeException('فقط دوره‌های در حال برگزاری می‌توانند آرشیو شوند.');
                }
                archive_course($cid);
                flash('success', 'دوره با موفقیت آرشیو شد.');
                redirect(base_url('admin/courses.php'));
            } elseif ($action === 'disable') {
                $course = course_by_id($cid);
                if (!in_array($course['status'], ['published','archived'])) {
                    throw new RuntimeException('وضعیت فعلی برای غیرفعال‌سازی مجاز نیست.');
                }
                if ($course['status'] === 'published') {
                    $activeEnrollments = db()->prepare("SELECT e.id, e.user_id, p.amount FROM enrollments e JOIN payments p ON p.enrollment_id = e.id AND p.status = 'paid' WHERE e.course_id = ? AND e.status = 'active'")->execute([$cid])->fetchAll();
                    $pdo = db();
                    $pdo->beginTransaction();
                    try {
                        foreach ($activeEnrollments as $enr) {
                            wallet_refund((int)$enr['user_id'], (int)$enr['amount'], 'بازگشت وجه دوره: ' . $course['title']);
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
                redirect(base_url('admin/courses.php'));
            } else { // save
                // ... (کدهای ایجاد/ویرایش بدون تغییر) ...
            }
        } catch (PDOException $e) {
            $errors['general'] = 'خطای دیتابیس: ' . $e->getMessage();
        } catch (Throwable $e) {
            $errors['general'] = $e->getMessage();
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

// تعداد دانشجویان فعال
$activeCounts = [];
$stmt = db()->query("SELECT course_id, COUNT(*) AS cnt FROM enrollments WHERE status = 'active' GROUP BY course_id");
foreach ($stmt as $row) {
    $activeCounts[(int)$row['course_id']] = (int)$row['cnt'];
}

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<!-- ... (فرم افزودن/ویرایش بدون تغییر) ... -->

<!-- لیست دوره‌ها -->
<div class="col-lg-7">
    <div class="card border-0 shadow-sm table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>عنوان</th><th>استاد</th><th>هزینه</th><th>وضعیت</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($items as $item): 
                    $active = $activeCounts[(int)$item['id']] ?? 0;
                    $stmt = db()->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?");
                    $stmt->execute([(int)$item['id']]);
                    $totalEnroll = (int)$stmt->fetchColumn();
                    $canDelete = ($totalEnroll == 0);
                ?>
                <!-- ... (دکمه‌های آرشیو/غیرفعال/حذف) ... -->
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// کدهای قبلی (قیمت و تقویم) بدون تغییر
</script>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>