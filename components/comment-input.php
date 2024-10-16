<?php if($this->allow('comment')): ?>
    <div id="<?php $this->respondId(); ?>" class="respond border-top">
        <div class="cancel-comment-reply">
            <?php $comments->cancelReply(); ?>
        </div>

        <h2 id="response"><?php _e('发表评论'); ?></h2>
        <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form">
            <div class="row">
                <!--评论内容输入-->
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 form-group">
                    <label for="textarea" class="required">
                        <?php _e('评论内容'); ?>
                        <span class="required">*</span>
                    </label>
                    <textarea name="text" id="textarea" class="textarea form-control" required placeholder="请在此处输入评论内容"><?php $this->remember('text'); ?></textarea>
                </div>
                <!--Emoji表情面板-->
                <?php if ($this->options->emojiPanel == 'on'): ?>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12 form-group">
                        <button aria-expanded="false" type="button" class="btn btn-sm" id="show-emoji-btn" data-url="<?php $this->options->themeUrl('emoji.php'); ?>">😀 Emoji表情</button>
                        <div id="emoji-panel" class="border shadow rounded" role="dialog" aria-label="Emoji表情面板（按 ESC 可关闭表情面板并转到评论内容输入框。）">
                            <div class="p-0 m-0 border-bottom">
                                <div id="emoji-classification" class="m-0 btn-group" role="group" aria-label="表情类型"">
                                    <button role="radio" aria-checked="true" aria-label="面部表情" title="面部表情" type="button" class="btn btn btn-sm selected" data-classification="smileys">😀</button>
                                    <button role="radio" aria-checked="false" aria-label="人物/手势" title="人物/手势" type="button" class="btn btn btn-sm" data-classification="character">👦</button>
                                    <button role="radio" aria-checked="false" aria-label="服装/配饰" title="服装/配饰" type="button" class="btn btn btn-sm" data-classification="clothing">👕</button>
                                    <button role="radio" aria-checked="false" aria-label="动物/自然" title="动物/自然" type="button" class="btn btn btn-sm" data-classification="animal">🐶</button>
                                    <button role="radio" aria-checked="false" aria-label="食物" title="食物" type="button" class="btn btn btn-sm" data-classification="food">🍏</button>
                                    <button role="radio" aria-checked="false" aria-label="运动" title="运动" type="button" class="btn btn btn-sm" data-classification="motion">⚽</button>
                                    <button role="radio" aria-checked="false" aria-label="旅行/地点" title="旅行/地点" type="button" class="btn btn-sm>" data-classification="tourism">🚚</button>
                                    <button role="radio" aria-checked="false" aria-label="物体" title="物体" type="button" class="btn btn-sm>" data-classification="objects">⌚</button>
                                    <button role="radio" aria-checked="false" aria-label="符号" title="符号" type="button" class="btn btn-sm>" data-classification="symbols">❤</button>
                                </div>
                            </div>
                            <h5 class="text-center py-2 m-0 border-bottom" id="emoji-title">表情类型</h5>
                            <div id="emoji-list" class="clearfix" role="list" aria-label="表情列表（按回车可以把表情添加到评论内容输入框）"></div>
                        </div>
                    </div>
                <?php  endif; ?>
                <?php if($this->user->hasLogin()): ?>
                    <div class="col-lg-12 comment-user">
                        <?php _e('登录身份: '); ?>
                        <a data-toggle="tooltip" data-placement="top" href="<?php $this->options->profileUrl(); ?>" title="当前登录身份：<?php $this->user->screenName(); ?>"><?php $this->user->screenName(); ?></a>.
                        <a data-toggle="tooltip" data-placement="top" href="<?php $this->options->logoutUrl(); ?>" title="退出"><?php _e('退出'); ?> &raquo;</a>
                    </div>
                <?php else: ?>
                    <!--姓名输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="author">
                            <?php _e('称呼'); ?>
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="author" id="author" class="text form-control" value="<?php $this->remember('author'); ?>" placeholder="请输入您的姓名或昵称" maxlength="20" required>
                    </div>
                    <!--Email输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="mail">
                            <?php _e('电子邮件地址'); ?>
                            <?php if ($this->options->commentsRequireMail): ?>
                                <span class="required">*</span>
                            <?php endif; ?>
                        </label>
                        <input type="email" name="mail" id="mail" class="text form-control" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail) echo 'required'; ?> placeholder="请输入您的电子邮件地址（不会公开）" maxlength="64">
                    </div>
                    <!--URL输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="url">
                            <?php _e('网站'); ?>
                            <?php if ($this->options->commentsRequireURL): ?>
                                <span class="required">*</span>
                            <?php endif; ?>
                        </label>
                        <input type="url" maxlength="64" name="url" id="url" class="text form-control" placeholder="<?php _e('请输入您的网站或博客地址'); ?>" value="<?php $this->remember('url'); ?>"<?php if ($this->options->commentsRequireURL) echo 'required'; ?>>
                    </div>
                <?php endif; ?>
                <!--提交按钮-->
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 form-group">
                    <button type="submit" class="submit btn"><?php _e('提交评论'); ?></button>
                </div>
            </div>
        </form>
    </div>
<?php else: ?>
    <h2 class="comments-off pt-4 border-top"><?php _e('评论已关闭'); ?></h2>
<?php endif; ?>