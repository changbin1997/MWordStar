<?php
/**
 * 标签云
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'page-tag';
$this->need('components/header.php');
?>

<div class="container tag-page main-content mb-0">
    <?php if ($this->options->breadcrumb == 'on'): ?>
        <nav aria-label="路径" class="breadcrumb-nav">
            <ol class="breadcrumb m-0 p-0">
                <li class="breadcrumb-item">
                    <a href="<?php $this->options->siteUrl(); ?>">首页</a>
                </li>
                <li class="breadcrumb-item">
                    <?php $this->category(' '); ?>
                </li>
                <li tabindex="0" class="breadcrumb-item active" aria-current="page"><?php $this->title(); ?></li>
            </ol>
        </nav>
    <?php endif; ?>
    <div class="row">
        <div class="archive col-md-12 col-lg-8 col-sm-12 content-area">
            <main class="mwordstar-block">
                <header class="entry-header border-bottom">
                    <h2 class="entry-title p-name">
                        <a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                    </h2>
                </header>
                <article>
                    <div class="post-content">
                        <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&ignoreZeroCount=1&desc=0')->to($tags); ?>
                        <?php if($tags->have()): ?>
                            <div class="row">
                                <?php while ($tags->next()): ?>
                                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6 my-1">
                                        <a role="listitem" target="<?php $this->options->sidebarLinkOpen(); ?>" data-toggle="tooltip" data-placement="top" href="<?php $tags->permalink(); ?>" rel="tag" title="<?php $tags->count(); ?> 篇文章"><?php $tags->name(); ?> (<?php $tags->count(); ?>)</a>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center pb-2"><?php _e('没有任何标签'); ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            </main>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>