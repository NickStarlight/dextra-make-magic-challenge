<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * The Character Model.
 * 
 * This model interacts with the `character` table
 * that is used to store characters from the book/movie
 * series `Harry Potter`.
 *
 * @see https://laravel.com/docs/8.x/eloquent
 * @see https://github.com/dextra/challenges/blob/master/backend/MAKE-MAGIC-PT.md
 * @author Nickolas Gomes Moraes
 * @version 1.0
 * @license WTFPL
 */
class Character extends Model
{
    use HasFactory;

    /** 
     * Enables UUID primary keys on Eloquent.
     * 
     * We're doing this in order to keep consistency with
     * Potter API, since they are using a UUID's.
     * 
     * @see https://github.com/goldspecdigital/laravel-eloquent-uuid
     */
    use Uuid;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'role', 'school', 'house',  'patronus'];

    /**
     * The attributes that should be hidden for arrays.
     * 
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];
}
