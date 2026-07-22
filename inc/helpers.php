<?php

/**
 * 设置语言
 *
 * @param string $language 语言设置选择的默认语言
 * @return void
 */
function languageInit($language) {
    $languageList = array('zh', 'en');
    // 如果有语言设置 Cookie 就优先使用 Cookie 存储的语言
    if (isset($_COOKIE['language']) && $_COOKIE['language'] != '') {
        $language = $_COOKIE['language'];
    }

    // 自动选择
    if ($language == 'auto') {
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // 浏览器没有发送语言信息就默认使用英文
            $language = 'en';
        } else {
            $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

            // 检查是否存在 intl 扩展中的函数
            if (function_exists('locale_accept_from_http')) {
                $userLanguage = locale_accept_from_http($acceptLang);
                $language = substr($userLanguage, 0, 2);
            } else {
                // 降级方案：直接截取 HTTP_ACCEPT_LANGUAGE 的前两个字符
                $language = strtolower(substr($acceptLang, 0, 2));
            }

            // 如果用户浏览器的语言是不支持的语言就使用英语
            if (!in_array($language, $languageList)) {
                $language = 'en';
            }
        }
    }

    // 选择中文
    if ($language == 'zh-CN' || $language == 'zh' || $language == null) {
        require_once __DIR__ . '/../languages/zh.php';
        $GLOBALS['t'] = ZH;
    }
    // 选择英文
    elseif ($language == 'en') {
        require_once __DIR__ . '/../languages/en.php';
        $GLOBALS['t'] = EN;
    }

    $GLOBALS['language'] = $language == null ? 'zh-CN' : $language;
}

/**
 * 把一些支持多语言显示的内容传给 JS 显示
 *
 * @return void
 */
function localizeScript() {
    // 需要传给 JS 的翻译内容
    $t = array(
        'pressEnterToAddTheEmojiToTheCommentInputField' => $GLOBALS['t']['emoji']['pressEnterToAddTheEmojiToTheCommentInputField'],
        'zoomIn' => $GLOBALS['t']['imageLightbox']['zoomIn'],
        'zoomOut' => $GLOBALS['t']['imageLightbox']['zoomOut'],
        'rotateLeft' => $GLOBALS['t']['imageLightbox']['rotateLeft'],
        'rotateRight' => $GLOBALS['t']['imageLightbox']['rotateRight'],
        'closeImage' => $GLOBALS['t']['imageLightbox']['closeImage'],
        'nextImage' => $GLOBALS['t']['imageLightbox']['nextImage'],
        'previousImage' => $GLOBALS['t']['imageLightbox']['previousImage'],
        'copyCode' => $GLOBALS['t']['code']['copyCode'],
        'copySuccess' => $GLOBALS['t']['code']['copySuccess'],
        'copyError' => $GLOBALS['t']['code']['copyError'],
        'cancelReply' => $GLOBALS['t']['comment']['cancelReply'],
        'enterThePasswordToViewIt' => $GLOBALS['t']['post']['enterThePasswordToViewIt'],
        'enterYourPassword' => $GLOBALS['t']['post']['enterYourPassword'],
        'submit' => $GLOBALS['t']['post']['submit'],
        'replyTo' => $GLOBALS['t']['comment']['replyTo'],
        'like' => $GLOBALS['t']['post']['like'],
        'categoryDistribution' => $GLOBALS['t']['dataPage']['categoryDistribution'],
        'tableOfContents' => $GLOBALS['t']['sidebar']['tableOfContents'],
        'category' => $GLOBALS['t']['post']['category'],
        'tag' => $GLOBALS['t']['post']['tag'],
        'author' => $GLOBALS['t']['post']['author'],
        'switchToDarkMode' => $GLOBALS['t']['themeColor']['switchToDarkMode'],
        'switchToLightMode' => $GLOBALS['t']['themeColor']['switchToLightMode'],
        'QRCode' => $GLOBALS['t']['post']['QRCode']
    );
    $t = json_encode($t);
    echo '<script type="text/javascript"> window.t = ' . $t . '; </script>';
}

/**
 * 根据语言格式化文章日期
 *
 * @param int $date 时间戳
 * @return string 格式化后的日期
 */
function postDateFormat($date) {
    if ($GLOBALS['language'] == 'zh' or $GLOBALS['language'] == 'zh-CN') {
        $date = date('Y年m月d日', $date);
    }else {
        $date = date('j M Y', $date);
    }
    return $date;
}

