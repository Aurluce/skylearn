<?php
$siteName    = setting('site_name', 'SKYLEARN');
$firstName   = $user['first_name'];
$className   = $user['class_name'] ?? 'Non définie';
$seriesCode  = $user['series_code'] ?? '';
$examPrep    = $user['exam'] ?? '';
$waSupport   = whatsapp_link("Bonjour, je suis $firstName de $className. ");

// Type → icône + couleur
$typeMeta = [
    'course'     => ['Cours',     'fa-book-open',      'bg-blue-100 text-blue-700'],
    'exercise'   => ['Exercice',  'fa-pen-to-square',  'bg-purple-100 text-purple-700'],
    'exam'       => ['Épreuve',   'fa-file-pen',       'bg-amber-100 text-amber-700'],
    'correction' => ['Corrigé',   'fa-circle-check',   'bg-green-100 text-green-700'],
];

// Statut abonnement
$subStatus = 'none';
if ($subscription) {
    $subStatus = $isExpiringSoon ? 'expiring' : 'active';
}

$daysLeftLabel = null;
if ($daysLeft !== null) {
    $daysLeftLabel = $daysLeft === 0 ? "Expire aujourd'hui"
                   : ($daysLeft === 1 ? 'Expire demain'
                   : "$daysLeft jours restants");
}

// Avatar initiales
$initials = mb_strtoupper(mb_substr($firstName, 0, 1) . mb_substr($user['last_name'], 0, 1));
?>

<!-- ══════════════ Bandeau d'annonce ══════════════ -->
<?php if ($announcement): ?>
<div class="border-b border-amber-200 bg-amber-50">
    <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-2.5 text-sm text-amber-900">
        <i class="fa-solid fa-bullhorn"></i>
        <span class="font-semibold"><?= e($announcement['title']) ?></span>
        <span class="hidden sm:inline text-amber-700">— <?= e($announcement['body']) ?></span>
    </div>
</div>
<?php endif; ?>

