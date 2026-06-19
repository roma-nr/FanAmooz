<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_teacher_approved();

$courseId = (int) ($_GET['course_id'] ?? 0);
$teacherId = (int) auth_id();
$courseStmt = db()->prepare('SELECT * FROM courses WHERE id = ? AND teacher_id = ?');
$courseStmt->execute([$courseId, $teacherId]);
$course = $courseStmt->fetch();

if (!$course) {
    flash('error', 'دوره یافت نشد.');
    redirect(base_url('teacher/courses.php'));
}

$pageTitle = 'مدیریت جلسات - ' . $course['title'];
$activeMenu = 'courses';
$errors = [];
$edit = null;

if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM course_sessions WHERE id = ? AND course_id = ?');
    $stmt->execute([(int) $_GET['edit'], $courseId]);
    $edit = $stmt->fetch() ?: null;
}

if (is_post() && verify_csrf()) {
    $action = $_POST['action'] ?? 'save';
    try {
        if ($action === 'delete') {
            $sid = (int) ($_POST['id'] ?? 0);
            $row = db()->prepare('SELECT video_path, audio_path, pdf_path FROM course_sessions WHERE id = ? AND course_id = ?');
            $row->execute([$sid, $courseId]);
            $files = $row->fetch();
            if ($files) {
                delete_upload($files['video_path']);
                delete_upload($files['audio_path']);
                delete_upload($files['pdf_path']);
            }
            db()->prepare('DELETE FROM course_sessions WHERE id = ? AND course_id = ?')->execute([$sid, $courseId]);
            flash('success', 'جلسه حذف شد.');
            redirect(base_url('teacher/sessions.php?course_id=' . $courseId));
        }

        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $sessionNumber = max(1, (int) ($_POST['session_number'] ?? 1));
        $description = trim($_POST['description'] ?? '');
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);
        
        // فیلدهای جدید (لینک و زمان کلاس آنلاین)
        $adobeConnectUrl = trim($_POST['adobe_connect_url'] ?? '');
        $scheduledAt = trim($_POST['scheduled_at'] ?? '') ?: null;

        if ($title === '') {
            $errors['title'] = 'عنوان جلسه الزامی است.';
        }
        
        // اعتبارسنجی لینک (اگر وارد شده باشد)
        if ($adobeConnectUrl !== '' && !validate_url($adobeConnectUrl)) {
            $errors['adobe_connect_url'] = 'لینک Adobe Connect معتبر نیست (با http:// یا https:// شروع شود).';
        }

        // فایل‌ها
        $video = $edit['video_path'] ?? null;
        $audio = $edit['audio_path'] ?? null;
        $pdf = $edit['pdf_path'] ?? null;

        // بررسی مهلت آپلود ویدئو (۲ روز بعد از کلاس)
        $uploadDeadline = null;
        if ($scheduledAt) {
            $uploadDeadline = date('Y-m-d H:i:s', strtotime($scheduledAt . ' +2 days'));
        }

        if (!empty($_FILES['video']['name'])) {
            if ($video) delete_upload($video);
            $video = handle_upload($_FILES['video'], 'courses/sessions', ['mp4', 'webm'], 200 * 1024 * 1024);
        }
        if (!empty($_FILES['audio']['name'])) {
            if ($audio) delete_upload($audio);
            $audio = handle_upload($_FILES['audio'], 'courses/sessions', ['mp3', 'wav'], 50 * 1024 * 1024);
        }
        if (!empty($_FILES['pdf']['name'])) {
            if ($pdf) delete_upload($pdf);
            $pdf = handle_upload($_FILES['pdf'], 'courses/sessions', ['pdf'], 20 * 1024 * 1024);
        }

        if (empty($errors)) {
            $fields = [
                $title, $sessionNumber, $description, 
                $video, $audio, $pdf, $sortOrder,
                $adobeConnectUrl, $scheduledAt
            ];
            if ($id > 0) {
                $fields[] = $id;
                $fields[] = $courseId;
                db()->prepare("
                    UPDATE course_sessions 
                    SET title=?, session_number=?, description=?, 
                        video_path=?, audio_path=?, pdf_path=?, sort_order=?,
                        adobe_connect_url=?, scheduled_at=?
                    WHERE id=? AND course_id=?
                ")->execute($fields);
                flash('success', 'جلسه ویرایش شد.');
            } else {
                db()->prepare("
                    INSERT INTO course_sessions 
                    (course_id, title, session_number, description, 
                     video_path, audio_path, pdf_path, sort_order,
                     adobe_connect_url, scheduled_at)
                    VALUES (?,?,?,?,?,?,?,?,?,?)
                ")->execute(array_merge([$courseId], $fields));
                flash('success', 'جلسه افزوده شد.');
            }
            redirect(base_url('teacher/sessions.php?course_id=' . $courseId));
        }
    } catch (Throwable $e) {
        $errors['general'] = $e->getMessage();
    }
}

$sessions = course_sessions_list($courseId);

// دریافت تاریخ جلسات برای نمایش مهلت
foreach ($sessions as &$s) {
    $deadline = null;
    if ($s['scheduled_at']) {
        $deadline = date('Y-m-d H:i:s', strtotime($s['scheduled_at'] . ' +2 days'));
    }
    $s['upload_deadline'] = $deadline;
    $s['is_upload_expired'] = $deadline && time() > strtotime($deadline) && empty($s['video_path']);
}

require dirname(__DIR__) . '/includes/layout/teacher_header.php';
?>

<p class="mb-3"><a href="courses.php" class="text-decoration-none">&larr; بازگشت به دوره‌ها</a></p>
<h1 class="h4 text-primary mb-1">مدیریت جلسات - <?= e($course['title']) ?></h1>
<p class="text-muted small mb-4">در این بخش، هم لینک کلاس آنلاین و هم فایل‌های محتوایی (ویدئو، PDF) را مدیریت کنید. ویدئو باید تا ۲ روز پس از برگزاری کلاس آپلود شود.</p>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger"><?= e($errors['general']) ?></div>
<?php endif; ?>
<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $edit ? 'ویرایش جلسه' : 'جلسه جدید' ?></h2>
                <form method="post" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">

                    <div class="mb-2">
                        <label class="form-label">عنوان جلسه <span class="text-danger">*</span></label>
                        <input name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= old('title', $edit['title'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['title'] ?? '' ?></div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">شماره جلسه</label>
                            <input type="number" name="session_number" class="form-control" min="1" value="<?= old('session_number', $edit['session_number'] ?? count($sessions) + 1) ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label">ترتیب</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= old('sort_order', $edit['sort_order'] ?? 0) ?>">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control" rows="2"><?= old('description', $edit['description'] ?? '') ?></textarea>
                    </div>

                    <hr>
                    <h6 class="text-primary">کلاس آنلاین (Adobe Connect) - اختیاری</h6>
                    <div class="mb-2">
                        <label class="form-label">لینک Adobe Connect</label>
                        <input name="adobe_connect_url" class="form-control <?= isset($errors['adobe_connect_url']) ? 'is-invalid' : '' ?>" dir="ltr" placeholder="https://..." value="<?= old('adobe_connect_url', $edit['adobe_connect_url'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['adobe_connect_url'] ?? '' ?></div>
                        <small class="text-muted">در صورت برگزاری کلاس آنلاین، لینک را وارد کنید.</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">زمان برگزاری کلاس</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" value="<?= $edit && $edit['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($edit['scheduled_at'])) : '' ?>">
                        <small class="text-muted">در صورت برگزاری کلاس آنلاین، زمان را تنظیم کنید. ویدئو تا ۲ روز بعد از این زمان باید آپلود شود.</small>
                    </div>

                    <hr>
                    <h6 class="text-primary">محتوای جلسه</h6>
                    <div class="mb-2">
                        <label class="form-label">ویدئو <span class="text-danger">* (در صورت وجود کلاس آنلاین، تا ۲ روز پس از کلاس)</span></label>
                        <input type="file" name="video" class="form-control" accept="video/*,.mp4,.webm">
                        <small class="text-muted">حداکثر ۲۰۰ مگابایت. در صورت برگزاری کلاس آنلاین، حتماً تا ۲ روز بعد آپلود کنید.</small>
                        <?php if ($edit && $edit['video_path']): ?>
                            <div class="mt-1"><span class="badge bg-success">ویدئو آپلود شده</span></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">فایل PDF (اختیاری)</label>
                        <input type="file" name="pdf" class="form-control" accept=".pdf">
                        <?php if ($edit && $edit['pdf_path']): ?>
                            <div class="mt-1"><span class="badge bg-secondary">PDF آپلود شده</span></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">فایل صوتی (اختیاری)</label>
                        <input type="file" name="audio" class="form-control" accept="audio/*,.mp3,.wav">
                        <?php if ($edit && $edit['audio_path']): ?>
                            <div class="mt-1"><span class="badge bg-info">صوت آپلود شده</span></div>
                        <?php endif; ?>
                    </div>

                    <button class="btn btn-primary"><?= $edit ? 'ذخیره' : 'افزودن' ?></button>
                    <?php if ($edit): ?>
                        <a href="sessions.php?course_id=<?= $courseId ?>" class="btn btn-link">انصراف</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="list-group shadow-sm">
            <?php foreach ($sessions as $s): ?>
                <?php 
                    $hasLive = !empty($s['adobe_connect_url']) && !empty($s['scheduled_at']);
                    $isExpired = $s['is_upload_expired'] ?? false;
                    $videoUploaded = !empty($s['video_path']);
                ?>
                <div class="list-group-item <?= $isExpired && !$videoUploaded ? 'list-group-item-danger' : '' ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="flex:1">
                            <strong>جلسه <?= (int) $s['session_number'] ?>:</strong> <?= e($s['title']) ?>
                            <?php if ($hasLive): ?>
                                <span class="badge bg-primary">آنلاین</span>
                                <small class="text-muted d-block">
                                    زمان: <?= e(format_datetime($s['scheduled_at'])) ?>
                                    <?php if ($s['adobe_connect_url']): ?>
                                        <a href="<?= e($s['adobe_connect_url']) ?>" target="_blank" rel="noopener" class="text-primary">(ورود به کلاس)</a>
                                    <?php endif; ?>
                                </small>
                            <?php endif; ?>
                            
                            <?php if ($s['description']): ?>
                                <p class="small text-muted mb-1"><?= e(mb_substr($s['description'], 0, 120)) ?></p>
                            <?php endif; ?>
                            
                            <div class="small">
                                <?php if ($s['video_path']): ?>
                                    <span class="badge bg-success">ویدئو ✓</span>
                                <?php elseif ($isExpired && $hasLive): ?>
                                    <span class="badge bg-danger">مهلت آپلود ویدئو گذشته!</span>
                                <?php elseif ($hasLive): ?>
                                    <span class="badge bg-warning text-dark">منتظر ویدئو (تا ۲ روز)</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">بدون ویدئو</span>
                                <?php endif; ?>
                                <?php if ($s['audio_path']): ?><span class="badge bg-info">صوت</span><?php endif; ?>
                                <?php if ($s['pdf_path']): ?><span class="badge bg-secondary">PDF</span><?php endif; ?>
                                <?php if ($s['adobe_connect_url']): ?><span class="badge bg-primary">لینک کلاس</span><?php endif; ?>
                            </div>
                        </div>
                        <div class="text-nowrap ms-2">
                            <a href="?course_id=<?= $courseId ?>&edit=<?= (int) $s['id'] ?>" class="btn btn-sm btn-outline-primary">ویرایش</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('حذف جلسه؟')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (!$sessions): ?>
                <div class="list-group-item text-muted text-center">هنوز جلسه‌ای ثبت نشده است.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/layout/teacher_footer.php'; ?>