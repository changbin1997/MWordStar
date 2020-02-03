<?php

//  文章的自定义字段
function themeFields($layout) {
    //  文章头图来源
    $imageSource = new Typecho_Widget_Helper_Form_Element_Select('imageSource', array(
        'article' => '使用文章中的第一张图片作为文章头图',
        'url' => '在文章头图输入框手动输入图片URL',
        'hide' => '不显示文章头图'
    ), 'article', _t('文章头图来源'), _t('如果文章头图 URL 为空或文章内容中没有图片将不会显示文章头图。'));
    $layout->addItem($imageSource);

    //  文章头图
    $image = new Typecho_Widget_Helper_Form_Element_Text('thumb', NULL, NULL, _t('文章头图'), _t('如果您在文章头图来源中设置了手动输入图片 URL 的话，请在这里输入图片 URL。'));
    $layout->addItem($image);

    //  自定义文章摘要内容
    $summaryContent = new Typecho_Widget_Helper_Form_Element_Textarea('summaryContent', null, null, _t('自定义摘要内容'), _t('您可以在此处为文章定义摘要内容，此处定义的摘要内容不受字数限制。'));
    $layout->addItem($summaryContent);

    //  显示版权声明
    $articleCopyright = new Typecho_Widget_Helper_Form_Element_Select('articleCopyright', array(
        'show' => '显示',
        'hide' => '不显示'
    ), 'show', _t('显示原创声明'), _t('开启后会在本篇文章底部显示版权声明。'));
    $layout->addItem($articleCopyright);
}

