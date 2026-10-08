<?php

/**
 * MWordStar 主题 - 语言本地化与日期时间
 *
 * 包含函数：
 *  - languageInit                      按 Cookie / 浏览器 / 后台设置加载语言包
 *  - getLanguageFromAcceptLanguage     解析 HTTP_ACCEPT_LANGUAGE 判断简体 / 繁体 / 英文
 *  - isTraditionalChineseTag           判断中文语言标签是否为繁体（台湾 / 香港 / 澳门 / Hant）
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
            $language = getLanguageFromAcceptLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }
    }

    // Cookie 和后台设置里可能保存的是完整的语言标签，这里统一成小写的连字符形式
    if (is_string($language)) {
        $language = str_replace('_', '-', strtolower($language));
    }

    // 选择繁体中文（台湾用语），台湾 / 香港 / 澳门 / Hant 都按繁体处理
    if ($language != null && isTraditionalChineseTag($language)) {
        require_once __DIR__ . '/../languages/zh-tw.php';
        $GLOBALS['t'] = ZH_TW;
        $GLOBALS['language'] = 'zh-TW';
        return;
    }

    // 选择简体中文
    // 没有语言设置以及没有标明简繁的中文（例如 zh）都使用简体
    if ($language == null || $language == 'zh' || preg_match('/^zh(-|$)/', $language)) {
        require_once __DIR__ . '/../languages/zh.php';
        $GLOBALS['t'] = ZH;
        $GLOBALS['language'] = 'zh-CN';
        return;
    }

    // 选择英文
    // 语言设置为 en 以及所有不支持的语言都使用英文
    require_once __DIR__ . '/../languages/en.php';
    $GLOBALS['t'] = EN;
    $GLOBALS['language'] = 'en';
}

/**
 * 解析浏览器发送的语言偏好，判断应该使用哪种语言
 *
 * 会先按 q 值对语言标签排序，再用优先级最高的标签判断：
 *  - 标签属于中文时，台湾 / 香港 / 澳门或带 Hant 的按繁体处理，其余按简体处理；
 *  - 标签和中文无关时（例如 es、fr）使用英文。
 *
 * 常见浏览器的语言偏好形式：
 *  - Chrome / Edge：zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7
 *  - Firefox：zh-TW,zh;q=0.8,en-US;q=0.5,en;q=0.3
 *  - Safari：zh-TW（旧版本为小写的 zh-tw），也可能使用 zh-Hant-TW、zh-Hans-CN 这类字形标签
 *  - 部分客户端会使用下划线，例如 zh_TW、zh_CN
 *
 * @param string $acceptLang HTTP_ACCEPT_LANGUAGE 的内容
 * @return string 主题支持的语言代码（zh-CN / zh-TW / en）
 */
function getLanguageFromAcceptLanguage($acceptLang) {
    // 统一成小写的连字符形式，方便处理 zh_TW 这类下划线写法
    $acceptLang = str_replace('_', '-', strtolower($acceptLang));
    $priorityList = array();
    foreach (explode(',', $acceptLang) as $index => $item) {
        $parts = explode(';', trim($item));
        $tag = trim($parts[0]);
        if ($tag == '') {
            continue;
        }
        // 语言标签后面的 q 表示优先级，没有 q 值时按 1 处理
        $q = 1.0;
        if (isset($parts[1]) && preg_match('/q\s*=\s*([0-9.]+)/', $parts[1], $matches)) {
            $q = (float)$matches[1];
        }
        $priorityList[] = array('tag' => $tag, 'q' => $q, 'index' => $index);
    }

    // 按 q 值从高到低排序，q 值相同时保持原来的先后顺序
    usort($priorityList, function ($a, $b) {
        if ($a['q'] == $b['q']) {
            return $a['index'] - $b['index'];
        }
        return $a['q'] < $b['q'] ? 1 : -1;
    });

    // 只按优先级最高的语言标签判断
    if (isset($priorityList[0])) {
        $tag = $priorityList[0]['tag'];
        if ($tag == 'zh' || strpos($tag, 'zh-') === 0) {
            return isTraditionalChineseTag($tag) ? 'zh-TW' : 'zh-CN';
        }
    }

    // 语言偏好和中文无关或者没有语言偏好时都使用英文
    return 'en';
}

/**
 * 判断中文语言标签是否使用繁体
 *
 * 台湾、香港、澳门默认使用繁体，标签中带 Hant（繁体字形）的同样按繁体处理；
 * 带 Hans（简体字形）以及没有标明简繁的（例如 zh）都按简体处理。
 *
 * @param string $tag 已经转换为小写连字符形式的语言标签
 * @return bool 使用繁体返回 true
 */
function isTraditionalChineseTag($tag) {
    if (preg_match('/^zh-(tw|hk|mo)(-|$)/', $tag)) {
        return true;
    }
    if (strpos($tag, 'hant') !== false) {
        return true;
    }
    return false;
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
