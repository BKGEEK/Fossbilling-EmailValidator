<?php

/**
 * Copyright 2022-2024 FOSSBilling
 * Copyright 2011-2021 BoxBilling, Inc.
 * SPDX-License-Identifier: Apache-2.0.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */

namespace FOSSBilling;

class EmailValidator
{
    protected $di = null;

    public function setDi($di)
    {
        $this->di = $di;
    }

    public function getDi()
    {
        return $this->di;
    }

    /**
     * 验证邮箱地址是否符合允许的后缀
     *
     * @param string $email 邮箱地址
     * @return bool
     * @throws InformationException
     */
    public function validateEmailDomain($email)
    {
        $config = $this->di['mod_config']('client');
        
        // 检查是否启用邮箱后缀验证
        $enabled = isset($config['email_domain_validation_enabled']) ? $config['email_domain_validation_enabled'] : false;
        if (!$enabled) {
            return true;
        }

        // 获取允许的邮箱后缀列表
        $allowedDomains = isset($config['email_allowed_domains']) ? $config['email_allowed_domains'] : [];
        $allowedDomains = is_array($allowedDomains) ? $allowedDomains : explode(',', $allowedDomains);
        
        // 清理和标准化后缀列表
        $allowedDomains = array_map('trim', $allowedDomains);
        $allowedDomains = array_map('strtolower', $allowedDomains);
        $allowedDomains = array_filter($allowedDomains);

        if (empty($allowedDomains)) {
            return true;
        }

        // 提取邮箱域名
        $emailParts = explode('@', $email);
        if (count($emailParts) !== 2) {
            throw new InformationException('无效的邮箱地址格式');
        }

        $domain = strtolower(trim($emailParts[1]));

        // 检查是否在允许的域名列表中
        foreach ($allowedDomains as $allowedDomain) {
            if ($domain === $allowedDomain || $this->matchDomainPattern($allowedDomain, $domain)) {
                return true;
            }
        }

        throw new InformationException('邮箱后缀不在允许的列表中，请联系管理员');
    }

    /**
     * 检查是否允许别名邮箱（如Gmail的+别名）
     *
     * @param string $email 邮箱地址
     * @return bool
     * @throws InformationException
     */
    public function validateEmailAlias($email)
    {
        $config = $this->di['mod_config']('client');
        
        // 检查是否启用别名邮箱验证
        $enabled = isset($config['email_alias_validation_enabled']) ? $config['email_alias_validation_enabled'] : false;
        if (!$enabled) {
            return true;
        }

        $emailParts = explode('@', $email);
        if (count($emailParts) !== 2) {
            throw new InformationException('无效的邮箱地址格式');
        }

        $localPart = $emailParts[0];
        
        // 检查是否包含别名符号（+）
        if (strpos($localPart, '+') !== false) {
            throw new InformationException('不允许使用别名邮箱（包含+符号）');
        }

        return true;
    }

    /**
     * 综合验证邮箱地址
     *
     * @param string $email 邮箱地址
     * @return bool
     * @throws InformationException
     */
    public function validateEmail($email)
    {
        // 首先验证邮箱格式
        $email = $this->di['tools']->validateAndSanitizeEmail($email);
        
        // 验证别名邮箱
        $this->validateEmailAlias($email);
        
        // 验证邮箱后缀
        $this->validateEmailDomain($email);
        
        return true;
    }

    /**
     * 简单的域名模式匹配，支持通配符
     *
     * @param string $pattern 模式（如 *.example.com）
     * @param string $domain 要匹配的域名
     * @return bool
     */
    private function matchDomainPattern($pattern, $domain)
    {
        // 如果模式不包含通配符，直接比较
        if (strpos($pattern, '*') === false) {
            return $pattern === $domain;
        }

        // 处理通配符匹配
        $pattern = str_replace('*', '.*', $pattern);
        $pattern = str_replace('.', '\.', $pattern);
        
        return preg_match('/^' . $pattern . '$/i', $domain) === 1;
    }
}