//  外观设置
function themeConfig($form) {
    //  站点Logo
    $logoUrl = new Typecho_Widget_Helper_Form_Element_Text('logoUrl', NULL, NULL, _t('站点 Logo 地址'), _t('Logo 会显示在标签页的标题前面。'));
    $form->addInput($logoUrl);

    //  站点副标题
    $tagline = new Typecho_Widget_Helper_Form_Element_Text('tagline', null, '生命不息，折腾不止', _t('站点副标题'), _t('站点副标题会显示在标签页标题的后面。'));
    $form->addInput($tagline);

    //  ICP信息
    $icp = new Typecho_Widget_Helper_Form_Element_Text('icp', null, null, _t('ICP 备案号'), _t('ICP 备案号会显示在网站的底部。'));
    $form->addInput($icp);

    //  返回顶部按钮
    $toTop = new Typecho_Widget_Helper_Form_Element_Radio('toTop', array(
        'show' => '显示',
        'hide' => '不显示'
    ), 'show', _t('在右下方显示返回顶部按钮'));
    $form->addInput($toTop);

    //  侧边栏
    $sidebarBlock = new Typecho_Widget_Helper_Form_Element_Checkbox('sidebarBlock',
        array(
            'ShowBlogInfo' => _t('显示博客信息'),
            'ShowRecentPosts' => _t('显示最新文章'),
            'ShowRecentComments' => _t('显示最近回复'),
            'ShowCategory' => _t('显示分类'),
            'ShowTag' => _t('显示标签云'),
            'ShowArchive' => _t('显示归档'),
            'ShowOther' => _t('显示其它杂项'),
            'HideLoginLink' => _t('隐藏登录入口')
        ),
        array('ShowBlogInfo', 'ShowRecentPosts','ShowRecentComments', 'ShowCategory', 'ShowTag', 'ShowArchive', 'ShowOther'), _t('侧边栏显示'), _t('您可以在这里设置需要显示在侧边栏上的内容，这里的设置会影响到移动设备的侧边栏显示。如果设置为不显示将不会出现 HTML。')
    );
    $form->addInput($sidebarBlock->multiMode());

    //  侧边栏（移动端）
    $sidebarBlockM = new Typecho_Widget_Helper_Form_Element_Checkbox('sidebarBlockM',
        array(
            'HideBlogInfo' => _t('在移动设备上隐藏博客信息'),
            'HideRecentPosts' => _t('在移动设备上隐藏最新文章'),
            'HideRecentComments' => _t('在移动设备上隐藏最新回复'),
            'HideCategory' => _t('在移动设备上隐藏分类'),
            'HideTag' => _t('在移动设备上隐藏标签云'),
            'HideArchive' => _t('在移动设备上隐藏文章归档'),
            'HideOther' => _t('在移动设备上隐藏其它功能区域'),
            'HideLinks' => _t('隐藏首页和全站友情链接')
        ), null, _t('侧边栏显示（移动设备）'), _t('在移动设备上，侧边栏会显示在文章的下方。您可以在这里设置需要在移动设备上隐藏的侧边栏内容，这里设置的内容不会影响到 PC版 的显示。这里的移动设备包括平板电脑和手机。这里的隐藏只是看不到内容，HTML代码还是在的。')
    );
    $form->addInput($sidebarBlockM->multiMode());

    //  侧边栏博客信息博主头像地址
    $avatarUrl = new Typecho_Widget_Helper_Form_Element_Text('avatarUrl', null, null, _t('博主头像地址'), _t('博主头像会显示在侧边栏的博客信息区域，如果省略会使用默认头像。'));
    $form->addInput($avatarUrl);

    //  侧边栏博客信息区域博主昵称
    $nickname = new Typecho_Widget_Helper_Form_Element_Text('nickname', null, null, _t('博主昵称'), _t('博主昵称会显示在侧边栏博客信息区域，如果省略会显示博客站点名称。'));
    $form->addInput($nickname);

    //  侧边栏博客信息博主昵称链接
    $nicknameUrl = new Typecho_Widget_Helper_Form_Element_Text('nicknameUrl', null, null, _t('博主昵称链接调转地址'), _t('在侧边栏的博客信息区域会显示一个包含博主昵称的链接，在这里可以填写链接的跳转地址，如果省略会使用博客首页地址。'));
    $form->addInput($nicknameUrl);

    //  侧边栏博客信息博主简介
    $Introduction = new Typecho_Widget_Helper_Form_Element_Text('Introduction', null, null, _t('博主简介'), _t('博主简介会显示在侧边栏博客信息区域的博主昵称下方，如果省略会使用设置中的站点描述信息。'));
    $form->addInput($Introduction);

    //  侧边栏博客信息的运行天数
    $birthday = new Typecho_Widget_Helper_Form_Element_Text('birthday', null, null, _t('站点创建时间'), _t('在这里填写站点创建时间后，在侧边栏的博客信息区域就会显示网站运行天数。如果省略 网站运行天数会显示为 0 天。站点创建时间的格式为：yyyy-mm-dd，例如：2019-11-11。'));
    $form->addInput($birthday);

    //  文章头图设置
    $headerImage = new Typecho_Widget_Helper_Form_Element_Checkbox('headerImage', array(
        'home' => _t('在首页显示文章头图'),
        'sidebarBlock' => _t('在侧边栏的最新文章区域显示文章头图'),
        'post' => _t('在文章页显示文章头图')
    ), array('home', 'post'), _t('文章头图设置'));
    $form->addInput($headerImage->multiMode());

    //  文章头图背景颜色
    $headerImageBg = new Typecho_Widget_Helper_Form_Element_Radio('headerImageBg', array(
        'random' => '随机颜色',
        'gray' => '灰色',
        'white' => '白色'
    ), 'gray', _t('文章头图背景颜色'), _t('文章头图背景颜色是在图片加载完成之前或图片无法加载时显示的颜色，如果图片使用了透明背景是可以看到背景颜色的。'));
    $form->addInput($headerImageBg);

    //  Emoji面板
    $emojiPanel = new Typecho_Widget_Helper_Form_Element_Radio('emojiPanel', array(
        'on' => '开启',
        'off' => '关闭'
    ), 'off', _t('评论区Emoji表情选择面板'), _t('开启后会在评论区的评论内容输入框下方显示一个 Emoji表情按钮，点击后会显示一个 Emoji表情面板。'));
    $form->addInput($emojiPanel);

    //  导航栏
    $navBar = new Typecho_Widget_Helper_Form_Element_Checkbox('navbar', array(
        'showClassification' => _t('显示文章分类')
    ), null, _t('导航栏'));
    $form->addInput($navBar->multiMode());

    //  导航栏颜色
    $navColor = new Typecho_Widget_Helper_Form_Element_Radio('navColor', array(
        'light' => '亮色',
        'dark' => '暗色'
    ), 'light', _t('导航栏颜色'));
    $form->addInput($navColor);

    //  文章摘要字数
    $summary = new Typecho_Widget_Helper_Form_Element_Text('summary', NULL, '120', _t('文章摘要字数'), _t('首页、分类页、标签页、搜索页 的文章摘要字数，默认为：120个字。'));
    $form->addInput($summary);

    //  首页友链
    $homeLinks = new Typecho_Widget_Helper_Form_Element_Textarea('homeLinks', NULL, NULL, _t('首页友情链接'), _t('首页友情链接只会显示在首页的侧边栏，需要 JSON 格式数据。如需查看详细说明可以访问：https://www.misterma.com/archives/819/。'));
    $form->addInput($homeLinks);

    //  全站友链
    $links = new Typecho_Widget_Helper_Form_Element_Textarea('links', NULL, NULL, _t('全站友情链接'), _t('全站友情链接会在每个页面的侧边栏显示，需要 JSON 格式数据。如需查看详细说明可以访问：https://www.misterma.com/archives/819/。'));
    $form->addInput($links);

    //  独立页友链
    $pageLinks = new Typecho_Widget_Helper_Form_Element_Textarea('pageLinks', null, null, _t('独立页友情链接'), _t('独立页友情链接只会在友情链接的页面显示，需要 JSON 格式 数据。如果要使用独立页友情链接需要创建一个独立页面，把 自定义模板设置为 友情链接。如需查看详细说明可以访问：https://www.misterma.com/archives/819/。'));
    $form->addInput($pageLinks);

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
        if (!in_array($cid, $views)) {
            //  如果cookie不存在才会加1
            $db->query($db->update('table.contents')->rows(array('views' => (int)$row['views'] + 1))->where('cid = ?', $cid));
            array_push($views, $cid);
            $views = implode(',', $views);
            Typecho_Cookie::set('extend_contents_views', $views);  //  记录查看cookie
        }
    }
    return $row['views'];
}

