<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
            ])
            ->allowedSorts(['name', 'email', 'created_at'])
            ->with('roles')
            ->defaultSort('-created_at')
            ->paginate(request('per_page', 15))
            ->appends(request()->query());

        return $this->success(UserResource::collection($users)->response()->getData(true));
    }
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return $this->created(new UserResource($user->load('roles')), 'User created successfully');
    }
    public function show(User $user): JsonResponse
    {
        return $this->success(new UserResource($user->load('roles')));
    }
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        return $this->success(new UserResource($user->fresh()->load('roles')), 'User updated successfully');
    }
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->noContent('User deleted successfully');
    }
}
