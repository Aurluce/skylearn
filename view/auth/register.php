<?php
$siteName       = setting('site_name', 'SKYLEARN');
$errors         = $errors         ?? [];
$old            = $old            ?? [];
$val            = fn(string $k, string $d = '') => e($old[$k] ?? $d);
$hasErr         = fn(string $k) => isset($errors[$k]);
$errClass       = fn(string $k) => $hasErr($k) ? ' border-red-400 focus:border-red-500 focus:ring-red-200' : ' border-slate-200 focus:border-brand focus:ring-brand-light';
$errMsg         = fn(string $k) => $hasErr($k)
    ? '<p class="mt-1 flex items-center gap-1 text-xs text-red-600"><i class="fa-solid fa-circle-exclamation"></i>' . e($errors[$k]) . '</p>'
    : '';
$currentStep    = ($hasErr('class_id') || $hasErr('series_id') || $hasErr('school_year'))
               ? 2
               : 1;
?>

<main class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-10">
    <div class="grid w-full gap-10 lg:grid-cols-2 lg:items-center">

        <!-- ══════════════ Colonne gauche : argumentaire ══════════════ -->
        <section class="hidden lg:block">
            <a href="<?= e(url()) ?>" class="inline-flex items-center gap-2">
                <img src="<?= asset('img/logo.jpeg') ?>" alt="<?= e($siteName) ?>"
                     class="h-12 w-12 rounded-full object-cover ring-2 ring-brand/20">
                <span class="text-2xl font-extrabold text-brand-dark"><?= e($siteName) ?></span>
            </a>

            <h1 class="mt-8 text-4xl font-extrabold leading-tight text-slate-900">
                Ton année scolaire,<br>enfin bien organisée 🎯
            </h1>
            <p class="mt-4 text-lg text-slate-600">
                Cours, exercices, épreuves et corrigés — exactement ceux de <strong>ta classe et ta série</strong>.
            </p>

            <ul class="mt-8 space-y-4">
                <li class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-light text-brand">
                        <i class="fa-solid fa-layer-group"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-slate-800">Séries couvertes</p>
                        <p class="text-sm text-slate-600">A4, A5, C, D, E, TI, ACC, CG, ESF…</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-light text-brand">
                        <i class="fa-solid fa-mobile-screen"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-slate-800">100 % mobile</p>
                        <p class="text-sm text-slate-600">Utilisable partout, même avec une connexion lente.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-light text-brand">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-slate-800">Paiement Mobile Money</p>
                        <p class="text-sm text-slate-600">MTN MoMo, Orange Money — activation instantanée.</p>
                    </div>
                </li>
            </ul>
        </section>

        <!-- ══════════════ Colonne droite : formulaire ══════════════ -->
        <section class="w-full">
            <div class="mx-auto max-w-lg rounded-3xl bg-white p-6 shadow-xl ring-1 ring-slate-200 sm:p-8">

                <!-- Logo mobile -->
                <div class="mb-6 flex flex-col items-center lg:hidden">
                    <img src="<?= asset('img/logo.jpeg') ?>" alt="<?= e($siteName) ?>"
                         class="h-14 w-14 rounded-full object-cover ring-2 ring-brand/20">
                    <h1 class="mt-2 text-xl font-extrabold text-brand-dark"><?= e($siteName) ?></h1>
                </div>

                <h2 class="text-2xl font-extrabold text-slate-900">Créer un compte</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Déjà inscrit ?
                    <a href="<?= e(url('login')) ?>" class="font-semibold text-brand hover:underline">Connecte-toi</a>
                </p>

                <!-- ════════════ Barre de progression ════════════ -->
                <div class="mt-6">
                    <div class="flex items-center justify-between text-xs font-medium">
                        <span id="step-label-1" class="flex items-center gap-2 <?= $currentStep === 1 ? 'text-brand' : 'text-slate-400' ?>">
                            <span class="step-dot inline-flex h-6 w-6 items-center justify-center rounded-full <?= $currentStep === 1 ? 'bg-brand text-white' : 'bg-slate-200 text-slate-500' ?>">1</span>
                            Identité
                        </span>
                        <span id="step-label-2" class="flex items-center gap-2 <?= $currentStep === 2 ? 'text-brand' : 'text-slate-400' ?>">
                            <span class="step-dot inline-flex h-6 w-6 items-center justify-center rounded-full <?= $currentStep === 2 ? 'bg-brand text-white' : 'bg-slate-200 text-slate-500' ?>">2</span>
                            Scolarité
                        </span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div id="progress-bar"
                             class="h-full rounded-full bg-brand transition-all duration-500"
                             style="width: <?= $currentStep === 1 ? '50' : '100' ?>%"></div>
                    </div>
                </div>

                <!-- ════════════ FORMULAIRE ════════════ -->
                <form method="post" action="<?= e(url('register')) ?>" id="register-form" class="mt-6" novalidate>
                    <?= csrf_field() ?>

                    <!-- ════════════ ÉTAPE 1 ════════════ -->
                    <fieldset id="step-1" class="<?= $currentStep === 1 ? '' : 'hidden' ?> space-y-4">
                        <legend class="sr-only">Étape 1 — Identité</legend>

                        <!-- Nom + Prénom -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-slate-700">Prénom *</label>
                                <input type="text" id="first_name" name="first_name" required autocomplete="given-name"
                                       value="<?= $val('first_name') ?>"
                                       class="mt-1 w-full rounded-xl bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('first_name') ?>">
                                <?= $errMsg('first_name') ?>
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-slate-700">Nom *</label>
                                <input type="text" id="last_name" name="last_name" required autocomplete="family-name"
                                       value="<?= $val('last_name') ?>"
                                       class="mt-1 w-full rounded-xl bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('last_name') ?>">
                                <?= $errMsg('last_name') ?>
                            </div>
                        </div>

                        <!-- Téléphone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700">
                                Téléphone * <span class="text-xs text-slate-400">(Cameroun)</span>
                            </label>
                            <div class="relative mt-1">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                                    <i class="fa-solid fa-phone text-sm"></i>
                                </span>
                                <input type="tel" id="phone" name="phone" required autocomplete="tel"
                                       placeholder="6XX XX XX XX"
                                       value="<?= $val('phone') ?>"
                                       class="w-full rounded-xl bg-slate-50 py-2.5 pl-10 pr-3 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('phone') ?>">
                            </div>
                            <?= $errMsg('phone') ?>
                        </div>

                        <!-- E-mail -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700">
                                E-mail <span class="text-xs text-slate-400">(facultatif)</span>
                            </label>
                            <div class="relative mt-1">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                                    <i class="fa-regular fa-envelope text-sm"></i>
                                </span>
                                <input type="email" id="email" name="email" autocomplete="email"
                                       placeholder="ton@email.com"
                                       value="<?= $val('email') ?>"
                                       class="w-full rounded-xl bg-slate-50 py-2.5 pl-10 pr-3 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('email') ?>">
                            </div>
                            <?= $errMsg('email') ?>
                        </div>

                        <!-- Mot de passe -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700">Mot de passe *</label>
                            <div class="relative mt-1">
                                <input type="password" id="password" name="password" required minlength="6"
                                       placeholder="Au moins 6 caractères" autocomplete="new-password"
                                       class="w-full rounded-xl bg-slate-50 py-2.5 pl-3 pr-10 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('password') ?>">
                                <button type="button" data-toggle-password="password"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-700">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <div class="mt-2 h-1 w-full overflow-hidden rounded-full bg-slate-200">
                                <div id="pwd-strength-bar" class="h-full w-0 rounded-full bg-red-500 transition-all duration-300"></div>
                            </div>
                            <p id="pwd-strength-text" class="mt-1 text-xs text-slate-400">Force du mot de passe</p>
                            <?= $errMsg('password') ?>
                        </div>

                        <!-- Confirmation -->
                        <div>
                            <label for="password_confirm" class="block text-sm font-medium text-slate-700">Confirmer *</label>
                            <div class="relative mt-1">
                                <input type="password" id="password_confirm" name="password_confirm" required minlength="6"
                                       autocomplete="new-password"
                                       class="w-full rounded-xl bg-slate-50 py-2.5 pl-3 pr-10 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('password') ?>">
                                <button type="button" data-toggle-password="password_confirm"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-700">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Bouton continuer -->
                        <button type="button" id="btn-next"
                                class="mt-2 w-full rounded-xl bg-brand py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:bg-brand-dark">
                            Continuer <i class="fa-solid fa-arrow-right ml-1"></i>
                        </button>
                    </fieldset>

                    <!-- ════════════ ÉTAPE 2 ════════════ -->
                    <fieldset id="step-2" class="<?= $currentStep === 2 ? '' : 'hidden' ?> space-y-4">
                        <legend class="sr-only">Étape 2 — Scolarité</legend>

                        <!-- Classe -->
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-slate-700">Classe *</label>
                            <select id="class_id" name="class_id" required
                                    class="mt-1 w-full rounded-xl bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('class_id') ?>">
                                <option value="">— Choisis ta classe —</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?= (int) $c['id'] ?>" <?= ($old['class_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                        <?= e($c['name']) ?><?= $c['exam'] ? ' — prépare le ' . e($c['exam']) : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?= $errMsg('class_id') ?>
                        </div>

                        <!-- ════ SÉLECTION DE SÉRIE (dynamique) ════ -->
                        <div id="series-block"
                             class="hidden overflow-hidden rounded-2xl border border-brand/20 bg-gradient-to-br from-brand-light/40 to-white p-4">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                                    <i class="fa-solid fa-layer-group text-brand"></i>
                                    Choisis ta série *
                                </label>
                                <span id="series-count" class="rounded-full bg-brand-light px-2 py-0.5 text-xs font-semibold text-brand"></span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">
                                La série détermine les matières et épreuves qui te seront proposées.
                            </p>

                            <!-- Cartes de séries (générées en JS) -->
                            <div id="series-grid"
                                 class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3"></div>

                            <input type="hidden" name="series_id" id="series_id" value="<?= $val('series_id') ?>">

                            <p id="series-error"
                               class="mt-2 hidden text-xs text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                <span>Sélectionne une série pour continuer.</span>
                            </p>
                        </div>

                        <!-- Ville + Année -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="city" class="block text-sm font-medium text-slate-700">Ville</label>
                                <input type="text" id="city" name="city"
                                       value="<?= $val('city') ?>"
                                       placeholder="Douala"
                                       class="mt-1 w-full rounded-xl bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('city') ?>">
                            </div>
                            <div>
                                <label for="school_year" class="block text-sm font-medium text-slate-700">Année scolaire</label>
                                <input type="text" id="school_year" name="school_year"
                                       value="<?= $val('school_year') ?>"
                                       placeholder="2025-2026"
                                       class="mt-1 w-full rounded-xl bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('school_year') ?>">
                                <?= $errMsg('school_year') ?>
                            </div>
                        </div>

                        <!-- Établissement -->
                        <div>
                            <label for="school" class="block text-sm font-medium text-slate-700">
                                Établissement <span class="text-xs text-slate-400">(facultatif)</span>
                            </label>
                            <input type="text" id="school" name="school"
                                   value="<?= $val('school') ?>"
                                   placeholder="Lycée Général Leclerc"
                                   class="mt-1 w-full rounded-xl bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('school') ?>">
                        </div>

                        <!-- CGU -->
                        <label class="flex items-start gap-2 text-xs text-slate-600">
                            <input type="checkbox" required id="accept-terms"
                                   class="mt-0.5 rounded border-slate-300 text-brand focus:ring-brand">
                            <span>
                                J'accepte les <a href="<?= e(url('about')) ?>" class="font-semibold text-brand hover:underline">conditions d'utilisation</a>
                                et la politique de confidentialité de <?= e($siteName) ?>.
                            </span>
                        </label>

                        <!-- Navigation étape 2 -->
                        <div class="flex gap-3">
                            <button type="button" id="btn-prev"
                                    class="w-1/3 rounded-xl border-2 border-slate-200 py-3 font-semibold text-slate-700 hover:border-brand hover:text-brand transition">
                                <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                            </button>
                            <button type="submit"
                                    class="flex-1 rounded-xl bg-brand py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:bg-brand-dark">
                                <i class="fa-solid fa-user-plus mr-1"></i> Créer mon compte
                            </button>
                        </div>
                    </fieldset>
                </form>

                <p class="mt-6 text-center text-xs text-slate-400">
                    &copy; <?= date('Y') ?> <?= e($siteName) ?> — Tous droits réservés.
                </p>
            </div>
        </section>
    </div>
