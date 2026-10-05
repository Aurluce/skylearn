<?php
$siteName = setting('site_name', 'SKYLEARN');
$granted  = $access['granted'];
$reason   = $access['reason'];

// type => [label, icône, badge, dégradé du bandeau]
$typeMeta = [
    'course'     => ['Cours',    'fa-book-open',     'bg-blue-100 text-blue-700',     'from-blue-600 to-brand-dark'],
    'exercise'   => ['Exercice', 'fa-pen-to-square', 'bg-purple-100 text-purple-700', 'from-purple-600 to-indigo-800'],
    'exam'       => ['Épreuve',  'fa-file-pen',      'bg-amber-100 text-amber-700',   'from-amber-500 to-orange-700'],
    'correction' => ['Corrigé',  'fa-circle-check',  'bg-green-100 text-green-700',   'from-emerald-500 to-green-800'],
];
[$typeLabel, $typeIcon, $typeColors, $typeGradient] =
    $typeMeta[$content['type']] ?? ['Contenu', 'fa-file', 'bg-slate-100 text-slate-700', 'from-slate-600 to-slate-900'];

$humanSize = function (int $bytes): string {
    if ($bytes < 1024) return $bytes . ' o';
    if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' Ko';
    return round($bytes / (1024 * 1024), 1) . ' Mo';
};

// kind => [icône, couleur du fond, couleur de l'icône]
$fileKindIcon = [
    'pdf'      => ['fa-file-pdf',   'bg-red-50',    'text-red-500'],
    'document' => ['fa-file-word',  'bg-blue-50',   'text-blue-500'],
    'image'    => ['fa-file-image', 'bg-purple-50', 'text-purple-500'],
    'video'    => ['fa-file-video', 'bg-amber-50',  'text-amber-500'],
    'other'    => ['fa-file',       'bg-slate-100', 'text-slate-500'],
];

$classUrl   = url('courses/' . rawurlencode((string) $content['class_slug']));
$subjectUrl = url('courses/' . rawurlencode((string) $content['class_slug']) . '/' . rawurlencode((string) $content['subject_slug']));

// Précédent / suivant parmi les contenus du chapitre
$prev = $next = null;
if (!empty($siblings)) {
    $ids = array_map(fn($s) => (int) $s['id'], $siblings);
    // $siblings = "autres" contenus : on propose simplement les deux premiers
    $prev = $siblings[0] ?? null;
    $next = $siblings[1] ?? null;
}

// URL de la vidéo (YouTube / Vimeo / direct)
$embed = null;
$videoUrl = $content['video_url'] ?? '';
if ($videoUrl) {
    if (preg_match('#youtube\.com/watch\?v=([\w-]+)#', $videoUrl, $m)
        || preg_match('#youtu\.be/([\w-]+)#', $videoUrl, $m)
        || preg_match('#youtube\.com/(?:embed|shorts)/([\w-]+)#', $videoUrl, $m)) {
        $embed = 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
    } elseif (preg_match('#vimeo\.com/(\d+)#', $videoUrl, $m)) {
        $embed = 'https://player.vimeo.com/video/' . $m[1];
    }
}
$pageUrl = url('content/' . (int) $content['id']);
?>

