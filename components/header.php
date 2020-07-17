<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$color = color($this->options->color);  //  获取颜色设置
$rounded = $this->options->rounded == 'rightAngle'?'rounded-0':'';  //  获取元素风格设置
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
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
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/bootstrap.min.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/icon.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/style.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/vs2015.css'); ?>" type="text/css">
    <script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/jquery-3.4.1.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/bootstrap.bundle.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/highlight.pack.js'); ?>"></script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/jquery.qrcode.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/app.js'); ?>"></script>
    <?php if ($this->is('post') && $this->fields->keywords): ?>
        <?php $this->header('keywords=' . $this->fields->keywords); ?>
    <?php else: ?>
        <?php $this->header(); ?>
    <?php endif; ?>
    <?php if ($this->options->cssCode): ?>
        <style type="text/css">
            <?php $this->options->cssCode(); ?>
        </style>
    <?php endif; ?>
    <?php if ($this->options->headHTML): ?>
        <?php $this->options->headHTML(); ?>
    <?php endif; ?>
</head>
<body data-rounded="<?php echo $rounded; ?>">
<header class="sticky-top">
    <nav class="navbar navbar-expand-lg <?php echo $color['bar'] ?>">
        <div class="container">
            <a class="navbar-brand" href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title(); ?> 首页"><?php $this->options->title(); ?></a>
            <button class="navbar-toggler border-0 px-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="导航菜单">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item <?php echo $this->is('index')?'active':''; ?>">
                        <a class="nav-link" <?php if($this->is('index')): ?> <?php endif; ?> href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a>
                    </li>
                    <?php if ($this->options->navbar && in_array('showClassification', $this->options->navbar)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:;" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                文章分类
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <?php $this->widget('Widget_Metas_Category_List')->parse('<a class="dropdown-item" href="{permalink}">{name}</a>'); ?>
                            </div>
                        </li>
                    <?php endif; ?>
                    <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                    <?php while($pages->next()): ?>
                        <li class="nav-item <?php echo $this->is('page', $pages->slug)?'active':''; ?>">
                            <a class="nav-link" href="<?php $pages->permalink(); ?>"><?php $pages->title(); ?></a>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php if (is_array($this->options->navbar) && in_array('showSearch', $this->options->navbar)): ?>
                    <form class="form-inline search-form" action="<?php $this->options->siteUrl(); ?>" method="post" role="search">
                        <div class="input-group">
                            <input data-url="<?php $this->options->siteUrl(); ?>" class="border-right-0 form-control form-control-md search-input <?php echo $rounded; ?>" type="text" placeholder="搜索" aria-label="搜索" required="required" name="s">
                            <div class="input-group-append">
                                <button class="btn my-sm-0 search-btn-light <?php echo $rounded; ?>" type="submit" aria-label="搜索" title="搜索" data-toggle="tooltip" data-placement="bottom">
                                    <i class="icon-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>