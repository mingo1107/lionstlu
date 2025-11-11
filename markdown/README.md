# lionstlu.org.tw 系統說明

本專案為 Yii2 Advanced 架構的網站系統，包含前台與後台兩部分。

- 前台：使用者瀏覽文章、會員註冊登入、票選互動、商品結帳與訂單查詢。
- 後台：內容與商品等資料維護、權限與人員管理、上傳管理。

請參考以下文件以快速了解與使用本系統：

- frontend.md：前台功能與控制器一覽
- backend.md：後台功能與控制器一覽
- setup.md：開發環境與部署設定

## 專案結構

- backend/ 後台程式碼與設定
- frontend/ 前台程式碼與設定
- common/ 共用 Model、設定與工具
- vendor/ 第三方套件（由 Composer 管理）

## 重要技術點

- Yii2（Pretty URL, AccessControl, ContentNegotiator）
- 使用者身分：
  - 前台 `common\models\MemberModel`
  - 後台 `common\models\UserModel`
- 權限管理（後台）：`AccessModel`, `AccessRoleModel`, `AccessUserModel`
- 檔案上傳：`backend/controllers/UploadController.php`（支援 S3、產生多版本圖片）

## 導覽

- 前台入口：`frontend/web/index.php`（由 Web Server 指向）
- 後台入口：`backend/web/index.php`

更多細節請見各子文件。
