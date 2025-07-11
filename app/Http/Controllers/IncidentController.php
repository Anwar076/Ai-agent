<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    /**
     * Display a listing of incidents
     */
    public function index()
    {
        $incidents = Incident::with('conversation')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('incidents.index', compact('incidents'));
    }

    /**
     * Display the specified incident
     */
    public function show(Incident $incident)
    {
        $incident->load('conversation');
        return view('incidents.show', compact('incident'));
    }

    /**
     * Resolve an incident
     */
    public function resolve(Request $request, Incident $incident)
    {
        $request->validate([
            'resolution' => 'required|string|max:2000'
        ]);

        $incident->markAsResolved($request->resolution);

        return response()->json([
            'success' => true,
            'message' => 'Incident resolved successfully!',
            'incident' => $incident->fresh()
        ]);
    }
}
