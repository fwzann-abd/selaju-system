<?php

namespace App\Http\Controllers\Admin;

use App\Models\Participant;
use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the participants.
     */
    public function index(Request $request)
    {
        $search = $request->query('q', '');

        $participants = Participant::query()
            ->when($search, function ($query) use ($search) {
                $query->where('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->with(['school:id,name,slug', 'student:id,account_id,name'])
            ->paginate(15);

        return view('admin.participants.index', [
            'participants' => $participants,
            'search' => $search,
            'pageTitle' => 'Daftar Peserta',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Peserta', 'url' => null],
                ['label' => 'Daftar', 'url' => null],
            ],
        ]);
    }

    /**
     * Show the form for creating a new participant.
     */
    public function create()
    {
        $schools = School::select('id', 'name', 'slug')->orderBy('name')->get();

        return view('admin.participants.create', [
            'schools' => $schools,
            'pageTitle' => 'Tambah Peserta',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Peserta', 'url' => route('admin.participants.index')],
                ['label' => 'Tambah', 'url' => null],
            ],
        ]);
    }

    /**
     * Store a newly created participant in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:accounts,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:accounts,email'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'school_id' => ['nullable', 'uuid', 'exists:schools,id'],
        ]);

        Participant::create([
            ...$validated,
            'is_active' => true,
        ]);

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil ditambahkan.');
    }

    /**
     * Display the specified participant.
     */
    public function show(Participant $participant)
    {
        return view('admin.participants.show', [
            'participant' => $participant->load('school:id,name,slug'),
            'pageTitle' => 'Detail Peserta',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Peserta', 'url' => route('admin.participants.index')],
                ['label' => $participant->name, 'url' => null],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function edit(Participant $participant)
    {
        $schools = School::select('id', 'name', 'slug')->orderBy('name')->get();

        return view('admin.participants.edit', [
            'participant' => $participant,
            'schools' => $schools,
            'pageTitle' => 'Edit Peserta',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Peserta', 'url' => route('admin.participants.index')],
                ['label' => 'Edit', 'url' => null],
            ],
        ]);
    }

    /**
     * Update the specified participant in storage.
     */
    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:accounts,username,' . $participant->uuid . ',uuid'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:accounts,email,' . $participant->uuid . ',uuid'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'school_id' => ['nullable', 'uuid', 'exists:schools,id'],
            'is_active' => ['boolean'],
        ]);

        $participant->update($validated);

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil diperbarui.');
    }

    /**
     * Remove the specified participant from storage.
     */
    public function destroy(Participant $participant)
    {
        $participant->delete();

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil dihapus.');
    }
}
