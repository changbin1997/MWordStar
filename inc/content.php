<?php

/**
 * MWordStar 主题 - 文章内容加工
 *
 * 包含函数：
 *  - articleDirectory                  生成文章目录树并注入锚点
 *  - renderArticleDirectory            递归渲染目录 HTML
 *  - lazyLoadImages                    图片懒加载（原生 / 兼容）
 *  - splitArticleContent               按 [-page-] 分页文章内容
 *  - addBootstrapTableClasses          为表格加 Bootstrap 样式
 *  - parseThemeShortcodes              解析自定义短代码（button / alert）
 *  - outputCustomHighlightCSS          输出代码高亮自定义 CSS
 *
 * @package MWordStar
 */

/**
 * 根据文章内的标题生成目录
 *
 * @param string $content 文章内容
 * @return array 返回文章内容和目录
 */
function articleDirectory($content) {
    $re = '#<h(\d)(.*?)>(.*?)</h\d>#im';
    preg_match_all($re, $content, $result);
    if (!is_array($result) or count($result[0]) < 1) {
        return array('content' => $content, 'directory' => null);
    }

    $treeList = array();
    $id = 1;
    foreach ($result[1] as $i => $level) {
        $treeList[$id] = array(
            'id' => $id,
            'parent_id' => 0,
            'level' => $level,
            'name' => trim(strip_tags($result[3][$i])),
            'rand' => mt_rand(1000, 9999)
        );
        $id ++;
    }

    for ($i = 2;$i <= count($treeList);$i ++) {
        $item = $treeList[$i];
        $prevItem = $treeList[$i - 1];
        if ($item['level'] == $prevItem['level']) {
            $treeList[$i]['parent_id'] = $prevItem['parent_id'];
            continue;
        }
        if ($item['level'] > $prevItem['level']) {
            $treeList[$i]['parent_id'] = $prevItem['id'];
            continue;
        }
        $parentId = 0;
        while ($item['level'] <= $prevItem['level']) {
            $parentId = $prevItem['parent_id'];
            if (!isset($treeList[($prevItem['id'] - 1)])) {
                break;
            }
            $prevItem = $treeList[($prevItem['id'] - 1)];
        }
        $treeList[$i]['parent_id'] = $parentId;
    }

    $tree = array();
    foreach ($treeList as $item) {
        if ($item[ 'parent_id' ] != 0 && !isset($treeList[$item['parent_id']])) {
            continue;
        }
        if (isset($treeList[$item['parent_id']])) {
            $treeList[$item['parent_id']]['children'][] = &$treeList[$item['id']];
        } else {
            $tree[] = &$treeList[$item['id']];
        }
    }

    $GLOBALS['directory'] = $treeList;
    $GLOBALS['directoryIndex'] = 1;
    $content = preg_replace_callback($re, function ($matches) {
        $name = urlencode(strip_tags($matches[3]));
        $span = '<span class="title-position" data-title="p-' . $GLOBALS['directory'][$GLOBALS['directoryIndex']]['id'] . '" id="p-' . $GLOBALS['directory'][$GLOBALS['directoryIndex']]['id'] . '"></span>' . $matches[0];
        $GLOBALS['directoryIndex'] ++;
        return $span;
    }, $content);

    return array(
        'content' => $content,
        'directory' => renderArticleDirectory($tree, '')
    );
}

/**
 * 生成目录 HTML
 *
 * @param $tree
 * @param $parent
 * @return string 返回文章目录HTML
 */
function renderArticleDirectory($tree, $parent = '') {
    $index = 1;
    $ariaLabel = $tree[0]['parent_id'] == 0?'aria-label="' . $GLOBALS['t']['sidebar']['tableOfContents'] . '"':'';
    $htmlStr = '<ul class="article-directory"' . $ariaLabel . '>';
    foreach ($tree as $item) {
        $num = $parent == ''?$index:$parent . '.' . $index;
        $htmlStr .= sprintf('<li><a rel="bookmark" data-directory="%s" class="directory-link" href="#%s">%s</a></li>', 'p-' . $item['id'], 'p-' . $item['id'], '<span class="mr-2 directory-num">' . $num . '</span>' . $item['name']);
        if (isset($item['children']) && count($item['children']) > 0) {
            $htmlStr .= renderArticleDirectory($item['children'], $num);
        }
        $index ++;
    }
    $htmlStr .= '</ul>';
    return $htmlStr;
}

/**
 * 给文章内容中的图片应用懒加载
 *
 * 原生懒加载会给 img 标签添加 loading="lazy" 属性，由浏览器自行延迟加载图片；
 * 兼容性懒加载会把图片的 src 替换为 data-src 并添加 load-img 类，由主题 JavaScript 在图片进入可视区时加载。
 *
 * @param string $content 文章内容
 * @param string $option  图片懒加载设置：native 为原生懒加载，compatible 为兼容性懒加载，其它值不处理
 * @return string 处理后的文章内容
 */
function lazyLoadImages($content, $option) {
    // 关闭图片懒加载时不处理
    if ($option != 'native' && $option != 'compatible') {
        return $content;
    }

    // 原生懒加载：给没有 loading 属性的 img 添加 loading="lazy"，由浏览器自行延迟加载图片
    if ($option == 'native') {
        return preg_replace('/<img\b(?![^>]*\bloading\s*=)/i', '<img loading="lazy"', $content);
    }

    // 兼容性懒加载：把 src 替换为 data-src 并添加 load-img 类，由主题 JavaScript 在图片进入可视区时加载
    $pattern = '/<img(.*?)src(.*?)=(.*?)"(.*?)">/i';
    $replacement = '<img$1data-src$3="$4"$5 class="load-img">';
    return preg_replace($pattern, $replacement, $content);
}

