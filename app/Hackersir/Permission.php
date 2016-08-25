<?php

namespace Hackersir;

use Zizaco\Entrust\EntrustPermission;

/**
 * Hackersir\Permission
 *
 * @property integer $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Hackersir\Role[] $roles
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Permission whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Permission whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Permission whereDisplayName($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Permission whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Permission extends EntrustPermission
{
}
