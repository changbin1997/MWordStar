<?php

/**
 * MWordStar 主题 - 语言本地化与日期时间
 *
 * 包含函数：
 *  - languageInit                      按 Cookie / 浏览器 / 后台设置加载语言包
 *  - localizeScript                    输出传给 JS 的多语言翻译
 *  - postDateFormat                    文章日期按语言格式化
 *  - getDayWithSuffix                  英文日序数后缀
 *  - commentDateFormat                 评论日期格式化
 *  - formatTimeDifference              相对时间（文本取自语言包，便于扩展新语言）
 *  - getDays                           两个时间戳相差天数
 *
 * @package MWordStar
 */

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
        'QRCode' => $GLOBALS['t']['post']['QRCode'],
        'loadMore' => $GLOBALS['t']['loadMore']['loadMore'],
        'loading' => $GLOBALS['t']['loadMore']['loading'],
        'noDescription' => $GLOBALS['t']['githubPage']['noDescription'],
        'unknown' => $GLOBALS['t']['githubPage']['unknown'],
        'captchaImageAlt' => $GLOBALS['t']['comment']['captchaImageAlt'],
        'captchaLoadError' => $GLOBALS['t']['comment']['captchaLoadError']
    );
    $t = json_encode($t);
    echo '<script type="text/javascript"> window.t = ' . $t . '; </script>';
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
        return formatTimeDifference($date);
    }
}

/**
 * 计算时间间隔
 *
 * 各单位的表述取自语言包中的 comment.timeDifference，
 * 每个单位包含 [单数, 复数] 两个模板，模板里的 %d 会被替换为数量，
 * 这样新增语言时只需要补充语言包，不需要再添加新的函数。
 *
 * @param int $timestamp 时间戳
 * @return string 返回格式化后的时间间隔
 */
function formatTimeDifference($timestamp) {
    $units = $GLOBALS['t']['comment']['timeDifference'];
    $diff = time() - $timestamp;
    // 时间戳在当前时间之后或者相差不到 1 秒时都按 1 秒计算
    if ($diff < 1) {
        $diff = 1;
    }

    if ($diff < 60) {
        return sprintf($diff == 1 ? $units['seconds'][0] : $units['seconds'][1], $diff);
    }

    $minutes = floor($diff / 60);
    if ($minutes < 60) {
        return sprintf($minutes == 1 ? $units['minutes'][0] : $units['minutes'][1], $minutes);
    }

    $hours = floor($minutes / 60);
    if ($hours < 24) {
        return sprintf($hours == 1 ? $units['hours'][0] : $units['hours'][1], $hours);
    }

    $days = floor($hours / 24);
    return sprintf($days == 1 ? $units['days'][0] : $units['days'][1], $days);
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
