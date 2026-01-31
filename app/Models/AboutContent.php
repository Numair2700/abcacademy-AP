<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutContent extends Model
{
    use HasFactory;

    protected $table = 'about_content';

    protected $fillable = [
        'section',
        'title',
        'content',
        'image',
        'order',
        'published',
    ];

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
