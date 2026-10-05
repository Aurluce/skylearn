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

        <!-- Colonne gauche -->
        <section class="hidden lg:block">
            <a href="<?= e(url()) ?>" class="inline-flex items-center gap-2">
                <img src="<?= asset('img/logo.jpeg') ?>" alt="<?= e($siteName) ?>"
                     class="h-12 w-12 rounded-full object-cover ring-2 ring-brand/20">
                <span class="text-2xl font-extrabold text-brand-dark"><?= e($siteName) ?></span>
            </a>

            <h1 class="mt-8 text-4xl font-extrabold leading-tight text-slate-900">
                Pas de panique 🔐
            </h1>
            <p class="mt-4 text-lg text-slate-600">
                Renseigne l'adresse e-mail de ton compte. On t'envoie un lien sécurisé pour choisir un nouveau mot de passe.
            </p>

            <div class="mt-10 rounded-2xl border border-brand/15 bg-gradient-to-br from-brand-light/60 to-white p-5">
                <p class="text-sm font-semibold text-brand-dark">
                    <i class="fa-solid fa-shield-halved mr-1 text-brand"></i>
                    Sécurité garantie
                </p>
                <ul class="mt-2 space-y-1 text-sm text-slate-700">
                    <li>• Lien valable 1 heure</li>
                    <li>• Usage unique</li>
                    <li>• Ton mot de passe actuel reste actif jusqu'au changement</li>
                </ul>
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

                <a href="<?= e(url('login')) ?>" class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-brand">
                    <i class="fa-solid fa-arrow-left"></i> Retour à la connexion
                </a>

                <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Mot de passe oublié ?</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Renseigne l'e-mail associé à ton compte.
                </p>

                <!-- Message info (générique) -->
                <?php if ($info): ?>
                    <div class="mt-4 flex items-start gap-2 rounded-xl bg-green-50 p-3 text-sm text-green-800">
                        <i class="fa-solid fa-circle-check mt-0.5"></i>
                        <span><?= e($info) ?></span>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= e(url('forgot-password')) ?>" class="mt-6 space-y-4" novalidate>
                    <?= csrf_field() ?>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Adresse e-mail *</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                                <i class="fa-regular fa-envelope text-sm"></i>
                            </span>
                            <input type="email" id="email" name="email" required autofocus
                                   autocomplete="email"
                                   placeholder="ton@email.com"
                                   value="<?= $val('email') ?>"
                                   class="w-full rounded-xl bg-slate-50 py-2.5 pl-10 pr-3 text-sm focus:bg-white focus:outline-none focus:ring-2<?= $errClass('email') ?>">
                        </div>
                        <?php if ($hasErr('email')): ?>
                            <p class="mt-1 flex items-center gap-1 text-xs text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i><?= e($errors['email']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-brand py-3 font-semibold text-white shadow-lg shadow-brand/25 transition hover:bg-brand-dark">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Envoyer le lien
                    </button>
                </form>

                <div class="mt-6 rounded-xl bg-slate-50 p-3 text-xs text-slate-500">
                    <p>
                        <i class="fa-solid fa-circle-info mr-1 text-brand"></i>
                        Tu n'as pas renseigné d'e-mail à l'inscription ? Contacte le support
                        <a href="<?= e(whatsapp_link('Bonjour, j\'ai oublié mon mot de passe.')) ?>"
                           target="_blank" rel="noopener"
                           class="font-semibold text-green-600 hover:underline">WhatsApp</a>.
                    </p>
                </div>

                <p class="mt-6 text-center text-xs text-slate-400">
                    &copy; <?= date('Y') ?> <?= e($siteName) ?> — Tous droits réservés.
                </p>
            </div>
        </section>
    </div>
</main>