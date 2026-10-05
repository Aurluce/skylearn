<?php
$siteName = setting('site_name', 'SKYLEARN');
$errors   = $errors ?? [];
$old      = $old    ?? [];
$info     = $info   ?? null;
$val      = fn(string $k, string $d = '') => e($old[$k] ?? $d);
$hasErr   = fn(string $k) => isset($errors[$k]);
$errClass = fn(string $k) => $hasErr($k) ? ' border-red-400 focus:border-red-500 focus:ring-red-200' : ' border-slate-200 focus:border-brand focus:ring-brand-light';
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
                Content de te revoir 👋
            </h1>
            <p class="mt-4 text-lg text-slate-600">
                Reprends là où tu t'es arrêté. Tes cours, tes exercices et tes épreuves t'attendent.
            </p>

            <div class="mt-10 rounded-2xl border border-brand/15 bg-gradient-to-br from-brand-light/60 to-white p-5">
                <p class="text-sm font-semibold text-brand-dark">
                    <i class="fa-solid fa-quote-left mr-1 text-brand"></i>
                    Astuce du jour
                </p>
                <p class="mt-2 text-sm italic text-slate-700">
                    « Révise 20 minutes par jour plutôt que 3 heures la veille. La régularité fait la différence. »
                </p>
            </div>
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

                <h2 class="text-2xl font-extrabold text-slate-900">Connexion</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Pas encore de compte ?
                    <a href="<?= e(url('register')) ?>" class="font-semibold text-brand hover:underline">Inscris-toi</a>
                </p>

                <!-- Message flash (ex : reset réussi) -->
                <?php if ($info): ?>
                    <div class="mt-4 flex items-start gap-2 rounded-xl bg-green-50 p-3 text-sm text-green-800">
                        <i class="fa-solid fa-circle-check mt-0.5"></i>
                        <span><?= e($info) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Erreur générale -->
                <?php if ($hasErr('general')): ?>
                    <div class="mt-4 flex items-start gap-2 rounded-xl bg-red-50 p-3 text-sm text-red-800">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <span><?= e($errors['general']) ?></span>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= e(url('login')) ?>" class="mt-6 space-y-4" novalidate>
                    <?= csrf_field() ?>

                    <!-- Identifiant -->
                    <div>
                        <label for="identifier" class="block text-sm font-medium text-slate-700">
                            Téléphone ou e-mail *
                        </label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                                <i class="fa-regular fa-user text-sm"></i>
                            </span>
                            <input type="text" id="identifier" name="identifier" required autofocus
                                   autocomplete="username"
                                   placeholder="6XX XX XX XX ou ton@email.com"
                                   value="<?= $val('identifier') ?>"
                                   class="w-full rounded-xl bg-slate-50 py-2.5 pl-10 pr-3 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('identifier') ?>">
                        </div>
                        <?php if ($hasErr('identifier')): ?>
                            <p class="mt-1 flex items-center gap-1 text-xs text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i><?= e($errors['identifier']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Mot de passe -->
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-slate-700">Mot de passe *</label>
                            <a href="<?= e(url('forgot-password')) ?>" class="text-xs font-semibold text-brand hover:underline">
                                Mot de passe oublié ?
                            </a>
                        </div>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" id="password" name="password" required
                                   autocomplete="current-password"
                                   class="w-full rounded-xl bg-slate-50 py-2.5 pl-10 pr-10 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('password') ?>">
                            <button type="button" data-toggle-password="password"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <?php if ($hasErr('password')): ?>
                            <p class="mt-1 flex items-center gap-1 text-xs text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i><?= e($errors['password']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Se souvenir -->
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember"
                               class="rounded border-slate-300 text-brand focus:ring-brand">
                        Se souvenir de moi pendant 30 jours
                    </label>

                    <!-- Submit -->
                    <button type="submit"
                            class="w-full rounded-xl bg-brand py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:bg-brand-dark">
                        <i class="fa-solid fa-right-to-bracket mr-1"></i> Se connecter
                    </button>
                </form>

                <!-- Séparateur + WhatsApp -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center text-xs uppercase">
                            <span class="bg-white px-3 text-slate-400">Besoin d'aide ?</span>
                        </div>
                    </div>

                    <a href="<?= e(whatsapp_link('Bonjour, j\'ai besoin d\'aide pour me connecter à ' . $siteName . '.')) ?>"
                       target="_blank" rel="noopener"
                       class="mt-4 flex items-center justify-center gap-2 rounded-xl border border-green-200 bg-green-50 py-2.5 text-sm font-semibold text-green-700 transition hover:bg-green-100">
                        <i class="fa-brands fa-whatsapp"></i>
                        Contacter le support WhatsApp
                    </a>
                </div>

                <p class="mt-6 text-center text-xs text-slate-400">
                    &copy; <?= date('Y') ?> <?= e($siteName) ?> — Tous droits réservés.
                </p>
            </div>
        </section>
    </div>
</main>

<script>
// Afficher / masquer le mot de passe
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