/**
 * 获取英文的日序数后缀
 *
 * @param int $timestamp 时间戳
 * @return string 英文的日序数后缀
 */
function getDayWithSuffix($timestamp) {
    // 提取日期中的天
    $day = date('j', $timestamp);
    // 根据天数返回对应的后缀
    if (!in_array(($day % 100), [11, 12, 13])) {
        switch ($day % 10) {
            case 1: return $day . 'st';
            case 2: return $day . 'nd';
            case 3: return $day . 'rd';
        }
    }
    return $day . 'th';
}

/**
 * 检测是否是QQ邮箱
 *
 * @param string $email 邮箱
 * @return bool
 */
function isQQEmail($email) {
    $re = '/^\d{6,11}\@qq\.com$/';
    preg_match($re, $email, $result);
    if (count($result)) {
        return true;
    }
    return false;
}

/**
 * 获取QQ头像，直接输出
 *
 * @param string $email 邮箱
 * @param string $name 称呼，用于 img 的 alt
 * @param int $size 头像尺寸
 * @return void
 */
function QQAvatar($email, $name, $size) {
    $qq = str_replace('@qq.com', '', $email);  // 获取QQ号
    $imgUrl = 'https://q2.qlogo.cn/headimg_dl?dst_uin=' . $qq . '&spec=' . $size;
    echo '<img src="' . $imgUrl . '" alt="' . $name . '" class="avatar">';
}

/**
 * 检查数据库字段
 *
 * @return void
 */
function checkField() {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    $adapter = $db->getAdapterName(); // 获取数据库驱动名称
    // 要检查的字段
    $fields = [
        'views' => 'INT DEFAULT 0 NOT NULL',
        'agree' => 'INT DEFAULT 0 NOT NULL'
    ];

    foreach ($fields as $colName => $colAttr) {
        $needAdd = true;
        // 针对 PostgreSQL 的特殊处理
        if (strpos($adapter, 'Pgsql') !== false) {
            // 查询 information_schema 检查字段是否存在
            $check = $db->fetchRow($db->select()->from('information_schema.columns')->where('table_name = ?', $prefix . 'contents')->where('column_name = ?', $colName));
            if (!empty($check)) {
                $needAdd = false; // 字段已存在，无需添加
            }
        }

        if ($needAdd) {
            try {
                // 根据数据库类型调整 SQL 语法
                if (strpos($adapter, 'Pgsql') !== false) {
                    // PostgreSQL: 使用双引号，移除 INT(10) 的长度限制（PgSQL不支持）
                    $pgAttr = str_replace('INT(10)', 'INTEGER', $colAttr);
                    $sql = 'ALTER TABLE "' . $prefix . 'contents" ADD COLUMN "' . $colName . '" ' . $pgAttr . ';';
                } else {
                    // MySQL / SQLite: 保持原有语法 (使用反引号)
                    $sql = 'ALTER TABLE `' . $prefix . 'contents` ADD `' . $colName . '` ' . $colAttr . ';';
                }

                $db->query($sql);
            } catch (Typecho_Db_Exception $e) {
                // 忽略错误
            }
        }
    }
}

/**
 * 设置文章阅读量
 *
 * @param object $archive 文章
 * @return int 返回阅读量
 */
function postViews($archive) {
    // 获取文章的 cid
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    // 查询出阅读量
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    // 是否是内容页
    if ($archive->is('single')) {
        // 获取阅读 cookie
        $views = Typecho_Cookie::get('extend_contents_views');
        if (empty($views)) {
            $views = array();
        } else {
            $views = explode(',', $views);
        }
        // 如果 cookie 不存在
        if (!in_array($cid, $views)) {
            // 阅读量 +1
            $db->query($db->update('table.contents')->rows(array('views' => (int)$row['views'] + 1))->where('cid = ?', $cid));
            $views[] = $cid;
            $views = implode(',', $views);
            // 写入阅读 cookie
            Typecho_Cookie::set('extend_contents_views', $views);
            // 返回的最终阅读量 +1
            $row['views'] ++;
        }
    }
    return $row['views'];
}

/**
 * 评论时间格式化
 *
 * @param int $date 日期时间戳
 * @param string $options 评论日期格式设置
 * @return string 返回格式化后的日期
 */
