<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'archive';
$this->need('components/header.php');
?>

<div class="container main-content">
    <?php if ($this->options->breadcrumb == 'on'): ?>
        <nav aria-label="路径" class="breadcrumb-nav">
            <ol class="breadcrumb m-0 p-0">
                <li class="breadcrumb-item">
                    <a href="<?php $this->options->siteUrl(); ?>">首页</a>
                </li>
                <li tabindex="0" class="breadcrumb-item active" aria-current="page"><?php $this->archiveTitle(' &raquo; ','',''); ?></li>
            </ol>
        </nav>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12 col-lg-8 col-sm-12 archives-list content-area">
            <div id="main" class="archives">
                <header class="page-header mwordstar-block">
                    <h1 class="archive-title">
                        <?php $this->archiveTitle(array(
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
                    <article class="post no-post mwordstar-block">
                        <h4 class="post-title" role="alert">无法查找到包含 <b><?php $this->archiveTitle(array('search' => '%s'), '', ''); ?></b> 的文章！</h4 >
                        <p class="ml-4">您可以尝试：</p>
                        <ol class="ml-1 mb-4">
                            <li>更换关键字重新搜索</li>
                            <li>在右侧或下方的文章分类区域选择分类查找</li>
                            <li>在右侧或下方的标签云区域选择标签查找</li>
                        </ol>
                    </article>
                <?php endif; ?>
            </div>
            <nav aria-label="分页导航区" class="pagination-nav">
                <?php $this->pageNav('<i class="icon-chevron-left"></i>', '<i class="icon-chevron-right"></i>', 1, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination justify-content-center', 'itemTag' => 'li',  'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => 'prev', 'nextClass' => 'next')); ?>
            </nav>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>