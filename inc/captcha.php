<?php

/**
 * MWordStar 主题 - 评论验证码
 *
 * 包含函数：
 *  - commentCaptchaEnabled             是否启用图片验证码
 *  - commentCaptchaHash                生成验证码校验值
 *  - commentCaptchaImage               生成并输出验证码图片
 *  - commentCaptchaJsonError           输出验证码错误 JSON
 *  - commentTurnstileVerify            校验 Cloudflare Turnstile
 *  - commentCaptchaFilter              评论提交前验证码校验（钩子回调）
 *
 * @package MWordStar
 */

/**
 * 判断评论算数验证码是否启用
 *
 * @return bool 启用返回 true，禁用返回 false
 */
function commentCaptchaEnabled() {
    return Helper::options()->commentCaptcha == 'image';
}

/**
 * 生成验证码校验值
 *
 * 把算数题答案和生成时间放在一起，用密钥运算出一个校验值，
 * 校验值会随表单输出给访客，提交评论时再重新运算比对。
 * 密钥取自后台设置，没有填写时使用默认密钥 12345678。
 *
 * @param int $answer 算数题答案
 * @param int $time 生成验证码的时间戳
 * @return string 校验值
 */
function commentCaptchaHash($answer, $time) {
    $secret = trim(Helper::options()->commentCaptchaSecret);

    if ($secret == '') {
        $secret = '12345678';
    }

    return hash_hmac('sha256', $answer . '|' . $time, $secret);
}

/**
 * 输出评论图片验证码
 *
 * 生成一道两位数以内的加法算数题，把算式绘制成图片并以 base64
 * 的形式用 JSON 输出，验证码有效期为 5 分钟。生成前会先检查
 * PHP 是否启用了 GD 图片处理库，未启用时不生成图片并输出错误信息。
 *
 * @return void
 */
function commentCaptchaImage() {
    // 评论提交请求不会渲染主题模板，需要手动加载语言文件
    if (!isset($GLOBALS['t'])) {
        languageInit(Helper::options()->language);
    }

    // 未启用验证码时不输出
    if (!commentCaptchaEnabled()) {
        commentCaptchaJsonError($GLOBALS['t']['comment']['captchaDisabled']);
        return;
    }

    // 检查 PHP 是否启用了 GD 图片处理库
    if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor') || !function_exists('imagestring')) {
        commentCaptchaJsonError($GLOBALS['t']['comment']['captchaGdMissing']);
        return;
    }

    $num1 = mt_rand(1, 10);
    $num2 = mt_rand(1, 10);
    $answer = $num1 + $num2;
    $time = time();

    // 创建验证码图片
    $width = 100;
    $height = 40;
    $image = imagecreatetruecolor($width, $height);

    // 背景色
    $bgColor = imagecolorallocate($image, 246, 248, 250);
    imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

    // 添加干扰线
    for ($i = 0; $i < 4; $i++) {
        $lineColor = imagecolorallocate($image, mt_rand(150, 230), mt_rand(150, 230), mt_rand(150, 230));
        imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
    }

    // 添加干扰点
    for ($i = 0; $i < 40; $i++) {
        $dotColor = imagecolorallocate($image, mt_rand(120, 235), mt_rand(120, 235), mt_rand(120, 235));
        imagesetpixel($image, mt_rand(0, $width - 1), mt_rand(0, $height - 1), $dotColor);
    }

    // 绘制算式文字
    $text = $num1 . ' + ' . $num2 . ' = ?';
    $textColor = imagecolorallocate($image, 45, 55, 75);
    $font = 5;
    $textWidth = strlen($text) * imagefontwidth($font);
    $textHeight = imagefontheight($font);
    $x = intval(($width - $textWidth) / 2);
    $y = intval(($height - $textHeight) / 2);
    imagestring($image, $font, $x, $y, $text, $textColor);

    // 把图片转换为 base64
    ob_start();
    imagepng($image);
    $imageData = ob_get_clean();
    imagedestroy($image);

    if ($imageData === false || $imageData == '') {
        commentCaptchaJsonError($GLOBALS['t']['comment']['captchaGenerateError']);
        return;
    }

    // 生成校验 token，token 中包含生成时间和校验值
    $hash = commentCaptchaHash($answer, $time);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'result' => 'success',
        'image' => base64_encode($imageData),
        'token' => $time . '.' . $hash
    ), JSON_UNESCAPED_UNICODE);
}

/**
 * 输出评论图片验证码的错误信息
 *
 * @param string $message 错误信息
 * @return void
 */
