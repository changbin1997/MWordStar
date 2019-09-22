<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="container main-content">
    <div class="row">
        <div class="col-md-12 col-lg-8 col-sm-12 page">
            <main>
                <header class="entry-header">
                    <h1 class="entry-title p-name" itemprop="name headline">
                        <a itemprop="url" href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                    </h1>
                </header>
                <div class="header-img <?php echo $this->fields->thumb?'':'d-none'; ?>"">
                    <a href="">
                        <img src="<?php $this->fields->thumb(); ?>" alt="<?php $this->title() ?>的头图">
                    </a>
                </div>
                <div class="article-info clearfix">
                    <div class="info">
                        <i class="icon-calendar icon"></i>
                        <a href="#"><?php $this->date('Y年m月d日'); ?></a>
                    </div>
                    <div class="info">
                        <i class="icon-user icon"></i>
                        <a href="<?php $this->author->permalink(); ?>"><?php $this->author(); ?></a>
                    </div>
                </div>
                <article>
                    <div class="post-content">
                        <?php $this->content(); ?>
                    </div>
                </article>
                <?php $this->need('comments.php'); ?>
            </main>
        </div>
        <?php $this->need('sidebar.php'); ?>
    </div>
</div>
<?php $this->need('footer.php'); ?>
