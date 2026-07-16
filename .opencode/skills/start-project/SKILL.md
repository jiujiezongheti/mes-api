---
name: start-project
description: 启动 MES 本地开发环境，在新终端窗口中启动 mes-api 和 mes-admin 服务
---

## 行为

当用户说"打开项目"、"启动项目"、"开始开发"或类似含义的话时，执行以下操作：

1. 打开新终端窗口，启动 mes-admin：
   ```
   Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd D:\code\mes\mes-admin; npm run dev"
   ```

2. 打开新终端窗口，启动 mes-api：
   ```
   Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd D:\code\mes\mes-api; php windows.php"
   ```

3. 告知用户两个服务已启动，并显示对应的访问地址。

## 注意

- 如果其中一个服务启动失败，单独告知用户哪个端口异常
- 不主动关闭已有窗口，只负责新开
