<?php

namespace Hackersir;

use Illuminate\Database\Eloquent\Model;

/**
 * Hackersir\Organizer
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $logo_url
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Hackersir\VoteEvent[] $voteEvents
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Organizer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Organizer whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Organizer whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Organizer whereLogoUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Organizer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Organizer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Organizer extends Model
{
    protected $table = 'organizers';
    protected $fillable = ['name', 'url', 'logo_url'];

    public function voteEvents()
    {
        return $this->hasMany(VoteEvent::class);
    }
}
