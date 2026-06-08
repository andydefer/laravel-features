<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Comments\Records;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\LaravelFeatures\Comments\Enums\CommentStatus;

final class CommentFilterRecord extends AbstractRecord
{
    public function __construct(
        public readonly ?string $commenter_type = null,
        public readonly ?int $commenter_id = null,
        public readonly ?string $commentable_type = null,
        public readonly ?int $commentable_id = null,
        public readonly ?int $parent_id = null,
        public readonly ?CommentStatus $status = null,
        public readonly ?bool $only_published = null,
    ) {}
}
