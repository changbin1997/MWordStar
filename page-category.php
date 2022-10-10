<?php
/**
 * 分类目录
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'page-category';
$this->need('components/header.php');
?>

<div class="container category-page main-content mb-0">
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
                        <p>共包含 <?php echo categoryCount(); ?> 个分类</p>
                        <?php $this->widget('Widget_Metas_Category_List')->to($category); ?>
                        <ul class="category-list pl-0 pl-2">
                            <?php while ($category->next()): ?>
                                <li>
                                    <i class="icon-folder-open icon mr-1"></i>
                                    <a data-toggle="tooltip" data-placement="top" href="<?php $category->permalink(); ?>" title="<?php if ($category->parent > 0) echo getParentCategory($category->parent) . ' 下的子分类'; ?> <?php $category->description(); ?>">
                                        <?php echo $category->name(); ?>(<?php $category->count(); ?>)
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </article>
            </main>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>
