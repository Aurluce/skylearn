<?php
$siteName   = setting('site_name', 'SKYLEARN');
$heroTitle  = setting('hero_title', 'Réussis ton année scolaire avec ' . $siteName);
$heroSub    = setting('hero_subtitle', setting('site_description', ''));
$ctaPrimary = setting('cta_primary_label', 'Créer un compte');
$ctaSecond  = setting('cta_secondary_label', 'Voir les abonnements');

$statStudents = setting('stat_students', '1500');
$statRes      = setting('stat_resources', (string) $contentCount);
$statExams    = setting('stat_exams', '250');
$statRate     = setting('stat_success_rate', '95');

// Icône selon le nom de la matière (mot-clé => icône), défaut : fa-book
function subject_icon(string $name): string
{
    $map = [
        'math'      => 'fa-square-root-variable',
        'physique'  => 'fa-atom',
        'chimie'    => 'fa-flask',
        'svt'       => 'fa-dna',
        'biolog'    => 'fa-dna',
        'fran'      => 'fa-feather-pointed',
        'angl'      => 'fa-language',
        'allem'     => 'fa-language',
        'espagnol'  => 'fa-language',
        'histoire'  => 'fa-landmark',
        'géo'       => 'fa-earth-africa',
        'geo'       => 'fa-earth-africa',
        'philo'     => 'fa-lightbulb',
        'info'      => 'fa-laptop-code',
        'éco'       => 'fa-chart-line',
        'eco'       => 'fa-chart-line',
        'ecm'       => 'fa-scale-balanced',
        'sport'     => 'fa-person-running',
        'eps'       => 'fa-person-running',
    ];
    $n = mb_strtolower($name);
    foreach ($map as $kw => $icon) {
        if (str_contains($n, $kw)) return $icon;
    }
    return 'fa-book';
}
?>

