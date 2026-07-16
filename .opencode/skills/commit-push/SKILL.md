---
name: commit-push
description: 整理并提交 MES 三个项目（mes-api / mes-admin / mes-mobile）的代码变更到 Git 仓库
---

## 行为

当用户说"整理代码"、"提交代码"、"推送代码"或类似含义的话时，执行以下操作：

1. **询问用户统一使用的 commit message**

2. **依次对三个仓库执行相同操作：**
   - mes-api: `D:\code\mes\mes-api`
   - mes-admin: `D:\code\mes\mes-admin`
   - mes-mobile: `D:\code\mes\mes-mobile`

   每个仓库执行：
   ```
   git add .
   git commit -m "<统一的消息>"
   git push origin main
   ```

3. **GitHub 推送失败时**，自动切换到 Gitee：
   ```
   git push gitee main
   ```

4. **汇总结果**，告知用户三个仓库的提交状态（成功/失败/跳过）。

## 注意

- 如果某仓库没有变更，跳过并告知用户
- 先执行 git status 确认变更再操作
- 推送失败时不要阻塞其他仓库的推送
