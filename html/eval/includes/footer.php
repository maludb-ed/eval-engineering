<?php
/**
 * footer.php — shared page footer + scripts.
 */
?>
<footer class="ev-footer border-top mt-5 py-4">
    <div class="container">
        <div class="row gy-3 align-items-center">
            <div class="col-md-8">
                <div class="fw-semibold mb-1">
                    <i class="bi bi-clipboard-data-fill me-1"></i><?= htmlspecialchars($COURSE['title']) ?>
                </div>
                <p class="text-secondary small mb-0">
                    <?= htmlspecialchars($COURSE['subtitle']) ?> &middot;
                    Domain <?= (int)$COURSE['domain_no'] ?>: <?= htmlspecialchars($COURSE['domain']) ?>
                    (<?= (int)$COURSE['weight'] ?>% of the exam).
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="text-secondary small">
                    Courseware <?= htmlspecialchars($COURSE['version']) ?> &middot;
                    updated <?= htmlspecialchars($COURSE['updated']) ?>
                </span>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="assets/app.js?v=<?= @filemtime(__DIR__ . '/../assets/app.js') ?>"></script>
</body>
</html>
