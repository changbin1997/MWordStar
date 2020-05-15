<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$color = color($this->options->color);
$rounded = $this->options->rounded == 'rightAngle'?'rounded-0':'';  //  获取元素风格设置
$this->need('components/header.php');
?>

<div class="container main-content">
    <?php if ($this->options->breadcrumb == 'on'): ?>
        <nav aria-label="路径" class="breadcrumb-nav">
            <ol class="breadcrumb m-0 p-0">
                <li class="breadcrumb-item">
                    <a href="<?php $this->options->siteUrl(); ?>" class="<?php echo $color['link']; ?>">首页</a>
                </li>
                <li tabindex="0" class="breadcrumb-item active" aria-current="page"><?php $this->archiveTitle(' &raquo; ','',''); ?></li>
            </ol>
        </nav>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12 col-lg-8 col-sm-12 archives-list content-area">
            <main id="main" class="archives">
                <header class="page-header <?php echo $rounded; ?>">
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
                    <?php $this->need('components/post-list.php'); ?>
                <?php else: ?>
                    <article class="post">
                        <h2 class="post-title" role="alert"><?php _e('没有搜索到您需要的文章'); ?></h2>
                    </article>
                <?php endif; ?>
            </main>
            <nav aria-label="分页导航区" class="pagination-nav">
                <?php $this->pageNav('&laquo;', '&raquo;', 1, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination justify-content-center ' . $color['name'], 'itemTag' => 'li',  'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => 'prev', 'nextClass' => 'next')); ?>
            </nav>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>