<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\BookRepositoryInterface;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;

class BookService
{
    public function __construct(private BookRepositoryInterface $repository) {}

    public function list(): array
    {
        return array_map(fn($book) => $book->toArray(), $this->repository->findAll());
    }

    public function get(int $id): array
    {
        $book = $this->repository->findById($id);

        if ($book === null) {
            throw new NotFoundException("Buku dengan ID {$id} tidak ditemukan");
        }

        return $book->toArray();
    }

    public function create(array $data): array
    {
        $this->validate($data);

        return $this->repository->create($this->normalize($data))->toArray();
    }

    public function update(int $id, array $data): array
    {
        $this->get($id); // pastikan buku ada, kalau tidak lempar 404
        $this->validate($data);

        return $this->repository->update($id, $this->normalize($data))->toArray();
    }

    public function delete(int $id): void
    {
        $this->get($id); // pastikan buku ada

        $this->repository->delete($id);
    }

    private function validate(array $data): void
    {
        if (trim((string) ($data['judul'] ?? '')) === '') {
            throw new ValidationException('Judul wajib diisi');
        }

        if (trim((string) ($data['penulis'] ?? '')) === '') {
            throw new ValidationException('Penulis wajib diisi');
        }

        if (!isset($data['tahun']) || !is_numeric($data['tahun']) || (int) $data['tahun'] < 1000) {
            throw new ValidationException('Tahun tidak valid');
        }

        if (!isset($data['stok']) || !is_numeric($data['stok']) || (int) $data['stok'] < 0) {
            throw new ValidationException('Stok tidak boleh negatif');
        }
    }

    private function normalize(array $data): array
    {
        return [
            'judul'   => trim($data['judul']),
            'penulis' => trim($data['penulis']),
            'tahun'   => (int) $data['tahun'],
            'stok'    => (int) $data['stok'],
        ];
    }
}