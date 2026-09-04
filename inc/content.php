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
 *  - stripThemeShortcodes              去除短代码语法仅保留包裹内容（支持嵌套）
 *  - postListSummary                   输出文章列表摘要（不含短代码语法）
 *  - outputCustomHighlightCSS          输出代码高亮自定义 CSS
 *  - addExternalLinkAttributes          为站外链接添加 target="_blank" 与 rel="noopener"
 *  - isInternalLink                     判断链接是否为本站链接
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
 * 去除文章内容中的短代码语法，仅保留其包裹的正文内容（支持嵌套）
 *
 * 与 parseThemeShortcodes 使用相同的标签白名单与代码块忽略规则，
 * 用于文章列表摘要等不需要把短代码解析为 HTML 的场景。
 *
 * @param string $content 含短代码语法的文本
 * @return string 去除短代码标记后的文本
 */
function stripThemeShortcodes($content) {
    // 定义支持的短代码标签，与 parseThemeShortcodes 保持一致
    $supported_tags = array('button', 'alert');
    $tags_pattern = implode('|', $supported_tags);
    // 前半部分匹配 <pre> / <code> 块（忽略其中的短代码）
    // 后半部分匹配 [tag ...]内容[/tag] 的短代码
    $pattern = '/(<pre\b[^>]*>.*?<\/pre>|<code\b[^>]*>.*?<\/code>)|\[(' . $tags_pattern . ')\b[^\]]*\](.*?)\[\/\2\]/is';

    // 自内向外反复替换，支持嵌套短代码；没有可替换内容时停止
    while (true) {
        $stripped = preg_replace_callback($pattern, function ($matches) {
            // 代码块原样保留，不解析其中的短代码
            if (!empty($matches[1])) {
                return $matches[1];
            }
            // 仅保留短代码包裹的内容
            return $matches[3];
        }, $content);

        if ($stripped === null || $stripped === $content) {
            break;
        }
        $content = $stripped;
    }

    return $content;
}

/**
 * 输出文章列表摘要
 *
 * 优先输出自定义摘要，否则自动截取文章内容；
 * 两种情况都会先去除短代码语法，只保留短代码包裹的正文。
 *
 * @param object $archive 当前文章对象
 * @param int    $length  摘要截取长度
 * @param string $trim    摘要截断后缀
 */
function postListSummary($archive, $length, $trim = '...') {
    // 自定义摘要：不受字数限制，去除短代码语法后原样输出
    if ($archive->fields->summaryContent) {
        echo stripThemeShortcodes($archive->fields->summaryContent);
        return;
    }

    // 自动摘要：去除短代码语法后截取纯文本
    $excerpt = stripThemeShortcodes($archive->excerpt);
    echo \Typecho\Common::subStr(strip_tags($excerpt), 0, $length, $trim);
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

/**
 * 为文章内容中的站外链接添加 target="_blank" 与 rel="noopener"
 *
 * 遍历文章内容中的 <a> 链接，当链接指向本站以外的站点时，
 * 自动添加 target="_blank"（新窗口打开）与 rel="noopener"（防止新窗口劫持）；
 * 本站链接（相对链接、锚点链接、同域名链接）以及 <pre> / <code> 代码块内的链接不处理。
 *
 * @param string $content 文章内容的 HTML 字符串
 * @param string $siteUrl 本站地址，例如 https://example.com/，通常传入 $this->options->siteUrl
 * @return string 处理后的 HTML 字符串
 */
function addExternalLinkAttributes($content, $siteUrl) {
    // 没有链接时直接返回原内容
    if (empty($content) || strpos($content, '<a') === false) {
        return $content;
    }

    // 匹配 <pre> / <code> 代码块（原样保留，不处理其中的链接）或 <a> 开始标签
    $pattern = '/(<pre\b[^>]*>.*?<\/pre>|<code\b[^>]*>.*?<\/code>)|<a\b([^>]*)>/is';

    return preg_replace_callback($pattern, function ($matches) use ($siteUrl) {
        // 命中代码块时直接返回，不处理其中的链接
        if (!empty($matches[1])) {
            return $matches[1];
        }

        $attrs = $matches[2];

        // 提取 href 属性（优先匹配双引号 / 单引号，其次兼容无引号写法）
        if (preg_match('/\bhref\s*=\s*(["\'])(.*?)\1/i', $attrs, $hrefMatches)) {
            $href = trim($hrefMatches[2]);
        } elseif (preg_match('/\bhref\s*=\s*([^\s>]+)/i', $attrs, $hrefMatches)) {
            $href = trim($hrefMatches[1]);
        } else {
            // 没有 href 属性的链接不处理
            return $matches[0];
        }

        // 本站链接不处理
        if (isInternalLink($href, $siteUrl)) {
            return $matches[0];
        }

        // 站外链接：添加 target="_blank"（已存在时不重复添加）
        if (!preg_match('/\btarget\s*=/i', $attrs)) {
            $attrs .= ' target="_blank"';
        }

        // 站外链接：添加 / 合并 rel="noopener"
        if (preg_match('/\brel\s*=\s*(["\'])(.*?)\1/i', $attrs, $relMatches)) {
            // 已有 rel 属性时，追加 noopener，避免覆盖原有值
            $relParts = preg_split('/\s+/', trim($relMatches[2]));
            if (!in_array('noopener', $relParts)) {
                $relParts[] = 'noopener';
                $attrs = str_replace($relMatches[0], 'rel=' . $relMatches[1] . implode(' ', $relParts) . $relMatches[1], $attrs);
            }
        } else {
            $attrs .= ' rel="noopener"';
        }

        return '<a' . $attrs . '>';
    }, $content);
}

/**
 * 判断链接是否为本站链接
 *
 * 锚点链接、相对路径、非 http(s) 协议的链接（如 mailto、tel）均视为本站链接；
 * http(s) 绝对链接与协议相对链接（//xxx.com）会与本站域名比较，
 * 域名一致视为本站链接，指向其它域名的视为站外链接。
 *
 * @param string $href    链接地址
 * @param string $siteUrl 本站地址
 * @return bool 为本站链接时返回 true
 */
function isInternalLink($href, $siteUrl) {
    // 空地址、锚点视为本站链接
    if ($href === '' || $href[0] === '#') {
        return true;
    }

    // 协议相对地址（//xxx.com）视为绝对地址，需要比较域名
    if (strpos($href, '//') === 0) {
        $href = 'http:' . $href;
    } elseif (!preg_match('#^https?://#i', $href)) {
        // 相对路径（/、./、../、直接路径）以及 mailto、tel 等非 http(s) 协议视为本站链接
        return true;
    }

    // 解析本站域名
    $siteHost = parse_url($siteUrl, PHP_URL_HOST);
    if (empty($siteHost)) {
        return true;
    }
    $siteHost = strtolower($siteHost);

    // 解析链接域名
    $linkHost = parse_url($href, PHP_URL_HOST);
    if (empty($linkHost)) {
        return true;
    }
    $linkHost = strtolower($linkHost);

    // 比较域名，忽略 www 前缀差异
    if (strpos($siteHost, 'www.') === 0) {
        $siteHost = substr($siteHost, 4);
    }
    if (strpos($linkHost, 'www.') === 0) {
        $linkHost = substr($linkHost, 4);
    }
    return $linkHost === $siteHost;
}
