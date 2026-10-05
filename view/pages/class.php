<section class="bg-gradient-to-br from-brand-light via-white to-white">
    <div class="mx-auto max-w-7xl px-4 py-12">
        <nav class="text-sm text-slate-500"><a href="<?= e(url('courses')) ?>" class="hover:text-brand">Cours</a><span class="mx-2">/</span><span class="font-semibold text-slate-700"><?= e($class['name']) ?></span></nav>
        <div class="mt-5 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-brand"><?= $class['cycle'] === 'lycee' ? 'Lycée' : 'Collège' ?></p>
                <h1 class="mt-2 text-4xl font-extrabold text-slate-900">Cours de <?= e($class['name']) ?></h1>
                <p class="mt-3 max-w-2xl text-slate-600">Choisis une matière pour explorer les chapitres et les ressources de ta classe.</p>
            </div>
            <?php if (!empty($class['exam'])): ?><span class="rounded-full bg-amber-100 px-4 py-2 text-sm font-bold text-amber-800">Préparation <?= e($class['exam']) ?></span><?php endif; ?>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10">
    <?php if (!$subjects): ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-600">Aucune matière n’est encore disponible pour cette classe.</div>
    <?php else: ?>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($subjects as $subject): ?>
                <a href="<?= e(url('courses/' . rawurlencode((string) $class['slug']) . '/' . rawurlencode((string) $subject['slug']))) ?>" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand hover:shadow-lg">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-light text-brand group-hover:bg-brand group-hover:text-white"><i class="fa-solid <?= e($subject['icon'] ?: 'fa-book-open') ?>" aria-hidden="true"></i></span>
                    <h2 class="mt-4 text-xl font-bold text-slate-900 group-hover:text-brand"><?= e($subject['name']) ?></h2>
                    <?php if (!empty($subject['series_code'])): ?><p class="mt-1 text-sm text-slate-500">Série <?= e($subject['series_code']) ?></p><?php endif; ?>
                    <?php if (!empty($subject['description'])): ?><p class="mt-2 line-clamp-2 text-sm text-slate-600"><?= e($subject['description']) ?></p><?php endif; ?>
                    <p class="mt-4 text-sm text-slate-500"><?= (int) $subject['chapter_count'] ?> chapitre<?= (int) $subject['chapter_count'] === 1 ? '' : 's' ?> <span class="mx-1">·</span> <?= (int) $subject['content_count'] ?> ressource<?= (int) $subject['content_count'] === 1 ? '' : 's' ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
