<?php
$siteName = setting('site_name', 'SKYLEARN');
$yearFilter = $_GET['year'] ?? '';
?>

<main class="mx-auto max-w-7xl px-4 py-10">

    <!-- En-tête -->
    <header class="text-center">
        <h1 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">
            Épreuves d'examen
        </h1>
        <p class="mt-3 mx-auto max-w-2xl text-slate-600">
            BEPC, Probatoire et Baccalauréat — sujets officiels et corrigés détaillés.
        </p>
    </header>

    <!-- Filtre classes -->
    <section class="mt-8">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Par classe</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            <a href="<?= e(url('exams')) ?>"
               class="rounded-full border border-brand bg-brand px-4 py-2 text-sm font-semibold text-white">
                Toutes
            </a>
            <?php foreach ($classes as $c): ?>
                <a href="<?= e(url('exams/' . rawurlencode((string) $c['slug']))) ?>"
                   class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-brand hover:text-brand">
                    <?= e($c['name']) ?>
                    <?php if ($c['exam']): ?>
                        <span class="ml-1 rounded-full bg-brand-light px-1.5 py-0.5 text-[10px] font-bold text-brand">
                            <?= e($c['exam']) ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Filtre années -->
    <?php if (!empty($years)): ?>
    <section class="mt-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Par année</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            <?php foreach ($years as $y):
                $active = $yearFilter == $y['exam_year'];
            ?>
                <a href="?year=<?= (int) $y['exam_year'] ?>#list"
                   class="rounded-full border px-3 py-1.5 text-xs font-semibold transition <?= $active ? 'border-brand bg-brand text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-brand hover:text-brand' ?>">
                    <?= (int) $y['exam_year'] ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Liste des épreuves -->
    <section id="list" class="mt-10">
        <?php if (empty($exams)): ?>
            <p class="rounded-2xl bg-slate-100 p-8 text-center text-sm text-slate-500">
                Aucune épreuve disponible pour le moment.
            </p>
        <?php else: ?>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($exams as $ex): ?>
                    <a href="<?= e(url('content/' . (int) $ex['id'])) ?>"
                       class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-700">
                                <?= e($ex['exam'] ?? 'Épreuve') ?>
                            </span>
                            <?php if ($ex['exam_year']): ?>
                                <span class="text-xs font-bold text-slate-400"><?= (int) $ex['exam_year'] ?></span>
                            <?php endif; ?>
                        </div>

                        <h3 class="mt-3 font-semibold text-slate-800 group-hover:text-brand">
                            <?= e($ex['title']) ?>
                        </h3>
                        <p class="mt-1 text-xs text-slate-500">
                            <?= e($ex['class_name']) ?>
                            <?php if ($ex['series_code']): ?>
                                · Série <?= e($ex['series_code']) ?>
                            <?php endif; ?>
                            · <?= e($ex['subject_name']) ?>
                        </p>

                        <div class="mt-3 flex items-center justify-between text-xs">
                            <span class="text-slate-400">
                                <i class="fa-regular fa-eye mr-1"></i><?= (int) $ex['views_count'] ?>
                            </span>
                            <?php if ($ex['has_correction']): ?>
                                <span class="inline-flex items-center gap-1 font-semibold text-green-600">
                                    <i class="fa-solid fa-circle-check"></i> Corrigé dispo
                                </span>
                            <?php else: ?>
                                <span class="text-slate-400">Pas de corrigé</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($ex['access_level'] !== 'free'): ?>
                            <div class="mt-3 flex items-center gap-1 text-xs text-brand">
                                <i class="fa-solid fa-lock"></i>
                                <span>Réservé aux abonnés</span>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>