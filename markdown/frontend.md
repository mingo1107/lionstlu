# 前台（frontend）功能與架構

本文件概述前台站台之架構、控制器與主要功能。

## 架構
- 框架：Yii2（Advanced）
- 命名空間：`frontend\controllers`
- 設定：`frontend/config/main.php`
  - Pretty URL 開啟、隱藏 `index.php`
  - 身分系統：`common\\models\\MemberModel`
  - `errorAction = site/error`

## 共用基底控制器
- `FrontendController`
  - `AccessControl`：
    - `signup` 允許匿名
    - `logout` 需登入
  - 非 Debug 且 80 埠：301 導向 HTTPS
  - 將 `qs/title/actionLabel` 注入 view

## 控制器與路由
- `SiteController`
  - `index`：首頁文章區塊（精選/最新/商品關聯）
  - `logout`、`contact`、`about`、`policy`
  - 會員：`signup`、`requestPasswordReset`、`resetPassword`
- `ArticleController`
  - `detail`：文章詳情、瀏覽數+1、SEO/OG 標籤
  - `category`：分類列表
  - `search`：關鍵字/分類/狀態搜尋
  - `xhr-vote`：會員投票（每日一次或一次性，依活動限制）
  - `xhr-share`：分享計數
- `MemberController`
  - 登入與註冊：`login`（整合登入與註冊表單）、`signup`（重定向至 login）
  - 密碼：`forgetPassword`（發送重置信件）、`resetPassword`（以 token 重設密碼）
  - Email 驗證：`verifyEmail`（驗證註冊 Email）
  - 會員中心：`center`（修改個資/密碼，需登入）
  - 客服：`service`（送單，需登入）、`reply`（查看回覆與紀錄，需登入）
  - Facebook：`fb-login`、`fb-login-callback`、`xhr-fb-login`
  - 權限控制：`center`、`service`、`reply` 需登入（`AccessControl`）
- `CheckoutController`
  - `index`：結帳（未登入可於流程中建帳號），建立 `Orders/OrdersDetail/OrdersStatusFlow`、更新庫存（含 rollback）
  - `finish`：完成頁
- `OrderController`（需登入）
  - `index`：歷史訂單列表（含明細）
  - `detail`：訂單詳情（以訂單編號+會員ID限制）
- `CsController`
  - `index`：客服表單送出
  - `cooperate`：合作提案送出

## SEO 與社群標籤
- 文章頁依內容註冊 `og:title/description/image` 與 `meta description/keywords`

## 錯誤處理與安全
- `errorAction = site/error`
- 重要操作（投票、下單）均有參數檢核與狀態驗證
- 會員動作多採 `AccessControl` 限制

## 視圖與資源
- `frontend/views/<controller>/<view>.php`
- 靜態資源於 `frontend/web`（部分由 .gitignore 保護）
