<?php $siteName = setting('site_name', 'SKYLEARN'); ?>

<main class="mx-auto max-w-7xl px-4 py-10">

    <nav class="text-sm text-slate-500">
        <a href="<?= e(url('exams')) ?>" class="hover:text-brand">Épreuves</a>
        <span class="mx-1">/</span>
        <a href="<?= e(url('exams/' . rawurlencode((string) $subject['class_slug']))) ?>" class="hover:text-brand"><?= e($subject['class_name']) ?></a>
        <span class="mx-1">/</span>
        <span class="font-semibold text-slate-700"><?= e($subject['name']) ?></span>
    </nav>

    <header class="mt-4 flex items-center gap-3">
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-light text-brand">
            <i class="fa-solid fa-file-pen text-xl"></i>
        </span>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">
                Épreuves — <?= e($subject['name']) ?>
            </h1>
            <p class="text-sm text-slate-500">
                <?= e($subject['class_name']) ?>
                <?php if ($subject['series_code']): ?>
                    · Série <?= e($subject['series_code']) ?>
                <?php endif; ?>
                <?php if ($subject['exam']): ?>
                    · Objectif : <strong><?= e($subject['exam']) ?></strong>
                <?php endif; ?>
            </p>
        </div>
    </header>

    <section class="mt-8">
        <?php if (empty($exams)): ?>
            <p class="rounded-2xl bg-slate-100 p-8 text-center text-sm text-slate-500">
                Aucune épreuve pour cette matière.
            </p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($exams as $ex): ?>
                    <div class="flex flex-wrap items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-brand">
                        <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                            <span class="text-xs font-bold"><?= (int) $ex['exam_year'] ?></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="<?= e(url('content/' . (int) $ex['id'])) ?>" class="font-semibold text-slate-800 hover:text-brand">
                                <?= e($ex['title']) ?>
                            </a>
                            <p class="text-xs text-slate-500">
                                <?= e($ex['chapter_title']) ?> · <i class="fa-regular fa-eye"></i> <?= (int) $ex['views_count'] ?>
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <?php if ($ex['has_correction']): ?>
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-bold text-green-700">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Corrigé
                                </span>
                            <?php endif; ?>
                            <?php if ($ex['access_level'] !== 'free'): ?>
                                <span class="rounded-full bg-brand-light px-2 py-0.5 text-[10px] font-bold text-brand">
                                    <i class="fa-solid fa-lock mr-1"></i> Abonné
                                </span>
                            <?php endif; ?>
                            <a href="<?= e(url('content/' . (int) $ex['id'])) ?>"
                               class="rounded-full bg-brand px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-brand-dark">
                                Ouvrir
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>