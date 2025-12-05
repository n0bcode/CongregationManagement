<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormationDocument extends Model
{
    /** @use HasFactory<\Database\Factories\FormationDocumentFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'formation_event_id',
        'file_name',
        'file_path',
        'document_type',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the formation event that owns the document.
     */
    public function formationEvent(): BelongsTo
    {
        return $this->belongsTo(FormationEvent::class);
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
