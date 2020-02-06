<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="container post-page main-content">
    <div class="row">
        <div class="col-md-12 col-lg-8 col-sm-12 article-page content-area">
            <main>
                <header class="entry-header">
                    <h1 class="entry-title p-name" itemprop="name headline">
                        <a itemprop="url" href="<?php $this->permalink() ?>" rel="bookmark"><?php $this->title() ?></a>
                    </h1>
                </header>
                <?php if ($this->options->headerImage && in_array('post', $this->options->headerImage)): ?>
                    <?php $img = postImg($this); ?>
                    <?php if ($img): ?>
                        <div class="header-img border-top">
                            <a tabindex="-1" aria-hidden="true" href="<?php $this->permalink() ?>" aria-label="<?php $this->title() ?>的头图" style="background-image: url(<?php echo $img; ?>);background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;"></a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="article-info clearfix border-bottom border-top">
                    <!--时间-->
                    <div class="info">
                        <i class="icon-calendar icon" aria-label="日期图标"></i>
                        <span data-toggle="tooltip" data-placement="top" tabindex="0" title="发布时间：<?php $this->date('Y年m月d日'); ?>"><?php $this->date('Y年m月d日'); ?></span>
                    </div>
                    <!--作者-->
                    <div class="info">
                        <i class="icon-user icon" aria-hidden="true"></i>
                        <a data-toggle="tooltip" data-placement="top" href="<?php $this->author->permalink(); ?>" title="作者：<?php $this->author(); ?>"><?php $this->author(); ?></a>
                    </div>
                    <!--阅读量-->
                    <div class="info">
                        <i class="icon-eye icon" aria-hidden="true"></i>
                        <span data-toggle="tooltip" data-placement="top" tabindex="0" title="阅读量：<?php echo getPostView($this); ?>"><?php echo getPostView($this); ?></span>
                    </div>
                    <!--评论-->
                    <div class="info">
                        <i class="icon-bubbles2 icon" aria-hidden="true"></i>
                        <a data-toggle="tooltip" data-placement="top" title="评论" href="#comments"><?php $this->commentsNum('%d 评论'); ?></a>
                    </div>
                    <!--分类-->
                    <div class="info">
                        <i class="icon-folder-open icon" aria-hidden="true"></i>
                        <?php $this->category(','); ?>
                    </div>
                    <!--标签-->
                    <div class="info tags">
                        <i class="icon-price-tags icon" aria-hidden="true"></i>
                        <?php $this->tags(' ', true, 'none'); ?>
                    </div>
                    <?php if ($this->user->hasLogin()): ?>
                        <div class="info d-sm-none d-none d-md-inline d-lg-inline d-xl-inline">
                            <i class="icon icon-pencil"></i>
                            <a href="<?php echo $this->options->siteUrl . 'admin/write-post.php?cid=' . $this->cid; ?>" >编辑</a>
                        </div>
                    <?php endif; ?>
                </div>
                <!--文章内容-->
                <article>
                    <div class="post-content">
                        <?php $this->content(); ?>
                        <?php if ($this->fields->articleCopyright != 'hide'): ?>
                            <hr>
                            <div class="alert alert-secondary">
                                版权声明：本文为原创文章，版权归 <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a> 所有，转载请联系博主获得授权！
                                <br>
                                本文地址：<a href="<?php $this->permalink() ?>"><?php $this->permalink() ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <span data-toggle="tooltip" data-placement="top" tabindex="0" title="发布时间：<?php $this->date('Y年m月d日'); ?>">最后编辑：<?php echo date('Y年m月d日', $this->modified);?></span>
                </article>
                <!--上一篇和下一篇文章的导航-->
                <nav class="post-navigation navbar border-top">
                    <div><div>上一篇</div><?php $this->thePrev('%s','没有了'); ?></div>
                    <div><div class="text-lg-right text-xl-right text-md-right">下一篇</div><?php $this->theNext('%s','没有了'); ?></div>
                </nav>
                <?php $this->need('comments.php'); ?>
            </main>
        </div>
        <?php $this->need('sidebar.php'); ?>
    </div>
</div>
<div id="max-img">
    <img src="" alt="" title="再次点击可关闭" class="shadow-lg">
    <div class="hide-img" role="button" aria-label="关闭大图" title="关闭大图" tabindex="0">×</div>
</div>
<?php $this->need('footer.php'); ?>
