<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="container post-page">
    <div class="row">
        <div class="col-md-12 col-lg-8 col-sm-12 article-page">
            <main>
                <header class="entry-header">
                    <h1 class="entry-title p-name" itemprop="name headline">
                        <a itemprop="url" href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                    </h1>
                </header>
                <?php if ($this->fields->thumb): ?>
                <div class="header-img">
                    <a href="">
                        <img src="<?php $this->fields->thumb(); ?>" alt="<?php $this->title() ?>的头图">
                    </a>
                </div>
                <?php endif; ?>
                <div class="article-info clearfix">
                    <!--时间-->
                    <div class="info">
                        <i class="icon-calendar icon" aria-label="日期图标"></i>
                        <span tabindex="0" title="发布时间"><?php $this->date('Y年m月d日'); ?></span>
                    </div>
                    <!--作者-->
                    <div class="info">
                        <i class="icon-user icon" aria-label="作者图标"></i>
                        <a href="<?php $this->author->permalink(); ?>" title="作者"><?php $this->author(); ?></a>
                    </div>
                    <!--阅读量-->
                    <div class="info">
                        <i class="icon-eye icon" aria-label="阅读量图标"></i>
                        <span tabindex="0" title="阅读量"><?php echo getPostView($this); ?></span>
                    </div>
                    <!--评论-->
                    <div class="info">
                        <i class="icon-bubbles2 icon" aria-label="评论图标"></i>
                        <a title="评论" href="#comments"><?php $this->commentsNum('%d 评论'); ?></a>
                    </div>
                    <!--分类-->
                    <div class="info">
                        <i class="icon-folder-open icon" aria-label="分类图标"></i>
                        <?php $this->category(','); ?>
                    </div>
                    <!--标签-->
                    <div class="info tags">
                        <i class="icon-price-tags icon" aria-label="标签图标"></i>
                        <?php $this->tags(' ', true, 'none'); ?>
                    </div>
                </div>
                <article>
                    <div class="post-content">
                        <?php $this->content(); ?>
                    </div>
                </article>
                <nav class="post-navigation navbar">
                    <div class="pagination pagination-sm">上一篇：<?php $this->thePrev('%s','没有了'); ?></div>
                    <div class="pagination justify-content-end">下一篇：<?php $this->theNext('%s','没有了'); ?></div>
                </nav>
                <?php $this->need('comments.php'); ?>
            </main>
        </div>
        <?php $this->need('sidebar.php'); ?>
    </div>
</div>
<div id="max-img">
    <img src="" alt="" title="再次点击可关闭" class="shadow-lg">
</div>
<?php $this->need('footer.php'); ?>
