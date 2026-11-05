<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\CategoryService;
use PDOException;
use Throwable;

class CategoryController
{
    public function __construct(private CategoryService $service) {}

    public function index(Request $request): void
    {
        $this->handle(fn() => Response::success($this->service->list(), 'Daftar kategori'));
    }

    public function show(Request $request, string $id): void
    {
        $this->handle(fn() => Response::success($this->service->get((int) $id), 'Detail kategori'));
    }

    public function store(Request $request): void
    {
        $this->handle(fn() => Response::success(
            $this->service->create($request->getBody()),
            'Kategori berhasil dibuat',
            201
        ));
    }

    public function update(Request $request, string $id): void
    {
        $this->handle(fn() => Response::success(
            $this->service->update((int) $id, $request->getBody()),
            'Kategori berhasil diperbarui'
        ));
    }

    public function destroy(Request $request, string $id): void
    {
        $this->handle(function () use ($id) {
            $this->service->delete((int) $id);
            Response::success(null, 'Kategori berhasil dihapus');
        });
    }

    private function handle(callable $action): void
    {
        try {
            $action();
        } catch (NotFoundException | ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode());
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                Response::error('Nama kategori sudah dipakai', 422);
                return;
            }
            Response::error('Terjadi kesalahan pada server', 500);
        } catch (Throwable $e) {
            Response::error('Terjadi kesalahan pada server', 500);
        }
    }
}