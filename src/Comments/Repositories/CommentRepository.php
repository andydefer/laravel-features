<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Comments\Repositories;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\LaravelFeatures\Comments\Enums\CommentStatus;
use AndyDefer\LaravelFeatures\Comments\Models\Comment;
use AndyDefer\LaravelFeatures\Comments\Records\CommentFilterRecord;
use AndyDefer\LaravelFeatures\Comments\Records\CommentRecord;
use AndyDefer\Repository\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;

final class CommentRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(
            modelClass: Comment::class,
            recordClass: CommentRecord::class,
        );
    }

    protected function applyFilters(Builder $query, AbstractRecord $filters): void
    {
        if (! $filters instanceof CommentFilterRecord) {
            return;
        }

        if ($filters->commenter_type !== null) {
            $query->where('commenter_type', $filters->commenter_type);
        }

        if ($filters->commenter_id !== null) {
            $query->where('commenter_id', $filters->commenter_id);
        }

        if ($filters->commentable_type !== null) {
            $query->where('commentable_type', $filters->commentable_type);
        }

        if ($filters->commentable_id !== null) {
            $query->where('commentable_id', $filters->commentable_id);
        }

        if ($filters->parent_id !== null) {
            $query->where('parent_id', $filters->parent_id);
        }

        if ($filters->status !== null) {
            $query->where('status', $filters->status->value);
        }

        if ($filters->only_published === true) {
            $query->where('status', CommentStatus::PUBLISHED->value);
        }
    }
}
