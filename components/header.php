<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 检测是否包含主题配色 cookie
if (isset($_COOKIE['themeColor'])) {
    // 如果 cookie 存储的浅色和默认浅色不一样
    if ($_COOKIE['themeColor'] != 'dark-color' && $_COOKIE['themeColor'] != $this->options->defaultLightColor) {
        // 重新设置 cookie
        setcookie('themeColor', $this->options->defaultLightColor, time() + 15552000, '/');
    }
    // 根据主题配色 cookie 设置配色
    $themeColor = $_COOKIE['themeColor'];
}else {
    // 如果不包含主题配色 cookie 就使用后台设置的默认配色
    $themeColor = $this->options->color;
}

// 获取代码高亮主题
$codeThemeColorSelected = $this->options->codeThemeColor;
$codeThemeColor = $codeThemeColorSelected;
// 根据主题配色模式来设置代码高亮主题
if ($codeThemeColor == 'auto') {
    $codeThemeColor = $themeColor == 'dark-color'?'github-dark':'stackoverflow-light';
}
// 如果代码高亮被禁用就不输出代码高亮主题设置
if ($this->options->codeHighlight != 'enable-highlight') {
    $codeThemeColor = 'code-theme-none';
}
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
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/bootstrap.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/icon.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/style.css'); ?>" type="text/css">
    <?php if ($this->is('post') && $this->fields->keywords or $this->fields->summaryContent): ?>
        <?php
        $metaContent = array();
        // 如果设置了自定义关键词就显示自定义关键词
        if ($this->fields->keywords) $metaContent['keywords'] = $this->fields->keywords;
        // 如果设置了自定义摘要内容就显示自定义摘要
        if ($this->fields->summaryContent) $metaContent['description'] = $this->fields->summaryContent;
        // 把包含自定义关键词和摘要的数组转为 URL 查询格式
        $metaContent = urldecode(http_build_query($metaContent));
        $this->header($metaContent);
        ?>
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
<body class="<?php echo $codeThemeColor; ?> <?php $this->options->codeHighlight(); ?> <?php echo $themeColor; ?>" data-rounded="<?php $this->options->rounded(); ?>" data-color="<?php echo $themeColor; ?>" data-code-theme="<?php echo $codeThemeColorSelected; ?>" data-pjax="<?php $this->options->pjax(); ?>">
<?php if ($this->options->pjax == 'on' && $this->options->pjaxProgressBar == 'on'): ?>
    <div id="progress-bar" style="display: none;">
        <div id="progress" style="background: <?php $this->options->pjaxProgressBarColor(); ?>;" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
    </div>
<?php endif; ?>
<header class="sticky-top">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title(); ?> 首页"><?php $this->options->title(); ?></a>
            <button class="navbar-toggler border-0 px-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="导航菜单">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item <?php echo $this->is('index')?'active':''; ?>">
                        <a class="nav-link" href="<?php $this->options->siteUrl(); ?>" <?php if ($this->is('index')) echo 'aria-current="page"'; ?>><?php _e('首页'); ?></a>
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
                            <a class="nav-link" href="<?php $pages->permalink(); ?>" <?php if ($this->is('page', $pages->slug)) echo 'aria-current="page"'; ?>><?php $pages->title(); ?></a>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php if (is_array($this->options->navbar) && in_array('showSearch', $this->options->navbar)): ?>
                    <form class="form-inline search-form" action="<?php $this->options->siteUrl(); ?>" method="post" role="search">
                        <div class="input-group">
                            <input data-url="<?php $this->options->siteUrl(); ?>" class="border-right-0 form-control form-control-md search-input" type="text" placeholder="搜索" aria-label="搜索" required="required" name="s">
                            <div class="input-group-append">
                                <button class="btn my-sm-0" type="submit" aria-label="搜索" title="搜索" data-toggle="tooltip" data-placement="bottom">
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