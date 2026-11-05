<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\BookService;
use Throwable;

class BookController
{
    public function __construct(private BookService $service) {}

    public function index(Request $request): void
    {
        $this->handle(fn() => Response::success($this->service->list(), 'Daftar buku'));
    }

    public function show(Request $request, string $id): void
    {
        $this->handle(fn() => Response::success($this->service->get((int) $id), 'Detail buku'));
    }

    public function store(Request $request): void
    {
        $this->handle(fn() => Response::success(
            $this->service->create($request->getBody()),
            'Buku berhasil dibuat',
            201
        ));
    }

    public function update(Request $request, string $id): void
    {
        $this->handle(fn() => Response::success(
            $this->service->update((int) $id, $request->getBody()),
            'Buku berhasil diperbarui'
        ));
    }

    public function destroy(Request $request, string $id): void
    {
        $this->handle(function () use ($id) {
            $this->service->delete((int) $id);
            Response::success(null, 'Buku berhasil dihapus');
        });
    }

    /** Menangkap exception dan mengubahnya menjadi response JSON dengan status yang tepat. */
    private function handle(callable $action): void
    {
        try {
            $action();
        } catch (NotFoundException | ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode());
        } catch (Throwable $e) {
            Response::error('Terjadi kesalahan pada server', 500);
        }
    }
}