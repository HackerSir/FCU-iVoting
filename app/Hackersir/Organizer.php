<?php

namespace Hackersir;

use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    protected $table = 'organizers';
    protected $fillable = ['name', 'url', 'logo_url'];

    public function voteEvents()
    {
        return $this->hasMany(VoteEvent::class);
    }
}