/**
 * 文章内容分页
 *
 * @param string $content 文章的 HTML 内容
 * @return array 分页后的内容数组
 */
function splitArticleContent($content) {
    $pattern = '/<(pre|code)\b[^>]*>.*?<\/\1>(*SKIP)(*FAIL)|<p>\s*\[-page-\]\s*<\/p>|\[-page-\]/is';
    // 使用 preg_split 进行分割
    return preg_split($pattern, $content);
}

/**
 * 为文章中的表格添加 Bootstrap 4 样式
 *
 * @param string $html 原始文章 HTML
 * @return string 处理后的 HTML
 */
function addBootstrapTableClasses($html) {
    // 没有表格直接返回原内容
    if (empty($html) || strpos($html, '<table') === false) {
        return $html;
    }

    // 创建 DOMDocument 并加载 HTML
    $dom = new DOMDocument();
    // 抑制因不标准 HTML 产生的警告
    libxml_use_internal_errors(true);
    // 添加 XML 声明确保 UTF-8 编码正确解析
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();

    // 获取所有表格元素
    $tables = $dom->getElementsByTagName('table');
    foreach ($tables as $table) {
        // 合并现有的 class 属性
        $oldClass = $table->getAttribute('class');
        $classes = array_filter(explode(' ', $oldClass));
        $classes = array_merge($classes, ['table', 'table-striped', 'table-bordered', 'table-hover']);
        $classes = array_unique($classes);
        $table->setAttribute('class', implode(' ', $classes));

        // 创建外层响应式容器 div
        $div = $dom->createElement('div');
        $div->setAttribute('class', 'table-responsive');

        // 将表格替换为 div，并将表格移入 div
        $table->parentNode->replaceChild($div, $table);
        $div->appendChild($table);
    }

    // 提取 body 内的所有内容（去除自动添加的 doctype/html/body 标签）
    $body = $dom->getElementsByTagName('body')->item(0);
    $newHtml = '';
    foreach ($body->childNodes as $child) {
        $newHtml .= $dom->saveHTML($child);
    }

    return $newHtml;
}

/**
 * 解析文章内容中的自定义短代码 (兼容 PHP 5.6)
 *
 * @param string $content 文章内容的 HTML 字符串
 * @return string 转换后的 HTML 字符串
 */
function parseThemeShortcodes($content) {
    // 定义支持的短代码标签，方便未来维护和添加新功能
    $supported_tags = array('button', 'alert');
    $tags_pattern = implode('|', $supported_tags);
    // 构造正则表达式
    // 前半部分匹配 <pre> 或 <code> 块（用于忽略）
    // 后半部分匹配类似 [tag attr="value"]内容[/tag] 的短代码
    $pattern = '/(<pre\b[^>]*>.*?<\/pre>|<code\b[^>]*>.*?<\/code>)|\[(' . $tags_pattern . ')\b([^\]]*?)\](.*?)\[\/\2\]/is';

    // 使用正则回调函数进行替换
    return preg_replace_callback($pattern, function($matches) {
        // 如果匹配到的是代码块 ($matches[1] 不为空)，直接原样返回，不解析其中的短代码
        if (!empty($matches[1])) {
            return $matches[1];
        }

        // 提取短代码各部分内容
        $tag = strtolower($matches[2]);
        $attr_string = $matches[3];
        $inner_content = $matches[4];

        // 解析属性字符串 (支持双引号和单引号，例如 url="xxx" 或 type='xxx')
        $atts = array();
        if (preg_match_all('/(\w+)\s*=\s*(["\'])(.*?)\2/i', $attr_string, $attr_matches)) {
            // $attr_matches[1] 是属性名，$attr_matches[3] 是属性值
            foreach ($attr_matches[1] as $index => $key) {
                $atts[strtolower($key)] = $attr_matches[3][$index];
            }
        }

        // 根据不同的短代码标签进行处理
        switch ($tag) {
            case 'button':
                // 获取属性，赋予默认值
                $url = isset($atts['url']) ? $atts['url'] : '#';
                $type = isset($atts['type']) ? $atts['type'] : 'primary';

                // 为了安全，属性值使用 htmlspecialchars 过滤 XSS
                return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="btn btn-' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . '">' . $inner_content . '</a>';

            case 'alert':
                $type = isset($atts['type']) ? $atts['type'] : 'primary';

                // alert 内部可能包含其他排版 HTML (如链接)，因此 $inner_content 不做转义
                return '<div class="alert alert-' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . '">' . $inner_content . '</div>';

            default:
                // 如果没有对应的处理逻辑，返回原文本
                return $matches[0];
        }
    }, $content);
}

/**
 * 解析并输出代码高亮自定义 CSS
 * 
 * @param string $input 用户在后台输入的 CSS 内容或 URL
 */
function outputCustomHighlightCSS($input) {
    // 去除首尾的空白字符
    $input = trim($input);
    // 如果输入为空，则直接返回
    if (empty($input)) {
        return;
    }
    // 正则匹配判断是否为 URL：
    if (preg_match('/^(https?:)?\/\/[^\s{}]+$/i', $input) || preg_match('/^\/[^\s{}]+$/i', $input)) {
        // 输出引用的 <link> 标签，并使用 htmlspecialchars 防止 XSS 注入
        echo '<link rel="stylesheet" href="' . htmlspecialchars($input, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    } else {
        // 容错处理：去除可能会出现的 style 标签
        $input = preg_replace('/<\/?style[^>]*>/i', '', $input);
        // 输出 <style> 标签
        echo "<style>\n" . trim($input) . "\n</style>\n";
    }
}
