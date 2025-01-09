<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'archive';

// 语言初始化
languageInit($this->options->language);
$this->need('components/header.php');
?>

<div id="main">
    <div class="container main-content">
        <?php if ($this->options->breadcrumb == 'on'): ?>
            <nav aria-label="<?php echo $GLOBALS['t']['breadcrumb']; ?>" class="breadcrumb-nav">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="<?php $this->options->siteUrl(); ?>"><?php echo $GLOBALS['t']['header']['home']; ?></a>
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
                                'category' => $GLOBALS['t']['archive']['postsUnderTheCategory'],
                                'search' => $GLOBALS['t']['archive']['postsContainingTheKeyword'],
                                'tag' => $GLOBALS['t']['archive']['postsTagged'],
                                'author' => $GLOBALS['t']['archive']['postsByAuthor']
                            ), '', ''); ?>
                        </h1>
                        <span><?php echo $this->getDescription(); ?></span>
                    </header>
                    <?php if ($this->have()): ?>
                        <?php $this->need('components/post-list.php'); ?>
                    <?php else: ?>
                        <article class="post no-post mwordstar-block">
                            <h4 class="post-title" role="alert"><?php printf($GLOBALS['t']['archive']['noPostsFoundContaining'], '<b>' . $this->archiveTitle . '</b>') ?></h4 >
                            <p class="ml-4"><?php echo $GLOBALS['t']['archive']['youCanTryTheFollowing']; ?></p>
                            <ol class="ml-1 mb-4">
                                <li><?php echo $GLOBALS['t']['archive']['trySearchingWithDifferentKeywords']; ?></li>
                                <li><?php echo $GLOBALS['t']['archive']['browsePostsByCategoryInTheSectionToTheRightOrBelow']; ?></li>
                                <li><?php echo $GLOBALS['t']['archive']['browsePostsByTagsInTheTagCloudSectionToTheRightOrBelow']; ?></li>
                            </ol>
                        </article>
                    <?php endif; ?>
                </div>
                <nav aria-label="<?php echo $GLOBALS['t']['pagination']['pagination']; ?>" class="pagination-nav">
                    <?php $this->pageNav('<i class="icon-chevron-left"></i>', '<i class="icon-chevron-right"></i>', 1, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination justify-content-center', 'itemTag' => 'li',  'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => 'prev', 'nextClass' => 'next')); ?>
                </nav>
            </div>
            <?php $this->need('components/sidebar.php'); ?>
        </div>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>