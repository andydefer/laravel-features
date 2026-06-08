<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Tests\Integration\Comments\Services;

use AndyDefer\LaravelFeatures\Comments\Models\Comment;
use AndyDefer\LaravelFeatures\Comments\Repositories\CommentRepository;
use AndyDefer\LaravelFeatures\Comments\Services\CommentService;
use AndyDefer\LaravelFeatures\Tests\Fixtures\Models\TestPost;
use AndyDefer\LaravelFeatures\Tests\Fixtures\Models\TestUser;
use AndyDefer\LaravelFeatures\Tests\IntegrationTestCase;
use RuntimeException;

final class CommentServiceIntegrationTest extends IntegrationTestCase
{
    private CommentService $commentService;

    private TestUser $user;

    private TestPost $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commentService = new CommentService(
            new CommentRepository
        );

        $this->user = TestUser::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->post = TestPost::create([
            'user_id' => $this->user->id,
            'title' => 'Test Post',
            'body' => 'Test content',
        ]);
    }

    public function test_add_creates_comment(): void
    {
        $comment = $this->commentService->add($this->user, $this->post, 'Great post!');

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertSame('Great post!', $comment->content);
        $this->assertSame($this->user->id, $comment->commenter_id);
        $this->assertSame($this->post->id, $comment->commentable_id);
    }

    public function test_add_with_parent_creates_reply(): void
    {
        $parent = $this->commentService->add($this->user, $this->post, 'Parent comment');
        $reply = $this->commentService->add($this->user, $this->post, 'Reply comment', $parent->id);

        $this->assertSame($parent->id, $reply->parent_id);
    }

    public function test_update_modifies_comment_content(): void
    {
        $comment = $this->commentService->add($this->user, $this->post, 'Original content');

        $updated = $this->commentService->update($comment->id, 'Updated content');

        $this->assertSame('Updated content', $updated->content);
    }

    public function test_update_throws_exception_when_comment_not_found(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Comment 999 not found');

        $this->commentService->update(999, 'New content');
    }

    public function test_delete_removes_comment(): void
    {
        $comment = $this->commentService->add($this->user, $this->post, 'Comment to delete');

        $this->assertNotNull($this->commentService->find($comment->id));

        $this->commentService->delete($comment->id);

        $this->assertNull($this->commentService->find($comment->id));
    }

    public function test_delete_throws_exception_when_comment_not_found(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Comment 999 not found');

        $this->commentService->delete(999);
    }

    public function test_hide_changes_status_to_hidden(): void
    {
        $comment = $this->commentService->add($this->user, $this->post, 'Comment to hide');

        $hidden = $this->commentService->hide($comment->id);

        $this->assertTrue($hidden->status->isHidden());
    }

    public function test_publish_changes_status_to_published(): void
    {
        $comment = $this->commentService->add($this->user, $this->post, 'Comment to publish');
        $this->commentService->hide($comment->id);

        $published = $this->commentService->publish($comment->id);

        $this->assertTrue($published->status->isPublished());
    }

    public function test_flag_changes_status_to_flagged(): void
    {
        $comment = $this->commentService->add($this->user, $this->post, 'Comment to flag');

        $flagged = $this->commentService->flag($comment->id);

        $this->assertTrue($flagged->status->isFlagged());
    }

    public function test_get_returns_all_comments_for_commentable(): void
    {
        $this->commentService->add($this->user, $this->post, 'First comment');
        $this->commentService->add($this->user, $this->post, 'Second comment');

        $comments = $this->commentService->get($this->post);

        $this->assertCount(2, $comments);
    }

    public function test_get_returns_only_published_comments_when_filtered(): void
    {
        $comment1 = $this->commentService->add($this->user, $this->post, 'Published comment');
        $comment2 = $this->commentService->add($this->user, $this->post, 'Hidden comment');
        $this->commentService->hide($comment2->id);

        $comments = $this->commentService->get($this->post, true);

        $this->assertCount(1, $comments);
        $this->assertSame($comment1->id, $comments->first()->id);
    }

    public function test_get_replies_returns_replies_for_parent(): void
    {
        $parent = $this->commentService->add($this->user, $this->post, 'Parent');
        $reply1 = $this->commentService->add($this->user, $this->post, 'Reply 1', $parent->id);
        $reply2 = $this->commentService->add($this->user, $this->post, 'Reply 2', $parent->id);

        $replies = $this->commentService->getReplies($parent->id);

        $this->assertCount(2, $replies);
    }

    public function test_find_returns_comment_by_id(): void
    {
        $comment = $this->commentService->add($this->user, $this->post, 'Find me');

        $found = $this->commentService->find($comment->id);

        $this->assertNotNull($found);
        $this->assertSame($comment->id, $found->id);
    }

    public function test_find_returns_null_when_not_found(): void
    {
        $found = $this->commentService->find(999);

        $this->assertNull($found);
    }

    public function test_get_by_commenter_returns_all_comments_from_user(): void
    {
        $post2 = TestPost::create([
            'user_id' => $this->user->id,
            'title' => 'Second Post',
            'body' => 'Another content',
        ]);

        $this->commentService->add($this->user, $this->post, 'Comment 1');
        $this->commentService->add($this->user, $post2, 'Comment 2');

        $comments = $this->commentService->getByCommenter($this->user);

        $this->assertCount(2, $comments);
    }

    public function test_count_returns_number_of_comments(): void
    {
        $this->commentService->add($this->user, $this->post, 'Comment 1');
        $this->commentService->add($this->user, $this->post, 'Comment 2');

        $count = $this->commentService->count($this->post);

        $this->assertSame(2, $count);
    }

    public function test_count_returns_only_published_when_filtered(): void
    {
        $comment1 = $this->commentService->add($this->user, $this->post, 'Published');
        $comment2 = $this->commentService->add($this->user, $this->post, 'Hidden');
        $this->commentService->hide($comment2->id);

        $count = $this->commentService->count($this->post, true);

        $this->assertSame(1, $count);
    }

    public function test_count_flagged_returns_number_of_flagged_comments(): void
    {
        $comment1 = $this->commentService->add($this->user, $this->post, 'Comment 1');
        $comment2 = $this->commentService->add($this->user, $this->post, 'Comment 2');

        $this->commentService->flag($comment1->id);

        $count = $this->commentService->countFlagged();

        $this->assertSame(1, $count);
    }

    public function test_count_hidden_returns_number_of_hidden_comments(): void
    {
        $comment1 = $this->commentService->add($this->user, $this->post, 'Comment 1');
        $comment2 = $this->commentService->add($this->user, $this->post, 'Comment 2');

        $this->commentService->hide($comment1->id);

        $count = $this->commentService->countHidden();

        $this->assertSame(1, $count);
    }

    public function test_count_published_returns_number_of_published_comments(): void
    {
        $this->commentService->add($this->user, $this->post, 'Comment 1');
        $this->commentService->add($this->user, $this->post, 'Comment 2');

        $count = $this->commentService->countPublished();

        $this->assertSame(2, $count);
    }
}
