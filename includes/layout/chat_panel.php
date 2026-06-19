<?php

declare(strict_types=1);

/** @var int $chatCourseId */
/** @var int $chatUserId */
/** @var array<int, array<string, mixed>> $chatMessages */
/** @var string $chatPostUrl */
/** @var string $chatApiUrl */

$chatMessages = $chatMessages ?? [];
$lastMsgId = 0;
foreach ($chatMessages as $cm) {
    $lastMsgId = max($lastMsgId, (int) $cm['id']);
}
mark_course_messages_read($chatUserId, $chatCourseId, $lastMsgId);
?>
<div class="chat-panel border-0 shadow-sm" data-course-id="<?= (int) $chatCourseId ?>" data-api-url="<?= e($chatApiUrl) ?>" data-last-id="<?= $lastMsgId ?>">
    <div class="chat-messages card border-0" id="chatMessagesBox">
        <div class="card-body p-3">
            <?php if (!$chatMessages): ?>
                <p class="text-muted small text-center mb-0 chat-empty-hint">هنوز پیامی نیست. گفتگو را شروع کنید.</p>
            <?php endif; ?>
            <?php foreach ($chatMessages as $m): ?>
                <?php $isMine = (int) $m['sender_id'] === $chatUserId; ?>
                <div class="chat-bubble-wrap <?= $isMine ? 'chat-mine' : 'chat-theirs' ?>" data-msg-id="<?= (int) $m['id'] ?>">
                    <div class="chat-meta small">
                        <?= e($m['sender_name']) ?>
                        <span class="text-muted"><?= e(format_datetime($m['created_at'])) ?></span>
                    </div>
                    <div class="chat-bubble"><?= nl2br(e($m['body'])) ?></div>
                    <?php if (!empty($m['file_path'])): ?>
                        <a href="<?= e(upload_url($m['file_path'])) ?>" class="chat-file-link small" target="_blank" rel="noopener">
                            <i class="bi bi-paperclip"></i> دانلود پیوست
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <form method="post" action="<?= e($chatPostUrl) ?>" enctype="multipart/form-data" class="chat-compose p-3 bg-white border-top">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="send_message">
        <textarea name="message_body" class="form-control mb-2" rows="2" placeholder="پیام خود را بنویسید..."></textarea>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <input type="file" name="message_file" class="form-control form-control-sm" style="max-width:220px" accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png">
            <button type="submit" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-send"></i> ارسال</button>
        </div>
    </form>
</div>
