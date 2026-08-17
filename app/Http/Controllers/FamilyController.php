<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\FamilyInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class FamilyController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $family = $user->families()->first();

        $members = $family ? $family->members()->get() : collect();
        $invitations = $family
            ? $family->invitations()->where('status', 'pending')->with('inviter')->get()
            : collect();

        $pendingInvitation = FamilyInvitation::where('email', $user->email)
            ->where('status', 'pending')
            ->with('family')
            ->first();

        return Inertia::render('family/Index', [
            'family' => $family,
            'members' => $members,
            'invitations' => $invitations,
            'pendingInvitation' => $pendingInvitation,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => ['required', 'string', 'max:255']]);

        $user = $request->user();

        if ($user->families()->exists()) {
            return back()->withErrors(['name' => 'أنت بالفعل عضو في عائلة']);
        }

        $family = Family::create([
            'name' => $request->name,
            'created_by' => $user->id,
        ]);

        $family->members()->attach($user->id, [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم إنشاء العائلة بنجاح']);

        return to_route('family.index');
    }

    public function invite(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = $request->user();
        $family = $user->families()->first();

        if (! $family) {
            return back()->withErrors(['email' => 'ليس لديك عائلة']);
        }

        $isMember = $family->members()->where('user_id', $request->email)->exists();
        if ($isMember) {
            return back()->withErrors(['email' => 'هذا الشخص عضو في العائلة بالفعل']);
        }

        FamilyInvitation::create([
            'family_id' => $family->id,
            'email' => $request->email,
            'invited_by' => $user->id,
            'token' => Str::random(64),
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم إرسال الدعوة']);

        return to_route('family.index');
    }

    public function accept(Request $request, FamilyInvitation $invitation): RedirectResponse
    {
        if ($invitation->email !== $request->user()->email) {
            abort(403);
        }

        if ($invitation->status !== 'pending' || $invitation->expires_at->isPast()) {
            return back()->withErrors(['error' => 'الدعوة منتهية الصلاحية']);
        }

        $invitation->update(['status' => 'accepted']);

        $invitation->family->members()->attach($request->user()->id, [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم الانضمام إلى العائلة']);

        return to_route('family.index');
    }

    public function removeMember(Request $request, Family $family, $userId): RedirectResponse
    {
        $user = $request->user();
        $member = $family->members()->wherePivot('user_id', $userId)->first();

        if (! $member) {
            abort(404);
        }

        $isAdmin = $family->members()->wherePivot('user_id', $user->id)->wherePivot('role', 'admin')->exists();
        if (! $isAdmin) {
            abort(403);
        }

        $family->members()->detach($userId);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم إزالة العضو']);

        return to_route('family.index');
    }

    public function leave(Request $request): RedirectResponse
    {
        $user = $request->user();
        $family = $user->families()->first();

        if (! $family) {
            return to_route('family.index');
        }

        $family->members()->detach($user->id);

        if ($family->members()->count() === 0) {
            $family->delete();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم مغادرة العائلة']);

        return to_route('family.index');
    }
}
