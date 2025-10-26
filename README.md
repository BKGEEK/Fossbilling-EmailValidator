# FOSSBilling

## 邮箱验证功能修改说明

### 修改内容

对 `library/FOSSBilling/Tools.php` 文件中的邮箱验证功能进行了以下修改：

#### 1. 别名邮箱检测规则

- **原规则**：检测多种别名邮箱格式，包括关键词匹配和临时邮箱域名
- **新规则**：只检测包含 `+` 和 `.` 的别名邮箱格式

#### 2. 域名白名单限制

新增了域名白名单功能，目前只允许以下域名：
- `gmail.com`
- `qq.com`  
- `163.com`

### 功能说明

#### `isAliasEmail` 方法

```php
/**
 * 检查是否为别名邮箱
 * 只检测包含+和.的别名邮箱格式，并限制只支持特定域名
 */
private function isAliasEmail(string $email): bool
```

**验证逻辑**：
1. 首先检查邮箱域名是否在白名单内
2. 如果域名不在白名单中，直接视为别名邮箱
3. 如果域名在白名单中，检查用户名部分是否包含 `+` 或 `.` 符号
4. 包含这些符号的邮箱被视为别名邮箱

#### `validateAndSanitizeEmail` 方法

```php
public function validateAndSanitizeEmail(string $email, bool $throw = true, bool $checkDNS = true, bool $disableAliasEmails = true)
```

**参数说明**：
- `$disableAliasEmails`：是否禁止别名邮箱（硬编码开关，默认开启）

### 使用示例

```php
$tools = new \FOSSBilling\Tools();

// 验证邮箱（默认禁止别名邮箱）
try {
    $validEmail = $tools->validateAndSanitizeEmail('user@gmail.com');
    echo "邮箱有效";
} catch (\FOSSBilling\InformationException $e) {
    echo "邮箱无效: " . $e->getMessage();
}

// 允许别名邮箱验证
$email = $tools->validateAndSanitizeEmail('user+alias@gmail.com', true, true, false);
```

### 添加允许的域名

如需添加更多允许的域名，请编辑 `$allowedDomains` 数组：

```php
$allowedDomains = [
    'gmail.com',
    'qq.com', 
    '163.com',
    // 在此处添加其他允许的域名
    'example.com',
    'your-domain.com'
];
```

### 注意事项

- 此修改会影响所有使用 `validateAndSanitizeEmail` 方法的邮箱验证
- 默认情况下，别名邮箱验证是开启的
- 如需完全禁用别名邮箱检查，请将 `$disableAliasEmails` 参数设置为 `false`
