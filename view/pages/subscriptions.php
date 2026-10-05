<?php
$siteName = setting('site_name', 'SKYLEARN');
$classPrices = $selectedClassId ? ($prices[$selectedClassId] ?? []) : [];
$monthlyPrice = null;
foreach ($plans as $plan) {
    if (($plan['code'] ?? '') === 'monthly') {
        $monthlyPrice = $classPrices[(int) $plan['id']] ?? null;
        break;
    }
}

$planCopy = [
    'weekly' => [
        'eyebrow' => 'Pour commencer',
        'description' => 'Un accès court pour réviser une notion ou préparer une évaluation.',
        'icon' => 'fa-bolt',
    ],
    'monthly' => [
        'eyebrow' => 'Le plus choisi',
        'description' => 'Un mois complet pour progresser avec un rythme régulier.',
        'icon' => 'fa-star',
    ],
    'yearly' => [
        'eyebrow' => 'Meilleure durée',
        'description' => 'Une année de travail et de révisions sans interruption.',
        'icon' => 'fa-crown',
    ],
];

$durationLabel = static function (int $days): string {
    return match ($days) {
        7 => '7 jours',
        30 => '30 jours',
        365 => '12 mois',
        default => $days . ' jours',
    };
};
$formatPrice = static fn(int $price): string => number_format($price, 0, ',', ' ') . ' FCFA';
?>

    <!-- En-tête -->
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-light via-white to-white">
        <div class="pointer-events-none absolute -right-24 -top-20 h-72 w-72 rounded-full bg-brand/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-36 -left-16 h-80 w-80 rounded-full bg-amber-300/10 blur-3xl"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-14 text-center sm:py-20">
            <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-bold uppercase tracking-wide text-brand shadow-sm ring-1 ring-brand/10">
                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                Simple, sécurisé et adapté à ta classe
            </span>
            <h1 class="mx-auto mt-5 max-w-3xl text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
                Le bon abonnement pour <span class="text-brand">réussir</span>
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg">
                Accède aux cours, exercices, épreuves et corrigés conçus pour ton niveau. Choisis ta classe pour afficher les tarifs correspondants.
            </p>

            <form method="get" action="<?= e(url('subscriptions')) ?>" class="mx-auto mt-8 max-w-sm text-left">
                <label for="class_id" class="mb-2 block text-sm font-semibold text-slate-700">Ta classe</label>
                <div class="relative">
                    <i class="fa-solid fa-graduation-cap pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-brand" aria-hidden="true"></i>
                    <select id="class_id" name="class_id" onchange="this.form.submit()"
                            class="w-full appearance-none rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-10 font-semibold text-slate-800 shadow-sm outline-none transition focus:border-brand focus:ring-4 focus:ring-brand/10">
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= (int) $class['id'] ?>" <?= (int) $class['id'] === $selectedClassId ? 'selected' : '' ?>>
                                <?= e($class['name']) ?><?= !empty($class['exam']) ? ' — ' . e($class['exam']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fa-solid fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
                </div>
                <noscript><button class="mt-3 rounded-xl bg-brand px-4 py-2 text-sm font-semibold text-white">Afficher les tarifs</button></noscript>
            </form>
            <?php if ($selectedClass): ?>
                <p class="mt-3 text-sm text-slate-500">Tarifs pour <strong class="text-slate-700"><?= e($selectedClass['name']) ?></strong><?= !empty($selectedClass['exam']) ? ' — ' . e($selectedClass['exam']) : '' ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Formules -->
    <section class="mx-auto max-w-7xl px-4 py-14 sm:py-16">
        <?php if (!$classes || !$plans): ?>
            <div class="mx-auto max-w-2xl rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-600"><i class="fa-solid fa-circle-info text-xl" aria-hidden="true"></i></span>
                <h2 class="mt-4 text-xl font-bold text-slate-900">Les formules arrivent bientôt</h2>
                <p class="mt-2 text-slate-600">Les tarifs ne sont pas encore disponibles. Reviens bientôt ou contacte notre équipe pour plus d’informations.</p>
                <a href="<?= e(url('contact')) ?>" class="mt-5 inline-flex items-center gap-2 font-semibold text-brand hover:underline">Contacter SKYLEARN <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i></a>
            </div>
        <?php else: ?>
            <div class="mb-9 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-brand">Nos abonnements</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Apprends à ton rythme</h2>
                <p class="mt-3 text-slate-600">Tous les tarifs sont affichés en FCFA. Aucun frais caché.</p>
            </div>

            <div class="grid items-stretch gap-5 lg:grid-cols-3">
                <?php foreach ($plans as $plan):
                    $planId = (int) $plan['id'];
                    $price = $classPrices[$planId] ?? null;
                    $copy = $planCopy[$plan['code']] ?? [
                        'eyebrow' => 'Formule flexible',
                        'description' => 'Un accès complet aux ressources pédagogiques de ta classe.',
                        'icon' => 'fa-book-open',
                    ];
                    $featured = ($plan['code'] ?? '') === 'monthly';
                    $hasDiscount = $plan['code'] === 'yearly' && $monthlyPrice && $price && $price < $monthlyPrice * 12;
                    $saving = $hasDiscount ? (int) round((1 - ($price / ($monthlyPrice * 12))) * 100) : 0;
                ?>
                    <article class="relative flex flex-col rounded-3xl border <?= $featured ? 'border-brand bg-white shadow-xl shadow-brand/10 ring-2 ring-brand' : 'border-slate-200 bg-white shadow-sm' ?> p-6 transition hover:-translate-y-1 hover:shadow-xl sm:p-7">
                        <?php if ($featured): ?>
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-brand px-4 py-1 text-xs font-bold text-white shadow-md">POPULAIRE</span>
                        <?php endif; ?>
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-brand"><?= e($copy['eyebrow']) ?></p>
                                <h3 class="mt-2 text-2xl font-extrabold text-slate-900"><?= e($plan['name']) ?></h3>
                            </div>
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl <?= $featured ? 'bg-brand text-white' : 'bg-brand-light text-brand' ?>">
                                <i class="fa-solid <?= e($copy['icon']) ?> text-lg" aria-hidden="true"></i>
                            </span>
                        </div>
                        <p class="mt-3 min-h-12 text-sm leading-6 text-slate-600"><?= e($copy['description']) ?></p>

                        <div class="mt-6 border-y border-slate-100 py-5">
                            <?php if ($price !== null): ?>
                                <p class="text-4xl font-extrabold tracking-tight text-slate-900"><?= e($formatPrice((int) $price)) ?></p>
                                <p class="mt-1 text-sm text-slate-500">pour <?= e($durationLabel((int) $plan['duration_days'])) ?></p>
                                <?php if ($hasDiscount): ?>
                                    <span class="mt-3 inline-flex items-center gap-1 rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700">
                                        <i class="fa-solid fa-arrow-trend-down" aria-hidden="true"></i> Économise <?= $saving ?> % sur l’année
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-2xl font-extrabold text-slate-400">Tarif indisponible</p>
                                <p class="mt-1 text-sm text-slate-500">Contacte-nous pour cette formule.</p>
                            <?php endif; ?>
                        </div>

                        <ul class="mt-6 flex-1 space-y-3 text-sm text-slate-700">
                            <?php foreach ([
                                ['fa-circle-check', 'Cours et fiches de révision'],
                                ['fa-circle-check', 'Exercices avec corrigés'],
                                ['fa-circle-check', 'Épreuves et sujets d’examen'],
                                ['fa-circle-check', 'Accès sur téléphone et ordinateur'],
                            ] as [$icon, $feature]): ?>
                                <li class="flex items-start gap-3"><i class="fa-solid <?= $icon ?> mt-0.5 text-green-500" aria-hidden="true"></i><span><?= $feature ?></span></li>
                            <?php endforeach; ?>
                        </ul>

                        <?php if ($price !== null): ?>
                            <a href="<?= e(url('register')) ?>" class="mt-7 inline-flex w-full items-center justify-center gap-2 rounded-xl <?= $featured ? 'bg-brand text-white shadow-lg shadow-brand/20 hover:bg-brand-dark' : 'bg-slate-900 text-white hover:bg-slate-700' ?> px-5 py-3 font-bold transition">
                                Choisir cette formule <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                            </a>
                            <p class="mt-3 text-center text-xs text-slate-400">Tu pourras confirmer ta formule après l’inscription.</p>
                        <?php else: ?>
                            <a href="<?= e(url('contact')) ?>" class="mt-7 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-5 py-3 font-bold text-slate-700 transition hover:border-brand hover:text-brand">
                                Demander le tarif <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Avantages -->
    <section class="border-y border-slate-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 py-12 sm:grid-cols-3">
            <?php foreach ([
                ['fa-mobile-screen-button', 'Paiement adapté', 'Des moyens de paiement conçus pour être simples et pratiques.'],
                ['fa-lock', 'Accès sécurisé', 'Tes contenus sont accessibles depuis ton espace personnel.'],
                ['fa-headset', 'Une équipe à l’écoute', 'Besoin d’aide ? Notre équipe est disponible pour t’accompagner.'],
            ] as [$icon, $benefitTitle, $description]): ?>
                <div class="flex gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-light text-brand"><i class="fa-solid <?= $icon ?>" aria-hidden="true"></i></span>
                    <div><h3 class="font-bold text-slate-900"><?= e($benefitTitle) ?></h3><p class="mt-1 text-sm leading-6 text-slate-600"><?= e($description) ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- FAQ -->
    <section class="mx-auto max-w-4xl px-4 py-14 sm:py-16">
        <div class="text-center">
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-brand">Questions fréquentes</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Besoin d’en savoir plus ?</h2>
        </div>
        <div class="mt-8 space-y-3">
            <?php foreach ([
                ['Que comprend un abonnement ?', 'Chaque formule donne accès aux ressources disponibles pour ta classe : cours, exercices, corrigés et épreuves.'],
                ['Pourquoi le tarif dépend-il de ma classe ?', 'Les tarifs sont définis par niveau afin de proposer une formule adaptée aux ressources et aux besoins de chaque classe.'],
                ['Comment activer mon abonnement ?', 'Crée ton compte, choisis ta formule depuis ton espace, puis suis les étapes de paiement affichées.'],
                ['Puis-je utiliser mon téléphone ?', 'Oui. SKYLEARN est conçu pour fonctionner sur téléphone, tablette et ordinateur.'],
            ] as [$question, $answer]): ?>
                <details class="group rounded-2xl border border-slate-200 bg-white p-5 open:border-brand/30 open:shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-slate-800">
                        <?= $question ?>
                        <i class="fa-solid fa-chevron-down text-xs text-brand transition group-open:rotate-180" aria-hidden="true"></i>
                    </summary>
                    <p class="mt-3 pr-7 text-sm leading-6 text-slate-600"><?= $answer ?></p>
                </details>
            <?php endforeach; ?>
        </div>
        <p class="mt-8 text-center text-sm text-slate-500">
            Une autre question ? <a href="<?= e(url('contact')) ?>" class="font-semibold text-brand hover:underline">Contacte notre équipe</a>.
        </p>
    </section>
