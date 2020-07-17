<?php
$color = color($this->options->color);
$rounded = $this->options->rounded == 'rightAngle'?'rounded-0':'';  //  获取元素风格设置
?>
<?php if($this->allow('comment')): ?>
    <div id="<?php $this->respondId(); ?>" class="respond border-top">
        <div class="cancel-comment-reply">
            <?php $comments->cancelReply(); ?>
        </div>

        <h2 id="response"><?php _e('發表評論'); ?></h2>
        <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form">
            <div class="row">
                <!--评论内容输入-->
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 form-group">
                    <label for="textarea" class="required"><?php _e('評論內容'); ?></label>
                    <textarea name="text" id="textarea" class="textarea form-control <?php echo $rounded; ?>" required placeholder="請在此處輸入評論內容"><?php $this->remember('text'); ?></textarea>
                </div>
                <!--Emoji表情面板-->
                <?php if ($this->options->emojiPanel == 'on'): ?>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12 form-group">
                        <button type="button" class="btn btn btn-sm <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" data-target="#emoji-box" data-toggle="collapse" aria-expanded="false" aria-controls="emoji-box" id="show-emoji" url="<?php $this->options->themeUrl('emoji.php'); ?>">
                            <span>😀</span>
                            <span>Emoji表情</span>
                        </button>
                        <div id="emoji-box" class="collapse" aria-label="表情面板">
                            <div class="mt-2 mb-2 border">
                                <div class="emoji-classification border-bottom" aria-label="表情類型" role="group">
                                    <button role="radio" aria-checked="true" aria-label="面部表情" title="面部表情" type="button" class="btn btn btn-sm <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" classification="smileys">😀</button>
                                    <button role="radio" aria-checked="false" aria-label="人物/手勢" title="人物/手勢" type="button" class="btn btn btn-sm <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" classification="character">👦</button>
                                    <button role="radio" aria-checked="false" aria-label="服裝/裝飾" title="服裝/裝飾" type="button" class="btn btn btn-sm <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" classification="clothing">👕</button>
                                    <button role="radio" aria-checked="false" aria-label="動物/自然" title="動物/自然" type="button" class="btn btn btn-sm <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" classification="animal">🐶</button>
                                    <button role="radio" aria-checked="false" aria-label="食物" title="食物" type="button" class="btn btn btn-sm <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" classification="food">🍏</button>
                                    <button role="radio" aria-checked="false" aria-label="運動" title="運動" type="button" class="btn btn btn-sm <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" classification="motion">⚽</button>
                                    <button role="radio" aria-checked="false" aria-label="旅行/地點" title="旅行/地點" type="button" class="btn btn-sm <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" classification="tourism">🚚</button>
                                    <button role="radio" aria-checked="false" aria-label="物體" title="物體" type="button" class="btn btn-sm <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" classification="objects">⌚</button>
                                    <button role="radio" aria-checked="false" aria-label="符號" title="符號" type="button" class="btn btn-sm <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" classification="symbols">❤</button>
                                </div>
                                <div class="emoji-select ml-2 mr-2 clearfix" aria-label="表情選擇" role="list">
                                    <div class="d-flex justify-content-center text-info m-3">
                                        <div class="spinner-border" role="status">
                                            <span class="sr-only">正在加載 Emoji</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php  endif; ?>
                <?php if($this->user->hasLogin()): ?>
                    <div class="col-lg-12 comment-user">
                        <?php _e('登錄身份: '); ?>
                        <a data-toggle="tooltip" data-placement="top" class="<?php echo $color['link']; ?>" href="<?php $this->options->profileUrl(); ?>" title="當前登錄身份：<?php $this->user->screenName(); ?>"><?php $this->user->screenName(); ?></a>.
                        <a data-toggle="tooltip" data-placement="top" class="<?php echo $color['link']; ?>" href="<?php $this->options->logoutUrl(); ?>" title="退出"><?php _e('退出'); ?> &raquo;</a>
                    </div>
                <?php else: ?>
                    <!--姓名输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="author" class="required"><?php _e('姓名'); ?></label>
                        <input type="text" name="author" id="author" class="text form-control <?php echo $rounded; ?>" value="<?php $this->remember('author'); ?>" required="required" placeholder="請輸入您的姓名或暱稱" maxlength="20">
                    </div>
                    <!--Email输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="mail"<?php if ($this->options->commentsRequireMail): ?> class="required"<?php endif; ?>><?php _e('電子郵件地址'); ?></label>
                        <input type="email" name="mail" id="mail" class="text form-control <?php echo $rounded; ?>" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required="required" <?php endif; ?> placeholder="請輸入您的電子郵件地址" maxlength="64">
                    </div>
                    <!--URL输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="url"<?php if ($this->options->commentsRequireURL): ?> class="required"<?php endif; ?>><?php _e('網站'); ?></label>
                        <input type="url" maxlength="64" name="url" id="url" class="text form-control <?php echo $rounded; ?>" placeholder="<?php _e('請輸入您的網站或博客地址'); ?>" value="<?php $this->remember('url'); ?>"<?php if ($this->options->commentsRequireURL): ?> required<?php endif; ?>>
                    </div>
                <?php endif; ?>
                <!--提交按钮-->
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 form-group">
                    <button type="submit" class="submit btn <?php echo $color['btn']; ?> <?php echo $rounded; ?>"><?php _e('提交評論'); ?></button>
                </div>
            </div>
        </form>
    </div>
<?php else: ?>
    <h2 class="comments-off pt-4 border-top"><?php _e('評論已關閉'); ?></h2>
<?php endif; ?>
