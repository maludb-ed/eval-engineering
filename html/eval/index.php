<?php
/**
 * index.php — home directory for the Eval Engineering study site.
 *
 * Deploys to /var/www/html/eval/ on the Apache server. This is the landing
 * page: course overview, the eight modules with their topics, the shared
 * business scenarios, and the assessment plan.
 */
require_once __DIR__ . '/includes/config.php';
$page_title = 'Home';
$page_desc  = 'Study the Evaluation, Testing & Optimisation domain of the Claude Certified Architect: Professional exam — 8 modules, ' . $TOPIC_COUNT . ' topics.';
require __DIR__ . '/includes/header.php';
?>

<main class="container my-4 my-lg-5">

    <!-- ============================ HERO ============================ -->
    <section class="ev-hero p-4 p-lg-5 mb-5" id="top">
        <div class="ev-hero-body row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge ev-badge-domain rounded-pill mb-3 px-3 py-2">
                    <i class="bi bi-mortarboard-fill me-1"></i>
                    CCAR-P · Domain <?= (int)$COURSE['domain_no'] ?> · <?= (int)$COURSE['weight'] ?>% of the exam
                </span>
                <h1 class="display-5 fw-bold mb-2"><?= htmlspecialchars($COURSE['title']) ?></h1>
                <p class="fs-5 mb-3 opacity-75"><?= htmlspecialchars($COURSE['subtitle']) ?></p>
                <p class="mb-4 opacity-75" style="max-width: 46rem;">
                    A self-paced study guide for the <strong><?= htmlspecialchars($COURSE['domain']) ?></strong>
                    domain. It goes deeper than its raw item count because these ideas are load-bearing for
                    Integration (RAG quality), Governance (safety evals), and Lifecycle (reporting results) too.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#modules" class="btn btn-light btn-lg px-4">
                        <i class="bi bi-collection me-1"></i> Browse the modules
                    </a>
                    <a href="#overview" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-info-circle me-1"></i> What's inside
                    </a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row row-cols-2 g-2 text-center">
                    <div class="col">
                        <div class="ev-hero-mini p-3 rounded-3" style="background: rgba(255,255,255,.08);">
                            <div class="fs-2 fw-bold"><?= count($MODULES) ?></div>
                            <div class="small opacity-75">modules</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="ev-hero-mini p-3 rounded-3" style="background: rgba(255,255,255,.08);">
                            <div class="fs-2 fw-bold"><?= (int)$TOPIC_COUNT ?></div>
                            <div class="small opacity-75">topics</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="ev-hero-mini p-3 rounded-3" style="background: rgba(255,255,255,.08);">
                            <div class="fs-2 fw-bold">~<?= (int)$COURSE['hours'] ?>h</div>
                            <div class="small opacity-75">time budget</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="ev-hero-mini p-3 rounded-3" style="background: rgba(255,255,255,.08);">
                            <div class="fs-2 fw-bold"><?= (int)$COURSE['weight'] ?>%</div>
                            <div class="small opacity-75">of the exam</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================== OVERVIEW ========================== -->
    <section id="overview" class="mb-5">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="ev-section-eyebrow mb-2">What you'll be able to do</div>
                <h2 class="h3 fw-bold mb-3">Learning outcomes</h2>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($OUTCOMES as $o): ?>
                        <li class="d-flex gap-2 mb-2">
                            <i class="bi bi-check-circle-fill ev-outcome-check mt-1"></i>
                            <span><?= htmlspecialchars($o) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-5">
                <div class="ev-section-eyebrow mb-2">How it's built</div>
                <h2 class="h3 fw-bold mb-3">The exam's point of view</h2>
                <p class="text-secondary">
                    Every item is written for an architect who must <em>defend</em> a measurement choice, not a
                    researcher chasing a benchmark. Four stems recur across the domain:
                </p>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-1-circle-fill ev-outcome-check mt-1"></i>
                        <span>"Quality dropped after X" — <span class="text-secondary">trace it to what changed.</span></span>
                    </div>
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-2-circle-fill ev-outcome-check mt-1"></i>
                        <span>"Prove this change is better" — <span class="text-secondary">gate, then confirm.</span></span>
                    </div>
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-3-circle-fill ev-outcome-check mt-1"></i>
                        <span>"Cut cost/latency without losing accuracy" — <span class="text-secondary">real numbers.</span></span>
                    </div>
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-4-circle-fill ev-outcome-check mt-1"></i>
                        <span>"What do we log" — <span class="text-secondary">the trace is the unit.</span></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================== MODULES ========================== -->
    <section id="modules" class="mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
            <div>
                <div class="ev-section-eyebrow mb-1">The syllabus</div>
                <h2 class="h3 fw-bold mb-0">Eight modules, <?= (int)$TOPIC_COUNT ?> topics</h2>
            </div>
            <span class="text-secondary small">Each module maps to an official task statement.</span>
        </div>

        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php foreach ($MODULES as $m): ?>
                <div class="col">
                    <div class="ev-module-card p-4 h-100" id="module-<?= (int)$m['no'] ?>">
                        <a class="ev-card-link stretched-link" href="module.php?m=<?= (int)$m['no'] ?>"
                           aria-label="Open Module <?= (int)$m['no'] ?>: <?= htmlspecialchars($m['title']) ?>">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="ev-module-icon"><i class="bi bi-<?= htmlspecialchars($m['icon']) ?>"></i></span>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="ev-module-no">Module <?= (int)$m['no'] ?></span>
                                        <span class="badge text-bg-light border">
                                            <i class="bi bi-clock me-1"></i><?= htmlspecialchars($m['time']) ?>
                                        </span>
                                    </div>
                                    <h3 class="h5 fw-bold mb-0 mt-1"><?= htmlspecialchars($m['title']) ?></h3>
                                </div>
                            </div>

                            <p class="text-secondary small mb-2"><?= htmlspecialchars($m['blurb']) ?></p>
                        </a>
                        <p class="small mb-3">
                            <span class="badge rounded-pill text-bg-secondary-subtle text-secondary-emphasis border ev-task-badge">
                                <i class="bi bi-bookmark-check me-1"></i>Task <?= htmlspecialchars($m['task']) ?>
                            </span>
                        </p>

                        <ul class="ev-topic-list">
                            <?php foreach ($m['topics'] as $t): ?>
                                <li>
                                    <a href="module.php?m=<?= (int)$m['no'] ?>#<?= htmlspecialchars($t['id']) ?>">
                                        <span class="ev-topic-id"><?= htmlspecialchars($t['id']) ?></span>
                                        <span><?= htmlspecialchars($t['title']) ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="mt-3 pt-2 border-top">
                            <a class="ev-module-open small fw-semibold text-decoration-none" href="module.php?m=<?= (int)$m['no'] ?>">
                                Study this module <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ========================== SCENARIOS ========================= -->
    <section id="scenarios" class="mb-5">
        <div class="ev-section-eyebrow mb-1">Recurring contexts</div>
        <h2 class="h3 fw-bold mb-2">Six business scenarios</h2>
        <p class="text-secondary mb-4" style="max-width: 48rem;">
            The lessons and questions reuse a small set of systems, so you build familiarity with a few
            realistic contexts instead of meeting a new one in every item.
        </p>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            <?php foreach ($SCENARIOS as $s): ?>
                <div class="col">
                    <div class="ev-scenario-card p-3 h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="ev-scenario-no"><?= (int)$s['no'] ?></span>
                            <span class="fw-semibold"><?= htmlspecialchars($s['name']) ?></span>
                        </div>
                        <div class="text-secondary small mb-1"><?= htmlspecialchars($s['desc']) ?></div>
                        <div class="small"><i class="bi bi-tags me-1 text-secondary"></i><span class="text-secondary"><?= htmlspecialchars($s['tags']) ?></span></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ========================= ASSESSMENT ========================= -->
    <section id="assessment" class="mb-4">
        <div class="ev-section-eyebrow mb-1">How you'll be tested</div>
        <h2 class="h3 fw-bold mb-2">Assessment plan</h2>
        <p class="text-secondary mb-4">
            Readiness bar: <strong>&ge; 80%</strong> on the domain mock, with no task statement below 70%.
        </p>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th scope="col">Instrument</th>
                        <th scope="col">When</th>
                        <th scope="col">Items</th>
                        <th scope="col">Purpose</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ASSESSMENT as $a): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($a['instrument']) ?></td>
                            <td><?= htmlspecialchars($a['when']) ?></td>
                            <td><span class="badge text-bg-light border"><?= htmlspecialchars($a['items']) ?></span></td>
                            <td class="text-secondary"><?= htmlspecialchars($a['purpose']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
