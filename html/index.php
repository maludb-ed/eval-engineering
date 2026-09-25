<?php
/**
 * index.php — site landing page ("Orbit View").
 *
 * Deploys to /var/www/html/ on the Apache server, one level above the
 * Eval Engineering study site in eval/. It renders the course as an orbit:
 * the course at the centre, the eight modules on a ring around it, and each
 * module's topics as satellites. Everything is drawn from eval/includes/config.php,
 * so the page follows the course structure without edits here.
 *
 * The SVG is rendered server-side and every node is a plain link, so the
 * page works without JavaScript; assets/landing.js adds the detail panel,
 * search, module filter, zoom and the motion toggle.
 */
require_once __DIR__ . '/eval/includes/config.php';

$EVAL_BASE = 'eval/';

/** Landing-page title. The course inside eval/ keeps its own name ($COURSE['title']). */
$SITE_TITLE = 'Claude Architect Certification';
// The orbit centre shows the title on two lines, split at the last space.
$cut = strrpos($SITE_TITLE, ' ');
$TITLE_LINES = $cut === false ? [$SITE_TITLE] : [substr($SITE_TITLE, 0, $cut), substr($SITE_TITLE, $cut + 1)];

/** Short module names for the orbit labels and filter chips (full titles are too long). */
$SHORT = [
    1 => 'Criteria & Metrics',
    2 => 'Datasets & Harness',
    3 => 'Graders',
    4 => 'A/B & Iteration',
    5 => 'Diagnosis & RCA',
    6 => 'Tokens · Latency · Cost',
    7 => 'Monitoring',
    8 => 'Cross-Domain',
];

/**
 * Orbit geometry. Modules sit on a ring of radius $R around ($cx, $cy), offset
 * half a step from the vertical so the first half of the course lands on the
 * right (cyan) and the second half on the left (gold).
 */
$cx = 500; $cy = 340; $R = 215; $SAT_R = 30;
$count = count($MODULES);
$step  = 360 / max($count, 1);
$nodes = [];
foreach ($MODULES as $i => $m) {
    $a  = deg2rad(-90 + $step / 2 + $step * $i);
    $x  = $cx + $R * cos($a);
    $y  = $cy + $R * sin($a);
    // Quadratic control point: midpoint pushed out along the perpendicular, for a gentle curve.
    $qx = ($cx + $x) / 2 - 36 * sin($a);
    $qy = ($cy + $y) / 2 + 36 * cos($a);
    $nodes[] = [
        'm'     => $m,
        'short' => $SHORT[$m['no']] ?? $m['title'],
        'group' => $i < $count / 2 ? 'cyan' : 'gold',
        'x'     => round($x, 2),
        'y'     => round($y, 2),
        'path'  => sprintf('M %d %d Q %.2f %.2f %.2f %.2f', $cx, $cy, $qx, $qy, $x, $y),
        'right' => cos($a) >= 0,
        'dur'   => 4 + ($i * 3) % 5,     // particle travel time, varied per edge
        'spin'  => 22 + ($i * 7) % 16,   // satellite orbit period
    ];
}
$HEX = ['cyan' => '#35dff5', 'gold' => '#f2ca71'];

/** Data for landing.js (detail panel + search). */
$LP_DATA = [
    'base'   => $EVAL_BASE,
    'course' => [
        'title'    => $SITE_TITLE,
        'badge'    => 'CA',
        'subtitle' => $COURSE['subtitle'],
        'domain'   => $COURSE['domain'],
        'domainNo' => $COURSE['domain_no'],
        'weight'   => $COURSE['weight'],
        'hours'    => $COURSE['hours'],
        'topics'   => $TOPIC_COUNT,
        'outcomes' => $OUTCOMES,
    ],
    'modules' => array_map(fn($n) => [
        'no'     => $n['m']['no'],
        'title'  => $n['m']['title'],
        'short'  => $n['short'],
        'time'   => $n['m']['time'],
        'task'   => $n['m']['task'],
        'blurb'  => $n['m']['blurb'],
        'group'  => $n['group'],
        'topics' => array_map(fn($t) => ['id' => $t['id'], 'title' => $t['title']], $n['m']['topics']),
    ], $nodes),
];