function commentCaptchaJsonError($message) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'result' => 'error',
        'message' => $message
    ), JSON_UNESCAPED_UNICODE);
}

/**
 * 校验 Cloudflare Turnstile 验证码
 *
 * 把前端 Turnstile 组件生成的 token 和后台设置的 Secret key 一起
 * 发送到 Cloudflare 官方接口校验，校验通过返回 true。token 只能
 * 使用一次，校验失败时返回 false。
 *
 * @param string $token Turnstile 组件生成的 token
 * @param string $secretKey 后台设置的 Cloudflare Turnstile Secret key
 * @param string|null $userIp 访问者 IP，可省略，未指定时使用请求 IP
 * @return bool 校验通过返回 true，否则返回 false
 */
function commentTurnstileVerify($token, $secretKey, $userIp = null) {
    // token 或密钥为空时直接判定不通过
    if (empty($token) || empty($secretKey)) {
        return false;
    }

    $secretKey = trim($secretKey);
    $token = trim($token);

    // 未指定 IP 时使用请求的 IP
    if (empty($userIp) && isset($_SERVER['REMOTE_ADDR'])) {
        $userIp = $_SERVER['REMOTE_ADDR'];
    }

    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $data = array(
        'secret' => $secretKey,
        'response' => $token
    );

    // remoteip 是可选参数，存在时才传递
    if (!empty($userIp)) {
        $data['remoteip'] = $userIp;
    }

    // 使用 cURL 发送 POST 请求
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $result = curl_exec($ch);
    curl_close($ch);

    // 请求失败时判定不通过
    if ($result === false) {
        return false;
    }

    // 解析接口返回的 JSON
    $response = json_decode($result, true);

    // 只信任接口明确返回 success 为 true 的结果
    return is_array($response) && isset($response['success']) && $response['success'] === true;
}

/**
 * 评论验证码校验
 *
 * 挂载到 Widget_Feedback 的 comment 钩子上，在评论写入前根据后台
 * 设置的验证码类型校验访客提交的验证码，校验失败时抛出异常阻止
 * 评论提交。已登录用户不需要输入验证码。
 *
 * @param array $comment 评论数据
 * @param Widget_Archive $post 评论所属的文章对象
 * @return array 评论数据
 * @throws Typecho_Widget_Exception 验证码错误或已过期时抛出异常
 */
function commentCaptchaFilter($comment, $post) {
    // 已登录用户不校验验证码
    if (Typecho_Widget::widget('Widget_User')->hasLogin()) {
        return $comment;
    }

    // 评论提交请求不会渲染主题模板，需要手动加载语言文件
    if (!isset($GLOBALS['t'])) {
        languageInit(Helper::options()->language);
    }

    // 根据后台设置选择验证方式
    $captchaType = Helper::options()->commentCaptcha;

    // 关闭验证码时不校验
    if ($captchaType == 'disable') {
        return $comment;
    }

    // Cloudflare Turnstile 验证
    if ($captchaType == 'turnstile') {
        $token = isset($_POST['cf-turnstile-response']) ? trim($_POST['cf-turnstile-response']) : '';

        if (!commentTurnstileVerify($token, Helper::options()->commentTurnstileSecret)) {
            throw new Typecho_Widget_Exception($GLOBALS['t']['comment']['turnstileError']);
        }

        return $comment;
    }

    // 图片算数验证码
    $answer = isset($_POST['captcha_answer']) ? trim($_POST['captcha_answer']) : '';
    $token = isset($_POST['captcha_token']) ? trim($_POST['captcha_token']) : '';

    // 从 token 中解析生成时间和校验值
    $tokenData = explode('.', $token, 2);
    $time = isset($tokenData[0]) ? intval($tokenData[0]) : 0;
    $hash = isset($tokenData[1]) ? $tokenData[1] : '';

    // 检查验证码是否在 5 分钟有效期内
    if ($time <= 0 || $time > time() || time() - $time > 300) {
        throw new Typecho_Widget_Exception($GLOBALS['t']['comment']['captchaExpired']);
    }

    // 用提交上来的答案和时间重新运算校验值并比对
    if ($answer == '' || $hash == '' || !ctype_digit($answer) || !hash_equals(commentCaptchaHash($answer, $time), $hash)) {
        throw new Typecho_Widget_Exception($GLOBALS['t']['comment']['captchaError']);
    }

    return $comment;
}

// 注册评论验证码校验钩子
Typecho_Plugin::factory('Widget_Feedback')->comment = 'commentCaptchaFilter';
