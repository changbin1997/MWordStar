<?php

//  文章的自定义字段
function themeFields($layout) {
    $image = new Typecho_Widget_Helper_Form_Element_Text('thumb', NULL, NULL, _t('文章头图'), _t('文章头图会显示在文章的顶部。'));
    $layout->addItem($image);
}

//  外观设置
function themeConfig($form) {
    //  站点Logo
    $logoUrl = new Typecho_Widget_Helper_Form_Element_Text('logoUrl', NULL, NULL, _t('站点 Logo 地址'), _t('Logo 会显示在标签页的标题前面。'));
    $form->addInput($logoUrl);
    //  站点副标题
    $tagline = new Typecho_Widget_Helper_Form_Element_Text('tagline', NULL, NULL, _t('站点副标题'), _t('站点副标题会显示在标签页标题的后面。'));
    $form->addInput($tagline);
    //  侧边栏
    $sidebarBlock = new Typecho_Widget_Helper_Form_Element_Checkbox('sidebarBlock',
        array(
            'ShowSocialInfo' => _t('显示社交信息'),
            'ShowRecentPosts' => _t('显示最新文章'),
            'ShowRecentComments' => _t('显示最近回复'),
            'ShowCategory' => _t('显示分类'),
            'ShowTag' => _t('显示标签云'),
            'ShowArchive' => _t('显示归档'),
            'ShowOther' => _t('显示其它杂项'),
            'HideLoginLink' => _t('隐藏登录入口')
        ),
        array('ShowRecentPosts', 'ShowRecentComments', 'ShowCategory', 'ShowTag', 'ShowArchive', 'ShowOther'), _t('侧边栏显示')
    );
    $form->addInput($sidebarBlock->multiMode());
    //  社交信息
    $socialInfo = new Typecho_Widget_Helper_Form_Element_Textarea('socialInfo', null, null, _t('社交信息'), _t('需要 JSON 格式，社交信息会显示在侧边栏。如需查看详细说明可以访问：https://www.misterma.com/archives/812/。'));
    $form->addInput($socialInfo);
    //  文章摘要字数
    $summary = new Typecho_Widget_Helper_Form_Element_Text('summary', NULL, NULL, _t('文章摘要字数'), _t('文章摘要字数，默认为：150 个字'));
    $form->addInput($summary);
    //  首页友链
    $homeLinks = new Typecho_Widget_Helper_Form_Element_Textarea('homeLinks', NULL, NULL, _t('全站友情链接'), _t('全站友情链接会在每个页面的下方显示，格式为：JSON。如需查看详细说明可以访问：https://www.misterma.com/archives/812/。'));
    $form->addInput($homeLinks);
    //  独立页友链
    $links = new Typecho_Widget_Helper_Form_Element_Textarea('links', NULL, NULL, _t('独立页友情链接'), _t('独立页友情链接只会在友情链接的页面显示，要求格式为：JSON。如果要使用独立页友情链接需要创建一个独立页面，把 自定义模板设置为：友情链接。如需查看详细说明可以访问：https://www.misterma.com/archives/812/。'));
    $form->addInput($links);
    //  自定义CSS
    $cssCode = new Typecho_Widget_Helper_Form_Element_Textarea('cssCode', NULL, NULL, _t('自定义 CSS'), _t('通过自定义 CSS 您可以很方便的设置页面样式，自定义 CSS 不会影响网站源代码。'));
    $form->addInput($cssCode);
    //  自定义 head 输出的 HTML
    $headHTML = new Typecho_Widget_Helper_Form_Element_Textarea('headHTML', null, null, _t('自定义 head 区域输出的 HTML'), _t('head 区域的 HTML 会在 head 内输出，可以用来定义一些网站统计的 JS 之类的。'));
    $form->addInput($headHTML);
    //  自定义 body 底部的 HTML
    $bodyHTML = new Typecho_Widget_Helper_Form_Element_Textarea('bodyHTML', null, null, _t('自定义 body 底部输出的 HTML'), _t('body 底部的 HTML 会在 footer 之后 body 尾部之前输出。'));
    $form->addInput($bodyHTML);
}

//  统计文章阅读量
function getPostView($archive) {
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')))) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0;');
        return 0;
    }
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    if ($archive->is('single')) {
        $views = Typecho_Cookie::get('extend_contents_views');
        if (empty($views)) {
            $views = array();
        } else {
            $views = explode(',', $views);
        }
        if (!in_array($cid, $views)) {//如果cookie不存在才会加1
            $db->query($db->update('table.contents')->rows(array('views' => (int)$row['views'] + 1))->where('cid = ?', $cid));
            array_push($views, $cid);
            $views = implode(',', $views);
            Typecho_Cookie::set('extend_contents_views', $views); //记录查看cookie
        }
    }
    return $row['views'];
}

//  输出社交信息
function socialInfo($info) {
    $icon = array(
        'facebook' => array(
            'icon' => 'icon-facebook',
            'name' => 'Facebook'
        ),
        'twitter' => array(
            'icon' => 'icon-twitter',
            'name' => 'Twitter'
        ),
        'weibo' => array(
            'icon' => 'icon-sina-weibo',
            'name' => '微博'
        ),
        'instagram' => array(
            'icon' => 'icon-instagram',
            'name' => 'Instagram'
        ),
        'github' => array(
            'icon' => 'icon-github',
            'name' => 'Github'
        ),
        'telegram' => array(
            'icon' => 'icon-telegram',
            'name' => 'Telegram'
        ),
        'linkedin' => array(
            'icon' => 'icon-linkedin2',
            'name' => 'LinkedIn'
        ),
        'steam' => array(
            'icon' => 'icon-steam',
            'name' => 'Steam'
        )
    );

    $info = json_decode($info);
    foreach ($info as $val) {
        echo '<a class="' . $icon[$val->name]['icon'] . '" href="' . $val->url . '" title="' . $icon[$val->name]['name'] . '" aria-label="' . $icon[$val->name]['name'] . '" target="_blank"></a>';
    }
}