<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\BookRepositoryInterface;
use App\Models\Book;
use PDO;

class MySQLBookRepository implements BookRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM books ORDER BY id DESC');

        return array_map(fn(array $row) => $this->toBook($row), $stmt->fetchAll());
    }

    public function findById(int $id): ?Book
    {
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->toBook($row) : null;
    }

    public function create(array $data): Book
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO books (judul, penulis, tahun, stok)
             VALUES (:judul, :penulis, :tahun, :stok)'
        );
        $stmt->execute([
            'judul'   => $data['judul'],
            'penulis' => $data['penulis'],
            'tahun'   => $data['tahun'],
            'stok'    => $data['stok'],
        ]);

        return $this->findById((int) $this->pdo->lastInsertId());
    }

    public function update(int $id, array $data): ?Book
    {
        $stmt = $this->pdo->prepare(
            'UPDATE books
             SET judul = :judul, penulis = :penulis, tahun = :tahun, stok = :stok
             WHERE id = :id'
        );
        $stmt->execute([
            'judul'   => $data['judul'],
            'penulis' => $data['penulis'],
            'tahun'   => $data['tahun'],
            'stok'    => $data['stok'],
            'id'      => $id,
        ]);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    private function toBook(array $row): Book
    {
        return new Book(
            (int) $row['id'],
            $row['judul'],
            $row['penulis'],
            (int) $row['tahun'],
            (int) $row['stok']
        );
    }
}