<?php if($this->allow('comment')): ?>
    <div id="<?php $this->respondId(); ?>" class="respond border-top">
        <div class="cancel-comment-reply">
            <?php $comments->cancelReply(); ?>
        </div>

        <h2 id="response"><?php echo $GLOBALS['t']['comment']['leaveAComment']; ?></h2>
        <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form">
            <div class="row">
                <!--评论内容输入-->
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 form-group">
                    <label for="textarea" class="required">
                        <?php echo $GLOBALS['t']['comment']['commentContent']; ?>
                        <span class="required">*</span>
                    </label>
                    <textarea name="text" id="textarea" class="textarea form-control" required placeholder="<?php echo $GLOBALS['t']['comment']['enterYourCommentHere']; ?>"><?php $this->remember('text'); ?></textarea>
                </div>
                <!--Emoji表情面板和私密评论复选框区域-->
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 form-group">
                    <div id="emoji-btn-and-hide-comment">
                        <!--emoji面板开关-->
                        <?php if ($this->options->emojiPanel == 'on'): ?>
                            <button aria-expanded="false" type="button" class="btn btn-sm" id="show-emoji-btn" data-url="<?php $this->options->themeUrl('emoji.php'); ?>">😀 <?php echo $GLOBALS['t']['emoji']['emoji']; ?></button>
                        <?php  endif; ?>
                        <!--私密评论选择-->
                        <div class="form-check">
                            <input data-toggle="tooltip" data-placement="top" title="<?php echo $GLOBALS['t']['comment']['secretCommentDescription']; ?>" type="checkbox" class="form-check-input" id="hide-comment">
                            <label class="form-check-label" for="hide-comment"><?php echo $GLOBALS['t']['comment']['secretComment']; ?></label>
                        </div>
                    </div>
                    <!--emoji面板-->
                    <?php if ($this->options->emojiPanel == 'on'): ?>
                        <div id="emoji-panel" class="border shadow rounded" role="dialog" aria-label="<?php echo $GLOBALS['t']['emoji']['emojiPanel']; ?>">
                            <div class="p-0 m-0 border-bottom">
                                <div id="emoji-classification" class="m-0 btn-group" role="group" aria-label="<?php echo $GLOBALS['t']['emoji']['emojiCategories']; ?>"">
                                    <button role="radio" aria-checked="true" aria-label="<?php echo $GLOBALS['t']['emoji']['smileys']; ?>" title="<?php echo $GLOBALS['t']['emoji']['smileys']; ?>" type="button" class="btn btn btn-sm selected" data-classification="smileys">😀</button>
                                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['peopleAndGestures']; ?>" title="<?php echo $GLOBALS['t']['emoji']['peopleAndGestures']; ?>" type="button" class="btn btn btn-sm" data-classification="character">👦</button>
                                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['clothingAndAccessories']; ?>" title="<?php echo $GLOBALS['t']['emoji']['clothingAndAccessories']; ?>" type="button" class="btn btn btn-sm" data-classification="clothing">👕</button>
                                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['animalsAndNature']; ?>" title="<?php echo $GLOBALS['t']['emoji']['animalsAndNature']; ?>" type="button" class="btn btn btn-sm" data-classification="animal">🐶</button>
                                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['food']; ?>" title="<?php echo $GLOBALS['t']['emoji']['food']; ?>" type="button" class="btn btn btn-sm" data-classification="food">🍏</button>
                                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['activity']; ?>" title="<?php echo $GLOBALS['t']['emoji']['activity']; ?>" type="button" class="btn btn btn-sm" data-classification="motion">⚽</button>
                                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['travelAndPlaces']; ?>" title="<?php echo $GLOBALS['t']['emoji']['travelAndPlaces']; ?>" type="button" class="btn btn-sm>" data-classification="tourism">🚚</button>
                                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['objects']; ?>" title="<?php echo $GLOBALS['t']['emoji']['objects']; ?>" type="button" class="btn btn-sm>" data-classification="objects">⌚</button>
                                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['symbols']; ?>" title="<?php echo $GLOBALS['t']['emoji']['symbols']; ?>" type="button" class="btn btn-sm>" data-classification="symbols">❤</button>
                                </div>
                            </div>
                            <h5 class="text-center py-2 m-0 border-bottom" id="emoji-title"><?php echo $GLOBALS['t']['emoji']['emojiCategories']; ?></h5>
                            <div id="emoji-list" class="clearfix" role="list" aria-label="<?php echo $GLOBALS['t']['emoji']['emojiList']; ?> <?php echo $GLOBALS['t']['emoji']['pressEnterToAddTheEmojiToTheCommentInputField']; ?>"></div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if($this->user->hasLogin()): ?>
                    <div class="col-lg-12 comment-user">
                        <?php echo $GLOBALS['t']['comment']['loggedInAs']; ?>
                        <a data-toggle="tooltip" data-placement="top" href="<?php $this->options->profileUrl(); ?>" title="<?php echo $GLOBALS['t']['comment']['loggedInAs']; ?> <?php $this->user->screenName(); ?>"><?php $this->user->screenName(); ?></a>.
                        <a data-toggle="tooltip" data-placement="top" href="<?php $this->options->logoutUrl(); ?>"><?php echo $GLOBALS['t']['sidebar']['logout']; ?> &raquo;</a>
                    </div>
                <?php else: ?>
                    <!--姓名输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="author">
                            <?php echo $GLOBALS['t']['comment']['name']; ?>
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="author" id="author" class="text form-control" value="<?php $this->remember('author'); ?>" placeholder="<?php echo $GLOBALS['t']['comment']['enterYourNameOrNickname']; ?>" maxlength="20" required>
                    </div>
                    <!--Email输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="mail">
                            <?php echo $GLOBALS['t']['comment']['emailAddress']; ?>
                            <?php if ($this->options->commentsRequireMail): ?>
                                <span class="required">*</span>
                            <?php endif; ?>
                        </label>
                        <input type="email" name="mail" id="mail" class="text form-control" value="<?php $this->remember('mail'); ?>" <?php if ($this->options->commentsRequireMail) echo 'required'; ?> placeholder="<?php echo $GLOBALS['t']['comment']['enterYourEmailAddress']; ?>" maxlength="64">
                    </div>
                    <!--URL输入-->
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                        <label for="url">
                            <?php echo $GLOBALS['t']['comment']['website']; ?>
                            <?php if ($this->options->commentsRequireURL): ?>
                                <span class="required">*</span>
                            <?php endif; ?>
                        </label>
                        <input type="url" maxlength="64" name="url" id="url" class="text form-control" placeholder="<?php echo $GLOBALS['t']['comment']['enterYourWebsiteOrBlogURL']; ?>" value="<?php $this->remember('url'); ?>" <?php if ($this->options->commentsRequireURL) echo 'required'; ?>>
                    </div>
                <?php endif; ?>
                <!--提交按钮-->
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 form-group">
                    <button type="submit" class="submit btn"><?php echo $GLOBALS['t']['comment']['submitComment']; ?></button>
                </div>
            </div>
        </form>
    </div>
<?php else: ?>
    <h2 class="comments-off pt-4 border-top"><?php _e('评论已关闭'); ?></h2>
<?php endif; ?>
