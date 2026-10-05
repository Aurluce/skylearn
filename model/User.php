<?php
declare(strict_types=1);

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';

    public function findByPhone(string $phone): ?array
    {
        $st = $this->db()->prepare("SELECT * FROM users WHERE phone = :p LIMIT 1");
        $st->execute(['p' => $phone]);
        return $st->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $st = $this->db()->prepare("SELECT * FROM users WHERE email = :e LIMIT 1");
        $st->execute(['e' => $email]);
        return $st->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $st = $this->db()->prepare(
            "INSERT INTO users
                (role_id, first_name, last_name, phone, email, password_hash,
                 class_id, school, city, school_year, status)
             VALUES
                (:role_id, :first_name, :last_name, :phone, :email, :password_hash,
                 :class_id, :school, :city, :school_year, :status)"
        );

        $st->execute([
            'role_id'       => $data['role_id'],
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'phone'         => $data['phone'],
            'email'         => $data['email'] ?? null,
            'password_hash' => $data['password_hash'],
            'class_id'      => $data['class_id'] ?? null,
            'school'        => $data['school'] ?? null,
            'city'          => $data['city'] ?? null,
            'school_year'   => $data['school_year'] ?? null,
            'status'        => $data['status'] ?? 'active',
        ]);

        return (int) $this->db()->lastInsertId();
    }
}