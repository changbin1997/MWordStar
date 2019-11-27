<?php
/**
 * 这是一套简洁的博客主题
 *
 * @package MWordStar
 * @author Mr Ma
 * @version 0.8
 * @link https://www.misterma.com
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');  //  头文件
?>

<div class="container home main-content">
    <div class="row">
        <div class="article-list col-md-12 col-lg-8 col-sm-12 content-area">
            <?php while ($this->next()):  //  开始循环  ?>
                <div class="post">
                    <?php if ($this->fields->thumb && $this->options->headerImage && in_array('home', $this->options->headerImage)): ?>
                        <div class="header-img">
                            <a tabindex="-1" aria-hidden="true" href="<?php $this->permalink() ?>" aria-label="<?php $this->title() ?>的头图" style="background-image: url(<?php $this->fields->thumb(); ?>);"></a>
                        </div>
                    <?php endif; ?>
                    <header class="entry-header border-bottom">
                        <h2 class="entry-title p-name">
                            <?php if ($this->sticky) $this->sticky(); ?>
                            <a href="<?php $this->permalink() ?>" rel="bookmark"><?php $this->title() ?></a>
                        </h2>
                    </header>
                    <div class="entry-summary">
                        <p><?php $this->fields->summaryContent?$this->fields->summaryContent():$this->excerpt($this->options->summary, '...'); ?></p>
                    </div>
                    <div class="article-info clearfix border-top">
                        <!--时间-->
                        <div class="info">
                            <i class="icon-calendar icon" aria-label="日期图标"></i>
                            <span tabindex="0" title="发布时间：<?php $this->date('Y年m月d日'); ?>"><?php $this->date('Y年m月d日'); ?></span>
                        </div>
                        <!--作者-->
                        <div class="info">
                            <i class="icon-user icon" aria-label="作者图标"></i>
                            <a href="<?php $this->author->permalink(); ?>" title="作者：<?php $this->author(); ?>"><?php $this->author(); ?></a>
                        </div>
                        <!--阅读量-->
                        <div class="info">
                            <i class="icon-eye icon" aria-label="阅读图标"></i>
                            <span tabindex="0" title="阅读量：<?php echo getPostView($this); ?>"><?php echo getPostView($this); ?></span>
                        </div>
                        <!--评论-->
                        <div class="info">
                            <i class="icon-bubbles2 icon" aria-label="评论图标"></i>
                            <a title="评论" href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('%d 评论'); ?></a>
                        </div>
                        <!--分类-->
                        <div class="info">
                            <i class="icon-folder-open icon" aria-label="分类图标"></i>
                            <?php $this->category(','); ?>
                        </div>
                        <a href="<?php $this->permalink() ?>" class="float-right d-sm-none d-none d-md-inline d-lg-inline d-xl-inline">阅读全文</a>
                        <?php if ($this->user->hasLogin()): ?>
                            <a href="<?php echo $this->options->siteUrl . 'admin/write-post.php?cid=' . $this->cid; ?>" class="float-right mr-3 d-sm-none d-none d-md-inline d-lg-inline d-xl-inline">编辑</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <nav aria-label="分页导航区" class="pagination-nav">
                <?php $this->pageNav('&laquo;', '&raquo;', 2, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination', 'itemTag' => 'li', 'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => 'prev', 'nextClass' => 'next')); ?>
            </nav>
        </div>
    <?php $this->need('sidebar.php'); ?>
    </div>
</div>
<?php $this->need('footer.php'); ?>
