<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use InvalidArgumentException;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'dob',
        'username',
        'email',
        'profile_pic_url',
        'bio',
        'status',
        'password',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'status' => 'string',
        ];
    }

    public function setStatusAttribute($value)
    {
        $validStatuses = ['active', 'inactive', 'pending'];
        if (in_array($value, $validStatuses)) {
            $this->attributes['status'] = $value;
        } else {
            throw new InvalidArgumentException("Invalid status value");
        }
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function reposts()
    {
        return $this->hasMany(Repost::class);
    }

    public function sentRequests()
    {
        return $this->hasMany(Friend::class, 'requester_id');
    }

    public function receivedRequests()
    {
        return $this->hasMany(Friend::class, 'accepter_id');
    }

    public function following()
    {
        return $this->sentRequests()->where('status', 'accepted')->get();
    }

    public function followers()
    {
        return $this->receivedRequests()->where('status', 'accepted')->get();
    }

    public function friends()
    {
        $sentRequests = $this->sentRequests()->where('status', 'accepted')->get();
        $receivedRequests = $this->receivedRequests()->where('status', 'accepted')->get();
        $friends = $sentRequests->merge($receivedRequests);

        return $friends;
    }
}
