<?php
$siteName = setting('site_name', 'SKYLEARN');
$path     = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = app_base_path();
if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath . '/'))) {
    $path = substr($path, strlen($basePath)) ?: '/';
}

// [url, label, icône Font Awesome]
$links = [
    ['/courses',       'Cours',        'fa-book-open'],
    ['/subjects',      'Matières',     'fa-shapes'],
     ['/exams',         'Épreuves',     'fa-file-pen'],  
    ['/subscriptions', 'Abonnements',  'fa-crown'],
    ['/testimonials',  'Témoignages',  'fa-comments'],
    ['/contact',       'Contact',      'fa-envelope'],
];
$isActive = fn(string $url): bool => $path === $url || str_starts_with($path, $url . '/');
?>
<header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/85 backdrop-blur supports-[backdrop-filter]:bg-white/60">
    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3" aria-label="Navigation principale">
        <a href="<?= e(url()) ?>" class="flex items-center gap-2">
            <img src="<?= asset('img/logo.jpeg') ?>" alt="<?= e($siteName) ?>"
                 class="h-9 w-9 rounded-full object-cover ring-2 ring-brand/20">
            <span class="text-xl font-extrabold tracking-tight text-brand-dark"><?= e($siteName) ?></span>
        </a>

        <!-- Menu desktop -->
        <div class="hidden items-center gap-1 text-sm font-medium md:flex">
            <?php foreach ($links as [$url, $label, $icon]): $active = $isActive($url); ?>
                <a href="<?= e(url($url)) ?>"
                   <?= $active ? 'aria-current="page"' : '' ?>
                   class="inline-flex items-center gap-2 rounded-full px-3 py-2 transition
                          <?= $active ? 'bg-brand-light text-brand' : 'text-slate-700 hover:bg-slate-100 hover:text-brand' ?>">
                    <i class="fa-solid <?= $icon ?> text-xs" aria-hidden="true"></i>
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
            <?php if (Auth::check()): ?>
                <!-- ============ UTILISATEUR CONNECTÉ ============ -->
                <a href="<?= e(url('dashboard')) ?>"
                   class="hidden items-center gap-2 rounded-full bg-brand px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-dark sm:inline-flex">
                    <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i> Mon espace
                </a>
                <form method="post" action="<?= e(url('logout')) ?>" class="hidden sm:inline">
                    <?= csrf_field() ?>
                    <button class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800"
                            title="Se déconnecter">
                        <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                        <span class="sr-only lg:not-sr-only">Déconnexion</span>
                    </button>
                </form>
            <?php else: ?>
                <!-- ============ UTILISATEUR DÉCONNECTÉ ============ -->
                <a href="<?= e(url('login')) ?>"
                   class="hidden items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-brand hover:text-brand sm:inline-flex">
                    <i class="fa-regular fa-user" aria-hidden="true"></i> Connexion
                </a>
                <a href="<?= e(url('register')) ?>"
                   class="inline-flex items-center gap-2 rounded-full bg-brand px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-dark">
                    <i class="fa-solid fa-user-plus text-xs" aria-hidden="true"></i> Inscription
                </a>
            <?php endif; ?>

            <!-- Burger mobile -->
            <button id="nav-toggle" type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100 md:hidden"
                    aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="nav-mobile">
                <i id="nav-toggle-icon" class="fa-solid fa-bars text-lg" aria-hidden="true"></i>
            </button>
        </div>
    </nav>

    <!-- Menu mobile -->
    <div id="nav-mobile" class="hidden border-t border-slate-200 bg-white px-4 py-3 md:hidden">
        <div class="flex flex-col gap-1 text-sm font-medium">
            <?php foreach ($links as [$url, $label, $icon]): $active = $isActive($url); ?>
                <a href="<?= e(url($url)) ?>"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 <?= $active ? 'bg-brand-light text-brand' : 'text-slate-700 hover:bg-slate-50' ?>">
                    <i class="fa-solid <?= $icon ?> w-5 text-center" aria-hidden="true"></i> <?= $label ?>
                </a>
            <?php endforeach; ?>

            <div class="my-2 border-t border-slate-100"></div>

            <?php if (Auth::check()): ?>
                <!-- Connecté -->
                <a href="<?= e(url('dashboard')) ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-semibold text-brand">
                    <i class="fa-solid fa-graduation-cap w-5 text-center" aria-hidden="true"></i> Mon espace
                </a>
                <form method="post" action="<?= e(url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-slate-500 hover:bg-slate-50">
                        <i class="fa-solid fa-right-from-bracket w-5 text-center" aria-hidden="true"></i> Déconnexion
                    </button>
                </form>
            <?php else: ?>
                <!-- Déconnecté -->
                <a href="<?= e(url('login')) ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fa-regular fa-user w-5 text-center" aria-hidden="true"></i> Connexion
                </a>
                <a href="<?= e(url('register')) ?>" class="flex items-center gap-3 rounded-lg bg-brand px-3 py-2.5 font-semibold text-white hover:bg-brand-dark">
                    <i class="fa-solid fa-user-plus w-5 text-center" aria-hidden="true"></i> Inscription
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