</main>

<script>
// ═══════════════════════════════════════════════════════════════════
//  DONNÉES INJECTÉES PAR PHP
// ═══════════════════════════════════════════════════════════════════
const SERIES_BY_CLASS = <?= json_encode($seriesByClass, JSON_UNESCAPED_UNICODE) ?>;
const OLD_SERIES_ID   = <?= json_encode((string) ($old['series_id'] ?? '')) ?>;
const INITIAL_STEP    = <?= $currentStep ?>;

// ═══════════════════════════════════════════════════════════════════
//  RÉFÉRENCES DOM
// ═══════════════════════════════════════════════════════════════════
const step1       = document.getElementById('step-1');
const step2       = document.getElementById('step-2');
const btnNext     = document.getElementById('btn-next');
const btnPrev     = document.getElementById('btn-prev');
const progressBar = document.getElementById('progress-bar');
const label1      = document.getElementById('step-label-1');
const label2      = document.getElementById('step-label-2');

const classSelect  = document.getElementById('class_id');
const seriesBlock  = document.getElementById('series-block');
const seriesGrid   = document.getElementById('series-grid');
const seriesInput  = document.getElementById('series_id');
const seriesError  = document.getElementById('series-error');
const seriesCount  = document.getElementById('series-count');

// ═══════════════════════════════════════════════════════════════════
//  NAVIGATION ENTRE ÉTAPES
// ═══════════════════════════════════════════════════════════════════
function goToStep(step) {
    const isStep1 = step === 1;

    step1.classList.toggle('hidden', !isStep1);
    step2.classList.toggle('hidden', isStep1);

    progressBar.style.width = isStep1 ? '50%' : '100%';

    [label1, label2].forEach((el, i) => {
        const isActive = (i + 1) === step;
        el.classList.toggle('text-brand', isActive);
        el.classList.toggle('text-slate-400', !isActive);
        el.querySelector('.step-dot').classList.toggle('bg-brand', isActive);
        el.querySelector('.step-dot').classList.toggle('text-white', isActive);
        el.querySelector('.step-dot').classList.toggle('bg-slate-200', !isActive);
        el.querySelector('.step-dot').classList.toggle('text-slate-500', !isActive);
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

btnNext?.addEventListener('click', () => {
    // Validation étape 1
    const required = ['first_name', 'last_name', 'phone', 'password', 'password_confirm'];
    let ok = true;

    required.forEach((id) => {
        const el = document.getElementById(id);
        if (!el.value.trim()) {
            el.classList.add('border-red-400');
            ok = false;
        } else {
            el.classList.remove('border-red-400');
        }
    });

    const pwd = document.getElementById('password').value;
    const pwd2 = document.getElementById('password_confirm').value;

    if (pwd.length < 6) {
        document.getElementById('password').classList.add('border-red-400');
        ok = false;
    }
    if (pwd !== pwd2) {
        document.getElementById('password_confirm').classList.add('border-red-400');
        ok = false;
    }

    if (ok) goToStep(2);
});

btnPrev?.addEventListener('click', () => goToStep(1));

// ═══════════════════════════════════════════════════════════════════
//  GESTION DYNAMIQUE DES SÉRIES
// ═══════════════════════════════════════════════════════════════════
function renderSeries() {
    const classId = classSelect.value;
    const series  = SERIES_BY_CLASS[classId] || [];

    // Reset
    seriesInput.value = '';
    seriesError.classList.add('hidden');
    seriesGrid.innerHTML = '';

    if (series.length === 0) {
        // Classe sans série → masquer le bloc avec animation
        seriesBlock.style.maxHeight = '0';
        seriesBlock.style.opacity = '0';
        setTimeout(() => seriesBlock.classList.add('hidden'), 300);
        return;
    }

    // Afficher le bloc avec animation
    seriesBlock.classList.remove('hidden');
    requestAnimationFrame(() => {
        seriesBlock.style.maxHeight = '600px';
        seriesBlock.style.opacity = '1';
        seriesBlock.style.transition = 'max-height .4s ease, opacity .4s ease';
    });

    seriesCount.textContent = series.length + ' série' + (series.length > 1 ? 's' : '');

    // Rendu des cartes
    series.forEach((s, index) => {
        const isSelected = String(s.id) === OLD_SERIES_ID;

        const card = document.createElement('button');
        card.type = 'button';
        card.dataset.seriesId = s.id;
        card.className = [
            'series-card group relative flex flex-col items-start gap-1 rounded-xl border-2 p-3 text-left transition-all duration-200',
            'hover:-translate-y-0.5 hover:shadow-md',
            isSelected
                ? 'border-brand bg-white shadow-md ring-2 ring-brand-light'
                : 'border-slate-200 bg-white hover:border-brand/40',
        ].join(' ');
        card.style.animationDelay = (index * 40) + 'ms';
        card.style.animation = 'fadeInUp .3s ease-out both';

        card.innerHTML = `
            <span class="flex h-9 w-9 items-center justify-center rounded-lg font-bold
                        ${isSelected ? 'bg-brand text-white' : 'bg-slate-100 text-slate-700 group-hover:bg-brand-light group-hover:text-brand'}">
                ${s.code}
            </span>
            <span class="mt-1 text-xs font-semibold leading-tight text-slate-800">${s.label}</span>
            ${isSelected ? '<i class="fa-solid fa-circle-check absolute right-2 top-2 text-brand"></i>' : ''}
        `;

        card.addEventListener('click', () => selectSeries(card, s.id));
        seriesGrid.appendChild(card);

        // Si déjà sélectionné → injecter dans l'input
        if (isSelected) seriesInput.value = s.id;
    });
}

function selectSeries(card, id) {
    // Désélectionner toutes les cartes
    seriesGrid.querySelectorAll('.series-card').forEach((c) => {
        c.classList.remove('border-brand', 'bg-white', 'shadow-md', 'ring-2', 'ring-brand-light');
        c.classList.add('border-slate-200', 'bg-white');
        c.querySelector('.fa-circle-check')?.remove();
        const span = c.querySelector('span:first-child');
        span.classList.remove('bg-brand', 'text-white');
        span.classList.add('bg-slate-100', 'text-slate-700');
    });

    // Sélectionner la carte cliquée
    card.classList.remove('border-slate-200');
    card.classList.add('border-brand', 'bg-white', 'shadow-md', 'ring-2', 'ring-brand-light');

    const span = card.querySelector('span:first-child');
    span.classList.remove('bg-slate-100', 'text-slate-700');
    span.classList.add('bg-brand', 'text-white');

    // Ajouter l'icône check
    const check = document.createElement('i');
    check.className = 'fa-solid fa-circle-check absolute right-2 top-2 text-brand';
    card.appendChild(check);

    // Mettre à jour l'input caché
    seriesInput.value = id;
    seriesError.classList.add('hidden');
}

classSelect?.addEventListener('change', renderSeries);

// ═══════════════════════════════════════════════════════════════════
//  FORCE DU MOT DE PASSE
// ═══════════════════════════════════════════════════════════════════
const pwdInput = document.getElementById('password');
const pwdBar   = document.getElementById('pwd-strength-bar');
const pwdText  = document.getElementById('pwd-strength-text');

pwdInput?.addEventListener('input', () => {
    const v = pwdInput.value;
    let score = 0;
    if (v.length >= 6)     score++;
    if (v.length >= 10)    score++;
    if (/[A-Z]/.test(v))   score++;
    if (/[0-9]/.test(v))   score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    const levels = [
        { w: '0%',   c: 'bg-red-500',    t: 'Force du mot de passe' },
        { w: '25%',  c: 'bg-red-500',    t: 'Faible' },
        { w: '50%',  c: 'bg-orange-500', t: 'Moyen' },
        { w: '75%',  c: 'bg-yellow-500', t: 'Bon' },
        { w: '100%', c: 'bg-green-500',  t: 'Excellent' },
        { w: '100%', c: 'bg-green-600',  t: 'Excellent' },
    ];
    const lvl = levels[Math.min(score, 5)];
    pwdBar.style.width = lvl.w;
    pwdBar.className = 'h-full rounded-full transition-all duration-300 ' + lvl.c;
    pwdText.textContent = lvl.t;
    pwdText.className = 'mt-1 text-xs ' + (score >= 4 ? 'text-green-600' : score >= 2 ? 'text-orange-500' : 'text-slate-400');
});

// ═══════════════════════════════════════════════════════════════════
//  AFFICHER / MASQUER MOT DE PASSE
// ═══════════════════════════════════════════════════════════════════
document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.togglePassword);
        if (!input) return;
        const isPwd = input.type === 'password';
        input.type = isPwd ? 'text' : 'password';
        btn.innerHTML = isPwd ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
    });
});

// ═══════════════════════════════════════════════════════════════════
//  VALIDATION FINALE AVANT SOUMISSION
// ═══════════════════════════════════════════════════════════════════
document.getElementById('register-form')?.addEventListener('submit', (e) => {
    const classHasSeries = (SERIES_BY_CLASS[classSelect.value] || []).length > 0;

    if (classHasSeries && !seriesInput.value) {
        e.preventDefault();
        seriesError.classList.remove('hidden');
        seriesBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }

    // Vérifier que le checkbox CGU est bien coché (déjà required en HTML mais filet de sécurité)
    if (!document.getElementById('accept-terms').checked) {
        e.preventDefault();
        alert('Merci d\'accepter les conditions d\'utilisation.');
        return false;
    }
});

// ═══════════════════════════════════════════════════════════════════
//  INITIALISATION
// ═══════════════════════════════════════════════════════════════════
if (INITIAL_STEP === 2 && classSelect.value) {
    renderSeries();
}
</script>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.series-card { animation-fill-mode: both; }
</style>