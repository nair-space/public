<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        return view('users.index', [
            'users' => $this->userService->getPaginatedUsers(),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $this->userService->createUser($request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(string $userId): View
    {
        $user = $this->userService->getUser($userId);

        if (!$user) {
            abort(404);
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(UserRequest $request, string $userId): RedirectResponse
    {
        $this->userService->updateUser($userId, $request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $userId): RedirectResponse
    {
        $this->userService->deleteUser($userId);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