//  生成文章头图背景颜色
function headerImageBgColor($color) {
    if ($color == null or $color == '') {
        return '#CCCCCC';
    }

    $bgColor = array(
        'random' => 'rgb(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ')',
        'gray' => '#CCCCCC',
        'white' => '#FFFFFF'
    );
    return $bgColor[$color];
}

//  根据设置获取文章头图
function postImg($a) {
    if (!$a->fields->imageSource) {
        $img = getPostImg($a);
        return $img == 'none'?false:$img;
    }
    switch ($a->fields->imageSource) {
        case 'url':
            return $a->fields->thumb?$a->fields->thumb:false;
            break;
        case 'article':
            $img = getPostImg($a);
            return $img == 'none'?false:$img;
            break;
        default:
            return false;
            break;
    }
}

//  获取文章的第一张图片
function getPostImg($archive) {
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    $rs = $db->fetchRow($db->select('table.contents.text')
        ->from('table.contents')
        ->where('cid=?', $cid));
    $text = $rs['text'];
    if (0 === strpos($text, '<!--markdown-->')) {
        preg_match('/!\[[^\]]*]\([^\)]*\)/i', $text, $img);
        if (empty($img)) {
            return 'none';
        } else {
            preg_match("/(?:\()(.*)(?:\))/i", $img[0], $result);
            $img_url = $result[1];
            return $img_url;
        }
    } else {
        preg_match_all("/\<img.*?src\=\"(.*?)\"[^>]*>/i", $text, $img);
        if (empty($img)) {
            return 'none';
        } else {
            $img_url = $img[1][0];
            return $img_url;
        }
    }
}