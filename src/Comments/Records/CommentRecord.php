<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Comments\Records;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Comments\Enums\CommentStatus;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;

final class CommentRecord extends AbstractRecord
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $commenter_type = null,
        public readonly ?int $commenter_id = null,
        public readonly ?string $commentable_type = null,
        public readonly ?int $commentable_id = null,
        public readonly ?string $content = null,
        public readonly ?int $parent_id = null,
        public readonly ?CommentStatus $status = null,
        public readonly ?StrictDataObject $metadata = null,
        public readonly ?DateTimeVO $created_at = null,
        public readonly ?DateTimeVO $updated_at = null,
        public readonly ?DateTimeVO $deleted_at = null,
    ) {}
}
