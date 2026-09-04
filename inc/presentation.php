<?php

/**
 * MWordStar 主题 - 前台展示辅助与侧边栏日历
 *
 * 包含函数：
 *  - postListStyle                     文章列表显示样式判断
 *  - mwordstarCalendarNormalizeMonth   规范化日历月份
 *  - mwordstarCalendarMonthFromRequest 从当前请求中解析年月
 *  - mwordstarCalendarDbTimestamp      时间戳转数据库 UTC 时间戳
 *  - mwordstarCalendarDate             按 Typecho 时区格式化日期
 *  - mwordstarCalendarFormatMonth      时间戳转月份路径字符串
 *  - mwordstarCalendarArchiveUrl       生成日历归档页 URL
 *  - mwordstarCalendarAttr             HTML 属性值转义
 *  - mwordstarCalendarMonthLabel       日历月份标签文本
 *  - getMonth                          获取侧边栏日历当前月份
 *  - getMonthPost                      获取指定月份的文章与前后月
 *  - calendar                          生成侧边栏日历 HTML
 *  - bootstrap4Pagination              Bootstrap4 分页
 *  - themeSeoTags                      输出 SEO 标签（canonical / noindex）
 *
 * @package MWordStar
 */

/**
 * 获取文章列表显示设置
 *
 * @param string $option 文章列表的全局设置
 * @param string $postOption 单篇文章的列表设置
 * @return string 文章列表显示设置
 */
function postListStyle($option, $postOption) {
    // 判断单篇文章的列表显示设置
    if ($postOption == 'summary' or $postOption == 'fullText') {
        return $postOption;
    }
    // 判断列表全局设置
    if ($option == 'fullText' or $option == 'summary') {
        return $option;
    }
    // 如果出现异常就默认显示文章摘要
    return 'summary';
}

/**
 * 规范化侧边栏日历月份
 */
function mwordstarCalendarNormalizeMonth($date = null) {
    $year = 0;
    $month = 0;

    if (is_array($date)) {
        $year = isset($date['year']) ? (int)$date['year'] : (isset($date[0]) ? (int)$date[0] : 0);
        $month = isset($date['month']) ? (int)$date['month'] : (isset($date[1]) ? (int)$date[1] : 0);
    } elseif (is_string($date) && preg_match('/(\d{4})[\/\-](\d{1,2})/', $date, $matches)) {
        $year = (int)$matches[1];
        $month = (int)$matches[2];
    }

    if ($year < 1 || $month < 1 || $month > 12) {
        $now = class_exists('Typecho_Date') ? new Typecho_Date() : null;
        $year = $now ? (int)$now->format('Y') : (int)date('Y');
        $month = $now ? (int)$now->format('n') : (int)date('n');
    }

    return array(
        'year' => sprintf('%04d', $year),
        'month' => sprintf('%02d', $month),
        'timestamp' => gmmktime(0, 0, 0, $month, 1, $year)
    );
}

/**
 * 从当前请求中提取年份和月份
 *
 * 优先从归档对象的 request 中获取 year 和 month 参数，
 * 如果获取失败则尝试从 REQUEST_URI、PATH_INFO、PHP_SELF 等路径中解析。
 *
 * @param object|null $archive 当前归档对象，可选
 * @return array|null 返回包含 'year' 和 'month' 的关联数组，解析失败返回 null
 */
function mwordstarCalendarMonthFromRequest($archive = null) {
    if (is_object($archive) && isset($archive->request)) {
        $year = isset($archive->request->year) ? (int)$archive->request->year : 0;
        $month = isset($archive->request->month) ? (int)$archive->request->month : 0;

        if ($year > 0 && $month > 0) {
            return array('year' => $year, 'month' => $month);
        }
    }

    $paths = array(
        isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '',
        isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '',
        isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : ''
    );

    foreach ($paths as $path) {
        if (preg_match('~(?:^|/)(\d{4})/(\d{1,2})(?:/\d{1,2})?(?:/|$)~', $path, $matches)) {
            return array('year' => (int)$matches[1], 'month' => (int)$matches[2]);
        }
    }

    return null;
}

/**
 * 将带有时区的时间戳转换为数据库存储用的 UTC 时间戳
 *
 * 因为 Typecho 在数据库中存储的时间戳是经过时区偏移的，
 * 此函数通过减去时区偏移量将其还原为 UTC 时间戳，用于数据库查询。
 *
 * @param int $timestamp 带有时区偏移的时间戳
 * @return int 减去时区偏移后的 UTC 时间戳
 */
function mwordstarCalendarDbTimestamp($timestamp) {
    $options = Helper::options();
    $timezone = isset($options->timezone) ? (int)$options->timezone : 0;

    return $timestamp - $timezone;
}