function commentDateFormat($date, $options = 'format1') {
    // 中文日期
    if ($options == 'format1') {
        return date('Y年m月d日 H:i', $date);
    }
    // - 分隔的日期
    if ($options == 'format2') {
        return date('Y-m-d H:i', $date);
    }
    // 英文日期
    if ($options == 'format3') {
        return date('F jS, Y \a\t h:i a', $date);
    }
    // 时间间隔
    if ($options == 'format4') {
        if ($GLOBALS['language'] == 'en') {
            // 英文
            return formatTimeDifferenceEN($date);
        }else {
            // 中文
            return formatTimeDifferenceZH($date);
        }
    }
}

/**
 * 计算时间间隔（中文）
 *
 * @param int $timestamp 时间戳
 * @return string 返回中文的时间间隔
 */
function formatTimeDifferenceZH($timestamp) {
    $timestamp = time() - $timestamp;
    if ($timestamp < 1) {
        return '1秒前';
    }else if ($timestamp < 60) {
        return $timestamp . '秒前';
    }else if ($timestamp > 60 && $timestamp < 3600) {
        return round($timestamp / 60, 0) . '分钟前';
    }else if ($timestamp > 3600 && $timestamp < 86400) {
        return round($timestamp / 3600, 0) . '小时前';
    }else {
        return round($timestamp / 86400, 0) . '天前';
    }
}

/**
 * 计算时间间隔（英文）
 *
 * @param int $timestamp 时间戳
 * @return string 返回英文的时间间隔
 */
function formatTimeDifferenceEN($timestamp) {
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return $diff == 1 ? "1 second ago" : "$diff seconds ago";
    }

    $minutes = floor($diff / 60);
    if ($minutes < 60) {
        return $minutes == 1 ? "1 minute ago" : "$minutes minutes ago";
    }

    $hours = floor($minutes / 60);
    if ($hours < 24) {
        return $hours == 1 ? "1 hour ago" : "$hours hours ago";
    }

    $days = floor($hours / 24);
    return $days == 1 ? "1 day ago" : "$days days ago";
}

/**
 * 获取父评论的姓名
 *
 * @param int $parent 评论的 coid
 * @return string 返回父评论的姓名链接
 */
function reply($parent) {
    $db = Typecho_Db::get();
    $commentInfo = $db->fetchRow($db->select('author,status,mail')->from('table.comments')->where('coid = ?', $parent));
    $link = '<a class="parent-link" data-parent="' . $parent . '" href="#comment-' . $parent . '">@' . $commentInfo['author'] .  '</a>';
    return $link;
}

/**
 * 获取点赞数量
 *
 * @param int $cid 文章的cid
 * @return array 返回点赞数量和文章是否被点赞过
 */
function agreeNum($cid) {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    $AgreeRecording = Typecho_Cookie::get('typechoAgreeRecording');
    if (empty($AgreeRecording)) {
        Typecho_Cookie::set('typechoAgreeRecording', json_encode(array(0)));
    }

    return array(
        //  点赞数量
        'agree' => $agree['agree'],
        //  文章是否点赞过
        'recording' => in_array($cid, json_decode(Typecho_Cookie::get('typechoAgreeRecording')))?true:false
    );
}

/**
 * 点赞
 *
 * @param int $cid 文章的cid
 * @return mixed 返回赞数
 */
function agree($cid) {
    $db = Typecho_Db::get();
    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    $agreeRecording = Typecho_Cookie::get('typechoAgreeRecording');
    if (empty($agreeRecording)) {
        Typecho_Cookie::set('typechoAgreeRecording', json_encode(array($cid)));
    }else {
        $agreeRecording = json_decode($agreeRecording);
        //  判断文章是否点赞过
        if (in_array($cid, $agreeRecording)) {
            //  如果当前文章的 cid 在 cookie 中就返回文章的赞数，不再往下执行
            return $agree['agree'];
        }
        array_push($agreeRecording, $cid);
        Typecho_Cookie::set('typechoAgreeRecording', json_encode($agreeRecording));
    }

    $db->query($db->update('table.contents')->rows(array('agree' => (int)$agree['agree'] + 1))->where('cid = ?', $cid));
    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    return $agree['agree'];
}

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
 * 获取文章分类数量
 *
 * @return int 返回文章分类数量
 */
function categoryCount() {
    $db = Typecho_Db::get();
    $row = $db->fetchRow(
        $db->select('COUNT(*) AS cnt')->from('table.metas')->where('type = ?', 'category')
    );

    if (!$row) return 0;
    return (int) ($row['cnt'] ?? $row['COUNT(*)'] ?? $row['count'] ?? 0);
}