<main class="mx-auto max-w-7xl px-4 py-8">

    <!-- ══════════════ EN-TÊTE PERSONNALISÉ ══════════════ -->
    <header class="rounded-3xl bg-gradient-to-br from-brand to-brand-dark p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 text-xl font-extrabold ring-1 ring-white/25">
                    <?= e($initials) ?>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold sm:text-3xl">
                        Bonjour <?= e($firstName) ?> 👋
                    </h1>
                    <p class="mt-1 text-sm text-brand-light">
                        <i class="fa-solid fa-graduation-cap mr-1"></i>
                        <?= e($className) ?>
                        <?php if ($seriesCode): ?>
                            <span class="mx-1">•</span>
                            Série <?= e($seriesCode) ?>
                        <?php endif; ?>
                        <?php if ($examPrep): ?>
                            <span class="mx-1">•</span>
                            Objectif : <strong><?= e($examPrep) ?></strong>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Notifications -->
            <a href="<?= e(url('notifications')) ?>"
               class="relative inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/25 transition hover:bg-white/25">
                <i class="fa-regular fa-bell"></i>
                Notifications
                <?php if ($unreadCount > 0): ?>
                    <span class="absolute -right-1 -top-1 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-brand">
                        <?= $unreadCount > 9 ? '9+' : $unreadCount ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>
    </header>

    <!-- ══════════════ GRILLE PRINCIPALE ══════════════ -->
    <div class="mt-8 grid gap-6 lg:grid-cols-3">

        <!-- ═══ Colonne principale (2/3) ═══ -->
        <div class="space-y-6 lg:col-span-2">

            <!-- ══ Carte abonnement ══ -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-bold text-slate-900">
                        <i class="fa-solid fa-crown mr-1 text-brand"></i>
                        Mon abonnement
                    </h2>
                    <?php if ($subStatus === 'active'): ?>
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Actif
                        </span>
                    <?php elseif ($subStatus === 'expiring'): ?>
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span> Expire bientôt
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            Aucun abonnement
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($subscription): ?>
                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Formule</p>
                            <p class="mt-1 font-semibold text-slate-800"><?= e($subscription['plan_name']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Expire le</p>
                            <p class="mt-1 font-semibold text-slate-800">
                                <?= date('d/m/Y', strtotime($subscription['ends_at'])) ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Temps restant</p>
                            <p class="mt-1 font-semibold <?= $isExpiringSoon ? 'text-amber-600' : 'text-green-600' ?>">
                                <?= e($daysLeftLabel) ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($isExpiringSoon): ?>
                        <div class="mt-4 rounded-xl bg-amber-50 p-3 text-sm text-amber-900">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            Ton abonnement expire bientôt. Renouvelle maintenant pour ne rien manquer.
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="<?= e(url('subscription')) ?>"
                           class="inline-flex items-center gap-2 rounded-full bg-brand px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-dark">
                            <i class="fa-solid fa-rotate"></i> Renouveler
                        </a>
                        <a href="<?= e(url('payments')) ?>"
                           class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-brand hover:text-brand">
                            <i class="fa-solid fa-receipt"></i> Historique
                        </a>
                    </div>
                <?php else: ?>
                    <p class="mt-4 text-sm text-slate-600">
                        Tu n'as pas encore d'abonnement actif. Choisis une formule pour accéder à tous les contenus de ta classe.
                    </p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-center">
                            <p class="text-xs text-slate-500">Hebdomadaire</p>
                            <p class="text-lg font-extrabold text-slate-800">
                                <span data-price-from="weekly">—</span>
                                <span class="text-xs font-medium text-slate-500">XAF</span>
                            </p>
                        </div>
                        <div class="rounded-xl border-2 border-brand bg-brand-light/50 p-3 text-center">
                            <p class="text-xs font-semibold text-brand">Mensuel</p>
                            <p class="text-lg font-extrabold text-brand-dark">
                                <span data-price-from="monthly">—</span>
                                <span class="text-xs font-medium text-brand">XAF</span>
                            </p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-center">
                            <p class="text-xs text-slate-500">Annuel</p>
                            <p class="text-lg font-extrabold text-slate-800">
                                <span data-price-from="yearly">—</span>
                                <span class="text-xs font-medium text-slate-500">XAF</span>
                            </p>
                        </div>
                    </div>
                    <a href="<?= e(url('subscriptions')) ?>"
                       class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-full bg-brand px-6 py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:bg-brand-dark">
                        <i class="fa-solid fa-crown"></i> Choisir une formule
                    </a>
                <?php endif; ?>
            </section>

            <!-- ══ Contenus récents ══ -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">
                        <i class="fa-solid fa-clock-rotate-left mr-1 text-brand"></i>
                        Nouveautés pour ta classe
                    </h2>
                    <a href="<?= e(url('my-courses')) ?>" class="text-xs font-semibold text-brand hover:underline">
                        Tout voir →
                    </a>
                </div>

                <?php if (empty($recentContents)): ?>
                    <div class="mt-4 rounded-xl bg-slate-50 p-6 text-center text-sm text-slate-500">
                        <i class="fa-regular fa-folder-open text-3xl text-slate-300"></i>
                        <p class="mt-2">Aucun contenu disponible pour le moment.</p>
                        <p class="text-xs text-slate-400">Reviens bientôt !</p>
                    </div>
                <?php else: ?>
                    <div class="mt-4 divide-y divide-slate-100">
                        <?php foreach ($recentContents as $c):
                            [$label, $icon, $colors] = $typeMeta[$c['type']] ?? ['Contenu', 'fa-file', 'bg-slate-100 text-slate-700'];
                        ?>
                            <a href="<?= e(url('content/' . (int) $c['id'])) ?>"
                               class="group flex items-center gap-4 py-3 transition hover:bg-slate-50 -mx-3 px-3 rounded-lg">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg <?= $colors ?>">
                                    <i class="fa-solid <?= $icon ?>"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800 group-hover:text-brand">
                                        <?= e($c['title']) ?>
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        <?= e($c['subject_name']) ?> • <?= e($c['chapter_title']) ?>
                                    </p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-slate-600">
                                        <?= e($label) ?>
                                    </span>
                                    <?php if ($c['access_level'] !== 'free'): ?>
                                        <p class="mt-1 text-[10px] text-brand">
                                            <i class="fa-solid fa-lock"></i> Abonné
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- ══ Historique abonnements ══ -->
            <?php if (!empty($history)): ?>
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    <i class="fa-solid fa-clock-rotate-left mr-1 text-brand"></i>
                    Mes derniers abonnements
                </h2>
                <div class="mt-4 space-y-2">
                    <?php foreach ($history as $h):
                        $badge = match($h['status']) {
                            'active'    => 'bg-green-100 text-green-700',
                            'pending'   => 'bg-amber-100 text-amber-700',
                            'expired'   => 'bg-slate-100 text-slate-600',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default     => 'bg-slate-100 text-slate-600',
                        };
                        $statusLabel = match($h['status']) {
                            'active'    => 'Actif',
                            'pending'   => 'En attente',
                            'expired'   => 'Expiré',
                            'cancelled' => 'Annulé',
                            default     => $h['status'],
                        };
                    ?>
                        <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800">
                                    <?= e($h['plan_name']) ?>
                                    <span class="ml-1 text-xs font-normal text-slate-500">
                                        · <?= number_format((int) $h['price_paid'], 0, ',', ' ') ?> XAF
                                    </span>
                                </p>
                                <p class="text-xs text-slate-500">
                                    Du <?= date('d/m/Y', strtotime($h['created_at'])) ?>
                                    <?php if ($h['ends_at']): ?>
                                        au <?= date('d/m/Y', strtotime($h['ends_at'])) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold <?= $badge ?>">
                                <?= $statusLabel ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>

        <!-- ═══ Colonne latérale (1/3) ═══ -->
        <aside class="space-y-6">

            <!-- ══ Statistiques rapides ══ -->
            <section class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <p class="mt-3 text-2xl font-extrabold text-slate-900"><?= $contentCount ?></p>
                    <p class="text-xs text-slate-500">Contenus disponibles</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-purple-100 text-purple-700">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <p class="mt-3 text-2xl font-extrabold text-slate-900"><?= $quizCount ?></p>
                    <p class="text-xs text-slate-500">Quiz disponibles</p>
                </div>
            </section>

            <!-- ══ Progression quiz ══ -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-wide text-slate-500">Ma progression</h3>

                <?php if ($quizAvg !== null): ?>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-brand"><?= number_format($quizAvg, 1, ',', ' ') ?>%</span>
                        <span class="text-xs text-slate-500">moyenne aux quiz</span>
                    </div>
                    <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-brand transition-all"
                             style="width: <?= min(100, (float) $quizAvg) ?>%"></div>
                    </div>
                <?php else: ?>
                    <p class="mt-3 text-sm text-slate-500">
                        Aucun quiz terminé pour l'instant. Lance-toi !
                    </p>
                <?php endif; ?>

                <?php if ($lastQuiz): ?>
                    <div class="mt-4 rounded-xl bg-slate-50 p-3">
                        <p class="text-xs font-semibold text-slate-500">Dernier quiz</p>
                        <p class="mt-1 truncate text-sm font-semibold text-slate-800">
                            <?= e($lastQuiz['title']) ?>
                        </p>
                        <p class="mt-1 text-xs text-slate-600">
                            Score : <strong><?= (int) $lastQuiz['score'] ?>/<?= (int) $lastQuiz['total'] ?></strong>
                            <span class="ml-1 text-slate-400">
                                · <?= date('d/m', strtotime($lastQuiz['finished_at'])) ?>
                            </span>
                        </p>
                    </div>
                <?php endif; ?>

                <a href="<?= e(url('quizzes')) ?>"
                   class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-brand hover:text-brand">
                    <i class="fa-solid fa-play"></i> Voir tous les quiz
                </a>
            </section>

            <!-- ══ Notifications récentes ══ -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-slate-500">Notifications</h3>
                    <a href="<?= e(url('notifications')) ?>" class="text-xs font-semibold text-brand hover:underline">
                        Tout voir
                    </a>
                </div>

                <?php if (empty($notifications)): ?>
                    <p class="mt-3 text-sm text-slate-500">
                        <i class="fa-regular fa-bell-slash mr-1"></i>
                        Aucune nouvelle notification.
                    </p>
                <?php else: ?>
                    <ul class="mt-3 space-y-3">
                        <?php foreach ($notifications as $n): ?>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-light text-xs text-brand">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800"><?= e($n['title']) ?></p>
                                    <p class="text-xs text-slate-500 line-clamp-2"><?= e($n['message']) ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <!-- ══ Support WhatsApp ══ -->
            <a href="<?= e($waSupport) ?>" target="_blank" rel="noopener"
               class="flex items-center gap-3 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800 transition hover:bg-green-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500 text-white">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                </span>
                <div>
                    <p>Besoin d'aide ?</p>
                    <p class="text-xs font-normal text-green-700">Écris-nous sur WhatsApp</p>
                </div>
            </a>
        </aside>
    </div>
</main>

<script>
// Compteurs animés sur les stats
document.querySelectorAll('[data-count]').forEach((el) => {
    const target = parseInt(el.dataset.count, 10);
    if (isNaN(target)) return;
    let current = 0;
    const step = Math.max(1, Math.floor(target / 30));
    const timer = setInterval(() => {
        current += step;
        if (current >= target) { current = target; clearInterval(timer); }
        el.textContent = current.toLocaleString('fr-FR');
    }, 25);
});

// Charger les tarifs par classe (async)
const userClass = <?= json_encode((int) ($user['class_id'] ?? 0)) ?>;
if (userClass) {
    fetch(<?= json_encode(url('api/prices')) ?> + '?class_id=' + encodeURIComponent(userClass))
        .then((r) => r.ok ? r.json() : null)
        .then((data) => {
            if (!data) return;
            Object.entries(data).forEach(([code, price]) => {
                const el = document.querySelector(`[data-price-from="${code}"]`);
                if (el && price) {
                    el.textContent = Number(price).toLocaleString('fr-FR');
                }
            });
        })
        .catch(() => { /* silencieux */ });
}
</script>