<?php

namespace App\Http\Controllers;

use App\Http\Resources\CharacterCollection;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use App\Rules\HouseExists;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The Character Controller.
 *
 * This controller relies on Route Model Binding provided by Laravel,
 * so almost every resource binding and return is made implicitly.
 * 
 * @see https://laravel.com/docs/8.x/routing#route-model-binding
 * @see https://github.com/dextra/challenges/blob/master/backend/MAKE-MAGIC-PT.md
 * @author Nickolas Gomes Moraes
 * @version 1.0
 * @license WTFPL
 */
class CharacterController extends Controller
{
    /**
     * Display a listing of all characters.
     *
     * @return \App\Http\Resources\CharacterCollection
     */
    public function index(Request $request): CharacterCollection
    {
        $request->validate([
            'house' => ['uuid', new HouseExists],
        ]);

        /** Conditionally applies the house filter if set */
        $data = Character::when($request->input('house'), function ($query, $house) {
            return $query->where('house', $house);
        })
            ->paginate(10);

        return new CharacterCollection($data);
    }

    /**
     * Store a newly created character in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response|\App\Http\Resources\CharacterResource
     */
    public function store(Request $request): Response|CharacterResource
    {
        $validated = $request->validate([
            'name' => 'required|max:255|string',
            'role' => 'required|max:255|string',
            'school' => 'required|max:255|string',
            'house' => ['required', 'uuid', new HouseExists],
            'patronus' => 'required|max:255|string'
        ]);

        $character = Character::create($validated);

        return new CharacterResource($character);
    }

    /**
     * Display the specified character.
     *
     * @param  \App\Models\Character  $character
     * 
     * @return \App\Http\Resources\CharacterResource
     */
    public function show(Character $character): CharacterResource
    {
        return new CharacterResource($character);
    }

    /**
     * Update the specified character in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Character  $character
     * 
     * @return \Illuminate\Http\Response|\App\Http\Resources\CharacterResource
     */
    public function update(Request $request, Character $character): Response|CharacterResource
    {
        $validated = $request->validate([
            'name' => 'required|max:255|string',
            'role' => 'required|max:255|string',
            'school' => 'required|max:255|string',
            'house' => ['required', 'uuid', new HouseExists],
            'patronus' => 'required|max:255|string'
        ]);

        $character->update($validated);

        return new CharacterResource($character);
    }

    /**
     * Remove the specified character from the database.
     *
     * @param  \App\Models\Character  $character
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy(Character $character): Response
    {
        $character->delete();

        /** @see https://jsonapi.org/format/#crud-deleting */
        return response()->noContent();
    }
}
