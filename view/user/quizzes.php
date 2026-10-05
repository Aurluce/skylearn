<section class="relative overflow-hidden bg-gradient-to-br from-brand-light via-white to-white">
    <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-brand/10 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:py-14">
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand">Entraînement</p>
        <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">Quiz et révisions</h1>
        <p class="mt-3 max-w-2xl text-lg leading-7 text-slate-600">Teste tes connaissances, révise les notions de ta classe et suis tes résultats au fil des tentatives.</p>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10 sm:py-12">
    <form method="get" action="<?= e(url('quizzes')) ?>" class="mb-10 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        <div class="mb-5">
            <p class="text-sm font-bold uppercase tracking-wide text-brand">Personnalise ta recherche</p>
            <h2 class="mt-1 text-xl font-extrabold text-slate-900">Choisis ta classe, ta série et ta matière</h2>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label for="quiz-class" class="mb-2 block text-sm font-semibold text-slate-700">Classe</label>
                <select id="quiz-class" name="class_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-medium text-slate-800 outline-none focus:border-brand focus:ring-4 focus:ring-brand/10">
                    <option value="">Choisir une classe</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= (int) $class['id'] ?>" <?= (int) $class['id'] === $selectedClassId ? 'selected' : '' ?>>
                            <?= e($class['name']) ?><?= !empty($class['exam']) ? ' — ' . e($class['exam']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="quiz-series" class="mb-2 block text-sm font-semibold text-slate-700">Série</label>
                <select id="quiz-series" name="series_id" onchange="this.form.submit()" <?= !$selectedClassId ? 'disabled' : '' ?> class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-medium text-slate-800 outline-none focus:border-brand focus:ring-4 focus:ring-brand/10 disabled:cursor-not-allowed disabled:opacity-50">
                    <option value="0" <?= $selectedSeriesId === 0 ? 'selected' : '' ?>>Toutes les séries / matières communes</option>
                    <?php foreach ($series as $item): ?>
                        <option value="<?= (int) $item['id'] ?>" <?= (int) $item['id'] === $selectedSeriesId ? 'selected' : '' ?>><?= e($item['code']) ?> — <?= e($item['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="quiz-subject" class="mb-2 block text-sm font-semibold text-slate-700">Matière</label>
                <select id="quiz-subject" name="subject_id" onchange="this.form.submit()" <?= !$subjects ? 'disabled' : '' ?> class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-medium text-slate-800 outline-none focus:border-brand focus:ring-4 focus:ring-brand/10 disabled:cursor-not-allowed disabled:opacity-50">
                    <option value="">Choisir une matière</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= (int) $subject['id'] ?>" <?= (int) $subject['id'] === $selectedSubjectId ? 'selected' : '' ?>><?= e($subject['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <noscript><button type="submit" class="mt-4 rounded-xl bg-brand px-5 py-2.5 text-sm font-bold text-white">Afficher les quiz</button></noscript>
    </form>

    <?php if (!$selectedSubject): ?>
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-light text-brand"><i class="fa-solid fa-list-check text-2xl" aria-hidden="true"></i></span>
            <h2 class="mt-5 text-xl font-bold text-slate-900">Sélectionne une matière</h2>
            <p class="mx-auto mt-2 max-w-xl text-slate-600">Choisis d’abord ta classe, puis ta série et la matière pour afficher les quiz disponibles.</p>
        </div>
    <?php elseif (empty($quizzes)): ?>
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-light text-brand"><i class="fa-solid fa-circle-question text-2xl" aria-hidden="true"></i></span>
            <h2 class="mt-5 text-xl font-bold text-slate-900">Aucun quiz pour <?= e($selectedSubject['name']) ?></h2>
            <p class="mx-auto mt-2 max-w-xl text-slate-600">Aucun quiz publié pour cette matière et cette sélection. Essaie une autre matière ou reviens bientôt.</p>
        </div>
    <?php else: ?>
        <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-brand">À toi de jouer</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Quiz disponibles</h2>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1.5 text-sm font-semibold text-slate-600"><?= count($quizzes) ?> quiz</span>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($quizzes as $quiz): ?>
                <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand hover:shadow-lg">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-100 text-purple-700"><i class="fa-solid fa-circle-question text-xl" aria-hidden="true"></i></span>
                        <?php if ($quiz['access_level'] === 'free'): ?>
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">Gratuit</span>
                        <?php elseif ($quiz['access_level'] === 'logged'): ?>
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">Compte requis</span>
                        <?php elseif ($quiz['access_level'] === 'plan'): ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700"><i class="fa-solid fa-star" aria-hidden="true"></i> Premium</span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-brand-light px-3 py-1 text-xs font-bold text-brand"><i class="fa-solid fa-lock" aria-hidden="true"></i> Abonnés</span>
                        <?php endif; ?>
                    </div>
                    <p class="mt-4 text-xs font-bold uppercase tracking-wide text-brand"><?= e($quiz['class_name']) ?> · <?= e($quiz['subject_name']) ?></p>
                    <h3 class="mt-1 text-xl font-extrabold leading-snug text-slate-900"><?= e($quiz['title']) ?></h3>
                    <?php if (!empty($quiz['chapter_title'])): ?><p class="mt-2 text-sm text-slate-500"><i class="fa-solid fa-list-ol mr-1" aria-hidden="true"></i><?= e($quiz['chapter_title']) ?></p><?php endif; ?>
                    <?php if (!empty($quiz['description'])): ?><p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600"><?= e($quiz['description']) ?></p><?php endif; ?>

                    <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-3 py-2"><i class="fa-solid fa-list-check text-brand" aria-hidden="true"></i><?= (int) $quiz['question_count'] ?> question<?= (int) $quiz['question_count'] === 1 ? '' : 's' ?></span>
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-3 py-2"><i class="fa-regular fa-clock text-brand" aria-hidden="true"></i><?= $quiz['duration_minutes'] ? (int) $quiz['duration_minutes'] . ' min' : 'Sans limite' ?></span>
                    </div>

                    <?php if ((int) $quiz['attempt_count'] > 0): ?>
                        <p class="mt-3 text-xs text-slate-500">
                            <?= (int) $quiz['attempt_count'] ?> tentative<?= (int) $quiz['attempt_count'] === 1 ? '' : 's' ?>
                            <?php if ($quiz['best_percentage'] !== null): ?> · Meilleur score : <strong class="text-slate-700"><?= number_format((float) $quiz['best_percentage'], 0) ?> %</strong><?php endif; ?>
                        </p>
                    <?php endif; ?>

                    <a href="<?= e(url('quizzes/' . (int) $quiz['id'])) ?>" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-brand px-4 py-3 font-bold text-white transition hover:bg-brand-dark">
                        <?= (int) $quiz['attempt_count'] > 0 ? 'Recommencer le quiz' : 'Commencer le quiz' ?> <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
