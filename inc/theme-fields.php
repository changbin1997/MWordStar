<?php

// 文章的自定义字段
function themeFields($layout) {
    //  文章列表显示设置
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Select('postListStyle', array(
        'default' => '使用系统设置',
        'fullText' => '文章列表直接显示全文',
        'summary' => '文章列表显示摘要和文章头图'
    ), 'default', _t('文章列表显示'), _t('文章列表包括首页、搜索页、归档 左侧的文章列表。在显示全文的情况下，文章列表不会显示文章头图，显示全文也支持使用 <b style="color: #C7254E;">&lt!--more--&gt</b> 来手动分隔摘要。')));

    // 文章头图显示设置
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Select('headerImgDisplay', array(
        'default' => '使用系统设置',
        'post-page-list' => '在文章列表和文章页显示文章头图',
        'post-list' => '只在文章列表显示文章头图',
        'post-page' => '只在文章页显示文章头图',
        'hide' => '不显示文章头图'
    ), 'default', _t('文章头图显示设置'), _t('您可以单独给文章设置文章头图显示。')));

    // 文章头图样式
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Select('postListHeaderImageStyle', array(
        'default' => '使用系统设置',
        'max' => '大图（文章头图在上方，标题和摘要在下方）',
        'mini' => '小图（图片在左侧，文章摘要在右侧）'
    ), 'default', _t('文章列表的头图样式'), _t('您可以给文章设置单独的文章头图样式。大图的图片长宽比为 8 比 3，小图的长宽比为 6 比 4，如果图片长宽比不符合要求，主题会自动裁剪图片来适配长宽比。')));

    // 文章头图来源
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Select('imageSource', array(
        'article' => '使用文章中的第一张图片作为文章头图',
        'url' => '在文章头图输入框手动输入图片URL',
        'default' => '使用系统设置'
    ), 'article', _t('文章头图来源'), _t('如果选择了使用文章中的第一张图片作为文章头图，在文章不包含图片的情况下将不会显示文章头图。如果选择了使用系统设置，需要在主题设置的默认文章头图输入框填写图片 URL，系统会在默认文章头图中随机选择一个 URL 加载。')));

    // 文章头图
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('thumb', null, null, _t('文章头图'), _t('如果您在文章头图来源中设置了手动输入图片 URL 的话，请在这里输入图片 URL。')));

    // 自定义文章摘要内容
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Textarea('summaryContent', null, null, _t('自定义摘要内容'), _t('您可以在此处为文章定义摘要内容，此处定义的摘要内容不受字数限制。')));

    // 显示版权声明
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Select('articleCopyright', array(
        'show' => '显示',
        'hide' => '不显示'
    ), 'show', _t('显示原创声明'), _t('开启后会在本篇文章底部显示版权声明。')));

    // 自定义关键词
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('keywords', null, null, _t('自定义关键词'), _t('您可以输入这篇文章的关键词，多个关键词之间用英文逗号分隔，如果为空 会使用这篇文章的标签作为关键词。')));

    // 文章有效期
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('expired', null, '0', _t('文章有效期'), _t('有的文章可能只是在某个时间段内有用，发布后如果长时间不更新的话，可能会给读者带去错误的信息。文章有效期可以设置一个天数，过了指定天数后，在文章开头会显示一条警示信息。0 或留空不显示。')));
}