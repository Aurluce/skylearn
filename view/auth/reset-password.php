<?php
$siteName = setting('site_name', 'SKYLEARN');
$errors   = $errors ?? [];
$old      = $old    ?? [];
$invalid  = $invalid ?? false;
$token    = $token   ?? '';
$hasErr   = fn(string $k) => isset($errors[$k]);
$errClass = fn(string $k) => $hasErr($k) ? ' border-red-400 focus:border-red-500 focus:ring-red-200' : ' border-slate-200 focus:border-brand focus:ring-brand-light';
?>

<main class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-10">
    <div class="grid w-full gap-10 lg:grid-cols-2 lg:items-center">

        <!-- Colonne gauche -->
        <section class="hidden lg:block">
            <a href="<?= e(url()) ?>" class="inline-flex items-center gap-2">
                <img src="<?= asset('img/logo.jpeg') ?>" alt="<?= e($siteName) ?>"
                     class="h-12 w-12 rounded-full object-cover ring-2 ring-brand/20">
                <span class="text-2xl font-extrabold text-brand-dark"><?= e($siteName) ?></span>
            </a>

            <h1 class="mt-8 text-4xl font-extrabold leading-tight text-slate-900">
                Nouveau mot de passe 🔐
            </h1>
            <p class="mt-4 text-lg text-slate-600">
                Choisis un mot de passe solide. Au moins 6 caractères, mélange lettres, chiffres et symboles.
            </p>

            <div class="mt-10 rounded-2xl border border-brand/15 bg-gradient-to-br from-brand-light/60 to-white p-5">
                <p class="text-sm font-semibold text-brand-dark">
                    <i class="fa-solid fa-lightbulb mr-1 text-brand"></i>
                    Bon à savoir
                </p>
                <p class="mt-2 text-sm text-slate-700">
                    Évite les dates de naissance, ton prénom ou « 123456 ». Utilise une phrase que toi seul connais.
                </p>
            </div>
        </section>

        <!-- Colonne droite -->
        <section class="w-full">
            <div class="mx-auto max-w-lg rounded-3xl bg-white p-6 shadow-xl ring-1 ring-slate-200 sm:p-8">

                <div class="mb-6 flex flex-col items-center lg:hidden">
                    <img src="<?= asset('img/logo.jpeg') ?>" alt="<?= e($siteName) ?>"
                         class="h-14 w-14 rounded-full object-cover ring-2 ring-brand/20">
                    <h1 class="mt-2 text-xl font-extrabold text-brand-dark"><?= e($siteName) ?></h1>
                </div>

                <?php if ($invalid): ?>
                    <!-- Lien invalide ou expiré -->
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                            <i class="fa-solid fa-link-slash text-2xl"></i>
                        </div>
                        <h2 class="mt-4 text-2xl font-extrabold text-slate-900">Lien invalide ou expiré</h2>
                        <p class="mt-2 text-sm text-slate-600">
                            Ce lien de réinitialisation n'est plus valable. Fais une nouvelle demande.
                        </p>
                        <a href="<?= e(url('forgot-password')) ?>"
                           class="mt-6 inline-block rounded-xl bg-brand px-6 py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:bg-brand-dark">
                            Demander un nouveau lien
                        </a>
                    </div>
                <?php else: ?>
                    <h2 class="text-2xl font-extrabold text-slate-900">Choisis un nouveau mot de passe</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Ce lien est valable 1 heure.
                    </p>

                    <?php if ($hasErr('general')): ?>
                        <div class="mt-4 flex items-start gap-2 rounded-xl bg-red-50 p-3 text-sm text-red-800">
                            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                            <span><?= e($errors['general']) ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= e(url('reset-password')) ?>" class="mt-6 space-y-4" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="<?= e($token) ?>">

                        <!-- Nouveau mot de passe -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700">Nouveau mot de passe *</label>
                            <div class="relative mt-1">
                                <input type="password" id="password" name="password" required minlength="6"
                                       autofocus autocomplete="new-password"
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
                            <?php if ($hasErr('password')): ?>
                                <p class="mt-1 flex items-center gap-1 text-xs text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i><?= e($errors['password']) ?>
                                </p>
                            <?php endif; ?>
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

                        <button type="submit"
                                class="w-full rounded-xl bg-brand py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:bg-brand-dark">
                            <i class="fa-solid fa-check mr-1"></i> Valider le nouveau mot de passe
                        </button>
                    </form>
                <?php endif; ?>

                <p class="mt-6 text-center text-xs text-slate-400">
                    &copy; <?= date('Y') ?> <?= e($siteName) ?> — Tous droits réservés.
                </p>
            </div>
        </section>
    </div>
</main>

<script>
// Force du mot de passe
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

// Afficher / masquer mot de passe
document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.togglePassword);
        if (!input) return;
        const isPwd = input.type === 'password';
        input.type = isPwd ? 'text' : 'password';
        btn.innerHTML = isPwd
            ? '<i class="fa-regular fa-eye-slash"></i>'
            : '<i class="fa-regular fa-eye"></i>';
    });
});
</script>