<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $table = "User";
    protected $primaryKey = "UID";
    public $timestamps = false;


    public function userstatus() {
        return $this->belongsTo(UserStatus::class, 'USID');
    }

    public function group() {
        return $this->belongsTo(Group::class, 'UGroup');
    }

    public function notes() {
        return $this->hasMany(Note::class, 'UID');
    }

    public function ownedgroups() {
        return $this->hasMany(Group::class, 'GROwnerID');
    }

    public function ownedgameorgs() {
        return $this->hasMany(GameOrg::class, 'GOOwnerID');
    }

    public function grouproles() {
        return $this->belongsToMany(Role::class, 'GroupAdmin', 'UID', 'RID')->withPivot('GRID');
    }

    public function gameorgroles() {
        return $this->belongsToMany(Role::class, 'GameOrgAdmin', 'UID', 'RID')->withPivot('GOID');
    }

    public function games() {
        return $this->belongsToMany(Game::class, 'GameUser', 'UID', 'GID');
    }

    public function gameusers() {
        return $this->hasMany(GameUser::class, 'UID');
    }

    public function sponsors() {
        return $this->belongsToMany(User::class, 'Sponsor', 'SSponsoredID', 'UID');
    }

    public function sponsoring() {
        return $this->belongsToMany(User::class, 'Sponsor', 'UID', 'SSponsoredID');
    }

    public function canSponsor() {
        if (config('goonauth.sponsor.allow') == true)
            return true;

        if ($this->grouproles()->count() !== 0)
            return true;
        if ($this->gameorgroles()->count() !== 0)
            return true;

        $max = config('goonauth.sponsor.max');
        if ($max !== null)
        {
            if ($this->sponsoring->count() <= $max)
                return true;
        }

        return false;
    }

    //-----------------------------------------------------------------------//

    public function getAuthIdentifierName()
    {
        return "UID";
    }

    public function getAuthIdentifier()
    {
        return $this->UID;
    }

    public function getAuthPassword()
    {
        return null;
    }

    public function getRememberTokenName()
    {
        return "URememberToken";
    }
}