<!-- ============== HERO ============== -->
<section class="relative overflow-hidden bg-gradient-to-br from-brand-light via-white to-white">
    <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-brand/10 blur-3xl animate-float"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-brand/5 blur-3xl animate-float-slow"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 md:grid-cols-2 md:py-24">
        <div class="animate-fade-up">
            <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-semibold text-brand shadow-sm ring-1 ring-brand/10">
                <span class="h-2 w-2 animate-pulse rounded-full bg-green-500"></span>
                Nouvelle année scolaire disponible
            </span>

            <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                <?= e($heroTitle) ?>
            </h1>

            <p class="mt-5 max-w-xl text-lg text-slate-600"><?= e($heroSub) ?></p>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="<?= e(url('register')) ?>"
                   class="inline-flex items-center gap-2 rounded-full bg-brand px-6 py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:-translate-y-0.5 hover:bg-brand-dark">
                    <i class="fa-solid fa-rocket" aria-hidden="true"></i>
                    <?= e($ctaPrimary) ?>
                    <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                </a>
                <a href="<?= e(url('subscriptions')) ?>"
                   class="inline-flex items-center gap-2 rounded-full border-2 border-slate-200 bg-white px-6 py-3 font-semibold text-slate-800 transition hover:border-brand hover:text-brand">
                    <i class="fa-solid fa-crown text-amber-500" aria-hidden="true"></i>
                    <?= e($ctaSecond) ?>
                </a>
            </div>

            <!-- Stats -->
            <dl class="mt-10 grid max-w-lg grid-cols-3 gap-4">
                <?php foreach ([
                    ['Élèves',     $statStudents . '+', 'fa-users'],
                    ['Ressources', $statRes . '+',      'fa-file-lines'],
                    ['Réussite',   $statRate . '%',     'fa-trophy'],
                ] as [$label, $val, $icon]): ?>
                    <div class="rounded-2xl bg-white/70 p-3 ring-1 ring-slate-200/70">
                        <dt class="flex items-center gap-1.5 text-xs uppercase tracking-wide text-slate-500">
                            <i class="fa-solid <?= $icon ?> text-brand" aria-hidden="true"></i> <?= $label ?>
                        </dt>
                        <dd class="mt-1 text-2xl font-extrabold text-brand-dark"><?= e($val) ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>

        <!-- Carte flottante -->
        <div class="relative animate-fade-up-delay">
            <div class="relative mx-auto max-w-sm rotate-2 rounded-3xl bg-white p-5 shadow-2xl ring-1 ring-slate-200 transition-transform duration-500 hover:rotate-0">
                <div class="flex items-center gap-3">
                    <img src="<?= asset('img/logo.jpeg') ?>" alt="<?= e($siteName) ?>"
                         class="h-12 w-12 rounded-full object-cover ring-2 ring-brand/20">
                    <div>
                        <p class="text-sm font-semibold text-slate-800"><?= e($siteName) ?></p>
                        <p class="text-xs text-slate-500"><?= e(setting('site_slogan', '')) ?></p>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    <?php foreach ([
                        ['Mathématiques — Tle', 'PDF',    'fa-file-pdf',  'bg-brand-light text-brand'],
                        ['Physique — 1ère',     'Vidéo',  'fa-circle-play','bg-green-100 text-green-700'],
                        ['Épreuve BEPC 2024',   'Corrigé','fa-circle-check','bg-amber-100 text-amber-700'],
                    ] as [$title, $tag, $icon, $cls]): ?>
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                            <span class="text-sm font-medium text-slate-700"><?= $title ?></span>
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold <?= $cls ?>">
                                <i class="fa-solid <?= $icon ?>" aria-hidden="true"></i> <?= $tag ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?= e(url('register')) ?>" class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-brand py-2 text-sm font-semibold text-white transition hover:bg-brand-dark">
                    <i class="fa-solid fa-play text-xs" aria-hidden="true"></i> Commencer gratuitement
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============== ATOUTS ============== -->
<section class="mx-auto max-w-7xl px-4 pt-12">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ([
            ['fa-book-open-reader', 'Cours structurés',   'Par classe et par matière'],
            ['fa-pen-to-square',    'Exercices corrigés', 'Pour t\'entraîner efficacement'],
            ['fa-file-circle-check','Épreuves d\'examen', $statExams . '+ sujets avec corrigés'],
            ['fa-mobile-screen',    'Partout, tout le temps', 'Sur téléphone, tablette ou PC'],
        ] as $i => [$icon, $title, $desc]): ?>
            <div class="animate-fade-up flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
                 style="animation-delay: <?= $i * 80 ?>ms">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-light text-brand">
                    <i class="fa-solid <?= $icon ?> text-lg" aria-hidden="true"></i>
                </span>
                <div>
                    <p class="font-semibold text-slate-900"><?= $title ?></p>
                    <p class="text-sm text-slate-500"><?= e($desc) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ============== ANNONCES ============== -->
