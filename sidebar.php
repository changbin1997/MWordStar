<div class="col-md-12 col-lg-4 col-sm-12 sidebar">
    <!--社交小工具-->
    <?php if ($this->options->sidebarBlock && in_array('ShowSocialInfo', $this->options->sidebarBlock) && $this->options->socialInfo): ?>
    <section aria-label="社交信息">
        <h4>社交信息</h4>
        <div class="social clearfix">
            <?php socialInfo($this->options->socialInfo); ?>
        </div>
    </section>
    <?php endif; ?>
    <!--最新文章-->
    <?php if (!$this->is('page', 'archives')): ?>
        <?php if ($this->options->sidebarBlock && in_array('ShowRecentPosts', $this->options->sidebarBlock)): ?>
            <section aria-label="近期文章" class="latest-articles">
                <h4>近期文章</h4>
                <ul>
                    <?php $latestArticles = $this->widget('Widget_Contents_Post_Recent'); ?>
                    <?php while ($latestArticles->next()): ?>
                    <li>
                        <a href="<?php $latestArticles->permalink(); ?>">
                            <?php if ($latestArticles->fields->thumb): ?>
                                <div class="article-img" style="background-image: url(<?php $latestArticles->fields->thumb(); ?>);" aria-label="<?php $latestArticles->title(); ?>的头图"></div>
                            <?php endif; ?>
                            <p><?php $latestArticles->title(); ?></p>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </section>
        <?php endif; ?>
    <?php endif; ?>
    <!--最新回复-->
    <?php if ($this->options->sidebarBlock && in_array('ShowRecentComments', $this->options->sidebarBlock)): ?>
        <section aria-label="最新回复">
            <h4>最新回复</h4>
            <ul class="list-unstyled">
                <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
                <?php while($comments->next()): ?>
                    <li class="media">
                        <?php $comments->gravatar('40', ''); ?>
                        <div class="media-body ml-2">
                            <a href="<?php $comments->permalink(); ?>"><?php $comments->author(false); ?></a>
                            <div><?php $comments->excerpt(20, '[...]'); ?></div>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    <?php endif; ?>
    <!--文章分类-->
    <?php if ($this->options->sidebarBlock && in_array('ShowCategory', $this->options->sidebarBlock)): ?>
        <section aria-label="文章分类">
            <h4>文章分类</h4>
            <ul>
                <?php $this->widget('Widget_Metas_Category_List')->parse('<li><a href="{permalink}" title="{description}">{name}</a> ({count})</li>'); ?>
            </ul>
        </section>
    <?php endif; ?>
    <!--标签云-->
    <?php if ($this->options->sidebarBlock && in_array('ShowTag', $this->options->sidebarBlock)): ?>
        <section aria-label="标签云">
            <h4>标签云</h4>
            <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&ignoreZeroCount=1&desc=0&limit=50')->to($tags); ?>
            <?php if($tags->have()): ?>
            <div class="ltags-list">
                <?php while ($tags->next()): ?>
                    <a href="<?php $tags->permalink(); ?>" rel="tag" class="size-<?php $tags->split(5, 10, 20, 30); ?>" title="<?php $tags->count(); ?> 篇文章"><?php $tags->name(); ?></a>
                <?php endwhile; ?>
                <?php else: ?>
                    <li><?php _e('没有任何标签'); ?></li>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
    <!--文章归档-->
    <?php if ($this->options->sidebarBlock && in_array('ShowArchive', $this->options->sidebarBlock)): ?>
        <section aria-label="文章归档">
            <h4>文章归档</h4>
            <ul>
                <?php $this->widget('Widget_Contents_Post_Date', 'type=month&format=Y年m月')->parse('<li><a href="{permalink}" title="{count}篇文章">{date}</a> ({count})</li>');
                ?>
            </ul>
        </section>
    <?php endif; ?>
    <!-- 其它功能-->
    <?php if ($this->options->sidebarBlock && in_array('ShowOther', $this->options->sidebarBlock)): ?>
        <section aria-label="其它功能">
            <h4>其它功能</h4>
            <ul>
                <?php if (!in_array('HideLoginLink', $this->options->sidebarBlock)): ?>
                    <?php if($this->user->hasLogin()): ?>
                        <li class="last"><a href="<?php $this->options->adminUrl(); ?>"><?php _e('进入后台'); ?> (<?php $this->user->screenName(); ?>)</a></li>
                        <li><a href="<?php $this->options->logoutUrl(); ?>"><?php _e('退出'); ?></a></li>
                    <?php else: ?>
                        <li class="last"><a href="<?php $this->options->adminUrl('login.php'); ?>"><?php _e('登录'); ?></a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <li><a target="_blank" href="<?php $this->options->feedUrl(); ?>"><?php _e('文章 RSS'); ?></a></li>
                <li><a target="_blank" href="<?php $this->options->commentsFeedUrl(); ?>"><?php _e('评论 RSS'); ?></a></li>
            </ul>
        </section>
    <?php endif; ?>
    <!--友情链接-->
    <?php if ($this->options->links or $this->options->homeLinks && $this->is('index')): ?>
        <section aria-label="友情链接">
            <h4>友情链接</h4>
            <ul>
                <?php if ($this->options->homeLinks && $this->is('index')): ?>
                    <?php $homeLinks = json_decode($this->options->homeLinks); ?>
                    <?php foreach ($homeLinks as $val): ?>
                        <li><a href="<?php echo $val->url; ?>" title="<?php echo isset($val->title)?$val->title:'暂无简介'; ?>" target="_blank"><?php echo $val->name; ?></a></li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($this->options->links): ?>
                    <?php $links = json_decode($this->options->links); ?>
                    <?php foreach ($links as $val): ?>
                        <li><a href="<?php echo $val->url; ?>" title="<?php echo isset($val->title)?$val->title:'暂无简介'; ?>" target="_blank"><?php echo $val->name; ?></a></li>
                    <?php endforeach;; ?>
                <?php endif; ?>
            </ul>
        </section>
    <?php endif; ?>
</div>