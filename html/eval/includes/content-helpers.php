<?php
/**
 * content-helpers.php — loading and rendering helpers for module study content.
 *
 * Study content lives in includes/content/module-N.php. Each file returns an
 * array keyed by topic id:
 *
 *   'ev-1-01' => [
 *       'body'      => '<html study content>',
 *       'questions' => [
 *           [
 *               'q'       => 'Question stem',
 *               'options' => ['A', 'B', 'C', 'D'],
 *               'answer'  => 2,               // 0-based index of correct option
 *               'explain' => 'Why the answer is right and the others are not.',
 *           ],
 *       ],
 *   ],
 */

/**
 * Load the content array for a module, or an empty array if the file is
 * missing (the page then renders topic stubs instead of failing).
 */
function ev_load_module_content(int $module_no): array
{
    $file = __DIR__ . '/content/module-' . $module_no . '.php';
    if (!is_file($file)) {
        return [];
    }
    $content = require $file;
    return is_array($content) ? $content : [];
}

/**
 * Render one topic's sample questions as an interactive quiz block.
 */
function ev_render_questions(string $topic_id, array $questions): void
{
    if (!$questions) {
        return;
    }
    ?>
    <div class="ev-quiz-block mt-4">
        <div class="ev-section-eyebrow mb-2">
            <i class="bi bi-patch-question me-1"></i>Check your understanding
        </div>
        <?php foreach ($questions as $qi => $q): ?>
            <div class="ev-quiz card mb-3" data-answer="<?= (int)$q['answer'] ?>"
                 id="<?= htmlspecialchars($topic_id) ?>-q<?= $qi + 1 ?>">
                <div class="card-body">
                    <div class="fw-semibold mb-3">
                        <span class="ev-quiz-no">Q<?= $qi + 1 ?></span>
                        <?= $q['q'] /* trusted courseware HTML */ ?>
                    </div>
                    <div class="ev-quiz-options d-flex flex-column gap-2 mb-3">
                        <?php foreach ($q['options'] as $oi => $opt): ?>
                            <button type="button" class="ev-quiz-option btn text-start" data-idx="<?= $oi ?>">
                                <span class="ev-quiz-letter"><?= chr(65 + $oi) ?></span>
                                <span><?= $opt /* trusted courseware HTML */ ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="ev-quiz-check btn btn-sm btn-outline-secondary" disabled>
                        Check answer
                    </button>
                    <div class="ev-quiz-result small fw-semibold mt-3 d-none"></div>
                    <div class="ev-quiz-explain small text-secondary mt-2 d-none">
                        <?= $q['explain'] /* trusted courseware HTML */ ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}
