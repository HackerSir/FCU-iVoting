<?php

namespace Hackersir;

use Illuminate\Database\Eloquent\Model;

/**
 * Hackersir\VoteBallot
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $vote_selection_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Hackersir\User $user
 * @property-read \Hackersir\VoteSelection $voteSelection
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteBallot whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteBallot whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteBallot whereVoteSelectionId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteBallot whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\VoteBallot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class VoteBallot extends Model
{
    protected $table = 'vote_ballots';
    protected $fillable = ['user_id', 'vote_selection_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voteSelection()
    {
        return $this->belongsTo(VoteSelection::class);
    }
}
