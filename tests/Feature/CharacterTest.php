<?php

namespace Tests\Feature;

use App\Models\Character;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

/**
 * The feature testing for all functions on the `characters` endpoint.
 *
 * @see https://github.com/dextra/challenges/blob/master/backend/MAKE-MAGIC-PT.md
 * @author Nickolas Gomes Moraes
 * @version 1.0
 * @license WTFPL
 */
class CharacterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Sets up the testing instance.
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        /** We fake every response so we don't spam their servers and for our test be able to run offline */
        Http::fake(function () {
            return Http::response(
                '{"houses":[{"id":"1760529f-6d51-4cb1-bcb1-25087fce5bde","values":["courage","bravery","nerve","chivalry"],"mascot":"lion","school":"Hogwarts School of Witchcraft and Wizardry","name":"Gryffindor","colors":["scarlet","gold"],"founder":"Goderic Gryffindor","houseGhost":"Nearly Headless Nick","headOfHouse":"Minerva McGonagall"},{"headOfHouse":"Pomona Sprout","houseGhost":"The Fat Friar","mascot":"badger","colors":["yellow","black"],"values":["hard work","patience","justice","loyalty"],"name":"Hufflepuff","id":"542b28e2-9904-4008-b038-034ab312ad7e","school":"Hogwarts School of Witchcraft and Wizardry","founder":"Helga Hufflepuff"},{"founder":"Rowena Ravenclaw","id":"56cabe3a-9bce-4b83-ba63-dcd156e9be45","headOfHouse":"Fillius Flitwick","colors":["blue"," bronze"],"mascot":"eagle","school":"Hogwarts School of Witchcraft and Wizardry","houseGhost":"The Grey Lady","name":"Ravenclaw","values":["intelligence","creativity","learning","wit"]},{"headOfHouse":"Severus Snape","values":["ambition","cunning","leadership","resourcefulness"],"id":"df01bd60-e3ed-478c-b760-cdbd9afe51fc","houseGhost":"The Bloody Baron","colors":["green","silver"],"name":"Slytherin","mascot":"serpent","founder":"Salazar Slytherin"}]}',
                200
            );
        });
    }

    /**
     * Tests if all characters are returned successfully.
     *
     * @return void
     */
    public function test_get_characters_success(): void
    {
        /** Run the DatabaseSeeder */
        $this->seed();

        /** Get the character list */
        $response = $this->get('api/characters');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'role',
                        'school',
                        'house',
                        'patronus'
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                "meta" => [
                    "current_page",
                    "from",
                    "last_page",
                    "links" => [
                        [
                            "url",
                            "label",
                            "active"
                        ],
                        [
                            "url",
                            "label",
                            "active"
                        ],
                        [
                            "url",
                            "label",
                            "active"
                        ],
                    ],
                    "path",
                    "per_page",
                    "to",
                    "total",
                ]
            ]);
    }

    /**
     * Tests if the house filter is behaving correctly.
     * 
     * @return void
     */
    public function test_get_filtered_by_house_characters(): void
    {
        /** We use static responses, so this is fine */
        $house = '1760529f-6d51-4cb1-bcb1-25087fce5bde';

        /** Run the DatabaseSeeder */
        $this->seed();

        /** Get the character list */
        $response = $this->get("api/characters?house=$house");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'role',
                        'school',
                        'house',
                        'patronus'
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                "meta" => [
                    "current_page",
                    "from",
                    "last_page",
                    "links" => [
                        [
                            "url",
                            "label",
                            "active"
                        ],
                        [
                            "url",
                            "label",
                            "active"
                        ],
                        [
                            "url",
                            "label",
                            "active"
                        ],
                    ],
                    "path",
                    "per_page",
                    "to",
                    "total",
                ]
            ]);

        /** Iterates over all characters and checks if their houses match */
        foreach ($response['data'] as $key => $value) {
            $this->assertEquals($house, $value['house']);
        }
    }

    /**
     * Tests for an invalid house filter.
     * 
     * @return void
     */
    public function test_get_invalid_house_filter_characters(): void
    {
        $house = 'fermento';

        /** Run the DatabaseSeeder */
        $this->seed();

        /** Get the character list */
        $response = $this->get("api/characters?house=$house");

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'errors' => [
                    [
                        'status',
                        'source',
                        'detail'
                    ]
                ]
            ])
            ->assertJsonPath('errors.0.status', 422)
            ->assertJsonPath('errors.0.detail.0', 'The house must be a valid UUID.')
            ->assertJsonPath('errors.0.detail.1', 'The house does not exist.');
    }

    /**
     * Tests for an unexisting house filter.
     * 
     * @return void
     */
    public function test_get_unexisting_house_filter_characters(): void
    {
        $house = Uuid::uuid4()->toString();

        /** Run the DatabaseSeeder */
        $this->seed();

        /** Get the character list */
        $response = $this->get("api/characters?house=$house");

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'errors' => [
                    [
                        'status',
                        'source',
                        'detail'
                    ]
                ]
            ])
            ->assertJsonPath('errors.0.status', 422)
            ->assertJsonPath('errors.0.detail.0', 'The house does not exist.');
    }

    /**
     * Tests if an empty list of characters can be returned
     * if the database is empty.
     *
     * @return void
     */
    public function test_get_characters_empty(): void
    {
        /** Get the character list */
        $response = $this->get('api/characters');

        $response->assertStatus(200)
            ->assertJsonPath('data', []);
    }

    /**
     * Tests if a character can be succesfully returned
     * from the database.
     * 
     * @return void
     */
    public function test_get_single_character(): void
    {
        /** Run the DatabaseSeeder */
        $this->seed();

        /** Finds any character */
        $character = Character::first();
        $id = $character->id;

        /** Get the character */
        $response = $this->get("api/characters/$id");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'role',
                    'school',
                    'house',
                    'patronus'
                ]
            ]);
        $this->assertEquals($response['data'], $character->toArray());
    }

    /**
     * Tests the response if a character does not exist
     * on the database.
     * 
     * @return void
     */
    public function test_get_unexisting_character(): void
    {
        $uuid = Uuid::uuid4()->toString();

        /** Get the character */
        $response = $this->get("api/characters/$uuid");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'errors' => [
                    '*' => [
                        'status',
                        'detail'
                    ]
                ]
            ])
            ->assertJsonPath('errors.0.status', 404)
            ->assertJsonPath('errors.0.detail', 'The specified resource does not exist.');
    }

    /**
     * Tests if a character can be created.
     * 
     * @return void
     */
    public function test_create_character(): void
    {
        /** Attempts to create the character */
        $response = $this->post('/api/characters', [
            'name' => 'Harry Potter',
            'role' => 'student',
            'school' => 'Hogwarts School of Witchcraft and Wizardry',
            'house' => '1760529f-6d51-4cb1-bcb1-25087fce5bde',
            'patronus' => 'stag'
        ]);

        $id = $response['data']['id'];

        /** Attempts to retrieve the created character from database */
        $character = Character::find($id);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'role',
                    'school',
                    'house',
                    'patronus'
                ]
            ]);
        $this->assertEquals($response['data'], $character->toArray());
    }

    /**
     * Test for creating a character with
     * an invalid house.
     * 
     * @return void
     */
    public function test_create_invalid_house_character(): void
    {
        /** Attempts to create the character */
        $response = $this->post('/api/characters', [
            'name' => 'Harry Potter',
            'role' => 'student',
            'school' => 'Hogwarts School of Witchcraft and Wizardry',
            'house' => 'bubbles',
            'patronus' => 'stag'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'errors' => [
                    [
                        'status',
                        'source',
                        'detail'
                    ]
                ]
            ])
            ->assertJsonPath('errors.0.status', 422)
            ->assertJsonPath('errors.0.detail.0', 'The house must be a valid UUID.')
            ->assertJsonPath('errors.0.detail.1', 'The house does not exist.');
    }

    /**
     * Test for creating a character with
     * an unexisting house.
     * 
     * @return void
     */
    public function test_create_unexisting_house_character(): void
    {
        /** Attempts to create the character */
        $response = $this->post('/api/characters', [
            'name' => 'Harry Potter',
            'role' => 'student',
            'school' => 'Hogwarts School of Witchcraft and Wizardry',
            'house' => '56454c0b-05b8-48b5-bf69-14e1c2bc2f1a', // This does not exist on the Potter API
            'patronus' => 'stag'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'errors' => [
                    [
                        'status',
                        'source',
                        'detail'
                    ]
                ]
            ])
            ->assertJsonPath('errors.0.status', 422)
            ->assertJsonPath('errors.0.detail.0', 'The house does not exist.');
    }

    /**
     * Tests if a character can be updated.
     * 
     * @return void
     */
    public function test_update_character(): void
    {
        /** Run the DatabaseSeeder */
        $this->seed();

        /** Get the first character available in the database */
        $character = Character::first();
        $id = $character->id;

        /** Attempts to update the character */
        $response = $this->patch("/api/characters/$id", [
            'name' => 'Harry Potter',
            'role' => 'student',
            'school' => 'Hogwarts School of Witchcraft and Wizardry',
            'house' => '1760529f-6d51-4cb1-bcb1-25087fce5bde',
            'patronus' => 'stag'
        ]);

        $idUpdated = $response['data']['id'];

        /** Attempts to retrieve the updated character from database */
        $characterUpdated = Character::find($idUpdated);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'role',
                    'school',
                    'house',
                    'patronus'
                ]
            ]);
        $this->assertEquals($response['data'], $characterUpdated->toArray());
    }

    /**
     * Tests for updating a unexisting character.
     * 
     * @return void
     */
    public function test_update_unexisting_character(): void
    {
        $uuid = Uuid::uuid4()->toString();

        /** Attempts to update the character */
        $response = $this->patch("/api/characters/$uuid", [
            'name' => 'Harry Potter',
            'role' => 'student',
            'school' => 'Hogwarts School of Witchcraft and Wizardry',
            'house' => '1760529f-6d51-4cb1-bcb1-25087fce5bde',
            'patronus' => 'stag'
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'errors' => [
                    '*' => [
                        'status',
                        'detail'
                    ]
                ]
            ])
            ->assertJsonPath('errors.0.status', 404)
            ->assertJsonPath('errors.0.detail', 'The specified resource does not exist.');
    }

    /**
     * Test for updating a character with
     * an invalid house.
     * 
     * @return void
     */
    public function test_update_invalid_house_character(): void
    {
        /** Run the DatabaseSeeder */
        $this->seed();

        /** Get the first character available in the database */
        $character = Character::first();
        $id = $character->id;

        /** Attempts to update the character */
        $response = $this->patch("/api/characters/$id", [
            'name' => 'Harry Potter',
            'role' => 'student',
            'school' => 'Hogwarts School of Witchcraft and Wizardry',
            'house' => 'bubbles',
            'patronus' => 'stag'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'errors' => [
                    [
                        'status',
                        'source',
                        'detail'
                    ]
                ]
            ])
            ->assertJsonPath('errors.0.status', 422)
            ->assertJsonPath('errors.0.detail.0', 'The house must be a valid UUID.')
            ->assertJsonPath('errors.0.detail.1', 'The house does not exist.');
    }

    /**
     * Test for updating a character with
     * an unexisting house.
     * 
     * @return void
     */
    public function test_update_unexisting_house_character(): void
    {
        /** Run the DatabaseSeeder */
        $this->seed();

        /** Get the first character available in the database */
        $character = Character::first();
        $id = $character->id;

        /** Attempts to update the character */
        $response = $this->patch("/api/characters/$id", [
            'name' => 'Harry Potter',
            'role' => 'student',
            'school' => 'Hogwarts School of Witchcraft and Wizardry',
            'house' => '56454c0b-05b8-48b5-bf69-14e1c2bc2f1a', // This does not exist on the Potter API
            'patronus' => 'stag'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'errors' => [
                    [
                        'status',
                        'source',
                        'detail'
                    ]
                ]
            ])
            ->assertJsonPath('errors.0.status', 422)
            ->assertJsonPath('errors.0.detail.0', 'The house does not exist.');
    }

    /**
     * Test for deleting a character.
     * 
     * @return void
     */
    public function test_delete_character(): void
    {
        /** Run the DatabaseSeeder */
        $this->seed();

        /** Get the first character available in the database */
        $character = Character::first();
        $id = $character->id;

        /** Attempts to delete the character */
        $response = $this->delete("/api/characters/$id");

        /** If everything worked, the character is gone from the database */
        $character = Character::find($id);

        $response->assertStatus(204);
        $this->assertNull($character);
    }

    /**
     * Test for deleting an unexisting character.
     * 
     * @return void
     */
    public function test_delete_unexisting_character(): void
    {
        $uuid = Uuid::uuid4()->toString();

        /** Attempts to delete the character */
        $response = $this->delete("/api/characters/$uuid");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'errors' => [
                    '*' => [
                        'status',
                        'detail'
                    ]
                ]
            ])
            ->assertJsonPath('errors.0.status', 404)
            ->assertJsonPath('errors.0.detail', 'The specified resource does not exist.');
    }
}
