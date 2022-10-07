<?php

// 读取侧边栏组件
$components = $GLOBALS['page'] == 'post'?$this->options->postPageSidebarComponent:$this->options->sidebarComponent;
//  如果侧边栏组件为空就使用默认设置
if ($components == null or $components == '') {
    $components = '博客信息,日历,搜索,最新文章,最新回复,文章分类,标签云,文章归档,其它功能,友情链接';
}
$components = str_replace(' ', '', $components);  //  去除空格
$components = explode(',', $components);
?>

<div class="col-md-12 col-lg-4 col-sm-12 sidebar">
    <?php foreach ($components as $component): ?>
        <?php if ($component == '博客信息'): ?>
            <section class="mwordstar-block">
                <h4>博客信息</h4>
                <div class="personal-information pt-2">
                    <?php if (!$this->options->nickname or !$this->options->birthday or !$this->options->avatarUrl) $userInfo = getAdminInfo(); ?>
                    <div class="user">
                        <img src="<?php $this->options->avatarUrl?$this->options->avatarUrl():gravatarUrl($userInfo['mail'], 72); ?>" alt="<?php echo $this->options->nickname?$this->options->nickname . '的头像':$this->options->title . '的头像'; ?>" class="rounded-circle avatar">
                        <div class="p-2">
                            <a class="user-name mt-2" target="_blank" href="<?php echo $this->options->nicknameUrl?$this->options->nicknameUrl:$this->options->siteUrl; ?>"><?php echo $this->options->nickname?$this->options->nickname:$userInfo['screenName']; ?></a>
                            <p class="introduction mt-1"><?php echo $this->options->Introduction?$this->options->Introduction:$this->options->description; ?></p>
                        </div>
                    </div>
                    <div class="website clearfix border-top">
                        <?php Typecho_Widget::widget('Widget_Stat')->to($quantity); ?>
                        <div class="info float-left border-right">
                            <p class="quantity"><?php $quantity->publishedPostsNum(); ?></p>
                            文章数
                        </div>
                        <div class="info float-left border-right">
                            <p class="quantity"><?php $quantity->publishedCommentsNum(); ?></p>
                            评论数
                        </div>
                        <div class="info float-left">
                            <p class="quantity"><?php echo $this->options->birthday?round((time() - strtotime($this->options->birthday)) / 86400, 0) . '天':round((time() - $userInfo['created']) / 86400, 0) . '天'; ?></p>
                            运行天数
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($component == '搜索'): ?>
            <section class="search mwordstar-block">
                <h4>搜索</h4>
                <div class="tag-list pt-2">
                    <form action="<?php $this->options->siteUrl(); ?>" method="post" role="form">
                        <div class="input-group">
                            <input type="search" class="form-control form-control-md" placeholder="搜索" aria-label="搜索" aria-describedby="button-addon2" required="required" name="s">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-md" aria-label="搜索" title="搜索" data-toggle="tooltip" data-placement="top">
                                    <span class="icon-search"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($component == '日历'): ?>
            <section class="calendar mwordstar-block">
                <?php $date = getMonth(); ?>
                <h4><?php echo $date[0] . '年' . $date[1] . '月'; ?></h4>
                <div class="tag-list pt-2">
                    <?php $calendar = calendar($date[0] . '-' . $date[1] . '-01', $this->options->siteUrl, $this->options->rewrite); ?>
                    <?php echo $calendar['calendar']; ?>
                    <nav class="pt-2 clearfix" aria-label="上个月及下个月">
                        <?php if ($calendar['previous']): ?>
                            <a class="p-0 float-left" href="<?php echo $calendar['previousUrl']; ?>"><?php echo date('Y年m月', strtotime($calendar['previous'] . '01')); ?></a>
                        <?php endif; ?>
                        <?php if ($calendar['next']): ?>
                            <a class="p-0 float-right"  href="<?php echo $calendar['nextUrl']; ?>"><?php echo date('Y年m月', strtotime($calendar['next'] . '01')); ?></a>
                        <?php endif; ?>
                    </nav>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($component == '最新文章'): ?>
            <section class="latest-articles mwordstar-block">
                <h4>最新文章</h4>
                <ul class="list-group" aria-label="最新文章">
                    <?php $latestArticles = $this->widget('Widget_Contents_Post_Recent'); ?>
                    <?php $postSize = 0; ?>
                    <?php while ($latestArticles->next()): ?>
                        <li class="border-bottom">
                            <a target="<?php $this->options->sidebarLinkOpen(); ?>" href="<?php $latestArticles->permalink(); ?>">
                                <?php if (is_array($this->options->headerImage) && in_array('sidebarBlock', $this->options->headerImage)): ?>
                                    <?php $img = postImg($latestArticles, $this->options->headerImageUrl); ?>
                                    <?php if ($img): ?>
                                        <div class="article-img" style="background-image: url(<?php echo $img; ?>);" aria-label="<?php $latestArticles->title(); ?>的头图"></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <p><?php $latestArticles->title(); ?></p>
                            </a>
                        </li>
                        <?php
                            $postSize ++;
                            if ($postSize == $this->options->postsListSize) {
                                break;
                            }
                        ?>
                    <?php endwhile; ?>
                </ul>
            </section>
        <?php endif; ?>
        <?php if ($component == '最新回复'): ?>
            <section class="latest-comment mwordstar-block">
                <h4>最新回复</h4>
                <ul class="list-unstyled list-group" aria-label="最新回复">
                    <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
                    <?php while($comments->next()): ?>
                        <li class="media border-bottom">
                            <?php
                            if ($this->options->QQAvatar == 'on' && isQQEmail($comments->mail)) {
                                QQAvatar($comments->mail, $comments->author, 40);
                            }else {
                                $comments->gravatar('50', '');
                            }
                            if ($comments->type == 'pingback') {
                                echo '<div class="pingback avatar">引用</div>';
                            }
                            ?>
                            <div class="media-body ml-2">
                                <a data-toggle="tooltip" data-placement="top" title="发表在 <?php $comments->title(); ?> 的评论" target="<?php $this->options->sidebarLinkOpen(); ?>" href="<?php $comments->permalink(); ?>"><?php $comments->author(false); ?></a>
                                <div class="comment-content"><?php $comments->excerpt(50, '...'); ?></div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>
        <?php endif; ?>
        <?php if ($component == '文章分类'): ?>
            <section class="category mwordstar-block">
                <h4>文章分类</h4>
                <ul class="list-group list-group-flush" aria-label="文章分类">
                    <?php $this->widget('Widget_Metas_Category_List')->to($category); ?>
                    <?php while ($category->next()): ?>
                        <li class="d-flex justify-content-between align-items-center border-bottom indentation-<?php $category->parent(); ?>">
                            <a target="<?php $this->options->sidebarLinkOpen(); ?>" data-toggle="tooltip" data-placement="top" href="<?php $category->permalink(); ?>" title="<?php if ($category->parent > 0) echo getParentCategory($category->parent) . ' 下的子分类 ' ?><?php $category->description(); ?>">
                                <?php echo $category->name(); ?>
                            </a>
                            <span class="badge badge-pill">
                                <?php $category->count(); ?>
                            </span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>
        <?php endif; ?>
        <?php if ($component == '标签云'): ?>
            <section class="tag-cloud mwordstar-block">
                <h4>标签云</h4>
                <?php $limit = $this->options->tagCount == 0?1000:$this->options->tagCount; ?>
                <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&ignoreZeroCount=1&desc=0&limit=' . $limit)->to($tags); ?>
                <?php if($tags->have()): ?>
                    <?php $tagCount = tagCount(); ?>
                    <div class="tag-list pt-2" aria-label="标签云" role="list">
                        <?php while ($tags->next()): ?>
                            <a role="listitem" target="<?php $this->options->sidebarLinkOpen(); ?>" data-toggle="tooltip" data-placement="top" href="<?php $tags->permalink(); ?>" rel="tag" class="py-1 px-2 d-inline-block tag-link" title="<?php $tags->count(); ?> 篇文章"><?php $tags->name(); ?></a>
                            <?php
                            ?>
                        <?php endwhile; ?>
                        <?php if ($this->options->tagPage && $tagCount > $limit): ?>
                            <a role="listitem" target="<?php $this->options->sidebarLinkOpen(); ?>" data-toggle="tooltip" data-placement="top" href="<?php $this->options->tagPage(); ?>" rel="tag" class="py-1 px-2 d-inline-block tag-link" title="点击查看更多标签">查看更多</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center pb-2"><?php _e('没有任何标签'); ?></p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
        <?php if ($component == '文章归档'): ?>
            <section class="mwordstar-block">
                <h4>文章归档</h4>
                <ul class="list-group list-group-flush" aria-label="文章归档">
                    <?php $postArchive = $this->widget('Widget_Contents_Post_Date', 'type=month&format=Y年m月'); ?>
                    <?php $archiveCount = 0; ?>
                    <?php while ($postArchive->next()): ?>
                    <li class="d-flex justify-content-between align-items-center border-bottom">
                        <a target="<?php echo $this->options->sidebarLinkOpen; ?>" data-toggle="tooltip" data-placement="top" href="<?php $postArchive->permalink(); ?>" title="<?php $postArchive->count(); ?>篇文章"><?php $postArchive->date(); ?></a>
                        <span class="badge badge-pill"><?php $postArchive->count(); ?></span>
                    </li>
                    <?php
                    $archiveCount ++;
                    //  判断是否启用了文章归档数量限制
                    if ($this->options->postArchiveCount != 0 && $this->options->postArchiveCount == $archiveCount) {
                        break;
                    }
                    ?>
                    <?php endwhile; ?>
                    <?php if ($this->options->archivePageUrl && $this->options->postArchiveCount != 0 && $this->options->postArchiveCount == $archiveCount): ?>
                    <li class="d-flex justify-content-between align-items-center">
                        <a href="<?php $this->options->archivePageUrl(); ?>">查看更多</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </section>
        <?php endif; ?>
        <?php if ($component == '其它功能'): ?>
            <section class="mwordstar-block">
                <h4>其它功能</h4>
                <ul class="list-group" aria-label="其它功能">
                    <?php if ($this->options->loginLink == 'show'): ?>
                        <?php if($this->user->hasLogin()): ?>
                            <li class="last border-bottom"><a href="<?php $this->options->adminUrl(); ?>"><?php _e('进入后台'); ?> (<?php $this->user->screenName(); ?>)</a></li>
                            <li class="list border-bottom"><a href="<?php $this->options->logoutUrl(); ?>"><?php _e('退出'); ?></a></li>
                        <?php else: ?>
                            <li class="last border-bottom"><a href="<?php $this->options->adminUrl('login.php'); ?>"><?php _e('登录'); ?></a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li class="border-bottom"><a target="_blank" href="<?php $this->options->feedUrl(); ?>"><?php _e('文章 RSS'); ?></a></li>
                    <li class="border-bottom"><a target="_blank" href="<?php $this->options->commentsFeedUrl(); ?>"><?php _e('评论 RSS'); ?></a></li>
                </ul>
            </section>
        <?php endif; ?>
        <?php if ($component == '友情链接'): ?>
            <?php if ($this->options->links or $this->options->homeLinks && $this->is('index')): ?>
                <section class="mwordstar-block">
                    <h4>友情链接</h4>
                    <ul class="list-group" aria-label="友情链接">
                        <?php if ($this->options->homeLinks && $this->is('index')): ?>
                            <?php $homeLinks = json_decode($this->options->homeLinks); ?>
                            <?php foreach ($homeLinks as $val): ?>
                                <li class="border-bottom"><a data-toggle="tooltip" data-placement="top" href="<?php echo $val->url; ?>" title="<?php echo isset($val->title)?$val->title:'暂无简介'; ?>" target="_blank"><?php echo $val->name; ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($this->options->links): ?>
                            <?php $links = json_decode($this->options->links); ?>
                            <?php foreach ($links as $val): ?>
                                <li class="border-bottom"><a data-toggle="tooltip" data-placement="top" href="<?php echo $val->url; ?>" title="<?php echo isset($val->title)?$val->title:'暂无简介'; ?>" target="_blank"><?php echo $val->name; ?></a></li>
                            <?php endforeach;; ?>
                        <?php endif; ?>
                    </ul>
                </section>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($component == '目录' && $GLOBALS['page'] == 'post' && $GLOBALS['post']['directory'] != null): ?>
            <section class="mwordstar-block directory d-none d-sm-none d-md-none d-lg-block d-xl-block">
                <h4 class="mx-0">目录</h4>
                <?php echo $GLOBALS['post']['directory']; ?>
            </section>
        <?php endif; ?>
    <?php endforeach;  ?>
</div>