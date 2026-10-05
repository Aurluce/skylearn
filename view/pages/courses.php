    <section class="relative overflow-hidden bg-gradient-to-br from-brand-light via-white to-white">
        <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-brand/10 blur-3xl"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-14 sm:py-16">
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-brand">Bibliothèque SKYLEARN</p>
            <h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">Cours par classe</h1>
            <p class="mt-4 max-w-2xl text-lg leading-7 text-slate-600">
                Choisis ton niveau pour retrouver les matières, les chapitres et les ressources disponibles.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12">
        <?php if (empty($classes)): ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <i class="fa-regular fa-folder-open text-4xl text-slate-300" aria-hidden="true"></i>
                <h2 class="mt-4 text-xl font-bold text-slate-900">Aucune classe disponible</h2>
                <p class="mt-2 text-slate-600">Les cours seront publiés ici dès qu’ils seront prêts.</p>
            </div>
        <?php else: ?>
            <?php foreach (['college' => 'Collège', 'lycee' => 'Lycée'] as $cycle => $cycleLabel):
                $cycleClasses = array_values(array_filter($classes, static fn(array $class): bool => $class['cycle'] === $cycle));
                if (!$cycleClasses) continue;
            ?>
                <div class="mb-10 last:mb-0">
                    <div class="mb-5 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-brand"><?= e($cycleLabel) ?></p>
                            <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Explore les niveaux</h2>
                        </div>
                        <span class="hidden rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-500 sm:inline-flex">
                            <?= count($cycleClasses) ?> classe<?= count($cycleClasses) > 1 ? 's' : '' ?>
                        </span>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <?php foreach ($cycleClasses as $class): ?>
                            <a href="<?= e(url('courses/' . rawurlencode((string) $class['slug']))) ?>"
                               class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand hover:shadow-lg">
                                <div class="flex items-start justify-between gap-3">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-light text-brand transition group-hover:bg-brand group-hover:text-white">
                                        <i class="fa-solid fa-graduation-cap text-lg" aria-hidden="true"></i>
                                    </span>
                                    <?php if (!empty($class['exam'])): ?>
                                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700"><?= e($class['exam']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="mt-4 text-xl font-extrabold text-slate-900 group-hover:text-brand"><?= e($class['name']) ?></h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    <?= (int) $class['subject_count'] ?> matière<?= (int) $class['subject_count'] !== 1 ? 's' : '' ?>
                                    <span class="mx-1">·</span>
                                    <?= (int) $class['content_count'] ?> ressource<?= (int) $class['content_count'] !== 1 ? 's' : '' ?>
                                </p>
                                <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-brand">
                                    Voir les cours <i class="fa-solid fa-arrow-right text-xs transition group-hover:translate-x-1" aria-hidden="true"></i>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
