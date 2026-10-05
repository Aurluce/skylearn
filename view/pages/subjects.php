<?php
$selectedCycle = $selectedCycle ?? 'all';
$visibleSubjects = array_values(array_filter(
    $subjects,
    static fn(array $subject): bool => $selectedCycle === 'all' || $subject['cycle'] === $selectedCycle
));
$subjectsByClass = [];
foreach ($visibleSubjects as $subject) {
    $subjectsByClass[(int) $subject['class_id']][] = $subject;
}

$subjectIcon = static function (array $subject): string {
    if (!empty($subject['icon']) && preg_match('/^fa-[a-z0-9-]+$/', $subject['icon'])) {
        return $subject['icon'];
    }

    $name = mb_strtolower($subject['name']);
    foreach ([
        'math' => 'fa-square-root-variable', 'physique' => 'fa-atom', 'chimie' => 'fa-flask',
        'svt' => 'fa-dna', 'biolog' => 'fa-dna', 'fran' => 'fa-feather-pointed',
        'angl' => 'fa-language', 'histoire' => 'fa-landmark', 'géo' => 'fa-earth-africa',
        'geo' => 'fa-earth-africa', 'philo' => 'fa-lightbulb', 'info' => 'fa-laptop-code',
        'éco' => 'fa-chart-line', 'eco' => 'fa-chart-line', 'sport' => 'fa-person-running',
    ] as $keyword => $icon) {
        if (str_contains($name, $keyword)) return $icon;
    }
    return 'fa-book-open';
};

$filterUrl = static fn(string $cycle): string => url('subjects') . ($cycle === 'all' ? '' : '?cycle=' . rawurlencode($cycle));
?>

<section class="relative overflow-hidden bg-gradient-to-br from-brand-light via-white to-white">
    <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-brand/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-28 -left-16 h-72 w-72 rounded-full bg-amber-300/10 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-14 sm:py-16">
        <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-bold uppercase tracking-wide text-brand shadow-sm ring-1 ring-brand/10">
            <i class="fa-solid fa-shapes" aria-hidden="true"></i> Ta bibliothèque scolaire
        </span>
        <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">Toutes les matières</h1>
        <p class="mt-4 max-w-2xl text-lg leading-7 text-slate-600">
            Retrouve tes matières, cours et exercices classés par niveau. Choisis une matière pour parcourir ses chapitres.
        </p>
        <div class="mt-7 flex flex-wrap gap-2" aria-label="Filtrer par cycle">
            <?php foreach (['all' => 'Toutes', 'college' => 'Collège', 'lycee' => 'Lycée'] as $cycle => $label): ?>
                <a href="<?= e($filterUrl($cycle)) ?>"
                   <?= $selectedCycle === $cycle ? 'aria-current="page"' : '' ?>
                   class="rounded-full px-4 py-2 text-sm font-semibold transition <?= $selectedCycle === $cycle ? 'bg-brand text-white shadow-md shadow-brand/20' : 'bg-white/80 text-slate-700 ring-1 ring-slate-200 hover:text-brand' ?>">
                    <?= e($label) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-12 sm:py-14">
    <?php if (!$visibleSubjects): ?>
        <div class="rounded-3xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                <i class="fa-regular fa-folder-open text-2xl" aria-hidden="true"></i>
            </span>
            <h2 class="mt-4 text-xl font-bold text-slate-900">Aucune matière disponible</h2>
            <p class="mt-2 text-slate-600">Les matières de ce niveau seront ajoutées prochainement.</p>
            <?php if ($selectedCycle !== 'all'): ?>
                <a href="<?= e($filterUrl('all')) ?>" class="mt-5 inline-flex font-semibold text-brand hover:underline">Voir toutes les matières</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand"><?= count($visibleSubjects) ?> matière<?= count($visibleSubjects) === 1 ? '' : 's' ?></p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Explore par classe</h2>
            </div>
            <a href="<?= e(url('courses')) ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-brand hover:text-brand-dark">
                Voir les classes <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
        </div>

        <?php foreach ($classes as $class):
            $classSubjects = $subjectsByClass[(int) $class['id']] ?? [];
            if (!$classSubjects) continue;
        ?>
            <section class="mb-10 last:mb-0">
                <header class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand text-white shadow-sm">
                            <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>
                        </span>
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900"><?= e($class['name']) ?></h3>
                            <p class="text-xs text-slate-500"><?= $class['cycle'] === 'college' ? 'Collège' : 'Lycée' ?><?= !empty($class['exam']) ? ' · Prépare le ' . e($class['exam']) : '' ?></p>
                        </div>
                    </div>
                    <a href="<?= e(url('courses/' . rawurlencode((string) $class['slug']))) ?>" class="text-sm font-semibold text-brand hover:underline">
                        Tous les cours <i class="fa-solid fa-arrow-right ml-1 text-xs" aria-hidden="true"></i>
                    </a>
                </header>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <?php foreach ($classSubjects as $subject): ?>
                        <a href="<?= e(url('courses/' . rawurlencode((string) $subject['class_slug']) . '/' . rawurlencode((string) $subject['slug']))) ?>"
                           class="group flex min-h-52 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand hover:shadow-lg">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-light text-brand transition group-hover:bg-brand group-hover:text-white">
                                <i class="fa-solid <?= e($subjectIcon($subject)) ?> text-lg" aria-hidden="true"></i>
                            </span>
                            <h4 class="mt-4 text-lg font-bold text-slate-900 group-hover:text-brand"><?= e($subject['name']) ?></h4>
                            <?php if (!empty($subject['description'])): ?>
                                <p class="mt-1 line-clamp-2 text-sm leading-5 text-slate-500"><?= e($subject['description']) ?></p>
                            <?php else: ?>
                                <p class="mt-1 text-sm text-slate-500">Cours et exercices pour progresser.</p>
                            <?php endif; ?>
                            <div class="mt-auto flex items-center justify-between pt-5 text-xs text-slate-500">
                                <span><i class="fa-solid fa-list-ol mr-1 text-brand" aria-hidden="true"></i><?= (int) $subject['chapter_count'] ?> chapitre<?= (int) $subject['chapter_count'] === 1 ? '' : 's' ?></span>
                                <span><i class="fa-solid fa-file-lines mr-1 text-brand" aria-hidden="true"></i><?= (int) $subject['content_count'] ?> ressource<?= (int) $subject['content_count'] === 1 ? '' : 's' ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<section class="mx-auto max-w-7xl px-4 pb-14">
    <div class="flex flex-col items-start justify-between gap-5 rounded-3xl bg-gradient-to-br from-brand to-brand-dark p-7 text-white shadow-lg sm:flex-row sm:items-center sm:p-9">
        <div>
            <h2 class="text-2xl font-extrabold">Prêt à commencer ?</h2>
            <p class="mt-2 max-w-xl text-sm leading-6 text-brand-light">Crée ton compte pour suivre tes cours et retrouver les ressources adaptées à ta classe.</p>
        </div>
        <a href="<?= e(url('register')) ?>" class="inline-flex shrink-0 items-center gap-2 rounded-full bg-white px-5 py-3 font-bold text-brand transition hover:bg-slate-100">
            Créer un compte <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
        </a>
    </div>
</section>
