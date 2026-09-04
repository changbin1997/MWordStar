<?php

/**
 * MWordStar 主题 - 文章头图
 *
 * 包含函数：
 *  - headerImageBgColor                生成文章头图背景颜色
 *  - headerImageDisplay                头图显示策略分发
 *  - postImg                           根据设置获取头图
 *  - getPostImg                        提取文章第一张图片
 *  - randomHeaderImage                 随机头图
 *  - getPostListHeaderImageStyle       头图样式（max / mini）判断
 *
 * @package MWordStar
 */

/**
 * 生成文章头图背景颜色
 *
 * @param string $color 头图颜色设置
 * @return string 返回颜色
 */
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

/**
 * 获取文章头图显示设置
 *
 * @param object $t 文章
 * @param array $options 全局的文章头图显示设置
 * @param string $defaultImageUrl 默认头图 URL
 * @return false|string 文章头图 URL
 */
function headerImageDisplay($t, $options, $defaultImageUrl) {
    // 在文章列表和文章页显示文章头图
    if ($t->fields->headerImgDisplay == 'post-page-list') {
        return postImg($t, $defaultImageUrl);
    }
    // 在文章列表显示文章头图
    if ($t->fields->headerImgDisplay == 'post-list' && $t->is('index') or $t->fields->headerImgDisplay == 'post-list' && $t->is('archive')) {
        return postImg($t, $defaultImageUrl);
    }
    // 在文章页显示文章头图
    if ($t->fields->headerImgDisplay == 'post-page' && $t->is('post') or $t->fields->headerImgDisplay == 'post-page' && $t->is('page')) {
        return postImg($t, $defaultImageUrl);
    }
    // 使用系统文章头图设置
    if ($t->fields->headerImgDisplay == 'default' or $t->fields->headerImgDisplay == null) {
        // 在文章列表显示头图
        if (is_array($options) && in_array('home', $options) && $t->is('index')) {
            return postImg($t, $defaultImageUrl);
        }
        // 在分类页、标签页、归档页的文章列表显示文章头图
        if (is_array($options) && in_array('home', $options) && $t->is('archive')) {
            return postImg($t, $defaultImageUrl);
        }
        // 在文章页显示头图
        if (is_array($options) && in_array('post', $options) && $t->is('post')) {
            return postImg($t, $defaultImageUrl);
        }
        // 在独立页显示文章头图
        if (is_array($options) && in_array('post', $options) && $t->is('page')) {
            return postImg($t, $defaultImageUrl);
        }
    }
    // 不显示文章头图
    if ($t->fields->headerImgDisplay == 'hide') return false;
    return false;
}

/**
 * 根据设置获取文章头图
 *
 * @param object $a 文章
 * @param string $defaultUrl 默认文章头图 URL
 * @return false|mixed 文章头图 URL
 */
function postImg($a, $defaultUrl) {
    // 手动输入文章头图
    if ($a->fields->imageSource == 'url' && $a->fields->thumb != '') {
        return $a->fields->thumb;
    }
    if ($a->fields->imageSource == 'default') {
        return randomHeaderImage($defaultUrl);
    }
    // 默认使用第一张图片作为文章头图
    $img = getPostImg($a);
    return $img == 'none'?false:$img;
}

/**
 * 获取文章的第一张图片
 *
 * @param object $archive 文章
 * @return false|string 返回文章头图或 false
 */
function getPostImg($archive) {

    $img = array();
    preg_match_all("/<img.*?src=\"(.*?)\".*?\/?>/i", $archive->content, $img);
    if (count($img) > 0 && count($img[0]) > 0) {
        $img_url = $img[1][0];
        return $img_url;
    } else {
        return false;
    }
}

/**
 * 获取随机文章头图
 *
 * @param string $imgUrl 默认文章头图URL
 * @return false|string 返回文章头图URL
 */
function randomHeaderImage($imgUrl) {
    if ($imgUrl == null or $imgUrl == '') return false;
    // 把 URL 按行拆分为数组
    $imgUrl = explode(PHP_EOL, $imgUrl);
    // 删除因为空行生成的数组空值
    $imgUrl = array_filter($imgUrl);
    // 如果只有一个 URL 就直接返回 URL
    if (count($imgUrl) < 2) return $imgUrl[0];
    // 随机返回一个 URL
    return $imgUrl[mt_rand(0, count($imgUrl) - 1)];
}

/**
 * 获取文章列表的文章头图样式设置
 *
 * @param string $postStyle 单篇文章的头图样式
 * @param string $optionsStyle 全局文章头图样式
 * @return string 返回文章头图样式设置
 */
function getPostListHeaderImageStyle($postStyle, $optionsStyle) {
    if ($postStyle == 'max' or $postStyle == 'mini') {
        return $postStyle;
    }
    if ($postStyle == 'default' or $postStyle == null) {
        if ($optionsStyle == 'max' or $optionsStyle == 'mini') {
            return $optionsStyle;
        }
        return 'max';
    }
    return 'max';
}
