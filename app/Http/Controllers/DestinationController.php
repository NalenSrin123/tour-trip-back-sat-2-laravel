<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function update(Request $request, Destination $destination): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $destination->update($validated);

        return $this->successResponse(
            $destination->fresh(),
            'Destination updated successfully.'
        );
    }

    public function destroy(Destination $destination): JsonResponse
    {
        $destination->delete();

        return $this->successResponse(
            null,
            'Destination deleted successfully.'
        );
    }
}
