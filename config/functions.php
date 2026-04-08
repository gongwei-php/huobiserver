<?php

/**
 * 安全 JSON 编码（全局通用）
 * 防 XSS、防特殊字符、支持中文、不转义斜杠
 * @param mixed $data 数据
 * @return string
 */
if (! function_exists('safe_json_encode')) {
    function safe_json_encode($data): string
    {
        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_QUOT |
                JSON_HEX_AMP
        );
    }
}
