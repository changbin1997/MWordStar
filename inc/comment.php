<?php

/**
 * MWordStar 主题 - 评论与头像
 *
 * 包含函数：
 *  - isQQEmail                         判断是否为 QQ 邮箱
 *  - QQAvatar                          输出 QQ 头像
 *  - reply                             父评论引用链接
 *  - gravatar                          输出 Gravatar 头像
 *  - parseSecretComment                解析评论 [hide] 私密内容
 *
 * @package MWordStar
 */

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
 * 解析评论内容中的 [hide] 私密短代码
 *
 * @param string         $content 原始评论内容
 * @param Typecho_Widget $comment 当前评论对象 (包含 authorId, mail 等信息)
 * @return array 返回包含解析状态的关联数组：
 *               - hide: (bool) 是否包含 [hide] 短代码
 *               - canView: (bool) 当前访问者是否有权限查看隐藏内容
 *               - content: (string, 可选) 解析后的评论内容（无权限时省略该字段）
 */
function parseSecretComment($content, $comment) {
    // 正则匹配 [hide]隐藏内容[/hide]
    $pattern = '/\[hide\](.*?)\[\/hide\]/is';

    // 如果评论中没有 [hide] 标签，属于普通评论：hide=false, canView=true, 返回原内容
    if (!preg_match($pattern, $content)) {
        return array(
            'hide'    => false,
            'canView' => true,
            'content' => $content
        );
    }

    // 获取当前登录用户信息
    $user = Typecho_Widget::widget('Widget_User');
    
    // 1. 判断是否为管理员 (登录且具备 administrator 权限)
    $isAdmin = $user->hasLogin() && $user->pass('administrator', true);
    
    // 2. 判断是否为当前评论的作者 (针对已登录用户)
    $isLoggedInAuthor = $user->hasLogin() && ($user->uid == $comment->authorId);
    
    // 3. 判断是否为当前评论的访客 (针对未登录的普通访客，比对 Cookie 中的邮箱)
    $rememberMail = Typecho_Cookie::get('__typecho_remember_mail');
    $isGuestAuthor = !empty($rememberMail) && ($rememberMail === $comment->mail);

    // 综合判断是否有权限查看隐藏内容
    $canView = $isAdmin || $isLoggedInAuthor || $isGuestAuthor;

    // 无权限查看：返回 hide=true, canView=false，不包含 content 字段
    if (!$canView) {
        return array(
            'hide'    => true,
            'canView' => false
        );
    }

    // 有权限查看：去除 [hide] 标签并保留内容
    $parsedContent = preg_replace_callback($pattern, function($matches) {
        return $matches[1];
    }, $content);

    return array(
        'hide'    => true,
        'canView' => true,
        'content' => $parsedContent
    );
}
