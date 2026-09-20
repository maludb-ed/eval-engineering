<?php
/**
 * header.php — shared page head + top navigation.
 *
 * Set $page_title and (optionally) $page_desc before including this file.
 * Include config.php first so $COURSE is available.
 */
if (!isset($COURSE)) {
    require_once __DIR__ . '/config.php';
}
$page_title = $page_title ?? $COURSE['title'];
$page_desc  = $page_desc  ?? 'Study guide for the Evaluation, Testing & Optimisation domain of the CCAR-P exam.';
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
    <title><?= htmlspecialchars($page_title) ?> · <?= htmlspecialchars($COURSE['title']) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/style.css?v=<?= @filemtime(__DIR__ . '/../assets/style.css') ?>" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top ev-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <i class="bi bi-clipboard-data-fill"></i>
            <span class="fw-semibold">Eval Engineering</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="index.php#overview">Overview</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#modules">Modules</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#scenarios">Scenarios</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#assessment">Assessment</a></li>
                <li class="nav-item ms-lg-2">
                    <button class="btn btn-sm btn-outline-secondary ev-theme-toggle" type="button"
                            aria-label="Toggle colour theme">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>
