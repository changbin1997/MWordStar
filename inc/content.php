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
 *  - parseThemeShortcodes              解析自定义短代码（button / alert / collapse / badge / hide）
 *  - canViewHideContent               判断 [hide] 隐藏内容的访问权限
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
 * @param int    $cid     当前文章/页面 ID，用于 [hide] 短代码的评论权限判断
 * @return string 转换后的 HTML 字符串
 */
function parseThemeShortcodes($content, $cid = 0) {
    // 短代码开关：关闭时不做任何解析，直接返回原文
    $shortcodeOption = Helper::options()->shortcode;
    if ($shortcodeOption !== null && $shortcodeOption != 'enable') {
        return $content;
    }

    // 页面级自增计数器，保证同一页面内多个 collapse / tabs 短代码的 id 唯一
    static $collapse_id = 0;
    static $tabs_id = 0;
    // 定义支持的短代码标签，方便未来维护和添加新功能
    $supported_tags = array('button', 'alert', 'collapse', 'badge', 'hide', 'progress', 'tabs');
    $tags_pattern = implode('|', $supported_tags);

    // 渲染单个短代码为 HTML；$rawText 为短代码原始文本，用于无法解析时兜底原样返回
    $renderTag = function ($tag, $attr_string, $inner_content, $rawText) use (&$collapse_id, &$tabs_id, $cid) {
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

            case 'badge':
                // 未指定 type 时默认使用 secondary，支持可选 url 渲染为链接
                $type = isset($atts['type']) ? $atts['type'] : 'secondary';

                $type_attr = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
                // 传入 url 时渲染为链接，否则渲染为 span
                if (isset($atts['url']) && $atts['url'] !== '') {
                    return '<a href="' . htmlspecialchars($atts['url'], ENT_QUOTES, 'UTF-8') . '" class="badge badge-' . $type_attr . '">' . $inner_content . '</a>';
                }
                return '<span class="badge badge-' . $type_attr . '">' . $inner_content . '</span>';

            case 'alert':
                $type = isset($atts['type']) ? $atts['type'] : 'primary';

                // alert 内部可能包含其他排版 HTML (如链接)，因此 $inner_content 不做转义
                return '<div class="alert alert-' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . '">' . $inner_content . '</div>';

            case 'collapse':
                // 递增生成唯一 id，保证一篇/一页中多个 collapse 不重复
                $collapse_id++;
                $title = isset($atts['title']) ? $atts['title'] : '点击展开';

                $id = 'collapse-' . $collapse_id;
                // 标题作为属性值，使用 htmlspecialchars 过滤 XSS；正文保留原有排版 HTML
                return '<div class="card collapse-box">'
                    . '<div class="card-header p-0 bg-light">'
                    . '<button class="btn btn-block text-left border-0 py-2 px-3 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#' . $id . '">'
                    . '<span class="font-weight-bold">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</span>'
                    . '<small class="text-muted">▼</small>'
                    . '</button>'
                    . '</div>'
                    . '<div class="collapse" id="' . $id . '">'
                    . '<div class="card-body">' . preg_replace('/^\<br>|\<br>$/', '', $inner_content) . '</div>'
                    . '</div>'
                    . '</div>';

            case 'hide':
                // 未指定 type 时默认使用 comment
                $type = isset($atts['type']) ? $atts['type'] : 'comment';
                if ($type != 'login') {
                    $type = 'comment';
                }
                // 无权限时显示的提示信息（支持多语言）
                $hideTips = isset($GLOBALS['t']['shortcode']) ? $GLOBALS['t']['shortcode'] : array();
                if ($type == 'login') {
                    $tip = isset($hideTips['hideByLogin']) ? $hideTips['hideByLogin'] : '此处内容已被隐藏，仅登录用户可见。';
                } else {
                    $tip = isset($hideTips['hideByComment']) ? $hideTips['hideByComment'] : '此处内容已被隐藏，需要在本文下方发送评论，评论审核通过后才可阅读。';
                }
                // 有权限时返回隐藏内容本体，无权限时返回提示信息
                return canViewHideContent($type, $cid)
                    ? preg_replace('/^\<br>|\<br>$/', '', $inner_content)
                    : '<div class="alert expiration-reminder">' . $tip . '</div>';

            case 'progress':
                // 未指定 type 时默认使用 primary 样式，映射为 Bootstrap 的 bg-* 颜色类
                $type = isset($atts['type']) ? $atts['type'] : 'primary';
                // 进度值支持 75 或 75% 写法：去除百分号与其它非数字字符，仅保留数字
                $value = (float)preg_replace('/[^0-9.]/', '', $inner_content);
                $type_attr = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
                return '<div class="progress">'
                    . '<div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $type_attr . '" role="progressbar" aria-valuenow="' . $value . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $value . '%;"></div>'
                    . '</div>';

            case 'tabs':
                // 匹配 tabs 内部的所有 [tab title="..."]内容[/tab]
                if (preg_match_all('/\[tab\b([^\]]*?)\](.*?)\[\/tab\]/is', $inner_content, $tab_matches)) {
                    $tabs_id++;
                    $widgetId = 'tabs-' . $tabs_id;

                    $nav_html = '';
                    $pane_html = '';
                    $tab_index = 0;
                    foreach ($tab_matches[1] as $i => $tab_attr_string) {
                        $tab_index++;
                        // 解析 tab 属性，提取 title
                        $tab_atts = array();
                        if (preg_match_all('/(\w+)\s*=\s*(["\'])(.*?)\2/i', $tab_attr_string, $tab_attr_matches)) {
                            foreach ($tab_attr_matches[1] as $attr_index => $key) {
                                $tab_atts[strtolower($key)] = $tab_attr_matches[3][$attr_index];
                            }
                        }
                        $tab_title = isset($tab_atts['title']) ? $tab_atts['title'] : 'Tab ' . $tab_index;

                        $buttonId = $widgetId . '-tab-' . $tab_index;
                        $paneId = $widgetId . '-pane-' . $tab_index;
                        // 第一项默认选中
                        $isActive = $tab_index === 1;

                        $nav_html .= '<li class="nav-item">'
                            . '<button class="nav-link' . ($isActive ? ' active' : '') . '" id="' . $buttonId . '"'
                            . ' data-toggle="tab" data-target="#' . $paneId . '" role="tab"'
                            . ' aria-controls="' . $paneId . '" aria-selected="' . ($isActive ? 'true' : 'false') . '">'
                            . htmlspecialchars($tab_title, ENT_QUOTES, 'UTF-8')
                            . '</button>'
                            . '</li>';

                        $pane_html .= '<div class="tab-pane fade' . ($isActive ? ' show active' : '') . '" id="' . $paneId . '"'
                            . ' role="tabpanel" aria-labelledby="' . $buttonId . '">'
                            . preg_replace('/^\<br>|\<br>$/', '', $tab_matches[2][$i])
                            . '</div>';
                    }

                    return '<div class="tab-box">'
                        . '<ul class="nav nav-tabs" role="tablist">' . $nav_html . '</ul>'
                        . '<div class="tab-content border-left border-right border-bottom" id="' . $widgetId . '-content">' . $pane_html . '</div>'
                        . '</div>';
                }
                // 没有解析到任何 tab 时返回原文本
                return $rawText;

            default:
                // 如果没有对应的处理逻辑，返回原文本
                return $rawText;
        }
    };

    // 递归解析短代码：支持短代码内再嵌套短代码，代码块原样保留
    $renderContent = null;
    $renderContent = function ($text) use (&$renderContent, &$renderTag, $tags_pattern) {
        // 前半部分匹配 <pre> 或 <code> 块（用于忽略），后半部分匹配短代码的开始 / 结束标签
        $pattern = '/(<pre\b[^>]*>.*?<\/pre>|<code\b[^>]*>.*?<\/code>)|\[(\/)?(' . $tags_pattern . ')\b([^\]]*?)\]/is';

        if (!preg_match_all($pattern, $text, $tokens, PREG_OFFSET_CAPTURE)) {
            return $text;
        }

        $result = '';
        $pos = 0;
        $count = count($tokens[0]);
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[0][$i][0];
            $offset = $tokens[0][$i][1];

            // 追加当前 token 之前的普通文本
            $result .= substr($text, $pos, $offset - $pos);

            // 命中代码块时原样返回，不解析其中的短代码
            if (!empty($tokens[1][$i][0])) {
                $result .= $token;
                $pos = $offset + strlen($token);
                continue;
            }

            $tag = strtolower($tokens[3][$i][0]);
            // 单独的结束标签（没有配对的开始标签）原样输出
            if ($tokens[2][$i][0] === '/') {
                $result .= $token;
                $pos = $offset + strlen($token);
                continue;
            }

            $attr_string = $tokens[4][$i][0];

            // 向后查找配对的结束标签，记录同名标签的嵌套深度（支持同标签互相嵌套）
            $depth = 1;
            $innerStart = $offset + strlen($token);
            $closeIndex = -1;
            for ($j = $i + 1; $j < $count; $j++) {
                // 代码块与其它标签不参与当前配对
                if (!empty($tokens[1][$j][0])) {
                    continue;
                }
                if (strtolower($tokens[3][$j][0]) !== $tag) {
                    continue;
                }
                if ($tokens[2][$j][0] === '/') {
                    $depth--;
                    if ($depth === 0) {
                        $closeIndex = $j;
                        break;
                    }
                } else {
                    $depth++;
                }
            }

            // 找不到配对的结束标签时，原样输出开始标签后继续处理
            if ($closeIndex === -1) {
                $result .= $token;
                $pos = $offset + strlen($token);
                continue;
            }

            // 提取内部内容并递归解析，实现短代码嵌套
            $inner_content = substr($text, $innerStart, $tokens[0][$closeIndex][1] - $innerStart);
            $inner_content = $renderContent($inner_content);

            // 渲染为 HTML；未识别的短代码按原始文本返回
            $rawText = substr($text, $offset, $tokens[0][$closeIndex][1] + strlen($tokens[0][$closeIndex][0]) - $offset);
            $result .= $renderTag($tag, $attr_string, $inner_content, $rawText);

            // 从结束标签之后继续向后解析
            $pos = $offset + strlen($rawText);
            $i = $closeIndex;
        }

        $result .= substr($text, $pos);
        return $result;
    };

    return $renderContent($content);
}

