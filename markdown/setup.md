# 開發與部署設定（setup）

本文件說明如何在本機與伺服器上安裝、設定與部署本專案。

## 1. 系統需求
- PHP（版本依專案現況；建議使用與現有伺服器相容版本）
- Composer（管理 PHP 依賴）
- Web Server：Nginx 或 Apache
- 資料庫：MySQL/MariaDB（依 common/config/main*.php 連線設定）

## 2. 專案安裝
1) 取得原始碼後，於專案根目錄安裝套件：
```
composer install
```

2) 建立與調整環境設定檔（依需求複製並修改）：
- `common/config/example_main-local.php` → `common/config/main-local.php`
- `common/config/example_params-local.php` → `common/config/params-local.php`
- `common/config/example_test-local.php` → `common/config/test-local.php`

3) 設定資料庫連線與站台參數：
- `common/config/main-local.php`：資料庫連線（dsn/username/password）
- `common/config/params-local.php`：站台參數（如第三方金鑰、Facebook 設定等）

4) 目錄寫入權限（Web Server 執行帳號需可寫入）：
- `backend/runtime`, `backend/web/assets`, `backend/web/upload`
- `frontend/runtime`, `frontend/web/assets`
- 如有其他上傳資料夾，請一併開放寫入

## 3. Web 根目錄設定
- 前台根目錄指向：`frontend/web`
- 後台根目錄指向：`backend/web`
- 啟用漂亮網址（Pretty URL），需設定重寫規則：

Apache（`.htaccess` 範例已存在於 `backend/web/.htaccess`，前台可類推）：
```
<IfModule mod_rewrite.c>
    Options +FollowSymlinks
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . index.php
</IfModule>
```

Nginx（server 區塊）示意：
```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 4. 入口與設定合併
- 前台入口：`frontend/web/index.php`
- 後台入口：`backend/web/index.php`
- 皆會合併載入：
  - `common/config/main.php`, `common/config/main-local.php`
  - `frontend|backend/config/main.php`, `frontend|backend/config/main-local.php`

## 5. 執行方式（本機開發）
- 以內建 PHP Server（僅開發用）為例：
```
php -S 127.0.0.1:8001 -t frontend/web
php -S 127.0.0.1:8002 -t backend/web
```
- 或使用既有 Apache/Nginx 設定對應到上述 web 根目錄。

## 6. 資料庫初始化與遷移
- 本專案以 Model 與自訂查詢為主，請依資料庫 Schema 建置資料表。
- 若有提供 migration 或 SQL 檔，請先匯入（目前請參考 `common/models` 與實際 DB 設定）。

## 7. 重要參數與第三方服務
- 上傳與檔案服務：`backend/controllers/UploadController.php`（可設定 S3）
- Facebook 登入：`frontend/controllers/MemberController.php` 使用 `yii::$app->params["fbAppId"]` 與 `fbSecret`
- 請於 `common/config/params-local.php` 設定上述金鑰與相關參數。

## 8. 安全建議
- 正式環境請關閉 Debug，並由 Web Server 強制 HTTPS（程式內部已有 80→HTTPS 的導向，但伺服器層設定更為可靠）
- 調整 session/cookie domain 與 secure 標記以符合網域策略
- 設定適當檔案/資料夾權限，避免上傳目錄執行 PHP

## 9. 疑難排解
- 若資源 404，請檢查 Web 根目錄是否正確指到 `frontend/web` 或 `backend/web`
- 若 URL 帶出 `index.php`，請檢查重寫（Rewrite）是否啟用
- 若登入/權限異常，請檢查 `common/models/UserModel` 與 `MemberModel` 的資料與 sessions 設定
- 若上傳失敗，請檢查上傳資料夾寫入權限與 `params-local.php` 的雲端金鑰設定
