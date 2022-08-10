<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['commentDateFormat'] = $this->options->commentDateFormat;
$GLOBALS['QQAvatar'] = $this->options->QQAvatar;
?>
<?php
function threadedComments($comments, $options) {
    $commentClass = '';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $commentClass .= ' comment-by-author';
        } else {
            $commentClass .= ' comment-by-user';
        }
    }

    $commentLevelClass = $comments->levels > 0 ? ' comment-child' : ' comment-parent';
    ?>

    <li id="li-<?php $comments->theId(); ?>" class="comment-body<?php
    if ($comments->levels > 0) {
        echo ' comment-child';
        $comments->levelsAlt(' comment-level-odd', ' comment-level-even');
    } else {
        echo ' comment-parent';
    }
    $comments->alt(' comment-odd', ' comment-even');
    echo $commentClass;
    ?>">
        <div id="<?php $comments->theId(); ?>" class="comment-box clearfix">
            <div class="comment-author clearfix">
                <?php
                if ($GLOBALS['QQAvatar'] == 'on' && isQQEmail($comments->mail)) {
                    QQAvatar($comments->mail, $comments->author, 40);
                }else {
                    $comments->gravatar('50', '');
                }
                if ($comments->type == 'pingback') {
                    echo '<div class="pingback avatar">引用</div>';
                }
                ?>
                <div class="comment-info float-left">
                    <b class="author"><?php $comments->author(); ?></b>
                    <?php if ($comments->authorId == $comments->ownerId): ?>
                        <span class="author badge badge-secondary">作者</span>
                    <?php endif; ?>
                    <?php if ($comments->status != 'approved'): ?>
                        <span class="author badge badge-secondary" title="您的评论目前只有您自己能看到，审核通过后才会公开显示。" data-toggle="tooltip" data-placement="top">评论审核中</span>
                    <?php endif; ?>
                    <a class="comment-time" href="<?php $comments->permalink(); ?>">
                        <?php echo dateFormat($comments->date->timeStamp, $GLOBALS['commentDateFormat']); ?>
                    </a>
                </div>
                <span class="comment-reply float-right">
                    <span data-id="<?php $comments->theId(); ?>">
                        <?php $comments->reply(); ?>
                    </span>
                </span>
            </div>
            <div class="comment-content" id="c-<?php $comments->theId(); ?>">
                <?php echo reply($comments->parent); ?>
                <?php $comments->content(); ?>
            </div>
        </div>
        <?php if ($comments->children) { ?>
            <div class="comment-children clearfix">
                <?php $comments->threadedComments($options); ?>
            </div>
        <?php } ?>
    </li>
<?php } ?>

<div id="comments" aria-label="评论区">
    <?php $this->comments()->to($comments); ?>
    <?php if ($this->options->commentInput == 'top') require_once 'comment-input.php'; ?>
    <?php if ($comments->have()): ?>
        <div class="comments-lists border-top">
            <h2><?php $this->commentsNum(_t('暂无评论'), _t('仅有一条评论'), _t('已有 %d 条评论')); ?></h2>

            <?php $comments->listComments(); ?>

            <nav aria-label="评论分页导航区" class="pagination-nav">
                <?php $comments->pageNav('<i class="icon-chevron-left"></i>', '<i class="icon-chevron-right"></i>', 1, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination justify-content-center ' , 'itemTag' => 'li',  'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => 'prev', 'nextClass' => 'next')); ?>
            </nav>
        </div>
    <?php endif; ?>
    <?php if ($this->options->commentInput == 'bottom' or $this->options->commentInput == null) require_once 'comment-input.php'; ?>
</div>
