<?php
$siteName = setting('site_name', 'SKYLEARN');
?>

<main class="mx-auto max-w-7xl px-4 py-10">

    <!-- Fil d'Ariane -->
    <nav class="text-sm text-slate-500">
        <a href="<?= e(url('exams')) ?>" class="hover:text-brand">Épreuves</a>
        <span class="mx-1">/</span>
        <span class="font-semibold text-slate-700"><?= e($class['name']) ?></span>
    </nav>

    <header class="mt-4">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Épreuves — <?= e($class['name']) ?>
        </h1>
        <?php if ($class['exam']): ?>
            <p class="mt-2 text-slate-600">
                Prépare le <strong><?= e($class['exam']) ?></strong> avec les sujets officiels des années précédentes.
            </p>
        <?php endif; ?>
    </header>

    <!-- Filtres par série -->
    <?php if (!empty($series)): ?>
    <section class="mt-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Filtrer par série</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            <a href="<?= e(url('exams/' . rawurlencode((string) $class['slug']))) ?>"
               class="rounded-full border border-brand bg-brand px-4 py-2 text-sm font-semibold text-white">
                Toutes les séries
            </a>
            <?php foreach ($series as $s): ?>
                <a href="?series=<?= (int) $s['id'] ?>#list"
                   class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-brand hover:text-brand">
                    <?= e($s['code']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Matières disponibles -->
    <?php if (!empty($subjects)): ?>
    <section class="mt-8">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Par matière</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($subjects as $s): if ((int) $s['exam_count'] === 0) continue; ?>
                <a href="<?= e(url('exams/' . rawurlencode((string) $class['slug']) . '/' . rawurlencode((string) $s['slug']))) ?>"
                   class="group flex items-center justify-between rounded-xl border border-slate-200 bg-white p-4 transition hover:border-brand hover:shadow-sm">
                    <div>
                        <p class="font-semibold text-slate-800 group-hover:text-brand"><?= e($s['name']) ?></p>
                        <p class="text-xs text-slate-500"><?= (int) $s['exam_count'] ?> épreuve(s)</p>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-300 group-hover:text-brand"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Épreuves -->
    <section id="list" class="mt-10">
        <h2 class="text-xl font-bold text-slate-900">Toutes les épreuves</h2>

        <?php if (empty($exams)): ?>
            <p class="mt-4 rounded-2xl bg-slate-100 p-8 text-center text-sm text-slate-500">
                Aucune épreuve disponible pour cette classe.
            </p>
        <?php else: ?>
            <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($exams as $ex): ?>
                    <a href="<?= e(url('content/' . (int) $ex['id'])) ?>"
                       class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:border-brand hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-700">
                                <?= e($class['exam'] ?? 'Épreuve') ?>
                            </span>
                            <?php if ($ex['exam_year']): ?>
                                <span class="text-xs font-bold text-slate-400"><?= (int) $ex['exam_year'] ?></span>
                            <?php endif; ?>
                        </div>

                        <h3 class="mt-3 font-semibold text-slate-800 group-hover:text-brand">
                            <?= e($ex['title']) ?>
                        </h3>
                        <p class="mt-1 text-xs text-slate-500">
                            <?= e($ex['subject_name']) ?>
                            <?php if (!empty($ex['series_code'])): ?>
                                · Série <?= e($ex['series_code']) ?>
                            <?php endif; ?>
                        </p>

                        <?php if ($ex['has_correction']): ?>
                            <p class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-green-600">
                                <i class="fa-solid fa-circle-check"></i> Corrigé disponible
                            </p>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>