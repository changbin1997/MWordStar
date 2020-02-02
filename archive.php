<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="container main-content">
    <div class="row">
        <div class="col-md-12 col-lg-8 col-sm-12 archives-list content-area">
            <main id="main" class="archives">
                <header class="page-header">
                    <h1 class="archive-title"><?php $this->archiveTitle(array(
                            'category' => _t('分类 %s 下的文章'),
                            'search' => _t('包含关键字 %s 的文章'),
                            'tag' => _t('标签 %s 下的文章'),
                            'author' => _t('%s 发布的文章')
                        ), '', ''); ?>
                    </h1>
                    <span><?php echo $this->getDescription(); ?></span>
                </header>
                <?php if ($this->have()): ?>
                <?php while($this->next()): ?>
                    <article class="post">
                        <?php if ($this->options->headerImage && in_array('home', $this->options->headerImage)): ?>
                            <?php $img = postImg($this); ?>
                            <?php if ($img): ?>
                                <div class="header-img">
                                    <a tabindex="-1" aria-hidden="true" href="<?php $this->permalink() ?>" aria-label="<?php $this->title() ?>的头图" style="background-image: url(<?php echo $img; ?>);background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;"></a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <header class="entry-header border-bottom">
                            <h2 class="entry-title p-name">
                                <a rel="bookmark" href="<?php $this->permalink(); ?>"><?php $this->title(); ?></a>
                            </h2>
                        </header>
                        <div class="entry-summary">
                            <p><?php $this->fields->summaryContent?$this->fields->summaryContent():$this->excerpt($this->options->summary, '...'); ?></p>
                        </div>
                        <div class="article-info clearfix border-top">
                            <!--时间-->
                            <div class="info">
                                <i class="icon-calendar icon" aria-hidden="true"></i>
                                <span tabindex="0" title="发布时间：<?php $this->date('Y年m月d日'); ?>"><?php $this->date('Y年m月d日'); ?></span>
                            </div>
                            <!--作者-->
                            <div class="info">
                                <i class="icon-user icon" aria-hidden="true"></i>
                                <a href="<?php $this->author->permalink(); ?>" title="作者：<?php $this->author(); ?>"><?php $this->author(); ?></a>
                            </div>
                            <!--阅读量-->
                            <div class="info">
                                <i class="icon-eye icon" aria-hidden="true"></i>
                                <span tabindex="0" title="阅读量：<?php echo getPostView($this); ?>"><?php echo getPostView($this); ?></span>
                            </div>
                            <!--评论-->
                            <div class="info">
                                <i class="icon-bubbles2 icon" aria-hidden="true"></i>
                                <a title="评论" href="#comments"><?php $this->commentsNum('%d 评论'); ?></a>
                            </div>
                            <!--分类-->
                            <div class="info">
                                <i class="icon-folder-open icon" aria-hidden="true"></i>
                                <?php $this->category(','); ?>
                            </div>
                            <a href="<?php $this->permalink() ?>" class="float-right d-sm-none d-none d-md-inline d-lg-inline d-xl-inline">阅读全文</a>
                            <?php if ($this->user->hasLogin()): ?>
                                <a href="<?php echo $this->options->siteUrl . 'admin/write-post.php?cid=' . $this->cid; ?>" class="float-right mr-3 d-sm-none d-none d-md-inline d-lg-inline d-xl-inline">编辑</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php else: ?>
                    <article class="post">
                        <h2 class="post-title"><?php _e('没有找到内容'); ?></h2>
                    </article>
                <?php endif; ?>
            </main>
            <nav aria-label="分页导航区" class="pagination-nav">
                <?php $this->pageNav('&laquo;', '&raquo;', 2, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination', 'itemTag' => 'li', 'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => 'prev', 'nextClass' => 'next')); ?>
            </nav>
        </div>
        <?php $this->need('sidebar.php'); ?>
    </div>
</div>
<?php $this->need('footer.php'); ?>