<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'original_name', 'filename', 'path', 'file_size', 'mime_type', 'owner_name', 'is_folder', 'parent_id'
    ];

    public function parent()
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Document::class, 'parent_id');
    }

    public function getFolderSize()
    {
        if (!$this->is_folder) {
            return $this->file_size ?? 0;
        }

        $size = 0;
        foreach ($this->children as $child) {
            $size += $child->getFolderSize();
        }
        return $size;
    }
}
