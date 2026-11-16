<?php
// page.php - Dynamic Page Renderer for all website pages
require_once __DIR__ . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');

if (empty($slug)) {
    header("Location: index.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND is_published = 1");
$stmt->execute([$slug]);
$page = $stmt->fetch();

if (!$page) {
    http_response_code(404);
    $pageTitle = 'Page Not Found';
    include __DIR__ . '/includes/header.php';
    ?>
    <main class="page-body-container">
        <div class="container" style="text-align: center; padding: 100px 0;">
            <span class="badge badge-danger" style="margin-bottom: 20px;">404 Error</span>
            <h1 style="font-size: 3rem; margin-bottom: 20px;">Page Not Found</h1>
            <p style="color: #94a3b8; max-width: 500px; margin: 0 auto 30px;">The requested page could not be located or may have been updated. Please return to the homepage.</p>
            <a href="index.php" class="btn btn-primary">Return to Homepage</a>
        </div>
    </main>
    <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $page['title'];
$pageDesc = $page['meta_description'] ?? substr(strip_tags($page['content']), 0, 160);

include __DIR__ . '/includes/header.php';
?>

<main>
    <!-- Page Header Header -->
    <section class="page-header">
        <div class="container">
            <span class="eyebrow">Official Page</span>
            <h1><?= clean($page['title']) ?></h1>
            <?php if (!empty($page['subtitle'])): ?>
                <p><?= clean($page['subtitle']) ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Page Article Content -->
    <section class="page-body-container">
        <div class="container">
            <article class="page-card-article">
                <?= $page['content'] ?>
                
                <div style="margin-top: 50px; padding-top: 25px; border-top: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; font-size: 0.85rem; color: var(--text-muted);">
                    <div>
                        Last Updated: <strong><?= date('F j, Y', strtotime($page['updated_at'])) ?></strong>
                    </div>
                    <div>
                        <a href="index.php" style="color: var(--accent-emerald-light); font-weight: 600;">&larr; Back to Home</a>
                    </div>
                </div>
            </article>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