/** Inline icons (24×24 stroke icons, same drawing style throughout). */
function lp_icon(string $name, int $size = 14): string
{
    static $paths = [
        'brand'  => '<path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"/>',
        'pause'  => '<rect x="14" y="4" width="4" height="16" rx="1"/><rect x="6" y="4" width="4" height="16" rx="1"/>',
        'play'   => '<polygon points="6 3 20 12 6 21 6 3"/>',
        'arrow'  => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
        'search' => '<path d="m21 21-4.34-4.34"/><circle cx="11" cy="11" r="8"/>',
        'minus'  => '<circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/><line x1="8" x2="14" y1="11" y2="11"/>',
        'plus'   => '<circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/><line x1="11" x2="11" y1="8" y2="14"/><line x1="8" x2="14" y1="11" y2="11"/>',
        'reset'  => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>',
        'signal' => '<path d="M2 20h.01"/><path d="M7 20v-4"/><path d="M12 20v-8"/><path d="M17 20V8"/><path d="M22 4v16"/>',
    ];
    return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none"'
         . ' stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
         . $paths[$name] . '</svg>';
}

$h = fn($s) => htmlspecialchars((string)$s);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $h($SITE_TITLE) ?>: the <?= $h($COURSE['domain']) ?> domain of the CCAR-P exam, in orbit — <?= $count ?> modules, <?= (int)$TOPIC_COUNT ?> topics.">
    <title><?= $h($SITE_TITLE) ?> — Every module, in orbit</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/landing.css?v=<?= @filemtime(__DIR__ . '/assets/landing.css') ?>" rel="stylesheet">
</head>
<body>

