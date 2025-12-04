<?php

namespace App\Models;

use App\Enums\DocumentCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'category',
        'folder_id',
        'community_id',
        'member_id',
        'uploaded_by',
    ];

    protected $casts = [
        'category' => DocumentCategory::class,
        'file_size' => 'integer',
    ];

    /**
     * Get the folder this document belongs to
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Get the community this document belongs to
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the member this document is related to
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who uploaded this document
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get file size in human-readable format
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Get temporary signed URL for secure download (expires in 1 hour)
     */
    public function getTemporaryUrl(int $expirationMinutes = 60): string
    {
        return URL::temporarySignedRoute(
            'documents.download',
            now()->addMinutes($expirationMinutes),
            ['document' => $this->id]
        );
    }

    /**
     * Get permanent download URL (requires authentication)
     */
    public function getDownloadUrl(): string
    {
        return route('documents.download', $this);
    }

    /**
     * Check if file exists in storage
     */
    public function fileExists(): bool
    {
        return Storage::exists($this->file_path);
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return Storage::delete($this->file_path);
        }

        return false;
    }

    /**
     * Scope to filter by category
     */
    public function scopeCategory($query, DocumentCategory|string $category)
    {
        if ($category instanceof DocumentCategory) {
            return $query->where('category', $category->value);
        }

        return $query->where('category', $category);
    }

    /**
     * Scope to filter by community
     */
    public function scopeForCommunity($query, ?int $communityId)
    {
        if ($communityId) {
            return $query->where('community_id', $communityId);
        }

        return $query;
    }

    /**
     * Scope to filter by member
     */
    public function scopeForMember($query, ?int $memberId)
    {
        if ($memberId) {
            return $query->where('member_id', $memberId);
        }

        return $query;
    }

    /**
     * Scope to filter by folder
     */
    public function scopeInFolder($query, ?int $folderId)
    {
        if ($folderId) {
            return $query->where('folder_id', $folderId);
        }

        return $query->whereNull('folder_id');
    }

    /**
     * Scope to search documents
     */
    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('file_name', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, ?string $startDate, ?string $endDate)
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Boot method to handle model events
     */
    protected static function booted(): void
    {
        // Delete file from storage when document is force deleted
        static::forceDeleted(function (Document $document) {
            $document->deleteFile();
        });
    }
}
