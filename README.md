# FOSSBilling 邮箱验证器

这是一个用于 FOSSBilling 的邮箱验证插件，提供高级邮箱地址验证功能，包括域名白名单验证和别名邮箱检测。

## 功能特性

- **邮箱域名验证**：限制用户只能使用特定的邮箱域名
- **通配符支持**：支持使用通配符（如 `*.example.com`）来匹配域名模式
- **别名邮箱检测**：检测并阻止使用别名邮箱（如 Gmail 的 `user+alias@gmail.com`）
- **配置化**：通过管理面板配置启用/禁用各项功能
- **与现有系统集成**：无缝集成到 FOSSBilling 的客户端注册和认证流程中

## 安装

1. 将整个目录复制到您的 FOSSBilling 安装目录中

## 配置

在```Service.php```中硬编码
### 邮箱域名验证设置

- `email_domain_validation_enabled`：启用/禁用邮箱域名验证
- `email_allowed_domains`：允许的邮箱域名列表（以逗号分隔）

### 别名邮箱验证设置

- `email_alias_validation_enabled`：启用/禁用别名邮箱检测

## 使用示例

### 配置允许的域名

在```Service.php```中硬编码

```
email_domain_validation_enabled: true
email_allowed_domains: 'allow domain'
```

### 禁用别名邮箱

```
email_alias_validation_enabled: true
```

此设置将阻止类似 `user+alias@gmail.com` 这样的邮箱地址注册。

## API 集成

插件自动集成到以下客户端服务方法中：

- `authorizeClient()` - 在用户认证时验证邮箱
- 客户端注册流程
- 邮箱更改流程

## 技术细节

### EmailValidator 类方法

- `validateEmail($email)` - 综合验证邮箱地址
- `validateEmailDomain($email)` - 验证邮箱域名
- `validateEmailAlias($email)` - 验证别名邮箱
- `matchDomainPattern($pattern, $domain)` - 域名模式匹配（支持通配符）

### 错误处理

验证失败时会抛出 `FOSSBilling\InformationException` 异常，包含相应的错误信息。

## 贡献

欢迎提交 Issue 和 Pull Request 来改进这个插件。
