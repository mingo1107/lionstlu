# 後台（backend）功能與架構

本文件概述後台管理系統之架構、控制器與主要功能。

## 架構
- 框架：Yii2（Advanced）
- 命名空間：`backend\controllers`
- 設定：`backend/config/main.php`
  - Pretty URL 開啟、隱藏 `index.php`
  - 身分系統：`common\\models\\UserModel`
  - `errorAction = site/error`

## 共用基底控制器
- `BackendController`
  - `AccessControl`：
    - 允許匿名：`site/login`, `site/error`, `site/logout`
    - 其餘動作需登入（`@`）
  - 自訂 `AccessUserFilter`（排除 `site/*`, `upload/*`）執行細部權限判斷
  - 非 Debug 且 80 埠：301 導向 HTTPS
  - 自動注入麵包屑、標題、權限選單（由 `AccessModel`/`AccessUserModel`/`AccessRoleModel` 提供）

## 控制器與功能
- `SiteController`
  - `index`：後台首頁
  - `login`：登入（`common\models\LoginForm`）
  - `logout`：登出
- `ArticleController`（文章）
  - `index/create/update/delete`
  - `select`（iframe 選擇廣告項：投票/商品）
  - `xhr-select`（JSON 回傳已選擇項目）
  - `genpicsee`：呼叫 pics.ee 產生短網址並寫回 `picsee_link`
  - 角色 7 僅能管理自己建立的內容
- `ArticleCategoryController`（文章分類）
  - `index/create/update/delete`
- `BannerController`（廣告）與 `SlideController`（首頁投影片）
  - `index/create/update/delete`
  - 以 `BannerModel::TYPE_BANNER` 與 `TYPE_SLIDE` 區分
- `ProductController`（商品）
  - `index/create/update/delete`
  - `standard/standard-create/standard-update/standard-delete`（商品規格）
  - 建立商品時會同時建立一筆上線規格
  - 角色 7 僅能管理自己商品
- `VoteController`（投票活動）
  - `index/create/update/delete`
  - `option`：管理投票選項（首次可新增，之後僅能修改名稱）
  - `record`：依票數排序之統計頁
  - 角色 7 僅能管理自己活動
- `MemberController`（會員）
  - `index`：會員列表（支援狀態、關鍵字搜尋，分頁顯示）
  - `create`：建立新會員
  - `update`：編輯會員資料（含密碼、Email、個人資訊等，具表單驗證）
- `UserController`（後台人員）
  - `index/create/update/delete`
  - 指派 `role_id` 或自訂 `access_list`
- `RoleController`（權限群組）
  - `index/create/update/delete`
  - `access_list` 以 JSON 儲存（勾選可見權限）
- `OrderController`（訂單）
  - `index/create/update/delete`
  - `delete` 支援多筆 id（逗號分隔）
- `CsController`（客服）
  - `index/update/delete`
- `CooperateController`（合作提案）
  - `index`
- `UploadController`（檔案上傳）
  - 全域以 JSON 回應（`ContentNegotiator`）
  - `index`：上傳並產出多版本檔案，提供 S3 URL（預設保留本機）
  - `normal-files`：上傳後推送至 S3 並刪除本機檔案
  - `base64`：輸入 URL 轉 Base64 圖片
  - `delete`：刪除 S3 檔案
  - 上傳路徑：`backend/web/upload/<category>/<version>`；URL/S3 由 `ball\helper\File` 產生

## 權限與選單
- 使用 `AccessModel` 取得可見權限，據此組出後台選單與功能權限
- `AccessRoleModel` 與 `AccessUserModel` 提供角色與個別權限設定

## 視圖與資源
- `backend/views/<controller>/<view>.php`
- `backend/views/layouts` 包含一般版型與登入版型

## 設定與入口
- 入口：`backend/web/index.php`
- 設定：`backend/config/*.php`
- Log：`yii\log\FileTarget` 收集 `error, warning`