/**
 * 根据 Typecho 时区设置格式化日期时间
 *
 * 将传入的时间戳加上时区偏移量后，使用 gmdate 输出指定格式的日期字符串。
 *
 * @param string $format 日期格式，与 PHP 的 date() 函数格式一致
 * @param int $timestamp 数据库中的时间戳（已含时区偏移）
 * @return string 格式化后的日期时间字符串
 */
function mwordstarCalendarDate($format, $timestamp) {
    $options = Helper::options();
    $timezone = isset($options->timezone) ? (int)$options->timezone : 0;

    return gmdate($format, (int)$timestamp + $timezone);
}

/**
 * 将时间戳格式化为月份路径字符串
 *
 * 将传入的时间戳格式化为 "Y/m/" 格式（例如 "2026/07/"），
 * 用于生成月份归档页面的链接。
 *
 * @param int $timestamp 数据库中的时间戳（已含时区偏移）
 * @return false|string 格式化成功返回 "Y/m/" 格式字符串，失败返回 false
 */
function mwordstarCalendarFormatMonth($timestamp) {
    if (!$timestamp) {
        return false;
    }

    return mwordstarCalendarDate('Y/m/', $timestamp);
}

/**
 * 生成日历归档页面的 URL
 *
 * 根据路由名称、年份、月份和可选的日期生成归档链接。
 * 优先使用 Typecho_Router 生成路由 URL，失败则手动拼接路径。
 *
 * @param string $route 路由名称（如 'archive_month'、'archive_day'）
 * @param int|string $year 年份
 * @param int|string $month 月份
 * @param int|string|null $day 日期，可选。传入时生成按天归档的 URL
 * @return string 完整的归档页面 URL
 */
function mwordstarCalendarArchiveUrl($route, $year, $month, $day = null) {
    $options = Helper::options();
    $value = array(
        'year' => sprintf('%04d', (int)$year),
        'month' => sprintf('%02d', (int)$month)
    );

    if ($day !== null) {
        $value['day'] = sprintf('%02d', (int)$day);
    }

    if (class_exists('Typecho_Router') && Typecho_Router::get($route)) {
        return Typecho_Router::url($route, $value, $options->index);
    }

    $path = $value['year'] . '/' . $value['month'] . '/';
    if ($day !== null) {
        $path .= $value['day'] . '/';
    }

    if (class_exists('Typecho_Common')) {
        return Typecho_Common::url($path, $options->index);
    }

    return rtrim($options->index, '/') . '/' . $path;
}

/**
 * 对 HTML 属性值进行转义处理
 *
 * 使用 htmlspecialchars 对传入的值进行实体转义，防止 XSS 攻击。
 * 适用于日历组件中输出 HTML 属性值的场景。
 *
 * @param mixed $value 需要转义的值，会被强制转换为字符串
 * @return string 转义后的安全 HTML 属性值字符串
 */
