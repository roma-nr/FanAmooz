<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$isResubmit = false;
$userRow = null;
$existingApp = null;
$errors = [];

if (auth_check()) {
    if (auth_role() !== 'teacher') {
        flash('error', 'برای تکمیل فرم استادی، ابتدا از حساب فعلی خارج شوید.');
        redirect(base_url());
    }
    teacher_refresh_session_status();
    $st = teacher_session_status();
    if ($st === 'pending' || $st === 'approved') {
        redirect(base_url('teacher/index.php'));
    }
    if ($st === 'rejected') {
        $isResubmit = true;
        $uid = (int) auth_id();
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ? AND role = ? LIMIT 1');
        $stmt->execute([$uid, 'teacher']);
        $userRow = $stmt->fetch() ?: null;
        $existingApp = teacher_application_for_user($uid);
        if (!$userRow || !$existingApp) {
            flash('error', 'اطلاعات درخواست یافت نشد.');
            redirect(base_url('teacher/index.php'));
        }
    }
}

$pageTitle = $isResubmit ? 'اصلاح درخواست همکاری' : 'درخواست همکاری استاد';
$provinces = db()->query('SELECT id, name FROM provinces WHERE is_active = 1 ORDER BY sort_order, name')->fetchAll();

if (is_post() && empty($errors)) {
    if (!verify_csrf()) {
        $errors['general'] = 'درخواست نامعتبر است.';
    } else {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = mb_strtolower(trim($_POST['email'] ?? ''), 'UTF-8');
        $phone = trim($_POST['phone'] ?? '') ?: null;
        $nationalId = trim($_POST['national_id'] ?? '');
        $provinceId = (int) ($_POST['province_id'] ?? 0);
        $institutionId = (int) ($_POST['institution_id'] ?? 0) ?: null;
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password_confirm'] ?? '';
        $education = trim($_POST['education'] ?? '');
        $workExperience = trim($_POST['work_experience'] ?? '');
        $skillsSummary = trim($_POST['skills_summary'] ?? '');

        // اعتبارسنجی کامل
        if ($fullName === '') {
            $errors['full_name'] = 'نام کامل الزامی است.';
        }
        if ($email === '') {
            $errors['email'] = 'ایمیل الزامی است.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'ایمیل معتبر نیست.';
        }
        if ($nationalId === '') {
            $errors['national_id'] = 'کد ملی الزامی است.';
        } elseif (!validate_national_id($nationalId)) {
            $errors['national_id'] = 'کد ملی نامعتبر است (۱۰ رقم).';
        }
        if ($provinceId <= 0) {
            $errors['province_id'] = 'انتخاب استان الزامی است.';
        }
        if (mb_strlen($education) < 20) {
            $errors['education'] = 'سوابق تحصیلی حداقل ۲۰ کاراکتر باشد.';
        }
        if (mb_strlen($workExperience) < 20) {
            $errors['work_experience'] = 'سوابق شغلی حداقل ۲۰ کاراکتر باشد.';
        }
        if ($skillsSummary === '' || mb_strlen($skillsSummary) < 10) {
            $errors['skills_summary'] = 'خلاصه توانمندی‌ها را تکمیل کنید (حداقل ۱۰ کاراکتر).';
        } elseif (mb_strlen($skillsSummary) > 1000) {
            $errors['skills_summary'] = 'خلاصه توانمندی‌ها حداکثر ۱۰۰۰ کاراکتر باشد.';
        }

        // بررسی دانشکده
        if ($institutionId > 0 && empty($errors['province_id'])) {
            $iq = db()->prepare('SELECT province_id FROM institutions WHERE id = ? AND is_active = 1');
            $iq->execute([$institutionId]);
            $instRow = $iq->fetch();
            if (!$instRow) {
                $errors['institution_id'] = 'دانشکده انتخاب‌شده معتبر نیست.';
            } elseif ((int) $instRow['province_id'] !== $provinceId) {
                $errors['institution_id'] = 'دانشکده باید متعلق به استان انتخاب‌شده باشد.';
            }
        }

        // رمز عبور
        if (!$isResubmit) {
            if (strlen($password) < 8) {
                $errors['password'] = 'رمز عبور حداقل ۸ کاراکتر باشد.';
            } elseif ($password !== $password2) {
                $errors['password_confirm'] = 'تکرار رمز عبور یکسان نیست.';
            }
        } else {
            if ($password !== '' || $password2 !== '') {
                if (strlen($password) < 8) {
                    $errors['password'] = 'رمز عبور جدید حداقل ۸ کاراکتر باشد.';
                } elseif ($password !== $password2) {
                    $errors['password_confirm'] = 'تکرار رمز عبور یکسان نیست.';
                }
            }
        }

        // بررسی تکراری بودن ایمیل (فقط در ثبت اولیه)
        if (!$isResubmit && empty($errors['email'])) {
            $dup = db()->prepare('SELECT id, role FROM users WHERE username = ? OR email = ? LIMIT 1');
            $dup->execute([$email, $email]);
            if ($dup->fetch()) {
                $errors['email'] = 'این ایمیل قبلاً ثبت شده است. اگر استاد هستید از ورود استفاده کنید.';
            }
        }

        if (empty($errors)) {
            try {
                $resumePath = null;
                $resumeInput = $_FILES['resume'] ?? null;
                $fileErr = is_array($resumeInput) ? (int) ($resumeInput['error'] ?? UPLOAD_ERR_NO_FILE) : UPLOAD_ERR_NO_FILE;
                if ($fileErr !== UPLOAD_ERR_NO_FILE) {
                    $resumePath = handle_upload($resumeInput, 'teachers/resumes', ['pdf', 'doc', 'docx'], 10 * 1024 * 1024);
                }

                if (!$isResubmit && $resumePath === null) {
                    $errors['resume'] = 'آپلود رزومه الزامی است (PDF یا Word، حداکثر ۱۰ مگابایت).';
                } else {
                    $pdo = db();
                    $pdo->beginTransaction();

                    if ($isResubmit) {
                        $uid = (int) $userRow['id'];
                        $sql = "UPDATE users SET full_name=?, username=?, email=?, phone=?, province_id=?, institution_id=?, national_id=?, teacher_status=?";
                        $execParams = [$fullName, $email, $email, $phone, $provinceId, $institutionId, $nationalId, 'pending'];
                        if ($password !== '') {
                            $sql .= ", password_hash=?";
                            $execParams[] = password_hash($password, PASSWORD_BCRYPT);
                        }
                        $sql .= " WHERE id=? AND role='teacher'";
                        $execParams[] = $uid;
                        $pdo->prepare($sql)->execute($execParams);

                        $oldResume = $existingApp['resume_path'] ?? '';
                        if ($resumePath !== null && $oldResume !== '') {
                            delete_upload($oldResume);
                        }
                        $newResume = $resumePath ?? $oldResume;
                        if ($newResume === null || $newResume === '') {
                            $pdo->rollBack();
                            throw new RuntimeException('رزومه در سیستم یافت نشد؛ لطفاً فایل جدید بارگذاری کنید.');
                        }
                        $pdo->prepare("UPDATE teacher_applications SET education=?, work_experience=?, skills_summary=?, resume_path=?, admin_note=NULL, reviewed_at=NULL, reviewed_by=NULL WHERE user_id=?")->execute([
                            $education, $workExperience, $skillsSummary, $newResume, $uid
                        ]);
                        $pdo->commit();
                        auth_login([
                            'id' => $uid,
                            'role' => 'teacher',
                            'full_name' => $fullName,
                            'username' => $email,
                            'first_login_done' => 1,
                            'institution_id' => $institutionId,
                            'teacher_status' => 'pending'
                        ]);
                        flash('success', 'درخواست شما مجدداً ارسال شد و در حال بررسی است.');
                        redirect(base_url('teacher/index.php'));
                    } else {
                        $hash = password_hash($password, PASSWORD_BCRYPT);
                        $ins = $pdo->prepare("INSERT INTO users (role, username, email, password_hash, full_name, phone, province_id, institution_id, national_id, teacher_status, first_login_done, is_active) VALUES ('teacher', ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 1, 1)");
                        $ins->execute([$email, $email, $hash, $fullName, $phone, $provinceId, $institutionId, $nationalId]);
                        $newId = (int) $pdo->lastInsertId();
                        $pdo->prepare("INSERT INTO teacher_applications (user_id, education, work_experience, skills_summary, resume_path) VALUES (?,?,?,?,?)")->execute([$newId, $education, $workExperience, $skillsSummary, $resumePath]);
                        $pdo->commit();
                        auth_login([
                            'id' => $newId,
                            'role' => 'teacher',
                            'full_name' => $fullName,
                            'username' => $email,
                            'first_login_done' => 1,
                            'institution_id' => $institutionId,
                            'teacher_status' => 'pending'
                        ]);
                        flash('success', 'حساب شما ایجاد شد. از همین ایمیل و رمز عبور برای ورود و پیگیری استفاده کنید.');
                        redirect(base_url('teacher/index.php'));
                    }
                }
            } catch (Throwable $e) {
                if (isset($pdo) && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors['general'] = $e->getMessage();
            }
        }
    }
}

$defaultProvince = $userRow ? (int) $userRow['province_id'] : 0;
$defaultInstitution = $userRow && $userRow['institution_id'] !== null ? (int) $userRow['institution_id'] : 0;

require dirname(__DIR__) . '/includes/layout/header.php';
?>

<div class="container py-4" style="max-width:720px">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 text-primary mb-3"><?= $isResubmit ? 'اصلاح و ارسال مجدد درخواست' : 'فرم درخواست همکاری تدریس' ?></h1>
            <p class="text-muted small"><?= $isResubmit ? 'پس از ارسال، وضعیت به «در حال بررسی» برمی‌گردد.' : 'پس از ثبت، یک حساب کاربری موقت با نقش استاد ایجاد می‌شود تا وضعیت را پیگیری کنید.' ?></p>

            <?php if (!empty($errors['general'])): ?><div class="alert alert-danger"><?= e($errors['general']) ?></div><?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="mt-3" novalidate>
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">نام و نام خانوادگی <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" value="<?= old('full_name', $userRow['full_name'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['full_name'] ?? '' ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ایمیل (نام کاربری ورود) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" dir="ltr" value="<?= old('email', $userRow['username'] ?? '') ?>" <?= $isResubmit ? 'readonly' : '' ?>>
                        <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">شماره تماس</label>
                        <input type="text" name="phone" class="form-control" dir="ltr" value="<?= old('phone', $userRow['phone'] ?? '') ?>" maxlength="11">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">کد ملی <span class="text-danger">*</span></label>
                        <input type="text" name="national_id" class="form-control <?= isset($errors['national_id']) ? 'is-invalid' : '' ?>" maxlength="10" pattern="\d{10}" dir="ltr" value="<?= old('national_id', $userRow['national_id'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['national_id'] ?? '' ?></div>
                        <small class="text-muted">۱۰ رقم</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">استان <span class="text-danger">*</span></label>
                        <select name="province_id" id="province_id" class="form-select <?= isset($errors['province_id']) ? 'is-invalid' : '' ?>">
                            <option value="">انتخاب استان</option>
                            <?php foreach ($provinces as $p): ?>
                                <option value="<?= (int) $p['id'] ?>" <?= old('province_id', $defaultProvince) == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= $errors['province_id'] ?? '' ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">دانشکده (اختیاری)</label>
                        <select name="institution_id" id="institution_id" class="form-select <?= isset($errors['institution_id']) ? 'is-invalid' : '' ?>">
                            <option value="0">---</option>
                        </select>
                        <div class="invalid-feedback"><?= $errors['institution_id'] ?? '' ?></div>
                    </div>
                </div>

                <?php if (!$isResubmit): ?>
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">رمز عبور <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" autocomplete="new-password" minlength="8">
                        <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                        <small class="text-muted">حداقل ۸ کاراکتر</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">تکرار رمز عبور <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirm" class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" autocomplete="new-password" minlength="8">
                        <div class="invalid-feedback"><?= $errors['password_confirm'] ?? '' ?></div>
                    </div>
                </div>
                <?php else: ?>
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">رمز عبور جدید (اختیاری)</label>
                        <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" autocomplete="new-password" minlength="8">
                        <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">تکرار رمز جدید</label>
                        <input type="password" name="password_confirm" class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" autocomplete="new-password" minlength="8">
                        <div class="invalid-feedback"><?= $errors['password_confirm'] ?? '' ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-3">
                    <label class="form-label">سوابق تحصیلی <span class="text-danger">*</span></label>
                    <textarea name="education" class="form-control <?= isset($errors['education']) ? 'is-invalid' : '' ?>" rows="4"><?= old('education', $existingApp['education'] ?? '') ?></textarea>
                    <div class="invalid-feedback"><?= $errors['education'] ?? '' ?></div>
                    <small class="text-muted">حداقل ۲۰ کاراکتر</small>
                </div>

                <div class="mt-3">
                    <label class="form-label">سوابق شغلی و تدریس <span class="text-danger">*</span></label>
                    <textarea name="work_experience" class="form-control <?= isset($errors['work_experience']) ? 'is-invalid' : '' ?>" rows="4"><?= old('work_experience', $existingApp['work_experience'] ?? '') ?></textarea>
                    <div class="invalid-feedback"><?= $errors['work_experience'] ?? '' ?></div>
                    <small class="text-muted">حداقل ۲۰ کاراکتر</small>
                </div>

                <div class="mt-3">
                    <label class="form-label">توانمندی‌ها و حوزه‌های تخصصی <span class="text-danger">*</span></label>
                    <textarea name="skills_summary" class="form-control <?= isset($errors['skills_summary']) ? 'is-invalid' : '' ?>" rows="3" maxlength="1000" placeholder="مثال: پایتون، شبکه، امنیت..."><?= old('skills_summary', $existingApp['skills_summary'] ?? '') ?></textarea>
                    <div class="invalid-feedback"><?= $errors['skills_summary'] ?? '' ?></div>
                    <small class="text-muted">حداکثر ۱۰۰۰ کاراکتر</small>
                </div>

                <div class="mt-3">
                    <label class="form-label">رزومه <?= $isResubmit ? '<small class="text-muted">(در صورت عدم تغییر، همان فایل قبلی)</small>' : '<span class="text-danger">*</span>' ?></label>
                    <input type="file" name="resume" class="form-control <?= isset($errors['resume']) ? 'is-invalid' : '' ?>" accept=".pdf,.doc,.docx" <?= $isResubmit ? '' : 'required' ?>>
                    <div class="invalid-feedback"><?= $errors['resume'] ?? '' ?></div>
                    <div class="form-text">PDF یا Word - حداکثر ۱۰ مگابایت</div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary"><?= $isResubmit ? 'ارسال مجدد' : 'ثبت درخواست' ?></button>
                    <a href="<?= e(base_url()) ?>" class="btn btn-outline-secondary">انصراف</a>
                    <?php if ($isResubmit): ?>
                        <a href="<?= e(base_url('teacher/index.php')) ?>" class="btn btn-outline-primary">بازگشت به داشبورد</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const provinceSelect = document.getElementById('province_id');
    const instSelect = document.getElementById('institution_id');
    const preInst = <?= (int) old('institution_id', $defaultInstitution) ?>;
    function loadInstitutions(pid) {
        if (!pid) { instSelect.innerHTML = '<option value="0">---</option>'; return; }
        instSelect.innerHTML = '<option value="0">در حال بارگذاری...</option>';
        fetch('<?= e(base_url('api/institutions.php')) ?>?province_id=' + pid)
            .then(r => r.json())
            .then(data => {
                instSelect.innerHTML = '<option value="0">---</option>';
                data.forEach(i => {
                    const opt = document.createElement('option');
                    opt.value = i.id;
                    opt.textContent = i.name;
                    if (preInst && Number(i.id) === preInst) opt.selected = true;
                    instSelect.appendChild(opt);
                });
            });
    }
    if (provinceSelect) {
        provinceSelect.addEventListener('change', function() { loadInstitutions(this.value); });
        if (provinceSelect.value) loadInstitutions(provinceSelect.value);
    }
})();
</script>

<?php require dirname(__DIR__) . '/includes/layout/footer.php'; ?>