/**
 * 判断当前访问者是否有权限查看 [hide] 隐藏内容
 *
 * 支持 comment / login 两种类型：
 *  - comment：登录用户直接可见；未登录访客需要在本文章（cid）发表过
 *    状态为 approved 的评论，且 Cookie 中的邮箱与该评论邮箱一致才可见。
 *  - login：仅登录用户可见。
 *
 * @param string $type 隐藏内容类型（comment / login）
 * @param int    $cid  当前文章/页面 ID
 * @return bool 有权限时返回 true
 */
function canViewHideContent($type, $cid = 0) {
    // 按 类型+文章ID 缓存判断结果，避免同一文章内多个 [hide] 重复查询数据库
    static $permissionCache = array();
    $cacheKey = $type . ':' . $cid;
    if (array_key_exists($cacheKey, $permissionCache)) {
        return $permissionCache[$cacheKey];
    }

    $user = Typecho_Widget::widget('Widget_User');

    // login 类型：仅登录用户可见
    if ($type == 'login') {
        $permission = $user->hasLogin();
        $permissionCache[$cacheKey] = $permission;
        return $permission;
    }

    // comment 类型：登录用户直接可见
    if ($user->hasLogin()) {
        $permissionCache[$cacheKey] = true;
        return true;
    }

    // 未登录：通过 Cookie 中的邮箱判断是否发表过已审核通过的评论
    $mail = Typecho_Cookie::get('__typecho_remember_mail');
    if (empty($mail) || empty($cid)) {
        $permissionCache[$cacheKey] = false;
        return false;
    }

    $db = Typecho_Db::get();
    $comment = $db->fetchRow($db->select()->from('table.comments')
        ->where('cid = ?', $cid)
        ->where('mail = ?', $mail)
        ->where('status = ?', 'approved')
        ->limit(1));

    $permission = !empty($comment);
    $permissionCache[$cacheKey] = $permission;
    return $permission;
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
    // 定义支持的短代码标签，与 parseThemeShortcodes 保持一致；tab 附属于 tabs，单独列出以便摘要去除
    $supported_tags = array('button', 'alert', 'collapse', 'badge', 'hide', 'progress', 'tabs', 'tab');
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
            // 隐藏内容不在摘要中输出，替换为提示文本
            if (strtolower($matches[2]) == 'hide') {
                $hideTips = isset($GLOBALS['t']['shortcode']) ? $GLOBALS['t']['shortcode'] : array();
                return isset($hideTips['hiddenInSummary']) ? $hideTips['hiddenInSummary'] : '隐藏内容，需进入文章页查看。';
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
    // 短代码关闭时直接使用 Typecho 默认的摘要输出方式，不做短代码处理
    $shortcodeOption = Helper::options()->shortcode;
    if ($shortcodeOption !== null && $shortcodeOption != 'enable') {
        if ($archive->fields->summaryContent) {
            echo $archive->fields->summaryContent;
        } else {
            echo \Typecho\Common::subStr(strip_tags($archive->excerpt), 0, $length, $trim);
        }
        return;
    }

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
