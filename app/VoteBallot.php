<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoteBallot extends Model
{
    protected $table = 'vote_ballots';
    protected $fillable = ['user_id', 'vote_selection_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function voteSelection()
    {
        return $this->belongsTo('App\VoteSelection');
    }
}
