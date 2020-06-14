<?php
/**
 * 友情链接
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$color = color($this->options->color);
$rounded = $this->options->rounded == 'rightAngle'?'rounded-0':'';  //  获取元素风格设置
$this->need('components/header.php');  //  头文件
?>

<div class="container link-page main-content mb-0">
    <?php if ($this->options->breadcrumb == 'on'): ?>
        <nav aria-label="路径" class="breadcrumb-nav">
            <ol class="breadcrumb m-0 p-0">
                <li class="breadcrumb-item">
                    <a href="<?php $this->options->siteUrl(); ?>" class="<?php echo $color['link']; ?>">首页</a>
                </li>
                <li tabindex="0" class="breadcrumb-item active" aria-current="page"><?php $this->title(); ?></li>
            </ol>
        </nav>
    <?php endif; ?>
    <div class="row">
        <div class="archive col-md-12 col-lg-8 col-sm-12 content-area">
            <main class="<?php echo $rounded; ?>">
                <header class="entry-header border-bottom">
                    <h2 class="entry-title p-name">
                        <a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                    </h2>
                </header>
                <article>
                    <div data-target="<?php $this->options->postLinkOpen(); ?>" class="post-content" data-color="<?php echo $color['link']; ?>">
                        <!--内页链接区域-->
                        <?php if ($this->options->pageLinks): ?>
                            <h3>内页链接</h3>
                            <?php $linkList = json_decode($this->options->pageLinks); ?>
                            <div class="row link-box" role="group" aria-label="内页链接">
                                <?php for ($i = 0;$i < count($linkList);$i ++): ?>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 link-grid">
                                        <div class="link">
                                            <a href="<?php echo $linkList[$i]->url; ?>" class="clearfix" target="_blank">
                                                <?php if (!isset($linkList[$i]->logoUrl) or $linkList[$i]->logoUrl == ''): ?>
                                                    <i class="link-logo float-left icon-link icon-logo rounded-circle" aria-label="站点Logo"></i>
                                                <?php else: ?>
                                                    <img src="<?php echo $linkList[$i]->logoUrl; ?>" alt="站点Logo" class="link-logo float-left rounded-circle">
                                                <?php endif; ?>
                                                <span class="link-name float-left"><?php echo $linkList[$i]->name; ?></span>
                                            </a>
                                            <?php if (!isset($linkList[$i]->title) or $linkList[$i]->title == ''): ?>
                                                <p>暂无简介</p>
                                            <?php else: ?>
                                                <p title="<?php echo $linkList[$i]->title; ?>"><?php echo $linkList[$i]->title; ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                        <!--全站链接区域-->
                        <?php if ($this->options->links): ?>
                        <h3>全站链接</h3>
                        <?php $linkList = json_decode($this->options->links); ?>
                        <div class="row link-box" role="group" aria-label="全站链接">
                            <?php for ($i = 0;$i < count($linkList);$i ++): ?>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12 link-grid">
                                    <div class="link">
                                        <a href="<?php echo $linkList[$i]->url; ?>" class="clearfix" target="_blank">
                                            <?php if (!isset($linkList[$i]->logoUrl) or $linkList[$i]->logoUrl == ''): ?>
                                                <i class="link-logo float-left icon-link icon-logo rounded-circle" aria-label="站点Logo"></i>
                                            <?php else: ?>
                                                <img src="<?php echo $linkList[$i]->logoUrl; ?>" alt="站点Logo" class="link-logo float-left rounded-circle">
                                            <?php endif; ?>
                                            <span class="link-name float-left"><?php echo $linkList[$i]->name; ?></span>
                                        </a>
                                        <?php if (!isset($linkList[$i]->title) or $linkList[$i]->title == ''): ?>
                                            <p>暂无简介</p>
                                        <?php else: ?>
                                            <p title="<?php echo $linkList[$i]->title; ?>"><?php echo $linkList[$i]->title; ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                        <!--首页链接区域-->
                        <?php if ($this->options->homeLinks): ?>
                        <h3>首页链接</h3>
                        <?php $links = json_decode($this->options->homeLinks); ?>
                        <div class="row link-box" role="group" aria-label="首页链接">
                            <?php for ($i = 0;$i < count($links);$i ++): ?>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12 link-grid">
                                    <div class="link">
                                        <a href="<?php echo $links[$i]->url; ?>" target="_blank" class="clearfix">
                                            <i class="link-logo float-left icon-link icon-logo rounded-circle"></i>
                                            <span class="link-name float-left"><?php echo $links[$i]->name; ?></span>
                                        </a>
                                        <?php if (!isset($links[$i]->title) or $links[$i]->title == ''): ?>
                                            <p>暂无简介</p>
                                        <?php else: ?>
                                            <p title="<?php echo $links[$i]->title; ?>"><?php echo $links[$i]->title; ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
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