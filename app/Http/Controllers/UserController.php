<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Role;
use App\Models\User;
use Inertia\Response;
use App\Actions\Jetstream\DeleteUser;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UserController extends Controller
{
    public function index(): Response
    {   
        $this->authorize('view', User::class);

        $data = request()->validate([
            'term' => 'string|nullable',
            'role' => 'numeric|nullable',
        ]); 

        $mQuery = User::query();

        $data['term'] = $data['term'] ?? '';
        $data['role'] = $data['role'] ?? '';

        $mQuery->where(function ($query) use ($data) {
            $query->where('surname', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('email', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('name', 'LIKE', '%'.$data['term'].'%');
        });

        if ($data['role'] !== '') {  
            $mQuery->where('role_id', $data['role']);
        }

        return Inertia::render('Admin/Users', 
            [
                'roles' => Role::all(),
                'users' => $mQuery->paginate(10),
                'params' => [
                    'term' => $data['term'],
                    'role' => $data['role'],
                ],
                'middleware' => auth()->user()->role->middlewares->pluck('name')->toArray()
            ]
        );
    }

    public function update(User $user, Request $request, UpdatesUserProfileInformation $updater)
    {
        $this->authorize('edit', User::class);

        if (Role::where('id', $request->input('role'))->get()->count() === 0)
            return redirect()->back()->withErrors([
                'role' => 'Invalid role group.'
            ]);

        $updater->update($user, $request->all());

        return app(ProfileInformationUpdatedResponse::class);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', User::class);

        if (auth()->user()->id === $user->id)
            return redirect()->back()->withErrors([
                'delete' => 'Can\'t delete yourself.'
            ]);

        app(DeleteUser::class)->delete($user);
    }
}
