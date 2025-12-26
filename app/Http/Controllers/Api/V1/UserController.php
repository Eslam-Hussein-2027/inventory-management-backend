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
  /**
   * Display a listing of users
   */
  public function index(): JsonResponse
  {
    $users = QueryBuilder::for(User::class)
      ->allowedFilters([
        AllowedFilter::partial('name'),
        AllowedFilter::partial('email'),
        AllowedFilter::callback(
          'role',
          fn($query, $value) =>
          $query->whereHas('roles', fn($q) => $q->where('name', $value))
        ),
      ])
      ->allowedSorts(['name', 'email', 'created_at'])
      ->with('roles')
      ->defaultSort('-created_at')
      ->paginate(request('per_page', 15))
      ->appends(request()->query());

    return $this->success(UserResource::collection($users)->response()->getData(true));
  }

  /**
   * Store a newly created user
   */
  public function store(StoreUserRequest $request): JsonResponse
  {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    $user->assignRole($request->role);

    // Handle avatar upload
    if ($request->hasFile('avatar')) {
      $user->addMediaFromRequest('avatar')
        ->toMediaCollection('avatar');
    }

    return $this->created(new UserResource($user->load('roles')), 'User created successfully');
  }

  /**
   * Display the specified user
   */
  public function show(User $user): JsonResponse
  {
    return $this->success(new UserResource($user->load('roles')));
  }

  /**
   * Update the specified user
   */
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

    // Handle avatar upload
    if ($request->hasFile('avatar')) {
      $user->clearMediaCollection('avatar');
      $user->addMediaFromRequest('avatar')
        ->toMediaCollection('avatar');
    }

    return $this->success(new UserResource($user->fresh()->load('roles')), 'User updated successfully');
  }

  /**
   * Remove the specified user
   */
  public function destroy(User $user): JsonResponse
  {
    $user->delete();

    return $this->noContent('User deleted successfully');
  }
}
