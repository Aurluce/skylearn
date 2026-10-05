<?php
$errors  = Session::flash('error')   ?? null;
$success = Session::flash('success') ?? null;
?>
<section class="mx-auto max-w-5xl">
    <h1 class="text-2xl font-bold">Gestion des séries</h1>
    <p class="mt-1 text-sm text-slate-500">
        Les séries s'appliquent surtout aux classes de 1ère et Tle (A4, C, D, E, TI, ACC, CG, ESF…).
    </p>

    <?php if ($success): ?>
        <div class="mt-4 rounded-xl bg-green-50 p-3 text-sm text-green-800"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="mt-4 rounded-xl bg-red-50 p-3 text-sm text-red-800"><?= e($errors) ?></div>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <form method="post" action="/admin/series"
          class="mt-6 grid gap-3 rounded-2xl bg-white p-5 shadow-sm sm:grid-cols-5">
        <?= csrf_field() ?>
        <select name="class_id" required class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
            <option value="">Classe</option>
            <?php foreach ($classes as $c): ?>
                <option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="code" placeholder="Code (A4, C…)" required
               class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <input type="text" name="label" placeholder="Libellé complet" required
               class="rounded-lg border border-slate-200 px-3 py-2 text-sm sm:col-span-2">
        <input type="number" name="position" value="0" min="0"
               class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <button class="rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white sm:col-span-5">
            Ajouter la série
        </button>
    </form>

    <!-- Liste -->
    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">Classe</th>
                    <th class="px-4 py-2">Code</th>
                    <th class="px-4 py-2">Libellé</th>
                    <th class="px-4 py-2">Actif</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($series as $s): ?>
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2"><?= e($s['class_name']) ?></td>
                        <td class="px-4 py-2 font-semibold"><?= e($s['code']) ?></td>
                        <td class="px-4 py-2"><?= e($s['label']) ?></td>
                        <td class="px-4 py-2">
                            <?= $s['is_active'] ? '✅' : '❌' ?>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <form method="post" action="/admin/series/<?= (int) $s['id'] ?>/delete"
                                  onsubmit="return confirm('Supprimer cette série ?')">
                                <?= csrf_field() ?>
                                <button class="text-red-600 hover:underline text-xs">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>