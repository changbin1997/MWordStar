<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('components/header.php'); ?>

<div class="container main-content">
    <div class="row">
        <div class="col-md-12 col-lg-8 col-sm-12 page content-area">
            <main>
                <header class="entry-header">
                    <h1 class="entry-title p-name" itemprop="name headline">
                        <a itemprop="url" href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
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
                        <i class="icon-calendar icon" aria-hidden="true"></i>
                        <span data-toggle="tooltip" data-placement="top" tabindex="0" title="发布日期：<?php $this->date('Y年m月d日'); ?>"><?php $this->date('Y年m月d日'); ?></span>
                    </div>
                    <!--作者-->
                    <div class="info">
                        <i class="icon-user icon" aria-hidden="true"></i>
                        <a data-toggle="tooltip" data-placement="top" href="<?php $this->author->permalink(); ?>" title="作者：<?php $this->author(); ?>"><?php $this->author(); ?></a>
                    </div>
                </div>
                <article>
                    <div class="post-content">
                        <?php $this->content(); ?>
                    </div>
                </article>
                <?php $this->need('components/comments.php'); ?>
            </main>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>
