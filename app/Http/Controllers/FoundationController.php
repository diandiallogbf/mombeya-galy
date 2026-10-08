<?php

namespace App\Http\Controllers;

use App\Models\AidRequest;
use App\Models\FoundationPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FoundationController extends Controller
{
    public function index(): View
    {
        return view('foundation.index', ['posts' => FoundationPost::published()->paginate(12)]);
    }

    public function show(FoundationPost $post): View
    {
        abort_unless($post->is_published, 404);

        return view('foundation.show', [
            'post' => $post,
            'others' => FoundationPost::published()->whereKeyNot($post->id)->take(4)->get(),
        ]);
    }

    public function aidForm(): View
    {
        return view('foundation.aid');
    }

    public function aidStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9 +().-]{8,}$/'],
            'transfer_method' => ['required', 'in:Orange Money,MTN Mobile Money'],
            'email' => ['nullable', 'email', 'max:150'],
            'message' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'full_name' => 'prénom et nom',
            'phone' => 'numéro de téléphone',
            'transfer_method' => 'moyen de transfert',
        ]);

        AidRequest::create($data + ['status' => 'nouvelle']);

        return redirect()->route('foundation.aid')
            ->with('success', 'Votre demande d\'aide a bien été envoyée. Notre équipe vous contactera rapidement.');
    }
}
