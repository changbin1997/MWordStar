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
            <!--博客信息-->
            <section class="mwordstar-block">
                <h4><?php echo $GLOBALS['t']['sidebar']['blogInfo']; ?></h4>
                <div class="personal-information pt-2">
                    <?php if (!$this->options->nickname or !$this->options->birthday or !$this->options->avatarUrl) $userInfo = getAdminInfo(); ?>
                    <div class="user">
                        <?php
                            $avatarName = $this->options->nickname ? $this->options->nickname . '的头像' : $this->options->title . '的头像';
                            if ($this->options->avatarUrl) {
                                echo '<img src="' . $this->options->avatarUrl . '" alt="' . $avatarName . '" class="avatar" />';
                            }else {
                                gravatar($userInfo['mail'], 56, $this->options->gravatarUrl, $avatarName);
                            }
                        ?>
                        <div class="p-2">
                            <a aria-describedby="blog-description" class="user-name mt-2" target="_blank" href="<?php echo $this->options->nicknameUrl?$this->options->nicknameUrl:$this->options->siteUrl; ?>"><?php echo $this->options->nickname?$this->options->nickname:$userInfo['screenName']; ?></a>
                            <p class="introduction mt-1" id="blog-description"><?php echo $this->options->Introduction?$this->options->Introduction:$this->options->description; ?></p>
                        </div>
                    </div>
                    <div class="website clearfix border-top">
                        <?php Typecho_Widget::widget('Widget_Stat')->to($quantity); ?>
                        <div class="info float-left border-right">
                            <p class="quantity"><?php $quantity->publishedPostsNum(); ?></p>
                            <?php echo $GLOBALS['t']['dataPage']['totalPosts']; ?>
                        </div>
                        <div class="info float-left border-right">
                            <p class="quantity"><?php $quantity->publishedCommentsNum(); ?></p>
                            <?php echo $GLOBALS['t']['sidebar']['totalComments']; ?>
                        </div>
                        <div class="info float-left">
                            <?php $runningSince = $this->options->birthday ? round((time() - strtotime($this->options->birthday)) / 86400, 0) : round((time() - $userInfo['created']) / 86400, 0); ?>
                            <p class="quantity"><?php printf($GLOBALS['t']['sidebar']['runningSince'][1], $runningSince); ?></p>
                            <?php echo $GLOBALS['t']['sidebar']['runningSince'][0]; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($component == '语言选择'): ?>
            <section class="language-select mwordstar-block">
                <h4>语言（Language）</h4>
                <ul class="list-group" aria-label="语言（Language）">
                    <li class="border-bottom">
                        <div class="custom-control custom-radio">
                            <input <?php if ($GLOBALS['language'] == 'zh' or $GLOBALS['language'] == 'zh-CN') echo 'checked'; ?> type="radio" class="custom-control-input change-language" name="language" id="zh-CN" data-language="zh-CN">
                            <label class="custom-control-label" for="zh-CN">简体中文</label>
                        </div>
                    </li>
                    <li class="border-bottom">
                        <div class="custom-control custom-radio">
                            <input <?php if ($GLOBALS['language'] == 'en') echo 'checked'; ?> type="radio" class="custom-control-input change-language" name="language" id="en" data-language="en">
                            <label class="custom-control-label" for="en">English</label>
                        </div>
                    </li>
                </ul>
            </section>
        <?php endif; ?>
        
        <?php if ($component == '自定义' && $this->options->customizeHTML): ?>
            <!--自定义HTML-->
            <section class="customize mwordstar-block">
                <h4><?php $this->options->customizeTitle(); ?></h4>
                <div class="customize-html pt-2"><?php $this->options->customizeHTML(); ?></div>
            </section>
        <?php endif; ?>
        
        <?php if ($component == '搜索'): ?>
            <!--搜索-->
            <section class="search mwordstar-block">
                <h4><?php echo $GLOBALS['t']['header']['search']; ?></h4>
                <div class="tag-list pt-2">
                    <form action="<?php $this->options->siteUrl(); ?>" method="post" role="form">
                        <div class="input-group">
                            <input type="search" class="form-control form-control-md" placeholder="<?php echo $GLOBALS['t']['header']['search']; ?>" aria-label="<?php echo $GLOBALS['t']['header']['search']; ?>" aria-describedby="button-addon2" required="required" name="s">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-md" aria-label="<?php echo $GLOBALS['t']['header']['search']; ?>" title="<?php echo $GLOBALS['t']['header']['search']; ?>" data-toggle="tooltip" data-placement="top">
                                    <span class="icon-search"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($component == '日历'): ?>
            <!--日历-->
            <section class="calendar mwordstar-block">
                <?php
                // 获取日历的月份
                $date = getMonth();
                // 根据语言使用不同的日期格式
                $format = $GLOBALS['language'] == 'en' ? 'F Y' : 'Y年m月';
                // 设置日历组件的标题
                $monthTimestamp = mktime(0, 0, 0, $date[1], 1, $date[0]);
                ?>
                <h4><?php echo date($format, $monthTimestamp); ?></h4>
                <div class="tag-list pt-2">
                    <?php $calendar = calendar($date[0] . '-' . $date[1] . '-01', $this->options->siteUrl, $this->options->rewrite); ?>
                    <?php echo $calendar['calendar']; ?>
                    <nav class="pt-2 clearfix" aria-label="<?php echo $GLOBALS['t']['sidebar']['previousAndNextMonths']; ?>">
                        <?php if ($calendar['previous']): ?>
                            <a class="p-0 float-left" href="<?php echo $calendar['previousUrl']; ?>"><?php echo date($format, strtotime($calendar['previous'] . '01')); ?></a>
                        <?php endif; ?>
                        <?php if ($calendar['next']): ?>
                            <a class="p-0 float-right"  href="<?php echo $calendar['nextUrl']; ?>"><?php echo date($format, strtotime($calendar['next'] . '01')); ?></a>
                        <?php endif; ?>
                    </nav>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($component == '最新文章'): ?>
            <!--最新文章-->
            <section class="latest-articles mwordstar-block">
                <h4><?php echo $GLOBALS['t']['sidebar']['latestPosts']; ?></h4>
                <?php $latestArticles = $this->widget('Widget_Contents_Post_Recent'); ?>
                <?php if ($latestArticles->have()): ?>
                <ul class="list-group" aria-label="<?php echo $GLOBALS['t']['sidebar']['latestPosts']; ?>">
                    <?php $postSize = 0; ?>
                    <?php while ($latestArticles->next()): ?>
                        <li class="border-bottom">
                            <a target="<?php $this->options->sidebarLinkOpen(); ?>" href="<?php $latestArticles->permalink(); ?>">
                                <?php if (is_array($this->options->headerImage) && in_array('sidebarBlock', $this->options->headerImage)): ?>
                                    <?php $img = postImg($latestArticles, $this->options->headerImageUrl); ?>
                                    <?php if ($img): ?>
                                        <div class="article-img" style="background-image: url(<?php echo $img; ?>);" aria-label="<?php echo $GLOBALS['t']['post']['featuredImage']; ?>"></div>
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
                <?php else: ?>
                    <p class="message pb-0"><?php echo $GLOBALS['t']['sidebar']['noPostsAvailableToDisplay']; ?></p>
                <?php endif; ?>    
            </section>
        <?php endif; ?>

        <?php if ($component == '最新回复'): ?>
            <!--最新回复-->
            <section class="latest-comment mwordstar-block">
                <h4><?php echo $GLOBALS['t']['sidebar']['recentComments']; ?></h4>
                <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
                <?php if ($comments->have()): ?>
                <ul class="list-unstyled list-group" aria-label="<?php echo $GLOBALS['t']['sidebar']['recentComments']; ?>">
                    <?php while($comments->next()): ?>
                        <li class="media border-bottom">
                            <?php
                                if ($comments->type == 'comment') {
                                    if ($this->options->QQAvatar == 'on' && isQQEmail($comments->mail)) {
                                        QQAvatar($comments->mail, $comments->author, 40);
                                    }else {
                                        gravatar($comments->mail, 50, $this->options->gravatarUrl, $comments->author);
                                    }
                                }
                                if ($comments->type == 'pingback') {
                                    echo '<div class="pingback avatar" role="img">引用</div>';
                                }
                            ?>
                            <div class="media-body ml-2">
                                <a data-toggle="tooltip" data-placement="top" title="<?php printf($GLOBALS['t']['sidebar']['commentOn'], $comments->title); ?>" target="<?php $this->options->sidebarLinkOpen(); ?>" href="<?php $comments->permalink(); ?>"><?php $comments->author(false); ?></a>
                                <div class="comment-content"><?php $comments->excerpt(50, '...'); ?></div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                    <p class="pb-0 message">没有可以显示的评论和回复</p>
                <?php endif; ?>    
            </section>
        <?php endif; ?>

        <?php if ($component == '文章分类'): ?>
            <!--分类-->
            <section class="category mwordstar-block">
                <h4><?php echo $GLOBALS['t']['sidebar']['categories']; ?></h4>
                <?php $this->widget('Widget_Metas_Category_List')->to($category); ?>
                <?php if ($category->have()): ?>
                <ul class="list-group list-group-flush" aria-label="<?php echo $GLOBALS['t']['sidebar']['categories']; ?>">
                    <?php while ($category->next()): ?>
                        <li class="d-flex justify-content-between align-items-center border-bottom indentation-<?php $category->parent(); ?>">
                            <a rel="index" target="<?php $this->options->sidebarLinkOpen(); ?>" data-toggle="tooltip" data-placement="top" href="<?php $category->permalink(); ?>" title="<?php if ($category->parent > 0) echo getParentCategory($category->parent) . ' 下的子分类 ' ?><?php $category->description(); ?>">
                                <?php echo $category->name(); ?>
                                <span class="sr-only"><?php printf($GLOBALS['t']['sidebar']['tagPostCount'], $category->count); ?></span>
                            </a>
                            <span class="badge badge-pill">
                                <?php $category->count(); ?>
                            </span>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                    <p class="pb-0 message"><?php echo $GLOBALS['t']['sidebar']['noCategoriesAvailableToDisplay']; ?></p>
                <?php endif; ?>    
            </section>
        <?php endif; ?>

        <?php if ($component == '标签云'): ?>
            <!--标签云-->
            <section class="tag-cloud mwordstar-block">
                <h4><?php echo $GLOBALS['t']['sidebar']['tags']; ?></h4>
                <?php $limit = $this->options->tagCount == 0?1000:$this->options->tagCount; ?>
                <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&ignoreZeroCount=1&desc=0&limit=' . $limit)->to($tags); ?>
                <?php if($tags->have()): ?>
                    <?php $tagCount = tagCount(); ?>
                    <div class="tag-list pt-2" aria-label="<?php echo $GLOBALS['t']['sidebar']['tags']; ?>" role="list">
                        <?php while ($tags->next()): ?>
                            <a role="listitem" target="<?php $this->options->sidebarLinkOpen(); ?>" data-toggle="tooltip" data-placement="top" href="<?php $tags->permalink(); ?>" rel="tag" class="py-1 px-2 d-inline-block tag-link" title="<?php printf($GLOBALS['t']['sidebar']['tagPostCount'], $tags->count); ?>"><?php $tags->name(); ?></a>
                            <?php
                            ?>
                        <?php endwhile; ?>
                        <?php if ($this->options->tagPage && $tagCount > $limit): ?>
                            <a role="listitem" target="<?php $this->options->sidebarLinkOpen(); ?>" data-toggle="tooltip" data-placement="top" href="<?php $this->options->tagPage(); ?>" rel="tag" class="py-1 px-2 d-inline-block tag-link" title="<?php echo $GLOBALS['t']['sidebar']['viewMoreTags']; ?>"><?php echo $GLOBALS['t']['sidebar']['viewMore']; ?></a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="pb-0 message"><?php echo $GLOBALS['t']['sidebar']['noTagsAvailableToDisplay']; ?></p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if ($component == '文章归档'): ?>
            <!--归档-->
            <section class="mwordstar-block">
                <h4><?php echo $GLOBALS['t']['sidebar']['archives']; ?></h4>
                <?php
                // 根据语言设置归档时间格式
                $format = $GLOBALS['language'] == 'en' ? 'F Y' : 'Y年m月';
                $postArchive = $this->widget('Widget_Contents_Post_Date', 'type=month&format=' . $format);
                ?>
                <?php if ($postArchive->have()): ?>
                <ul class="list-group list-group-flush" aria-label="<?php echo $GLOBALS['t']['sidebar']['archives']; ?>">
                    <?php $archiveCount = 0; ?>
                    <?php while ($postArchive->next()): ?>
                    <li class="d-flex justify-content-between align-items-center border-bottom">
                        <a rel="archives" target="<?php echo $this->options->sidebarLinkOpen; ?>" data-toggle="tooltip" data-placement="top" href="<?php $postArchive->permalink(); ?>" title="<?php printf($GLOBALS['t']['sidebar']['tagPostCount'], $postArchive->count); ?>"><?php $postArchive->date(); ?></a>
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
                        <a href="<?php $this->options->archivePageUrl(); ?>"><?php echo $GLOBALS['t']['sidebar']['viewMore']; ?></a>
                    </li>
                    <?php endif; ?>
                </ul>
                <?php else: ?>
                    <p class="pb-0 message"><?php echo $GLOBALS['t']['sidebar']['coPostsAvailableToGenerateAnArchive']; ?></p>
                <?php endif; ?>    
            </section>
        <?php endif; ?>

        <?php if ($component == '其它功能'): ?>
            <!--其它功能-->
            <section class="mwordstar-block">
                <h4><?php echo $GLOBALS['t']['sidebar']['other']; ?></h4>
                <ul class="list-group" aria-label="<?php echo $GLOBALS['t']['sidebar']['other']; ?>">
                    <?php if ($this->options->loginLink == 'show'): ?>
                        <?php if($this->user->hasLogin()): ?>
                            <li class="last border-bottom"><a href="<?php $this->options->adminUrl(); ?>"><?php printf($GLOBALS['t']['sidebar']['dashboard'], $this->user->screenName); ?></a></li>
                            <li class="list border-bottom"><a href="<?php $this->options->logoutUrl(); ?>"><?php echo $GLOBALS['t']['sidebar']['logout']; ?></a></li>
                        <?php else: ?>
                            <li class="last border-bottom"><a href="<?php $this->options->adminUrl('login.php'); ?>"><?php echo $GLOBALS['t']['sidebar']['login']; ?></a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li class="border-bottom"><a target="_blank" href="<?php $this->options->feedUrl(); ?>"><?php echo $GLOBALS['t']['sidebar']['RSSforPosts']; ?></a></li>
                    <li class="border-bottom"><a target="_blank" href="<?php $this->options->commentsFeedUrl(); ?>"><?php echo $GLOBALS['t']['sidebar']['RSSforComments']; ?></a></li>
                </ul>
            </section>
        <?php endif; ?>

        <?php if ($component == '友情链接'): ?>
            <?php if ($this->options->links or $this->options->homeLinks && $this->is('index')): ?>
                <!--友情链接-->
                <section class="mwordstar-block">
                    <h4><?php echo $GLOBALS['t']['sidebar']['usefulLinks']; ?></h4>
                    <ul class="list-group" aria-label="<?php echo $GLOBALS['t']['sidebar']['usefulLinks']; ?>">
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
            <!--目录-->
            <section class="mwordstar-block directory d-none d-sm-none d-md-none d-lg-block d-xl-block">
                <h4 class="mx-0"><?php echo $GLOBALS['t']['sidebar']['tableOfContents']; ?></h4>
                <?php echo $GLOBALS['post']['directory']; ?>
            </section>
        <?php endif; ?>
    <?php endforeach;  ?>
    <?php if ($GLOBALS['page'] == 'post'): ?>
        <div class="reference-line"></div>
    <?php endif; ?>
</div>