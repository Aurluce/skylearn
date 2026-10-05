<?php
$siteName = setting('site_name', 'SKYLEARN');
$slogan   = setting('site_slogan', '');
$email    = setting('contact_email', '');
$phone    = setting('contact_phone', '');
$city     = setting('address_city', '');

// clé setting => [label, classe Font Awesome]
$socials = [
    'facebook_url'  => ['Facebook',  'fa-brands fa-facebook-f'],
    'tiktok_url'    => ['TikTok',    'fa-brands fa-tiktok'],
    'youtube_url'   => ['YouTube',   'fa-brands fa-youtube'],
    'instagram_url' => ['Instagram', 'fa-brands fa-instagram'],
    'linkedin_url'  => ['LinkedIn',  'fa-brands fa-linkedin-in'],
];
?>
<footer class="mt-16 border-t border-slate-200 bg-white">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Marque -->
        <div>
            <a href="/" class="flex items-center gap-2">
                <img src="<?= asset('img/logo.jpeg') ?>" alt="<?= e($siteName) ?>"
                     class="h-10 w-10 rounded-full object-cover ring-2 ring-brand/20">
                <span class="text-lg font-extrabold text-brand-dark"><?= e($siteName) ?></span>
            </a>
            <?php if ($slogan): ?>
                <p class="mt-3 text-sm text-slate-600"><?= e($slogan) ?></p>
            <?php endif; ?>
            <p class="mt-4 text-xs text-slate-400">
                &copy; <?= date('Y') ?> <?= e($siteName) ?>. Tous droits réservés.
            </p>
        </div>

        <!-- Navigation -->
        <div>
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-800">Explorer</h3>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <?php foreach ([
                    ['/courses',       'Cours',        'fa-book-open'],
                    ['/subjects',      'Matières',     'fa-shapes'],
                    ['/subscriptions', 'Abonnements',  'fa-crown'],
                    ['/testimonials',  'Témoignages',  'fa-comments'],
                    ['/about',         'À propos',     'fa-circle-info'],
                ] as [$url, $label, $icon]): ?>
                    <li>
                        <a href="<?= $url ?>" class="group inline-flex items-center gap-2 hover:text-brand">
                            <i class="fa-solid <?= $icon ?> w-4 text-center text-slate-400 group-hover:text-brand" aria-hidden="true"></i>
                            <?= $label ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Contact -->
        <div>
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-800">Contact</h3>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <?php if ($phone): ?>
                    <li>
                        <a href="tel:<?= e(preg_replace('/\s+/', '', $phone)) ?>" class="inline-flex items-center gap-2 hover:text-brand">
                            <i class="fa-solid fa-phone w-4 text-center text-brand" aria-hidden="true"></i> <?= e($phone) ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($email): ?>
                    <li>
                        <a href="mailto:<?= e($email) ?>" class="inline-flex items-center gap-2 hover:text-brand">
                            <i class="fa-solid fa-envelope w-4 text-center text-brand" aria-hidden="true"></i> <?= e($email) ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($city): ?>
                    <li class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-location-dot w-4 text-center text-brand" aria-hidden="true"></i> <?= e($city) ?>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="<?= e(whatsapp_link('Bonjour, je viens du site.')) ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 font-medium text-green-600 hover:text-green-700">
                        <i class="fa-brands fa-whatsapp w-4 text-center text-lg" aria-hidden="true"></i> WhatsApp
                    </a>
                </li>
            </ul>
        </div>

        <!-- Réseaux -->
        <div>
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-800">Suivez-nous</h3>
            <div class="mt-3 flex flex-wrap gap-2">
                <?php foreach ($socials as $key => [$label, $iconClass]): ?>
                    <?php $url = social_link($key); ?>
                    <?php if ($url): ?>
                        <a href="<?= e($url) ?>" target="_blank" rel="noopener"
                           aria-label="<?= e($label) ?>" title="<?= e($label) ?>"
                           class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:-translate-y-0.5 hover:bg-brand hover:text-white">
                            <i class="<?= $iconClass ?> text-base" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php $waUrl = whatsapp_link('Bonjour, je viens du site ' . $siteName . '.'); ?>
                <?php if ($waUrl !== '#'): ?>
                    <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener"
                       aria-label="WhatsApp" title="WhatsApp"
                       class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 transition hover:-translate-y-0.5 hover:bg-green-500 hover:text-white">
                        <i class="fa-brands fa-whatsapp text-lg" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            </div>
            <ul class="mt-4 space-y-2 text-xs text-slate-500">
                <li><a href="/about" class="inline-flex items-center gap-2 hover:text-brand"><i class="fa-solid fa-scale-balanced w-4 text-center" aria-hidden="true"></i> Mentions légales</a></li>
                <li><a href="/about" class="inline-flex items-center gap-2 hover:text-brand"><i class="fa-solid fa-file-contract w-4 text-center" aria-hidden="true"></i> Conditions d'utilisation</a></li>
            </ul>
        </div>
    </div>
</footer>