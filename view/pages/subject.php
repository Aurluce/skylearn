<?php
$contentTypes = [
    'course'     => ['Cours et fiches', 'fa-book-open', 'bg-blue-100 text-blue-700'],
    'exercise'   => ['Exercices', 'fa-pen-to-square', 'bg-purple-100 text-purple-700'],
    'exam'       => ['Épreuves', 'fa-file-pen', 'bg-amber-100 text-amber-700'],
    'correction' => ['Corrigés', 'fa-circle-check', 'bg-green-100 text-green-700'],
];
?>

<section class="relative overflow-hidden bg-gradient-to-br from-brand-light via-white to-white">
    <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-brand/10 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:py-14">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-500" aria-label="Fil d’Ariane">
            <a href="<?= e(url('courses')) ?>" class="hover:text-brand">Cours</a>
            <i class="fa-solid fa-chevron-right text-[10px]" aria-hidden="true"></i>
            <a href="<?= e(url('courses/' . rawurlencode((string) $subject['class_slug']))) ?>" class="hover:text-brand"><?= e($subject['class_name']) ?></a>
            <i class="fa-solid fa-chevron-right text-[10px]" aria-hidden="true"></i>
            <span class="font-semibold text-slate-700"><?= e($subject['name']) ?></span>
        </nav>

        <div class="mt-6 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand">Programme scolaire</p>
                <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl"><?= e($subject['name']) ?></h1>
                <p class="mt-3 text-slate-600">
                    <?= e($subject['class_name']) ?>
                    <?php if (!empty($subject['series_code'])): ?><span class="mx-1">·</span>Série <?= e($subject['series_code']) ?><?php endif; ?>
                    <?php if (!empty($subject['exam'])): ?><span class="mx-1">·</span>Préparation <?= e($subject['exam']) ?><?php endif; ?>
                </p>
                <?php if (!empty($subject['description'])): ?><p class="mt-3 max-w-2xl leading-7 text-slate-600"><?= e($subject['description']) ?></p><?php endif; ?>
            </div>
            <div class="flex gap-2 text-center">
                <div class="rounded-2xl border border-white bg-white/80 px-4 py-3 shadow-sm">
                    <p class="text-2xl font-extrabold text-brand"><?= count($chapters) ?></p>
                    <p class="text-xs font-semibold text-slate-500">chapitres</p>
                </div>
                <div class="rounded-2xl border border-white bg-white/80 px-4 py-3 shadow-sm">
                    <p class="text-2xl font-extrabold text-brand"><?= array_sum(array_map(static fn(array $chapter): int => (int) $chapter['content_count'], $chapters)) ?></p>
                    <p class="text-xs font-semibold text-slate-500">ressources</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-5xl px-4 py-10 sm:py-12">
    <?php if (!$chapters): ?>
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-light text-brand"><i class="fa-regular fa-folder-open text-2xl" aria-hidden="true"></i></span>
            <h2 class="mt-5 text-xl font-bold text-slate-900">Le programme arrive bientôt</h2>
            <p class="mx-auto mt-2 max-w-lg text-slate-600">Les chapitres et ressources de cette matière n’ont pas encore été publiés. Reviens bientôt pour découvrir les cours, exercices et épreuves.</p>
            <a href="<?= e(url('courses/' . rawurlencode((string) $subject['class_slug']))) ?>" class="mt-6 inline-flex items-center gap-2 rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-dark"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour aux matières</a>
        </div>
    <?php else: ?>
        <ol class="space-y-5">
            <?php foreach ($chapters as $chapter):
                $items = $chapterContents[(int) $chapter['id']] ?? [];
                $itemsByType = [];
                foreach ($items as $item) {
                    $itemsByType[$item['type']][] = $item;
                }
            ?>
                <li id="chapter-<?= (int) $chapter['id'] ?>" class="scroll-mt-24 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 bg-slate-50/70 p-5 sm:p-6">
                        <div class="flex items-start gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand text-sm font-extrabold text-white shadow-sm"><?= (int) $chapter['position'] ?></span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-brand">Chapitre <?= (int) $chapter['position'] ?></p>
                                <h2 class="mt-1 text-xl font-extrabold text-slate-900"><?= e($chapter['title']) ?></h2>
                                <?php if (!empty($chapter['description'])): ?><p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600"><?= e($chapter['description']) ?></p><?php endif; ?>
                            </div>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-slate-200"><?= count($items) ?> ressource<?= count($items) === 1 ? '' : 's' ?></span>
                    </div>

                    <div class="space-y-5 p-5 sm:p-6">
                        <?php if (!$items): ?>
                            <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">Aucune ressource publiée dans ce chapitre pour le moment.</p>
                        <?php else: ?>
                            <?php foreach ($contentTypes as $type => [$typeLabel, $typeIcon, $typeColor]):
                                if (empty($itemsByType[$type])) continue;
                            ?>
                                <section aria-label="<?= e($typeLabel) ?>">
                                    <h3 class="mb-2 flex items-center gap-2 text-sm font-bold text-slate-700">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg <?= $typeColor ?>"><i class="fa-solid <?= $typeIcon ?> text-xs" aria-hidden="true"></i></span>
                                        <?= e($typeLabel) ?>
                                        <span class="text-xs font-medium text-slate-400">(<?= count($itemsByType[$type]) ?>)</span>
                                    </h3>
                                    <ul class="grid gap-2 sm:grid-cols-2">
                                        <?php foreach ($itemsByType[$type] as $content): ?>
                                            <li>
                                                <a href="<?= e(url('content/' . (int) $content['id'])) ?>" class="group flex h-full items-center gap-3 rounded-2xl border border-slate-200 p-4 transition hover:border-brand hover:bg-brand-light/20 hover:shadow-sm">
                                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl <?= $typeColor ?>"><i class="fa-solid <?= $typeIcon ?>" aria-hidden="true"></i></span>
                                                    <span class="min-w-0 flex-1">
                                                        <span class="block font-semibold leading-5 text-slate-800 group-hover:text-brand"><?= e($content['title']) ?></span>
                                                        <span class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                                            <?php if (!empty($content['exam_year'])): ?><span><?= (int) $content['exam_year'] ?></span><?php endif; ?>
                                                            <?php if ($content['access_level'] !== 'free'): ?><span class="inline-flex items-center gap-1"><i class="fa-solid fa-lock text-[10px]" aria-hidden="true"></i> Abonné</span><?php else: ?><span class="text-green-700">Gratuit</span><?php endif; ?>
                                                        </span>
                                                    </span>
                                                    <i class="fa-solid fa-arrow-right shrink-0 text-xs text-slate-300 transition group-hover:translate-x-1 group-hover:text-brand" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </section>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</section>
