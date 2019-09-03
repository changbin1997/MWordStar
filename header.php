<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
//  获取巨幕的配置信息
if ($this->options->Jumbotron && in_array('showJumbotron', $this->options->Jumbotron)) {
    $Jumbotron = true;
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php
            $this->archiveTitle(array(
                'category'  =>  _t('分类 %s 下的文章'),
                'search'    =>  _t('包含关键字 %s 的文章'),
                'tag'       =>  _t('标签 %s 下的文章'),
                'author'    =>  _t('%s 发布的文章')
            ), '', ' - ');
        ?>
        <?php $this->options->title(); ?>
        <?php echo $this->is('index')?'- ' . $this->options->tagline:''; ?>
    </title>
    <link rel="icon" href="<?php echo $this->options->logoUrl?$this->options->logoUrl:$this->options->siteUrl . 'favicon.ico'; ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/bootstrap.min.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/icon.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/style.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/vs2015.css'); ?>" type="text/css">
    <script type="text/javascript" src="<?php $this->options->themeUrl('js/jquery-3.4.1.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('js/highlight.pack.js'); ?>"></script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('js/app.js'); ?>"></script>
    <?php $this->header(); ?>
    <?php if ($this->options->cssCode): ?>
        <style type="text/css">
            <?php $this->options->cssCode(); ?>
        </style>
    <?php endif; ?>
    <?php if ($this->options->headHTML): ?>
        <?php $this->options->headHTML(); ?>
    <?php endif; ?>
</head>
<body>
<header class="<?php echo $Jumbotron?'position-fixed':'sticky-top'; ?>">
    <nav class="navbar navbar-expand-lg navbar-dark <?php echo !$Jumbotron?'bg-dark':''; ?>">
        <div class="container">
            <a class="navbar-brand" href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title(); ?> 首页"><?php $this->options->title(); ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="导航菜单">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item <?php echo $this->is('index')?'active':''; ?>">
                        <a class="nav-link" <?php if($this->is('index')): ?> <?php endif; ?> href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a>
                    </li>
                    <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                    <?php while($pages->next()): ?>
                        <li class="nav-item <?php echo $this->is('page', $pages->slug)?'active':''; ?>">
                            <a class="nav-link" <?php if($this->is('page', $pages->slug)): ?> <?php endif; ?> href="<?php $pages->permalink(); ?>"><?php $pages->title(); ?></a>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <form class="form-inline" action="<?php $this->options->siteUrl(); ?>" method="post" role="search">
                    <div class="input-group">
                        <input class="form-control form-control-md" type="search" placeholder="搜索" aria-label="搜索" required="required" name="s">
                        <div class="input-group-append">
                            <button class="btn btn-primary my-sm-0" type="submit">搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </nav>
</header>
<?php if ($Jumbotron): ?>
    <div class="jumbotron jumbotron-fluid" STYLE="background-image: url(<?php $this->options->JumbotronBG?$this->options->JumbotronBG():$this->options->themeUrl('img/bg.jpg'); ?>)">
        <div class="container">
            <h1 class="text-center">
                <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>
            </h1>
            <?PHP if ($this->options->tagline): ?>
                <p class="text-center"><?php $this->options->tagline(); ?></p>
            <?php endif; ?>
            <?php if ($this->options->socialInfo): ?>
                <nav class="social-info">
                    <?php socialInfo($this->options->socialInfo); ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>