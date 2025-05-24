<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'birth_date',
        'photo_path',
        'role'
    ];

    private int $id;
    private string $username;
    private string $password;
    private string $firstName;
    private ?string $lastName;
    private string $birthDate;
    private ?string $photoPath;
    private string $role;
    private string $createdAt;

    public function find(int $id): ?static
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToObject($user);
        }

        return null;
    }

    public function findByUsername(string $username): ?static
    {
        return $this->findOneBy('username', $username);
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO users (username, password, first_name, last_name, birth_date, photo_path, role)
            VALUES (:username, :password, :firstName, :lastName, :birthDate, :photoPath, :role)
        ');

        return $stmt->execute([
            'username' => $data['username'],
            'password' => $data['password'],
            'firstName' => $data['first_name'],
            'lastName' => $data['last_name'],
            'birthDate' => $data['birth_date'],
            'photoPath' => $data['photo_path'] ?? null,
            'role' => $data['role'] ?? 'user'
        ]);
    }

    public function update(array $data): bool
    {
        $stmt = $this->db->prepare('
            UPDATE users 
            SET username = :username, first_name = :firstName, last_name = :lastName, 
                birth_date = :birthDate, photo_path = :photoPath, role = :role
            WHERE id = :id
        ');

        return $stmt->execute([
            'username' => $data['username'],
            'firstName' => $data['first_name'],
            'lastName' => $data['last_name'],
            'birthDate' => $data['birth_date'],
            'photoPath' => $data['photo_path'] ?? null,
            'role' => $data['role'] ?? 'user',
            'id' => $this->id
        ]);
    }

    public function updatePassword(string $password): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :password WHERE id = :id');
        return $stmt->execute(['password' => $password, 'id' => $this->id]);
    }

    public function delete(): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    public function getComplaints(): array
    {
        return $this->findBy('user_id', $this->id);
    }

    public function getSuggestions(): array
    {
        return $this->findBy('user_id', $this->id);
    }

    public function getFeedbackGiven(): array
    {
        return $this->findBy('admin_id', $this->id);
    }

    protected function mapToObject(array $data): static
    {
        $this->id = (int)$data['id'];
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->firstName = $data['first_name'];
        $this->lastName = $data['last_name'];
        $this->birthDate = $data['birth_date'];
        $this->photoPath = $data['photo_path'];
        $this->role = $data['role'];
        $this->createdAt = $data['created_at'];

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getBirthDate(): string
    {
        return $this->birthDate;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
    
    public function getFullName(): string
    {
        return $this->firstName . ($this->lastName ? ' ' . $this->lastName : '');
    }
}
