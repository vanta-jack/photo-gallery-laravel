<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * MilestoneController
 * 
 * Manages milestones - life events tracking (baby months, school years, etc.).
 * Private by design: users only see their own milestones.
 */
class MilestoneController extends Controller
{
    /**
     * Display user's milestones
     * 
     * Auth required: only show current user's milestones
     */
    public function index(): View
    {
        $milestones = Milestone::where('user_id', auth()->id())
            ->with('photo')
            ->latest()
            ->paginate(20);

        return view('milestones.index', compact('milestones'));
    }

    /**
     * Show milestone creation form
     */
    public function create(): View
    {
        return view('milestones.create');
    }

    /**
     * Store new milestone
     */
    public function store(StoreMilestoneRequest $request): RedirectResponse
    {
        $milestone = Milestone::create([
            'user_id' => $request->user()->id,
            'photo_id' => $request->photo_id,
            'stage' => $request->stage,
            'label' => $request->label,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('milestones.show', $milestone)
            ->with('status', 'Milestone created successfully!');
    }

    /**
     * Display single milestone
     */
    public function show(Milestone $milestone): View|RedirectResponse
    {
        // Privacy: only owner or admin can view
        if (auth()->id() !== $milestone->user_id && auth()->user()->role !== 'admin') {
            return redirect()
                ->route('milestones.index')
                ->with('error', 'You cannot view this milestone.');
        }

        $milestone->load('photo');

        return view('milestones.show', compact('milestone'));
    }

    /**
     * Show edit form
     */
    public function edit(Milestone $milestone): View
    {
        $this->authorize('update', $milestone);

        return view('milestones.edit', compact('milestone'));
    }

    /**
     * Update milestone
     */
    public function update(UpdateMilestoneRequest $request, Milestone $milestone): RedirectResponse
    {
        $this->authorize('update', $milestone);

        $milestone->update($request->validated());

        return redirect()
            ->route('milestones.show', $milestone)
            ->with('status', 'Milestone updated successfully!');
    }

    /**
     * Delete milestone
     */
    public function destroy(Milestone $milestone): RedirectResponse
    {
        $this->authorize('delete', $milestone);

        $milestone->delete();

        return redirect()
            ->route('milestones.index')
            ->with('status', 'Milestone deleted successfully!');
    }
}