<!-- ══════════════ Bandeau titre ══════════════ -->
<section class="relative overflow-hidden bg-gradient-to-br <?= $typeGradient ?> text-white">
    <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-24 left-10 h-64 w-64 rounded-full bg-black/10 blur-3xl"></div>
    <i class="fa-solid <?= $typeIcon ?> pointer-events-none absolute -bottom-6 right-6 hidden text-[10rem] text-white/10 md:block" aria-hidden="true"></i>

    <div class="relative mx-auto max-w-7xl px-4 pb-10 pt-6">
        <!-- Fil d'Ariane -->
        <nav aria-label="Fil d'Ariane" class="-mx-1 flex items-center gap-1 overflow-x-auto whitespace-nowrap px-1 text-xs text-white/75 [scrollbar-width:none]">
            <a href="<?= e(url('courses')) ?>" class="hover:text-white"><i class="fa-solid fa-house" aria-hidden="true"></i><span class="sr-only">Cours</span></a>
            <i class="fa-solid fa-chevron-right text-[9px] text-white/40" aria-hidden="true"></i>
            <a href="<?= e($classUrl) ?>" class="hover:text-white"><?= e($content['class_name']) ?></a>
            <?php if ($content['series_code']): ?>
                <span class="rounded-full bg-white/20 px-2 py-0.5 font-semibold text-white">Série <?= e($content['series_code']) ?></span>
            <?php endif; ?>
            <i class="fa-solid fa-chevron-right text-[9px] text-white/40" aria-hidden="true"></i>
            <a href="<?= e($subjectUrl) ?>" class="hover:text-white"><?= e($content['subject_name']) ?></a>
            <i class="fa-solid fa-chevron-right text-[9px] text-white/40" aria-hidden="true"></i>
            <span class="max-w-[14rem] truncate font-semibold text-white" aria-current="page"><?= e($content['title']) ?></span>
        </nav>

        <div class="mt-6 flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-bold uppercase tracking-wide <?= explode(' ', $typeColors)[1] ?>">
                <i class="fa-solid <?= $typeIcon ?>" aria-hidden="true"></i> <?= e($typeLabel) ?>
            </span>
            <?php if ($content['exam_year']): ?>
                <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur"><?= (int) $content['exam_year'] ?></span>
            <?php endif; ?>
            <?php if ($content['access_level'] === 'free'): ?>
                <span class="inline-flex items-center gap-1 rounded-full bg-green-400/90 px-3 py-1 text-xs font-bold text-green-950">
                    <i class="fa-solid fa-unlock" aria-hidden="true"></i> Gratuit
                </span>
            <?php elseif ($content['access_level'] === 'subscriber'): ?>
                <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur">
                    <i class="fa-solid fa-crown text-yellow-300" aria-hidden="true"></i> Abonnés
                </span>
            <?php elseif ($content['access_level'] === 'plan'): ?>
                <span class="inline-flex items-center gap-1 rounded-full bg-yellow-400 px-3 py-1 text-xs font-bold text-slate-900">
                    <i class="fa-solid fa-star" aria-hidden="true"></i> Premium
                </span>
            <?php endif; ?>
        </div>

        <h1 class="mt-4 max-w-4xl text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">
            <?= e($content['title']) ?>
        </h1>

        <?php if (!empty($content['description'])): ?>
            <p class="mt-3 max-w-3xl text-base text-white/85 sm:text-lg"><?= e($content['description']) ?></p>
        <?php endif; ?>

        <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-white/80">
            <span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-eye" aria-hidden="true"></i><?= (int) $content['views_count'] ?> vues</span>
            <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-layer-group" aria-hidden="true"></i><?= e($content['chapter_title']) ?></span>
            <?php if ($content['author_first']): ?>
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-user-pen" aria-hidden="true"></i>Par <?= e($content['author_first'] . ' ' . $content['author_last']) ?></span>
            <?php endif; ?>
            <?php if ($content['published_at']): ?>
                <span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-calendar" aria-hidden="true"></i><?= date('d/m/Y', strtotime($content['published_at'])) ?></span>
            <?php endif; ?>
        </div>

        <!-- Actions rapides -->
        <div class="mt-6 flex flex-wrap gap-2">
            <button type="button" id="copy-link" data-url="<?= e($pageUrl) ?>"
                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-semibold backdrop-blur transition hover:bg-white/25">
                <i class="fa-regular fa-copy" aria-hidden="true"></i>
                <span id="copy-label">Copier le lien</span>
            </button>
            <a href="<?= e(whatsapp_link('Regarde ce contenu sur ' . $siteName . ' : ' . $content['title'] . ' ' . $pageUrl)) ?>"
               target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-semibold backdrop-blur transition hover:bg-white/25">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Partager
            </a>
            <?php if ($granted && !empty($files) && $content['is_downloadable']): ?>
                <a href="#fichiers"
                   class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow transition hover:bg-slate-100">
                    <i class="fa-solid fa-download" aria-hidden="true"></i> Fichiers (<?= count($files) ?>)
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<main class="mx-auto max-w-7xl px-4 py-8">
    <div class="grid gap-8 lg:grid-cols-3">

        <!-- ══════════════ Colonne principale ══════════════ -->
        <article class="min-w-0 space-y-6 lg:col-span-2">

        <?php if ($granted): ?>

            <?php if ($correction): ?>
                <div class="flex flex-wrap items-center gap-4 rounded-2xl border border-green-200 bg-gradient-to-r from-green-50 to-white p-4 shadow-sm">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-500 text-lg text-white shadow shadow-green-500/30">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-green-900">Corrigé disponible</p>
                        <p class="truncate text-xs text-green-700"><?= e($correction['title']) ?></p>
                    </div>
                    <a href="<?= e(url('content/' . (int) $correction['id'])) ?>"
                       class="inline-flex shrink-0 items-center gap-2 rounded-full bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">
                        Voir le corrigé <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($parent): ?>
                <div class="flex flex-wrap items-center gap-4 rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50 to-white p-4 shadow-sm">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-500 text-lg text-white shadow shadow-blue-500/30">
                        <i class="fa-solid fa-arrow-turn-up" aria-hidden="true"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-blue-900">Corrigé de l'épreuve</p>
                        <p class="truncate text-xs text-blue-700"><?= e($parent['title']) ?></p>
                    </div>
                    <a href="<?= e(url('content/' . (int) $parent['id'])) ?>"
                       class="inline-flex shrink-0 items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                        <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i> Voir l'épreuve
                    </a>
                </div>
            <?php endif; ?>

            <!-- Vidéo -->
            <?php if ($videoUrl): ?>
                <section aria-label="Vidéo" class="overflow-hidden rounded-2xl bg-black shadow-lg ring-1 ring-slate-900/10">
                    <?php if ($embed): ?>
                        <div class="relative aspect-video">
                            <iframe src="<?= e($embed) ?>" title="<?= e($content['title']) ?>" loading="lazy"
                                    class="absolute inset-0 h-full w-full" frameborder="0"
                                    allow="accelerometer; encrypted-media; picture-in-picture; fullscreen"
                                    allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <video controls preload="metadata" class="aspect-video w-full">
                            <source src="<?= e($videoUrl) ?>">
                            Ton navigateur ne supporte pas la vidéo.
                        </video>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <!-- Corps HTML -->
            <?php if (!empty($content['body'])): ?>
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="prose prose-slate max-w-none prose-headings:font-extrabold prose-headings:text-slate-900 prose-a:text-brand prose-img:rounded-xl prose-table:text-sm">
                        <?= $content['body'] /* déjà nettoyé côté admin */ ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Fichiers -->
            <?php if (!empty($files)): ?>
                <section id="fichiers" class="scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="flex items-center gap-3 text-lg font-bold text-slate-900">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-light text-brand">
                            <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                        </span>
                        Fichiers joints
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600"><?= count($files) ?></span>
                    </h2>

                    <ul class="mt-5 space-y-3">
                        <?php foreach ($files as $f):
                            [$fIcon, $fBg, $fColor] = $fileKindIcon[$f['file_kind']] ?? $fileKindIcon['other'];
                        ?>
                            <li class="group flex flex-wrap items-center gap-4 rounded-xl border border-slate-200 bg-white p-3 transition hover:border-brand hover:shadow-md">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl <?= $fBg ?> text-2xl <?= $fColor ?>">
                                    <i class="fa-solid <?= $fIcon ?>" aria-hidden="true"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800"><?= e($f['original_name']) ?></p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        <span class="font-semibold uppercase"><?= e($f['file_kind']) ?></span>
                                        <span class="mx-1 text-slate-300">•</span><?= $humanSize((int) $f['size_bytes']) ?>
                                    </p>
                                </div>
                                <?php if ($content['is_downloadable']): ?>
                                    <a href="<?= e(url('download/' . (int) $f['id'])) ?>"
                                       class="inline-flex shrink-0 items-center gap-2 rounded-full bg-brand px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-brand/20 transition hover:bg-brand-dark">
                                        <i class="fa-solid fa-download" aria-hidden="true"></i> Télécharger
                                    </a>
                                <?php else: ?>
                                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600">
                                        <i class="fa-solid fa-lock" aria-hidden="true"></i> Lecture seule
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if (empty($content['body']) && empty($files) && !$videoUrl): ?>
                <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500">
                    <i class="fa-regular fa-file-lines text-4xl text-slate-300" aria-hidden="true"></i>
                    <p class="mt-3 font-medium">Ce contenu n'a pas encore de ressource attachée.</p>
                    <p class="mt-1 text-xs">Reviens bientôt, il sera complété très vite.</p>
                </div>
            <?php endif; ?>

            <!-- Navigation entre contenus -->
            <?php if ($prev || $next): ?>
                <nav aria-label="Autres contenus" class="grid gap-3 sm:grid-cols-2">
                    <?php foreach ([['prev', $prev, 'fa-arrow-left', 'À voir aussi'], ['next', $next, 'fa-arrow-right', 'Ensuite']] as [$dir, $item, $ic, $lbl]): ?>
                        <?php if ($item): ?>
                            <a href="<?= e(url('content/' . (int) $item['id'])) ?>"
                               class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-brand hover:shadow-md <?= $dir === 'next' ? 'sm:flex-row-reverse sm:text-right' : '' ?>">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition group-hover:bg-brand group-hover:text-white">
                                    <i class="fa-solid <?= $ic ?>" aria-hidden="true"></i>
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-[11px] font-semibold uppercase tracking-wide text-slate-400"><?= $lbl ?></span>
                                    <span class="block truncate text-sm font-semibold text-slate-800 group-hover:text-brand"><?= e($item['title']) ?></span>
                                </span>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <!-- ══════════════ ACCÈS REFUSÉ ══════════════ -->
            <section class="relative overflow-hidden rounded-3xl border border-brand/20 bg-white shadow-sm">
                <!-- Aperçu flouté -->
                <div class="pointer-events-none select-none p-8 opacity-40 blur-[3px]" aria-hidden="true">
                    <div class="h-4 w-1/2 rounded bg-slate-300"></div>
                    <div class="mt-4 h-3 w-full rounded bg-slate-200"></div>
                    <div class="mt-2 h-3 w-11/12 rounded bg-slate-200"></div>
                    <div class="mt-2 h-3 w-4/5 rounded bg-slate-200"></div>
                    <div class="mt-6 h-32 w-full rounded-xl bg-slate-200"></div>
                    <div class="mt-6 h-3 w-full rounded bg-slate-200"></div>
                    <div class="mt-2 h-3 w-2/3 rounded bg-slate-200"></div>
                </div>

                <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-b from-white/60 via-white/90 to-white p-6">
                    <div class="max-w-md text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-brand to-brand-dark text-2xl text-white shadow-lg shadow-brand/30">
                            <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        </div>

                        <?php if ($reason === 'need_login'): ?>
                            <h2 class="mt-4 text-xl font-extrabold text-slate-900">Connecte-toi pour accéder à ce contenu</h2>
                            <p class="mt-2 text-sm text-slate-600">Ce contenu est réservé aux utilisateurs inscrits. C'est gratuit et rapide.</p>
                            <div class="mt-6 flex flex-wrap justify-center gap-3">
                                <a href="<?= e(url('register')) ?>"
                                   class="inline-flex items-center gap-2 rounded-full bg-brand px-6 py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:-translate-y-0.5 hover:bg-brand-dark">
                                    <i class="fa-solid fa-user-plus" aria-hidden="true"></i> Créer un compte
                                </a>
                                <a href="<?= e(url('login')) ?>"
                                   class="inline-flex items-center gap-2 rounded-full border-2 border-brand px-6 py-3 font-semibold text-brand transition hover:bg-brand-light/50">
                                    <i class="fa-regular fa-user" aria-hidden="true"></i> Se connecter
                                </a>
                            </div>

                        <?php elseif ($reason === 'need_subscription'): ?>
                            <h2 class="mt-4 text-xl font-extrabold text-slate-900">Ce contenu est réservé aux abonnés</h2>
                            <p class="mt-2 text-sm text-slate-600">Choisis une formule pour accéder à tous les cours, exercices et épreuves de ta classe.</p>
                            <a href="<?= e(url('subscriptions')) ?>"
                               class="mt-6 inline-flex items-center gap-2 rounded-full bg-brand px-6 py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:-translate-y-0.5 hover:bg-brand-dark">
                                <i class="fa-solid fa-crown" aria-hidden="true"></i> Voir les formules
                            </a>

                        <?php elseif ($reason === 'need_plan'): ?>
                            <h2 class="mt-4 text-xl font-extrabold text-slate-900">Contenu premium</h2>
                            <p class="mt-2 text-sm text-slate-600">
                                Ce contenu nécessite la formule
                                <strong class="text-brand"><?= e($access['plan_name'] ?? 'spécifique') ?></strong>.
                            </p>
                            <a href="<?= e(url('subscriptions')) ?>"
                               class="mt-6 inline-flex items-center gap-2 rounded-full bg-brand px-6 py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:-translate-y-0.5 hover:bg-brand-dark">
                                <i class="fa-solid fa-arrow-up-right-dots" aria-hidden="true"></i> Voir les formules
                            </a>
                        <?php endif; ?>

                        <p class="mt-5 flex items-center justify-center gap-2 text-xs text-slate-500">
                            <i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i>
                            Paiement par MTN MoMo ou Orange Money
                        </p>
                    </div>
                </div>
                <div class="h-72 sm:h-80" aria-hidden="true"></div>
            </section>
        <?php endif; ?>
        </article>

        <!-- ══════════════ Colonne latérale ══════════════ -->
        <aside class="space-y-6 lg:col-span-1">
            <div class="space-y-6 lg:sticky lg:top-24">

                <?php if (count($chapters) > 1): ?>
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-slate-500">
                        <i class="fa-solid fa-list-ol text-brand" aria-hidden="true"></i> Chapitres
                    </h3>
                    <ol class="mt-3 max-h-80 space-y-1 overflow-y-auto pr-1">
                        <?php foreach ($chapters as $ch):
                            $isCurrent = ((int) $ch['id'] === (int) $content['chapter_id']);
                        ?>
                            <li>
                                <a href="<?= e($subjectUrl) ?>#chapter-<?= (int) $ch['id'] ?>"
                                   <?= $isCurrent ? 'aria-current="true"' : '' ?>
                                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm transition <?= $isCurrent ? 'bg-brand-light font-semibold text-brand' : 'text-slate-700 hover:bg-slate-50' ?>">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-bold <?= $isCurrent ? 'bg-brand text-white' : 'bg-slate-100 text-slate-500' ?>">
                                        <?= (int) $ch['position'] ?>
                                    </span>
                                    <span class="truncate"><?= e($ch['title']) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </section>
                <?php endif; ?>

                <?php if (!empty($siblings)): ?>
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-slate-500">
                        <i class="fa-solid fa-layer-group text-brand" aria-hidden="true"></i> Autres contenus
                    </h3>
                    <ul class="mt-3 space-y-1">
                        <?php foreach ($siblings as $sib):
                            [$lbl, $ic, $badge] = $typeMeta[$sib['type']] ?? ['Contenu', 'fa-file', 'bg-slate-100 text-slate-700'];
                        ?>
                            <li>
                                <a href="<?= e(url('content/' . (int) $sib['id'])) ?>"
                                   class="group flex items-center gap-3 rounded-xl px-2 py-2 transition hover:bg-slate-50">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-sm <?= $badge ?>">
                                        <i class="fa-solid <?= $ic ?>" aria-hidden="true"></i>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-medium text-slate-700 group-hover:text-brand"><?= e($sib['title']) ?></span>
                                        <span class="block text-xs text-slate-400"><?= e($lbl) ?></span>
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php endif; ?>

                <!-- Support -->
                <a href="<?= e(whatsapp_link('Bonjour, j\'ai une question sur le contenu : ' . $content['title'])) ?>"
                   target="_blank" rel="noopener"
                   class="group flex items-center gap-3 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 p-4 text-white shadow-lg shadow-green-500/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/20">
                        <i class="fa-brands fa-whatsapp text-2xl" aria-hidden="true"></i>
                    </span>
                    <span class="flex-1">
                        <span class="block text-sm font-bold">Une question ?</span>
                        <span class="block text-xs text-green-50">Écris-nous sur WhatsApp</span>
                    </span>
                    <i class="fa-solid fa-arrow-right text-sm transition group-hover:translate-x-1" aria-hidden="true"></i>
                </a>
            </div>
        </aside>
    </div>
</main>

<script>
(function () {
    var btn = document.getElementById('copy-link');
    var label = document.getElementById('copy-label');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var done = function () {
            label.textContent = 'Lien copié !';
            setTimeout(function () { label.textContent = 'Copier le lien'; }, 2000);
        };
        var url = btn.dataset.url;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(done);
        } else {
            var t = document.createElement('textarea');
            t.value = url; document.body.appendChild(t); t.select();
            document.execCommand('copy'); document.body.removeChild(t); done();
        }
    });
})();
</script>