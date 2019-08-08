<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php function threadedComments($comments, $options) {
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
                <?php $comments->gravatar('50', ''); ?>
                <div class="comment-info float-left">
                    <b><?php $comments->author(); ?></b>
                    <?php if ($comments->authorId == $comments->ownerId): ?>
                    <span class="author badge badge-secondary">作者</span>
                    <?php endif; ?>
                    <a class="comment-time" href="<?php $comments->permalink(); ?>"><?php $comments->date('Y年m月d日 H:i'); ?></a>
                </div>
                <span class="comment-reply float-right"><?php $comments->reply(); ?></span>
            </div>
            <div class="comment-meta"></div>
            <?php $comments->content(); ?>
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
    <?php if ($comments->have()): ?>
        <div class="comments-lists">
            <h2><?php $this->commentsNum(_t('暂无评论'), _t('仅有一条评论'), _t('已有 %d 条评论')); ?></h2>

            <?php $comments->listComments(); ?>

            <?php $comments->pageNav('&laquo; 前一页', '后一页 &raquo;'); ?>
        </div>
    <?php endif; ?>

    <?php if($this->allow('comment')): ?>
    <div id="<?php $this->respondId(); ?>" class="respond">
        <div class="cancel-comment-reply">
        <?php $comments->cancelReply(); ?>
        </div>
    
    	<h2 id="response"><?php _e('发表评论'); ?></h2>
    	<form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form">
            <div class="row">
                <!--评论内容输入-->
                <div class="col-lg-12 form-group">
                    <label for="textarea" class="required"><?php _e('评论内容'); ?></label>
                    <textarea name="text" id="textarea" class="textarea form-control" required placeholder="请在此处输入评论内容"><?php $this->remember('text'); ?></textarea>
                </div>
                <?php if($this->user->hasLogin()): ?>
                <div class="col-lg-12 comment-user">
                    <?php _e('登录身份: '); ?><a href="<?php $this->options->profileUrl(); ?>" title="当前登录身份：<?php $this->user->screenName(); ?>"><?php $this->user->screenName(); ?></a>. <a href="<?php $this->options->logoutUrl(); ?>" title="退出"><?php _e('退出'); ?> &raquo;</a>
                </div>
                <?php else: ?>
                    <!--姓名输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="author" class="required"><?php _e('姓名'); ?></label>
                        <input type="text" name="author" id="author" class="text form-control" value="<?php $this->remember('author'); ?>" required="required" placeholder="请输入您的姓名或昵称" maxlength="20">
                    </div>
                    <!--Email输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="mail"<?php if ($this->options->commentsRequireMail): ?> class="required"<?php endif; ?>><?php _e('电子邮件地址'); ?></label>
                        <input type="email" name="mail" id="mail" class="text form-control" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required="required" <?php endif; ?> placeholder="请输入您的电子邮件地址" maxlength="64">
                    </div>
                    <!--URL输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="url"<?php if ($this->options->commentsRequireURL): ?> class="required"<?php endif; ?>><?php _e('网站'); ?></label>
                        <input type="url" maxlength="64" name="url" id="url" class="text form-control" placeholder="<?php _e('请输入您的网站或博客地址'); ?>" value="<?php $this->remember('url'); ?>"<?php if ($this->options->commentsRequireURL): ?> required<?php endif; ?>>
                    </div>
                <?php endif; ?>
                <!--提交按钮-->
                <div class="col-lg-12 form-group">
                    <button type="submit" class="submit btn"><?php _e('提交评论'); ?></button>
                </div>
            </div>
    	</form>
    </div>
    <?php else: ?>
    <h2 class="comments-off"><?php _e('评论已关闭'); ?></h2>
    <?php endif; ?>
</div>
