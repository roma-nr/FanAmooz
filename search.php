<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$query = trim($_GET['q'] ?? '');
$pageTitle = 'جستجو: ' . e($query);
require __DIR__ . '/includes/layout/header.php';
?>

<div class="container ptb-100">
    <h1 class="h3 mb-4">نتایج جستجو برای «<span id="queryDisplay"><?= e($query) ?></span>»</h1>

    <!-- بخش دوره‌ها -->
    <div id="coursesSection" style="display:none;">
        <h2 class="h5 text-primary mb-3">دوره‌ها (<span id="coursesCount">0</span>)</h2>
        <div id="coursesResults" class="row"></div>
        <nav id="coursesPagination" class="mt-3 d-flex justify-content-center"></nav>
        <hr class="my-4">
    </div>

    <!-- بخش اطلاعیه‌ها -->
    <div id="announcementsSection" style="display:none;">
        <h2 class="h5 text-primary mb-3">اطلاعیه‌ها (<span id="announcementsCount">0</span>)</h2>
        <div id="announcementsResults" class="row"></div>
        <nav id="announcementsPagination" class="mt-3 d-flex justify-content-center"></nav>
    </div>

    <div id="noResults" class="alert alert-info" style="display:none;">موردی یافت نشد.</div>
</div>

<script>
const query = '<?= addslashes($query) ?>';
const limit = 6;

function renderSection(data, containerId, paginationId) {
    const container = document.getElementById(containerId);
    const pagination = document.getElementById(paginationId);
    container.innerHTML = '';
    pagination.innerHTML = '';

    if (data.results.length === 0) return;

    data.results.forEach(item => {
        const col = document.createElement('div');
        col.className = 'col-md-6 mb-3';
        col.innerHTML = `
            <div class="card h-100">
                <div class="card-body">
                    <span class="badge bg-warning text-dark mb-2">${item.type === 'course' ? 'دوره' : 'اطلاعیه'}</span>
                    <h5><a href="${item.url}">${item.highlighted_title}</a></h5>
                    <p class="small text-muted">${item.highlighted_description}</p>
                </div>
            </div>
        `;
        container.appendChild(col);
    });

    if (data.total_pages > 1) {
        let html = '<ul class="pagination">';
        for (let i = 1; i <= data.total_pages; i++) {
            html += `<li class="page-item ${i === data.page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }
        html += '</ul>';
        pagination.innerHTML = html;
    }
}

function loadResults(pageCourses = 1, pageAnnouncements = 1) {
    fetch(`<?= base_url('api/search.php') ?>?q=${encodeURIComponent(query)}&page_courses=${pageCourses}&page_announcements=${pageAnnouncements}&limit=${limit}`)
        .then(res => res.json())
        .then(data => {
            // دوره‌ها
            const coursesData = data.courses;
            document.getElementById('coursesCount').textContent = coursesData.total;
            document.getElementById('coursesSection').style.display = coursesData.results.length > 0 ? 'block' : 'none';
            renderSection(coursesData, 'coursesResults', 'coursesPagination');

            // اطلاعیه‌ها
            const announcementsData = data.announcements;
            document.getElementById('announcementsCount').textContent = announcementsData.total;
            document.getElementById('announcementsSection').style.display = announcementsData.results.length > 0 ? 'block' : 'none';
            renderSection(announcementsData, 'announcementsResults', 'announcementsPagination');

            // پیام خالی
            document.getElementById('noResults').style.display =
                (coursesData.results.length === 0 && announcementsData.results.length === 0) ? 'block' : 'none';

            // بازچسباندن event listener برای pagination
            attachPaginationEvents();
        });
}

function attachPaginationEvents() {
    document.querySelectorAll('#coursesPagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = parseInt(this.dataset.page);
            loadResults(page, 1);
            window.scrollTo({ top: 300, behavior: 'smooth' });
        });
    });
    document.querySelectorAll('#announcementsPagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = parseInt(this.dataset.page);
            loadResults(1, page);
            window.scrollTo({ top: 300, behavior: 'smooth' });
        });
    });
}

if (query.length >= 2) {
    loadResults(1, 1);
} else {
    document.getElementById('noResults').style.display = 'block';
    document.getElementById('noResults').textContent = 'حداقل دو حرف وارد کنید.';
}
</script>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>