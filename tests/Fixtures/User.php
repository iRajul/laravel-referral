<?php

namespace Jijunair\LaravelReferral\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Jijunair\LaravelReferral\Traits\Referrable;

class User extends Model
{
    use Referrable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
    ];
}