<main class="lp-root">

    <!-- ============================ HEADER ============================ -->
    <header class="lp-header">
        <a class="lp-brand" href="./">
            <?= lp_icon('brand', 18) ?>
            <span class="lp-wordmark"><?= $h($TITLE_LINES[0]) ?><?php if (isset($TITLE_LINES[1])): ?> <b><?= $h($TITLE_LINES[1]) ?></b><?php endif; ?></span>
            <span class="lp-tagline">Orbit view</span>
        </a>
        <div class="lp-header-right">
            <span class="lp-badge">Domain <?= (int)$COURSE['domain_no'] ?> · <?= (int)$COURSE['weight'] ?>% of exam</span>
            <button type="button" class="lp-pill lp-motion" aria-pressed="true" title="Pause motion">
                <span class="lp-motion-on"><?= lp_icon('pause') ?>Pause</span>
                <span class="lp-motion-off" hidden><?= lp_icon('play') ?>Play</span>
            </button>
            <a class="lp-pill lp-pill-cta" href="<?= $EVAL_BASE ?>"><?= lp_icon('arrow') ?>Open course</a>
        </div>
    </header>

    <div class="lp-body">

        <!-- ============================ LEFT RAIL ============================ -->
        <div class="lp-left">
            <p class="lp-eyebrow">CCAR-P / <?= $h($COURSE['domain']) ?></p>
            <h1 class="lp-heading"><?= $h($SITE_TITLE) ?><br><span>Every module.</span></h1>
            <p class="lp-subcopy">
                A self-paced study guide <?= $h($COURSE['subtitle']) ?>. The course sits at the centre,
                its modules in orbit — pick one to see what it covers.
            </p>
            <a class="lp-cta" href="<?= $EVAL_BASE ?>"><?= lp_icon('arrow', 16) ?>Start studying</a>

            <div class="lp-stats">
                <div><span><?= sprintf('%02d', $count) ?></span><label>Modules</label></div>
                <div><span><?= sprintf('%02d', $TOPIC_COUNT) ?></span><label>Topics</label></div>
                <div><span><?= sprintf('%02d', count($SCENARIOS)) ?></span><label>Scenarios</label></div>
                <div><span><?= sprintf('%02d', $COURSE['hours']) ?></span><label>Hours</label></div>
            </div>

            <div class="lp-search">
                <?= lp_icon('search') ?>
                <input type="text" id="lp-search" placeholder="Search modules, topics…" aria-label="Search the course" autocomplete="off">
            </div>
            <div class="lp-search-results" id="lp-results" hidden></div>

            <div class="lp-chips" role="group" aria-label="Focus a module">
                <button type="button" class="lp-chip active" data-module="">All modules</button>
                <?php foreach ($nodes as $n): ?>
                    <button type="button" class="lp-chip lp-<?= $n['group'] ?>" data-module="<?= (int)$n['m']['no'] ?>" aria-pressed="false"><?= $h($n['short']) ?></button>
                <?php endforeach; ?>
            </div>

            <div class="lp-legend">
                <p><span class="lp-dot lp-dot-cyan"></span> Design &amp; measure — modules 1–<?= (int)($count / 2) ?></p>
                <p><span class="lp-dot lp-dot-gold"></span> Diagnose &amp; operate — modules <?= (int)($count / 2) + 1 ?>–<?= $count ?></p>
                <p><span class="lp-dot lp-dot-topic"></span> Topic</p>
            </div>

            <div class="lp-zoom" role="group" aria-label="Zoom">
                <button type="button" data-zoom="out" aria-label="Zoom out"><?= lp_icon('minus') ?></button>
                <span id="lp-zoom-value">100%</span>
                <button type="button" data-zoom="in" aria-label="Zoom in"><?= lp_icon('plus') ?></button>
                <button type="button" data-zoom="reset" aria-label="Reset zoom" title="Reset view"><?= lp_icon('reset') ?></button>
            </div>

            <p class="lp-footer-note"><?= $h($COURSE['version']) ?> · updated <?= $h($COURSE['updated']) ?></p>
        </div>

        <!-- ============================ ORBIT STAGE ============================ -->
        <div class="lp-stage-scroll">
            <div class="lp-stage-zoom" id="lp-stage">
                <svg class="lp-network" viewBox="0 0 1000 680" role="group"
                     aria-label="<?= $h($SITE_TITLE) ?> at the centre, <?= $count ?> modules in orbit">
                    <defs>
                        <filter id="lpGlow" x="-60%" y="-60%" width="220%" height="220%">
                            <feGaussianBlur stdDeviation="4" result="b"/>
                            <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
                        </filter>
                        <radialGradient id="lpCore">
                            <stop offset="0" stop-color="#35dff5" stop-opacity=".55"/>
                            <stop offset=".55" stop-color="#35dff5" stop-opacity=".12"/>
                            <stop offset="1" stop-color="#35dff5" stop-opacity="0"/>
                        </radialGradient>
                    </defs>

                    <g aria-hidden="true">
                        <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="146" fill="none" stroke="#35dff5" stroke-opacity=".08" stroke-width=".5"/>
                        <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="156" fill="none" stroke="#f2ca71" stroke-opacity=".08" stroke-width=".5"/>
                        <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $R ?>" fill="none" stroke="#35dff5" stroke-opacity=".08" stroke-width=".5"/>
                        <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $R + 11 ?>" fill="none" stroke="#f2ca71" stroke-opacity=".08" stroke-width=".5"/>
                        <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $R + 62 ?>" fill="none" stroke="#35dff5" stroke-opacity=".08" stroke-width=".75" stroke-dasharray="2 6"/>
                    </g>

                    <g aria-hidden="true">
                        <?php foreach ($nodes as $n): $no = (int)$n['m']['no']; $c = $HEX[$n['group']]; ?>
                        <g class="lp-edge" data-module="<?= $no ?>">
                            <path id="lp-edge-<?= $no ?>" d="<?= $n['path'] ?>" fill="none" stroke="<?= $c ?>" stroke-opacity=".32" stroke-width="1.25"/>
                            <circle r="2.2" fill="<?= $c ?>" opacity=".85">
                                <animateMotion dur="<?= $n['dur'] ?>s" repeatCount="indefinite"><mpath href="#lp-edge-<?= $no ?>"/></animateMotion>
                            </circle>
                        </g>
                        <?php endforeach; ?>
                    </g>

                    <!-- Centre: the course -->
                    <a class="lp-node lp-centre" href="<?= $EVAL_BASE ?>" data-module="course"
                       aria-label="<?= $h($SITE_TITLE) ?> — course overview">
                        <g transform="translate(<?= $cx ?> <?= $cy ?>)">
                            <circle r="104" fill="#04080f"/>
                            <circle r="104" fill="url(#lpCore)" class="lp-core"/>
                            <circle r="92" fill="none" stroke="#f2ca71" stroke-width="2" stroke-opacity=".85" filter="url(#lpGlow)"/>
                            <g class="lp-spin" style="--spin:60s">
                                <circle r="104" fill="none" stroke="#35dff5" stroke-opacity=".45" stroke-width="1" stroke-dasharray="1 9"/>
                            </g>
                            <g class="lp-spin lp-spin-rev" style="--spin:40s">
                                <circle r="78" fill="none" stroke="#f2ca71" stroke-opacity=".3" stroke-width="1" stroke-dasharray="40 18 4 18"/>
                            </g>
                            <?php foreach ($TITLE_LINES as $k => $line): ?>
                            <text y="<?= (count($TITLE_LINES) > 1 ? -12 : -2) + 20 * $k ?>" text-anchor="middle" class="lp-centre-label"><?= $h($line) ?></text>
                            <?php endforeach; ?>
                            <text y="<?= count($TITLE_LINES) > 1 ? 28 : 16 ?>" text-anchor="middle" class="lp-centre-sub">DOMAIN <?= (int)$COURSE['domain_no'] ?> · <?= (int)$COURSE['weight'] ?>% OF EXAM</text>
                        </g>
                    </a>

                    <!-- Modules, each with its topics as satellites -->
                    <?php foreach ($nodes as $n): $m = $n['m']; $no = (int)$m['no']; $c = $HEX[$n['group']]; $tc = count($m['topics']); ?>
                    <a class="lp-node lp-module" href="<?= $EVAL_BASE ?>module.php?m=<?= $no ?>" data-module="<?= $no ?>"
                       aria-label="Module <?= $no ?>: <?= $h($m['title']) ?>, <?= $tc ?> topics, <?= $h($m['time']) ?>">
                        <g transform="translate(<?= $n['x'] ?> <?= $n['y'] ?>)">
                            <circle r="<?= $SAT_R ?>" fill="none" stroke="<?= $c ?>" stroke-opacity=".16" stroke-width=".75"/>
                            <g class="lp-spin<?= $no % 2 ? '' : ' lp-spin-rev' ?>" style="--spin:<?= $n['spin'] ?>s">
                                <?php foreach ($m['topics'] as $k => $t): $ta = 2 * M_PI * $k / $tc; ?>
                                <circle cx="<?= round($SAT_R * cos($ta), 2) ?>" cy="<?= round($SAT_R * sin($ta), 2) ?>" r="2.6" fill="#04080f" stroke="<?= $c ?>" stroke-width="1.2"/>
                                <?php endforeach; ?>
                            </g>
                            <circle r="16" fill="#0a1420" stroke="<?= $c ?>" stroke-width="1.5" filter="url(#lpGlow)"/>
                            <text y="4" text-anchor="middle" class="lp-node-no" fill="<?= $c ?>"><?= $no ?></text>
                            <text x="<?= $n['right'] ? 42 : -42 ?>" y="-2" text-anchor="<?= $n['right'] ? 'start' : 'end' ?>" class="lp-node-label" fill="<?= $c ?>"><?= $h($n['short']) ?></text>
                            <text x="<?= $n['right'] ? 42 : -42 ?>" y="13" text-anchor="<?= $n['right'] ? 'start' : 'end' ?>" class="lp-node-sub">MODULE <?= $no ?> · <?= $h(strtoupper($m['time'])) ?> · <?= $tc ?> TOPICS</text>
                        </g>
                    </a>
                    <?php endforeach; ?>
                </svg>
            </div>
            <div class="lp-status-line">
                <?= lp_icon('signal') ?>
                <span class="lp-status-text" id="lp-status" aria-live="polite"><?= $count ?> modules in orbit</span>
            </div>
        </div>
    </div>

    <!-- ============================ MODULE INDEX ============================ -->
    <section class="lp-index" id="modules" aria-labelledby="lp-index-title">
        <p class="lp-eyebrow">Index / all modules</p>
        <h2 class="lp-index-title" id="lp-index-title">The full orbit, <span>in order.</span></h2>
        <div class="lp-index-grid">
            <?php foreach ($nodes as $n): $m = $n['m']; ?>
            <a class="lp-card lp-<?= $n['group'] ?>" href="<?= $EVAL_BASE ?>module.php?m=<?= (int)$m['no'] ?>">
                <span class="lp-card-kicker">Module <?= sprintf('%02d', $m['no']) ?> · <?= $h($m['time']) ?> · <?= count($m['topics']) ?> topics</span>
                <span class="lp-card-title"><?= $h($m['title']) ?></span>
                <span class="lp-card-blurb"><?= $h($m['blurb']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <p class="lp-index-foot">
            <?= $h($SITE_TITLE) ?> · <?= $h($COURSE['subtitle']) ?> · <?= $h($COURSE['version']) ?>
        </p>
    </section>

</main>

<!-- Detail panel, filled in by landing.js -->
<aside class="lp-panel" id="lp-panel" role="dialog" aria-modal="false" aria-labelledby="lp-panel-title" hidden></aside>

<script type="application/json" id="lp-data"><?= json_encode($LP_DATA, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
<script src="assets/landing.js?v=<?= @filemtime(__DIR__ . '/assets/landing.js') ?>"></script>
</body>
</html>
