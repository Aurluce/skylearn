<?php
$wa = whatsapp_link('Bonjour, je viens du site ' . setting('site_name', 'SKYLEARN') . '.');
?>
<?php if ($wa !== '#'): ?>
<a href="<?= e($wa) ?>" target="_blank" rel="noopener"
   class="group fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-full bg-green-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-green-500/30 transition hover:scale-105 hover:bg-green-600 focus:outline-none focus-visible:ring-4 focus-visible:ring-green-300"
   aria-label="Contacter sur WhatsApp">
    <span class="absolute inset-0 -z-10 animate-ping rounded-full bg-green-400/40 [animation-duration:2.5s]"></span>
    <i class="fa-brands fa-whatsapp text-2xl leading-none" aria-hidden="true"></i>
    <span class="hidden sm:inline">Discuter sur WhatsApp</span>
</a>
<?php endif; ?>