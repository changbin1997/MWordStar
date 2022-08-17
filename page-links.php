<?php
/**
 * 友情链接
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$GLOBALS['page'] = 'page-links';

$linkArr = array();
//  是否包含内页链接
if ($this->options->pageLinks) {
    array_push($linkArr, array(
        'title' => '内页链接',
        'links' => json_decode($this->options->pageLinks)
    ));
}
//  是否包含首页链接
if ($this->options->homeLinks) {
    array_push($linkArr, array(
        'title' => '首页链接',
        'links' => json_decode($this->options->homeLinks)
    ));
}
//  是否包含全站链接
if ($this->options->links) {
    array_push($linkArr, array(
        'title' => '全站链接',
        'links' => json_decode($this->options->links)
    ));
}
$this->need('components/header.php');  //  头文件
?>

<div class="container link-page main-content mb-0">
    <?php if ($this->options->breadcrumb == 'on'): ?>
        <nav aria-label="路径" class="breadcrumb-nav">
            <ol class="breadcrumb m-0 p-0">
                <li class="breadcrumb-item">
                    <a href="<?php $this->options->siteUrl(); ?>">首页</a>
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
                    <div data-target="<?php $this->options->postLinkOpen(); ?>" class="post-content" data-code-line-num="<?php $this->options->codeLineNum(); ?>">
                        <?php if (count($linkArr)): ?>
                            <?php foreach ($linkArr as $link): ?>
                                <h3><?php echo $link['title']; ?></h3>
                                <div class="row link-box" role="group" aria-label="<?php echo $link['title']; ?>">
                                    <?php foreach ($link['links'] as $val): ?>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 link-grid">
                                            <div class="link">
                                                <a href="<?php echo $val->url; ?>" class="clearfix" target="_blank">
                                                    <?php if (isset($val->logoUrl)): ?>
                                                        <img src="<?php echo $val->logoUrl; ?>" alt="站点Logo" class="link-logo float-left rounded-circle">
                                                    <?php else: ?>
                                                        <i class="link-logo float-left icon-link icon-logo rounded-circle" aria-label="站点Logo"></i>
                                                    <?php endif; ?>
                                                    <span class="link-name float-left">
                                                        <?php echo $val->name; ?>
                                                    </span>
                                                </a>
                                                <p title="<?php echo isset($val->title)?$val->title:'暂无简介'; ?>">
                                                    <?php echo isset($val->title)?$val->title:'暂无简介'; ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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