/**
 * 获取标签数量
 *
 * @return int 返回标签数量
 */
function tagCount() {
    $db = Typecho_Db::get();
    $row = $db->fetchRow(
        $db->select('COUNT(*) AS cnt')->from('table.metas')->where('type = ?', 'tag')
    );

    if (!$row) return 0;
    return (int) ($row['cnt'] ?? $row['COUNT(*)'] ?? $row['count'] ?? 0);
}

/**
 * 获取总阅读量
 *
 * @return int 返回总阅读量
 */
function viewsCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchRow($db->select('SUM(views) AS viewsCount')->from('table.contents'));
    if ($count['viewsCount'] == null) $count['viewsCount'] = 0;
    return $count['viewsCount'];
}

/**
 * 获取总点赞数
 *
 * @return int 返回总点赞数
 */
function agreeCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchRow($db->select('SUM(agree) AS agreeCount')->from('table.contents'));
    if ($count['agreeCount'] == null) $count['agreeCount'] = 0;
    return $count['agreeCount'];
}

/**
 * 获取阅读量排名前 5 的 5 篇文章的信息
 *
 * @return array 返回阅读量排名前5的文章标题、链接、阅读量
 */
function top5post() {
    $db = Typecho_Db::get();
    $top5Post = $db->fetchAll($db->select()->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->order('views', Typecho_Db::SORT_DESC)->offset(0)->limit(5));
    $postList =array();
    foreach ($top5Post as $post) {
        // 生成文章链接
        $permalink = Typecho_Common::url(Typecho_Router::url('post', $post), Helper::options()->index);
        $postList[] = array(
            'title' => $post['title'],
            'link' => $permalink,
            'views' => $post['views']
        );
    }
    return $postList;
}

/**
 * 获取评论数排名前 5 的 5 篇文章的信息
 *
 * @return array 返回评论数排名前5的文章标题、链接、评论数
 */
function top5CommentPost() {
    $db = Typecho_Db::get();
    $top5Post = $db->fetchAll($db->select()->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->order('commentsNum', Typecho_Db::SORT_DESC)->offset(0)->limit(5));
    $postList = array();
    foreach ($top5Post as $post) {
        // 生成文章链接
        $permalink = Typecho_Common::url(Typecho_Router::url('post', $post), Helper::options()->index);
        $postList[] = array(
            'title' => $post['title'],
            'link' => $permalink,
            'commentsNum' => $post['commentsNum']
        );
    }
    return $postList;
}

/**
 * 获取 ECharts 格式要求的文章更新日历
 *
 * @param int $start 起始时间戳
 * @param int $end 结束时间戳
 * @return array 返回用于日历的文章更新数据
 */
function postCalendar($start, $end) {
    $db = Typecho_Db::get();
    $dateList = $db->fetchAll($db->select('created')->from('table.contents')->where('created > ?', $start)->where('created < ?', $end));
    if (count($dateList) < 1) {
        return array();
    }
    $dateList2 = array();
    foreach ($dateList as $val) {
        array_push($dateList2, date('Y-m-d', $val['created']));
    }
    $dateList2 = array_count_values($dateList2);
    $key = array_keys($dateList2);
    $dateList = array();

    for ($i = 0;$i < count($dateList2);$i ++) {
        $dateList[] = array(
            $key[$i],
            $dateList2[$key[$i]]
        );
    }

    return $dateList;
}

/**
 * 获取 ECharts 格式要求的评论更新日历
 *
 * @param int $start 起始时间戳
 * @param int $end 结束时间戳
 * @return array 返回用于日历的评论动态数据
 */
function commentCalendar($start, $end) {
    $db = Typecho_Db::get();
    $dateList = $db->fetchAll($db->select('created')->from('table.comments')->where('created > ?', $start)->where('created < ?', $end));
    if (count($dateList) < 1) {
        return array();
    }
    $dateList2 = array();
    foreach ($dateList as $val) {
        array_push($dateList2, date('Y-m-d', $val['created']));
    }
    $dateList2 = array_count_values($dateList2);
    $key = array_keys($dateList2);
    $dateList = array();

    for ($i = 0;$i < count($dateList2);$i ++) {
        $dateList[] = array(
            $key[$i],
            $dateList2[$key[$i]]
        );
    }

    return $dateList;
}

/**
 * 获取每个分类的文章数量
 *
 * @return array 返回每个分类的文章数量
 */
function categoryPostCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchAll($db->select('name', 'count AS value')->from('table.metas')->where('type = ?', 'category'));
    if (count($count) < 1) {
        return array();
    }
    return $count;
}

/**
 * 获取父分类的名称
 *
 * @param int $categoryId 分类id
 * @return string 返回父分类的名称
 */
function getParentCategory($categoryId) {
    $db = Typecho_Db::get();
    $category = $db->fetchRow($db->select()->from('table.metas')->where('mid = ?', $categoryId));
    return $category['name'];
}

/**
 * 获取网站管理员的用户信息
 *
 * @return object 管理员用户信息
 */
function getAdminInfo() {
    $db = Typecho_Db::get();
    $userInfo = $db->fetchRow($db->select('mail', 'url', 'screenName', 'created')->from('table.users')->where('group = ?', 'administrator'));
    return $userInfo;
}

/**
 * 获取 Gravatar 头像，直接输出 img
 *
 * @param string $email 邮箱
 * @param int $size 头像尺寸
 * @param string $gravatarUrl 自定义 gravatarUrl 源
 * @param string $alt 头像图片描述
 * @return void
 */
function gravatar($email, $size, $gravatarUrl = '', $alt = '') {
    $url = $gravatarUrl . md5(strtolower(trim($email))) . '?s=' . $size;
    if ($gravatarUrl == '' or $gravatarUrl == null) {
        $url = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=' . $size;
    }
    echo '<img src="' . $url . '" alt="' . $alt . '" class="avatar" />';
}

/**
 * 计算两个时间之间相差的天数
 *
 * @param int $time1 时间戳
 * @param int $time2 时间戳
 * @return false|float 返回天数
 */
function getDays($time1, $time2) {
    return floor(($time2 - $time1) / 86400);
}

/**
 * 把图片的 src 替换为 data-src，用于图片懒加载
 *
 * @param string $content 文章内容
 * @return string 替换后的文章内容
 */
function replaceImgSrc($content) {
    $pattern = '/<img(.*?)src(.*?)=(.*?)"(.*?)">/i';
    $replacement = '<img$1data-src$3="$4"$5 class="load-img">';
    return preg_replace($pattern, $replacement, $content);
}

/**
 * 根据秒数偏移量设置全局时区
 * * @param int|string $offset Typecho 格式的时区偏移量 (例如: "28800" 或 28800)
 * @return void
 */
function setTimezoneByOffset($offset) {
    // 强制转换为整数
    $offset = (int) $offset;

    // 尝试根据偏移量获取合法的时区名称 (例如 "Asia/Shanghai" 或 "Etc/GMT-8")
    $timezone_name = timezone_name_from_abbr('', $offset, 0);
    // 如果获取失败（极少数情况），或者获取到的是 false
    if ($timezone_name === false) {
        // 手动回退逻辑：构建 Etc/GMT 时区
        $hours = $offset / 3600;
        if ($hours > 0) {
            $timezone_name = 'Etc/GMT-' . $hours;
        } else {
            $timezone_name = 'Etc/GMT+' . abs($hours);
        }
    }

    // 设置全局时区
    @date_default_timezone_set($timezone_name);
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
 * 生成 Bootstrap4 分页
 *
 * @param object $archive 包含 pageNav 方法的 typecho 文章或评论对象
 * @param string $previousPageTitle 用于上一页 title 的文字
 * @param string $nextPageTitle 用于下一页 title 的文字
 * @return void
 */
function bootstrap4Pagination($archive, $previousPageTitle, $nextPageTitle) {
    ob_start();
    // typecho 分页
    $archive->pageNav('<i class="icon-chevron-left"></i>', '<i class="icon-chevron-right"></i>', 1, '...', array(
        'wrapTag' => 'ul',
        'wrapClass' => 'pagination justify-content-center',
        'itemTag' => 'li',
        'textTag' => 'a',
        'currentClass' => 'active',
        'prevClass' => 'prev',
        'nextClass' => 'next'
    ));
    $content = ob_get_contents();
    ob_end_clean();

    // 如果没有分页则不输出
    if (empty($content)) {
        return;
    }

    // 给 li 加入 page-item
    $content = preg_replace('/<li(\s+)class="/i', '<li$1class="page-item ', $content);
    $content = preg_replace('/<li>/i', '<li class="page-item">', $content);

    // 给 a 加入 page-link
    $content = preg_replace('/<a href=/', '<a class="page-link" href=', $content);
    $content = str_replace('<a>', '<a class="page-link">', $content);

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

    echo $content;
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