function mwordstarCalendarAttr($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * 获取日历月份标签文本
 *
 * 将传入的日期归一化后，使用 gmdate 按指定格式生成月份显示文本。
 * 例如中文环境可输出 "2026年07月"，英文环境可输出 "July 2026"。
 *
 * @param array|string|null $date 日期数据，可以是 ['year' => Y, 'month' => m] 数组、
 *                                 "Y-m-d" 格式字符串或 null（使用当前月份）
 * @param string $format 月份标签的日期格式，与 PHP 的 date() 格式一致
 * @return string 格式化后的月份标签字符串
 */
function mwordstarCalendarMonthLabel($date, $format) {
    $date = mwordstarCalendarNormalizeMonth($date);
    return gmdate($format, $date['timestamp']);
}

/**
 * 获取月份，用于侧边栏日历
 *
 * @param object|null $archive 当前归档对象
 * @return false|string[] 返回月份
 */
function getMonth($archive = null) {
    $date = mwordstarCalendarNormalizeMonth(mwordstarCalendarMonthFromRequest($archive));
    return array($date['year'], $date['month']);
}

/**
 * 获取指定月份的文章，用于侧边栏日历
 *
 * @return array 返回本月文章和前后月的月份
 */
function getMonthPost($date = null) {
    $date = mwordstarCalendarNormalizeMonth($date ?: getMonth());
    $year = (int)$date['year'];
    $month = (int)$date['month'];
    $start = gmmktime(0, 0, 0, $month, 1, $year);
    $end = gmmktime(23, 59, 59, $month, (int)gmdate('t', $start), $year);
    $dbStart = mwordstarCalendarDbTimestamp($start);
    $dbEnd = mwordstarCalendarDbTimestamp($end);

    $db = Typecho_Db::get();
    $post = $db->fetchAll($db->select('table.contents.created')->from('table.contents')->where('created >= ?', $dbStart)->where('created <= ?', $dbEnd)->where('type = ?', 'post')->where('status = ?', 'publish'));
    $previous = $db->fetchAll($db->select('table.contents.created')->from('table.contents')->where('created < ?', $dbStart)->where('type = ?', 'post')->where('status = ?', 'publish')->offset(0)->limit(1)->order('created', Typecho_Db::SORT_DESC));
    $next = $db->fetchAll($db->select('table.contents.created')->from('table.contents')->where('created > ?', $dbEnd)->where('type = ?', 'post')->where('status = ?', 'publish')->offset(0)->limit(1)->order('created', Typecho_Db::SORT_ASC));

    $days = array();
    foreach ($post as $val) {
        $day = (int)mwordstarCalendarDate('j', $val['created']);
        $days[$day] = isset($days[$day]) ? $days[$day] + 1 : 1;
    }

    return array(
        'post' => array_keys($days),
        'days' => $days,
        'previous' => count($previous) ? mwordstarCalendarFormatMonth($previous[0]['created']) : false,
        'next' => count($next) ? mwordstarCalendarFormatMonth($next[0]['created']) : false
    );
}

/**
 * 生成日历
 *
 * @param string $month 月份
 * @param string $url
 * @param $rewrite
 * @return array 返回日历 HTML 和前后月份的名称和链接
 */
function calendar($month = null, $url = null, $rewrite = null, $archive = null) {
    if (is_object($url) && $archive === null) {
        $archive = $url;
        $url = null;
    }

    $monthArr = mwordstarCalendarNormalizeMonth($month ?: getMonth($archive));
    $post = getMonthPost($monthArr);
    $year = (int)$monthArr['year'];
    $monthNumber = (int)$monthArr['month'];

    $calendar = '';
    $week_arr = array('S', 'M', 'T', 'W', 'T', 'F', 'S');
    if ($GLOBALS['language'] == 'zh' or $GLOBALS['language'] == 'zh-CN') {
        $week_arr = array('日', '一', '二', '三', '四', '五', '六');
    }

    $this_month_days = (int)gmdate('t', $monthArr['timestamp']);
    $this_month_one_n = (int)gmdate('w', $monthArr['timestamp']);
    $total_rows = (int)ceil(($this_month_days + $this_month_one_n) / 7);
    $format = $GLOBALS['language'] == 'en' ? 'F Y' : 'Y年m月';
    $monthLabel = mwordstarCalendarMonthLabel($monthArr, $format);

    $calendar .= '<table aria-label="' . mwordstarCalendarAttr(sprintf($GLOBALS['t']['sidebar']['calendar'], $monthLabel)) . '" class="table table-bordered table-sm m-0"><thead><tr>';

    foreach ($week_arr as $k => $v) {
        $classes = array('text-center', 'py-2');
        if ($k == 0) {
            $classes[] = 'sunday';
        } elseif ($k == 6) {
            $classes[] = 'saturday';
        }

        $calendar .= '<th class="' . implode(' ', $classes) . '">' . mwordstarCalendarAttr($v) . '</th>';
    }

    $calendar .= '</tr></thead><tbody>';

    for ($row = 0; $row < $total_rows; $row++) {
        $calendar .= '<tr>';
        for ($week = 0; $week <= 6; $week++) {
            $day = $row * 7 + $week - $this_month_one_n + 1;

            if ($day < 1 || $day > $this_month_days) {
                $calendar .= '<td></td>';
                continue;
            }

            if (isset($post['days'][$day])) {
                $dayUrl = mwordstarCalendarArchiveUrl('archive_day', $year, $monthNumber, $day);
                $title = sprintf($GLOBALS['t']['sidebar']['tagPostCount'], $post['days'][$day]);
                $calendar .= '<td class="active text-center py-2"><a rel="archives" href="' . mwordstarCalendarAttr($dayUrl) . '" class="p-0" title="' . mwordstarCalendarAttr($title) . '" data-toggle="tooltip" data-placement="top"><b>' . $day . '</b></a></td>';
            } else {
                $calendar .= '<td class="text-center py-2">' . $day . '</td>';
            }
        }
        $calendar .= '</tr>';
    }

    $calendar .= '</tbody></table>';

    return array(
        'calendar' => $calendar,
        'previous' => $post['previous'],
        'next' => $post['next'],
        'previousUrl' => $post['previous'] ? mwordstarCalendarArchiveUrl('archive_month', substr($post['previous'], 0, 4), substr($post['previous'], 5, 2)) : '',
        'nextUrl' => $post['next'] ? mwordstarCalendarArchiveUrl('archive_month', substr($post['next'], 0, 4), substr($post['next'], 5, 2)) : ''
    );
}

/**
 * 生成 Bootstrap4 分页，并判断是否有下一页
 *
 * @param object $archive 包含 pageNav 方法的 typecho 文章或评论对象
 * @param string $previousPageTitle 用于上一页 title 的文字
 * @param string $nextPageTitle 用于下一页 title 的文字
 * @return bool 有下一页返回 true，否则返回 false（包括没有分页的情况）
 */
function bootstrap4Pagination($archive, $previousPageTitle, $nextPageTitle) {
    ob_start();
    // typecho 分页
    $archive->pageNav('<i class="icon-chevron-left"></i>', '<i class="icon-chevron-right"></i>', 1, '...', array(
        'wrapTag' => 'ul',
        'wrapClass' => 'pagination justify-content-center',
        'itemTag' => 'li',
        'textTag' => 'span',
        'currentClass' => 'active',
        'prevClass' => 'prev',
        'nextClass' => 'next'
    ));
    $content = ob_get_contents();
    ob_end_clean();

    // 如果没有分页则不输出，并返回 false
    if (empty($content)) {
        return false;
    }

    // 给 li 加入 page-item
    $content = preg_replace('/<li(\s+)class="/i', '<li$1class="page-item ', $content);
    $content = preg_replace('/<li>/i', '<li class="page-item">', $content);

    // 给 a 加入 page-link
    $content = preg_replace('/<a href=/', '<a class="page-link" href=', $content);

    // 将 Typecho 默认的 <span> 替换为带类的 <span> (用于当前页高亮和省略号)
    $content = preg_replace('/<span>/', '<span class="page-link">', $content);

    // 为当前激活状态添加 aria-current="page"
    $content = str_replace('<li class="page-item active"><a class="page-link"', '<li class="page-item active"><a aria-current="page" class="page-link"', $content);

    // 给上一页和下一页的链接添加文本提示
    $content = preg_replace_callback(
        '/<a\s+(class="page-link"[^>]*href="[^"]*"[^>]*)><i\s+class="icon-chevron-left"><\/i><\/a>/i',
        function($matches) use ($previousPageTitle) {
            return '<a ' . $matches[1] . ' aria-label="' . $previousPageTitle . '" title="' . $previousPageTitle . '" data-toggle="tooltip" data-placement="top"><i class="icon-chevron-left"></i></a>';
        },
        $content
    );
    $content = preg_replace_callback(
        '/<a\s+(class="page-link"[^>]*href="[^"]*"[^>]*)><i\s+class="icon-chevron-right"><\/i><\/a>/i',
        function($matches) use ($nextPageTitle) {
            return '<a ' . $matches[1] . ' aria-label="' . $nextPageTitle . '" title="' . $nextPageTitle . '" data-toggle="tooltip" data-placement="top"><i class="icon-chevron-right"></i></a>';
        },
        $content
    );

    // 检查是否存在下一页链接（通过查找最终生成的下一页图标）
    $hasNext = (strpos($content, 'icon-chevron-right') !== false);

    echo $content;
    return $hasNext;
}

/**
 * 输出自定义的 SEO 标签 (Canonical & Noindex)
 *
 * @param object $obj 传入的 $this 对象
 * @param array $seoOptions SEO 相关的设置
 */
function themeSeoTags($obj) {
    $options = Helper::options();
    // 搜索页添加 noindex
    if ($obj->is('search') && $options->searchPageNoindex == 'show') {
        echo '<meta name="robots" content="noindex, follow">';
    }
    // 日期归档页添加 noindex
    if ($obj->is('date') && $options->dateArchivePageNoindex == 'show') {
        echo '<meta name="robots" content="noindex, follow">';
    }
    // 作者归档页添加 noindex
    if ($obj->is('author') && $options->authorPageNoindex == 'show') {
        echo '<meta name="robots" content="noindex, follow">';
    }

    // 输出 canonical 链接
    // 文章页和独立页面
    if ($obj->is('post') || $obj->is('page')) {
        echo '<link rel="canonical" href="' . $obj->permalink . '" />';
    }
    // 获取当前的路由路径信息
    $path = Typecho_Request::getInstance()->getPathInfo();
    $currentUrl = Typecho_Common::url($path, $options->index);
    // 分类和标签归档页
    if ($obj->is('tag') || $obj->is('category')) {
        echo '<link rel="canonical" href="' . $currentUrl . '" />';
    }
    // 首页
    if ($obj->is('index')) {
        if ($path === '/' || empty($path)) {
            echo '<link rel="canonical" href="' . rtrim($options->siteUrl, '/') . '/" />' . "\n";
        } else {
            echo '<link rel="canonical" href="' . $currentUrl . '" />' . "\n";
        }
    }
}
