<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of users.
     *
     * @return View
     */
    public function index(): View
    {
        $users = User::withCount(['ownedBooks', 'loans', 'reviews'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $user = User::with(['ownedBooks', 'loans', 'reviews'])
            ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Toggle user role between 'user' and 'admin'.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function toggleRole(int $id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            // Prevent toggling your own role
            if ($user->id === auth()->id()) {
                return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle!');
            }

            $user->role = $user->role === 'admin' ? 'user' : 'admin';
            $user->save();

            return back()->with('success', 'Rôle de l\'utilisateur mis à jour avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting your own account
            if ($user->id === auth()->id()) {
                return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte!');
            }

            $user->delete();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Utilisateur supprimé avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}
