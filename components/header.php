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

// 导航栏自定义链接
$navLinks = null;
if ($this->options->navLinks) $navLinks = json_decode($this->options->navLinks);

// body class
$bodyClass = array(
    // 代码高亮主题
    $codeThemeColor,
    // 是否开启代码高亮
    $this->options->codeHighlight,
    // 主题配色模式
    $themeColor
);
// 如果开启了代码块高亮就把代码块行号设置添加到 body class
if ($this->options->codeHighlight == 'enable-highlight') {
    $bodyClass[] = 'line-num-' . $this->options->codeLineNum;
}
// 把 body class 数组转为字符串，方便直接输出
$bodyClass = implode(' ', $bodyClass);
?>
<!doctype html>
<html lang="<?php echo $GLOBALS['language']; ?>">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php
        $this->archiveTitle(array(
            'category' => $GLOBALS['t']['archive']['postsUnderTheCategory'],
            'search' => $GLOBALS['t']['archive']['postsContainingTheKeyword'],
            'tag' => $GLOBALS['t']['archive']['postsTagged'],
            'author' => $GLOBALS['t']['archive']['postsByAuthor']
        ), '', ' - ');
        ?>
        <?php $this->options->title(); ?>
        <?php echo $this->is('index')?'- ' . $this->options->tagline:''; ?>
    </title>
    <link rel="icon" href="<?php echo $this->options->logoUrl?$this->options->logoUrl:$this->options->siteUrl . 'favicon.ico'; ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/bootstrap.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/icon.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/style.css'); ?>" type="text/css">
    <?php localizeScript(); ?>
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
<body class="<?php echo $bodyClass; ?>" data-rounded="<?php $this->options->rounded(); ?>" data-color="<?php echo $themeColor; ?>" data-code-theme="<?php echo $codeThemeColorSelected; ?>" data-pjax="<?php $this->options->pjax(); ?>">
<?php if ($this->options->pjax == 'on' && $this->options->pjaxProgressBar == 'on'): ?>
    <div id="progress-bar" style="display: none;">
        <div id="progress" style="background: <?php $this->options->pjaxProgressBarColor(); ?>;" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
    </div>
<?php endif; ?>
<header class="sticky-top">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <?php if ($this->options->navLogoUrl): ?>
                <a class="navbar-brand" href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title(); ?>">
                    <img src="<?php $this->options->navLogoUrl(); ?>" alt="<?php $this->options->title(); ?>" height="<?php $this->options->navLogoHeight(); ?>">
                </a>
            <?php else: ?>
                <a class="navbar-brand" href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title(); ?> 首页"><?php $this->options->title(); ?></a>
            <?php endif; ?>
            <button class="navbar-toggler border-0 px-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?php echo $GLOBALS['t']['header']['navigationMenu']; ?>">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item <?php echo $this->is('index')?'active':''; ?>">
                        <a class="nav-link" href="<?php $this->options->siteUrl(); ?>" <?php if ($this->is('index')) echo 'aria-current="page"'; ?>><?php echo $GLOBALS['t']['header']['home']; ?></a>
                    </li>
                    <?php if ($this->options->navbar && in_array('showClassification', $this->options->navbar)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:;" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo $GLOBALS['t']['sidebar']['categories']; ?>
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
                    <?php if ($this->options->navLinks && is_array($navLinks)): ?>
                        <?php foreach ($navLinks as $link): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $link->url; ?>"><?php echo $link->name; ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <?php if (is_array($this->options->navbar) && in_array('showSearch', $this->options->navbar)): ?>
                    <form class="form-inline search-form" action="<?php $this->options->siteUrl(); ?>" method="post" role="search">
                        <div class="input-group">
                            <input data-url="<?php $this->options->siteUrl(); ?>" class="border-right-0 form-control form-control-md search-input" type="text" placeholder="<?php echo $GLOBALS['t']['header']['search']; ?>" aria-label="<?php echo $GLOBALS['t']['header']['search']; ?>" required="required" name="s">
                            <div class="input-group-append">
                                <button class="btn my-sm-0" type="submit" aria-label="<?php echo $GLOBALS['t']['header']['search']; ?>" title="<?php echo $GLOBALS['t']['header']['search']; ?>" data-toggle="tooltip" data-placement="bottom">
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