<?php if (!empty($announcements)): ?>
<section class="mx-auto max-w-7xl px-4 pt-8">
    <div class="grid gap-3 md:grid-cols-3">
        <?php foreach ($announcements as $a): ?>
            <div class="animate-fade-up rounded-2xl border border-brand/10 bg-white p-4 shadow-sm">
                <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-brand">
                    <i class="fa-solid fa-bullhorn" aria-hidden="true"></i> Annonce
                </p>
                <p class="mt-1 font-semibold text-slate-800"><?= e($a['title']) ?></p>
                <p class="mt-1 line-clamp-2 text-sm text-slate-600"><?= e($a['body']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ============== MATIÈRES ============== -->
<section class="mx-auto max-w-7xl px-4 py-16">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Toutes les matières, du collège au lycée</h2>
            <p class="mt-2 max-w-2xl text-slate-600">
                Choisis ta classe et accède à des cours structurés, exercices corrigés et épreuves d'examen.
            </p>
        </div>
        <a href="<?= e(url('subjects')) ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-brand hover:text-brand-dark">
            Voir toutes les matières <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
        </a>
    </div>

    <?php if (empty($subjects)): ?>
        <p class="mt-8 rounded-xl bg-slate-100 p-6 text-center text-sm text-slate-500">
            <i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>
            Les matières seront bientôt disponibles. Revenez très vite !
        </p>
    <?php else: ?>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($subjects as $i => $s): ?>
                <a href="<?= e(url('courses/' . rawurlencode((string) $s['class_slug']) . '/' . rawurlencode((string) $s['slug']))) ?>"
                   class="group animate-fade-up rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand hover:shadow-lg"
                   style="animation-delay: <?= $i * 60 ?>ms">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-light text-brand transition group-hover:bg-brand group-hover:text-white">
                        <i class="fa-solid <?= subject_icon($s['name']) ?> text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="mt-3 font-semibold text-slate-900 group-hover:text-brand"><?= e($s['name']) ?></h3>
                    <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                        <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i> <?= e($s['class_name']) ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- ============== FORMULES ============== -->
<section class="bg-slate-900 text-white">
    <div class="mx-auto max-w-7xl px-4 py-16">
        <div class="text-center">
            <h2 class="text-2xl font-extrabold sm:text-3xl">Des formules simples et abordables</h2>
            <p class="mt-2 text-slate-300">
                <i class="fa-solid fa-mobile-screen-button mr-1" aria-hidden="true"></i>
                Paye par Mobile Money (MTN ou Orange), active ton accès immédiatement.
            </p>
        </div>

        <?php if (empty($plans)): ?>
            <p class="mt-8 text-center text-sm text-slate-400">Les formules seront bientôt disponibles.</p>
        <?php else: ?>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <?php foreach ($plans as $i => $p):
                    $highlight = ($p['code'] === 'monthly');
                ?>
                    <div class="animate-fade-up relative rounded-3xl p-6 <?= $highlight ? 'scale-[1.02] bg-brand ring-4 ring-brand/40' : 'bg-slate-800' ?>"
                         style="animation-delay: <?= $i * 100 ?>ms">
                        <?php if ($highlight): ?>
                            <span class="absolute -top-3 left-1/2 inline-flex -translate-x-1/2 items-center gap-1 rounded-full bg-yellow-400 px-3 py-1 text-xs font-bold text-slate-900">
                                <i class="fa-solid fa-star" aria-hidden="true"></i> Le plus choisi
                            </span>
                        <?php endif; ?>
                        <h3 class="text-lg font-bold"><?= e($p['name']) ?></h3>
                        <p class="mt-1 flex items-center gap-2 text-sm text-slate-300">
                            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                            <?= (int) $p['duration_days'] ?> jours d'accès
                        </p>
                        <p class="mt-4 text-3xl font-extrabold">
                            <?= number_format((int) $p['price_from'], 0, ',', ' ') ?>
                            <span class="text-base font-medium text-slate-300">XAF</span>
                        </p>
                        <p class="mt-1 text-xs text-slate-400">à partir de</p>

                        <a href="<?= e(url('subscriptions')) ?>"
                           class="mt-6 flex items-center justify-center gap-2 rounded-full py-2.5 font-semibold transition <?= $highlight ? 'bg-white text-brand hover:bg-slate-100' : 'bg-brand text-white hover:bg-brand-dark' ?>">
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i> Choisir
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============== COMMENT ÇA MARCHE ============== -->
<section class="mx-auto max-w-7xl px-4 py-16">
    <h2 class="text-center text-2xl font-extrabold text-slate-900 sm:text-3xl">Comment ça marche ?</h2>
    <div class="mt-12 grid gap-6 md:grid-cols-3">
        <?php
        $steps = [
            ['1', 'fa-user-plus',    'Crée ton compte',      'Inscris-toi gratuitement en moins d\'une minute avec ton numéro de téléphone.'],
            ['2', 'fa-wallet',       'Choisis ta formule',   'Paye par MTN MoMo ou Orange Money — l\'activation est automatique.'],
            ['3', 'fa-book-open',    'Accède aux contenus',  'Cours, exercices, épreuves et corrigés de ta classe, disponibles immédiatement.'],
        ];
        foreach ($steps as $i => [$num, $icon, $title, $desc]):
        ?>
            <div class="animate-fade-up relative rounded-2xl border border-slate-200 bg-white p-6 pt-8 shadow-sm"
                 style="animation-delay: <?= $i * 120 ?>ms">
                <span class="absolute -top-4 left-6 inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand text-sm font-bold text-white shadow">
                    <?= $num ?>
                </span>
                <i class="fa-solid <?= $icon ?> text-2xl text-brand" aria-hidden="true"></i>
                <h3 class="mt-3 font-bold text-slate-900"><?= e($title) ?></h3>
                <p class="mt-2 text-sm text-slate-600"><?= e($desc) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ============== TÉMOIGNAGES ============== -->
<?php if (!empty($testimonials)): ?>
<section class="bg-brand-light/50">
    <div class="mx-auto max-w-7xl px-4 py-16">
        <h2 class="text-center text-2xl font-extrabold text-slate-900 sm:text-3xl">Ils réussissent avec <?= e($siteName) ?></h2>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            <?php foreach ($testimonials as $i => $t): ?>
                <blockquote class="animate-fade-up relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                            style="animation-delay: <?= $i * 100 ?>ms">
                    <i class="fa-solid fa-quote-right absolute right-5 top-5 text-2xl text-brand/10" aria-hidden="true"></i>
                    <?php if (!empty($t['rating'])): $r = max(0, min(5, (int) $t['rating'])); ?>
                        <div class="text-sm text-yellow-400" aria-label="Note : <?= $r ?> sur 5">
                            <?php for ($k = 1; $k <= 5; $k++): ?>
                                <i class="<?= $k <= $r ? 'fa-solid' : 'fa-regular' ?> fa-star" aria-hidden="true"></i>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                    <p class="mt-3 text-sm italic text-slate-700">« <?= e($t['content']) ?> »</p>
                    <footer class="mt-4 flex items-center gap-3">
                        <?php if (!empty($t['photo_path'])): ?>
                            <img src="<?= e(url(ltrim((string) $t['photo_path'], '/'))) ?>" alt="" class="h-10 w-10 rounded-full object-cover">
                        <?php else: ?>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand text-sm font-bold text-white">
                                <?= e(mb_substr($t['author_name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-sm font-semibold text-slate-800"><?= e($t['author_name']) ?></p>
                            <?php if (!empty($t['author_role'])): ?>
                                <p class="text-xs text-slate-500"><?= e($t['author_role']) ?></p>
                            <?php endif; ?>
                        </div>
                    </footer>
                </blockquote>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============== CTA FINAL ============== -->
<section class="mx-auto max-w-7xl px-4 py-16">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand to-brand-dark p-8 text-center text-white shadow-xl sm:p-12">
        <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <i class="fa-solid fa-graduation-cap mb-3 text-4xl text-white/80" aria-hidden="true"></i>
        <h2 class="text-2xl font-extrabold sm:text-3xl">Prêt à réussir ton année ?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-brand-light">
            Rejoins des centaines d'élèves qui utilisent <?= e($siteName) ?> chaque jour.
        </p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="<?= e(url('register')) ?>"
               class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 font-semibold text-brand shadow transition hover:bg-slate-100">
                <i class="fa-solid fa-user-plus" aria-hidden="true"></i> Créer mon compte
            </a>
            <a href="<?= e(whatsapp_link('Bonjour, je veux en savoir plus sur ' . $siteName . '.')) ?>"
               target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 rounded-full border-2 border-white/40 px-6 py-3 font-semibold text-white transition hover:bg-white/10">
                <i class="fa-brands fa-whatsapp text-xl" aria-hidden="true"></i> Nous contacter
            </a>
        </div>
    </div>
</section>