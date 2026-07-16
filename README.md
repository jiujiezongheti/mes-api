# mes-api

MES 制造执行系统后端 API 服务，基于 Webman 开发。

## 技术栈

| 名称 | 版本 |
|------|------|
| PHP | ^8.1 |
| Webman | ^2.1 |
| Workerman | ^5.0 |
| MySQL | ^8.0 |

## 功能列表

- [ ] 系统管理：用户 / 角色 / 菜单 / 字典 / 日志
- [ ] 生产管理：工单 / 报工 / 进度跟踪
- [ ] 工艺管理：工序 / 工艺路线
- [ ] 质量管理：来料检 / 过程检 / 不合格品处理
- [ ] 设备管理：台账 / 点检 / 保养 / 维修
- [ ] 物料管理：档案 / 库存 / 批次追溯
- [ ] 报表统计：生产 / 质量 / 设备 OEE

## 快速开始

```bash
# 环境要求
# PHP 8.1+，依赖 ext-pdo、ext-mbstring、ext-bcmath

# 安装依赖
composer install

# 复制环境配置
cp .env.example .env

# 修改 .env 中的数据库连接信息

# 启动服务
php windows.php

# 访问地址
http://127.0.0.1:8787
```

## 目录结构

```
app/
├── admin/          # 后台管理接口
│   ├── controller/
│   └── validate/
├── mobile/         # 移动端接口
│   ├── controller/
│   └── validate/
├── api/            # 第三方 OpenAPI
├── common/         # 公共层
│   ├── model/
│   ├── logic/
│   ├── enums/
│   └── exceptions/
├── middleware/     # 中间件
config/             # 配置文件
database/           # 迁移 / 种子
tests/              # 测试
```

## 相关项目

- [mes-admin](https://github.com/xxx/mes-admin) — PC 管理后台
- [mes-mobile](https://github.com/xxx/mes-mobile) — 移动端

## 截图

> TODO

## 开源协议

MIT
