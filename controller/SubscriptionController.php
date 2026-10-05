<?php
declare(strict_types=1);

class SubscriptionController extends Controller
{
    public function plans(...$args): void
    {
        $db = Database::pdo();
        $classes = $db->query(
            "SELECT id, name, cycle, exam
             FROM classes
             WHERE is_active = 1
             ORDER BY position"
        )->fetchAll();

        $plans = $db->query(
            "SELECT id, code, name, duration_days
             FROM subscription_plans
             WHERE is_active = 1
             ORDER BY position"
        )->fetchAll();

        $priceRows = $db->query(
            "SELECT class_id, plan_id, price
             FROM class_plan_prices
             WHERE is_active = 1"
        )->fetchAll();

        $prices = [];
        foreach ($priceRows as $row) {
            $prices[(int) $row['class_id']][(int) $row['plan_id']] = (int) $row['price'];
        }

        $selectedClassId = (int) ($_GET['class_id'] ?? 0);
        $classIds = array_map(static fn(array $class): int => (int) $class['id'], $classes);
        if (!in_array($selectedClassId, $classIds, true)) {
            $selectedClassId = $classIds[0] ?? 0;
        }

        $selectedClass = null;
        foreach ($classes as $class) {
            if ((int) $class['id'] === $selectedClassId) {
                $selectedClass = $class;
                break;
            }
        }

        $this->render('pages/subscriptions', [
            'title'           => 'Abonnements et tarifs',
            'metaDescription' => 'Choisis la formule SKYLEARN adaptée à ta classe et accède aux cours, exercices et épreuves.',
            'classes'         => $classes,
            'plans'           => $plans,
            'prices'          => $prices,
            'selectedClass'   => $selectedClass,
            'selectedClassId' => $selectedClassId,
        ]);
    }

    public function show(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : SubscriptionController::show';
    }

    public function checkout(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : SubscriptionController::checkout';
    }



public function pricesForClass(): void
{
    $classId = (int) ($_GET['class_id'] ?? 0);
    if (!$classId) {
        $this->json([], 400);
    }

    $rows = Database::pdo()->prepare(
        "SELECT p.code, MIN(cpp.price) AS price
         FROM subscription_plans p
         LEFT JOIN class_plan_prices cpp
                ON cpp.plan_id = p.id
               AND cpp.class_id = :cid
               AND cpp.is_active = 1
         WHERE p.is_active = 1
         GROUP BY p.id
         ORDER BY p.position"
    );
    $rows->execute(['cid' => $classId]);

    $out = [];
    foreach ($rows->fetchAll() as $r) {
        $out[$r['code']] = (int) $r['price'];
    }

    $this->json($out);
}

}