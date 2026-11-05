<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CategoryRepositoryInterface;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $repository) {}

    public function list(): array
    {
        return array_map(fn($c) => $c->toArray(), $this->repository->findAll());
    }

    public function get(int $id): array
    {
        $category = $this->repository->findById($id);

        if ($category === null) {
            throw new NotFoundException("Kategori dengan ID {$id} tidak ditemukan");
        }

        return $category->toArray();
    }

    public function create(array $data): array
    {
        $this->validate($data);

        return $this->repository->create($this->normalize($data))->toArray();
    }

    public function update(int $id, array $data): array
    {
        $this->get($id);
        $this->validate($data);

        return $this->repository->update($id, $this->normalize($data))->toArray();
    }

    public function delete(int $id): void
    {
        $this->get($id);
        $this->repository->delete($id);
    }

    private function validate(array $data): void
    {
        if (trim((string) ($data['nama'] ?? '')) === '') {
            throw new ValidationException('Nama kategori wajib diisi');
        }
    }

    private function normalize(array $data): array
    {
        $deskripsi = isset($data['deskripsi']) ? trim((string) $data['deskripsi']) : null;

        return [
            'nama'      => trim($data['nama']),
            'deskripsi' => $deskripsi === '' ? null : $deskripsi,
        ];
    }
}