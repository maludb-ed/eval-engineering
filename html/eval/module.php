<?php
/**
 * module.php — renders one module's full study guide.
 *
 * URL: module.php?m=N (N = 1..8). Each topic renders as an article with the
 * study content from includes/content/module-N.php followed by its sample
 * questions. Deep-linkable: module.php?m=1#ev-1-02.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/content-helpers.php';

$m_no = (int)($_GET['m'] ?? 0);
$module = null;
foreach ($MODULES as $candidate) {
    if ($candidate['no'] === $m_no) {
        $module = $candidate;
        break;
    }
}
if ($module === null) {
    header('Location: index.php#modules');
    exit;
}

$content   = ev_load_module_content($m_no);
$prev      = $m_no > 1 ? $MODULES[$m_no - 2] : null;
$next      = $m_no < count($MODULES) ? $MODULES[$m_no] : null;

$page_title = 'Module ' . $m_no . ' · ' . $module['title'];
$page_desc  = $module['blurb'];
require __DIR__ . '/includes/header.php';
?>

<main class="container my-4 my-lg-5">

    <!-- ======================= MODULE HEADER ======================== -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php#modules">Modules</a></li>
            <li class="breadcrumb-item active" aria-current="page">Module <?= (int)$module['no'] ?></li>
        </ol>
    </nav>

    <header class="ev-module-header p-4 p-lg-5 mb-4" id="top">
        <div class="d-flex align-items-start gap-3">
            <span class="ev-module-icon ev-module-icon-lg"><i class="bi bi-<?= htmlspecialchars($module['icon']) ?>"></i></span>
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="ev-module-no">Module <?= (int)$module['no'] ?> of <?= count($MODULES) ?></span>
                    <span class="badge text-bg-light border"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($module['time']) ?></span>
                </div>
                <h1 class="h2 fw-bold mb-2"><?= htmlspecialchars($module['title']) ?></h1>
                <p class="text-secondary mb-2" style="max-width: 46rem;"><?= htmlspecialchars($module['blurb']) ?></p>
                <span class="badge rounded-pill text-bg-secondary-subtle text-secondary-emphasis border ev-task-badge">
                    <i class="bi bi-bookmark-check me-1"></i>Task <?= htmlspecialchars($module['task']) ?>
                </span>
            </div>
        </div>
    </header>

    <div class="row g-4 g-lg-5">

        <!-- ========================= SIDEBAR ======================== -->
        <aside class="col-lg-3 order-lg-2">
            <div class="ev-toc sticky-lg-top">
                <div class="ev-section-eyebrow mb-2">In this module</div>
                <nav class="nav flex-column ev-toc-nav mb-4">
                    <?php foreach ($module['topics'] as $t): ?>
                        <a class="nav-link" href="#<?= htmlspecialchars($t['id']) ?>">
                            <span class="ev-topic-id"><?= htmlspecialchars($t['id']) ?></span>
                            <?= htmlspecialchars($t['title']) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <div class="d-flex flex-column gap-2">
                    <?php if ($prev): ?>
                        <a class="btn btn-sm btn-outline-secondary text-start" href="module.php?m=<?= (int)$prev['no'] ?>">
                            <i class="bi bi-arrow-left me-1"></i> M<?= (int)$prev['no'] ?>: <?= htmlspecialchars($prev['title']) ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($next): ?>
                        <a class="btn btn-sm btn-outline-secondary text-start" href="module.php?m=<?= (int)$next['no'] ?>">
                            M<?= (int)$next['no'] ?>: <?= htmlspecialchars($next['title']) ?> <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </aside>

        <!-- ========================== TOPICS ======================== -->
        <div class="col-lg-9 order-lg-1">
            <?php foreach ($module['topics'] as $ti => $t): ?>
                <?php $tc = $content[$t['id']] ?? null; ?>
                <article class="ev-topic mb-5" id="<?= htmlspecialchars($t['id']) ?>">
                    <div class="ev-topic-header mb-3 pb-2 border-bottom">
                        <div class="ev-section-eyebrow mb-1">
                            Topic <?= $ti + 1 ?> of <?= count($module['topics']) ?> ·
                            <span class="ev-topic-id"><?= htmlspecialchars($t['id']) ?></span>
                        </div>
                        <h2 class="h3 fw-bold mb-0"><?= htmlspecialchars($t['title']) ?></h2>
                    </div>

                    <?php if ($tc): ?>
                        <div class="ev-topic-body">
                            <?= $tc['body'] /* trusted courseware HTML */ ?>
                        </div>
                        <?php ev_render_questions($t['id'], $tc['questions'] ?? []); ?>
                    <?php else: ?>
                        <div class="alert alert-light border text-secondary">
                            <i class="bi bi-hourglass-split me-1"></i>
                            Content for this topic is being prepared.
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>

            <!-- Bottom prev/next -->
            <div class="d-flex justify-content-between gap-2 border-top pt-4">
                <?php if ($prev): ?>
                    <a class="btn btn-outline-secondary" href="module.php?m=<?= (int)$prev['no'] ?>">
                        <i class="bi bi-arrow-left me-1"></i> Module <?= (int)$prev['no'] ?>
                    </a>
                <?php else: ?>
                    <a class="btn btn-outline-secondary" href="index.php#modules">
                        <i class="bi bi-grid me-1"></i> All modules
                    </a>
                <?php endif; ?>
                <?php if ($next): ?>
                    <a class="btn btn-primary ev-btn-accent" href="module.php?m=<?= (int)$next['no'] ?>">
                        Module <?= (int)$next['no'] ?> <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                <?php else: ?>
                    <a class="btn btn-primary ev-btn-accent" href="index.php#assessment">
                        Assessment plan <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
