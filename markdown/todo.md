# 會員功能 To-Do 追蹤清單

本文件用於追蹤「會員功能」之實作與驗收。勾選框代表完成狀態。

## 範圍（Scope）
- 前台：登入、登出、忘記密碼（發信）、重設密碼、權限保護頁（學習中心、工具下載、直播、影片回放）。
- 後台：會員資料列表檢視、搜尋、資料更改。

---

## 前置作業（Email/Mailer）
- [x] 設定 SMTP 參數（common/config/params-local.php）
- [x] 驗證 mailer 可發信（測試信）
- [x] 忘記密碼信件樣板與寄件者資訊確認（common/mail/*）

## 前台（Frontend）
- [x] 登入流程驗證與調整
  - 入口：`/member/login`（frontend/controllers/MemberController::actionLogin）
  - [x] 未登入訪客導向登入頁（保留 referrer，登入後導回）
  - [x] 導航列：登入/註冊/登出項目顯示切換
- [x] 登出流程驗證
  - 入口：`/site/logout`（frontend/controllers/SiteController::actionLogout）
- [x] 忘記密碼（發信）
  - 入口：`/member/forget-password`（MemberController::actionForgetPassword）
  - [x] 表單驗證、成功/失敗訊息顯示
- [x] 重設密碼
  - 入口：`/member/reset-password?token=...`（MemberController::actionResetPassword）
  - [x] Token 驗證/過期處理
  - [x] 成功後導向登入
- [x] 權限保護頁（需登入）
  - 頁面：學習中心、工具下載、直播、影片回放
  - [x] 設計 routes 與 views（學習中心、工具下載透過 article_category.is_login 控制）
  - [x] 以 AccessControl 或 beforeAction 驗證登入（ArticleController::actionCategory 與 actionDetail）
  - [x] 未登入導向首頁（選單隱藏保護頁，直接輸入網址導向首頁）

## 後台（Backend）
- [ ] 會員列表檢視與搜尋
  - 入口：`/member/index`（backend/controllers/MemberController::actionIndex）
  - [ ] 關鍵字/狀態搜尋、分頁
- [ ] 會員資料編輯
  - 入口：`/member/update?id=...`（MemberController::actionUpdate）
  - [ ] 欄位驗證（密碼異動、Email 格式等）
  - [ ] 成功/失敗訊息處理

## 測試（Testing）
- [x] 建立測試帳號（一般會員、管理員）
- [ ] 用例驗證
  - [x] 登入/登出
  - [x] 忘記密碼發信與收信
  - [x] 重設密碼成功與 token 異常情境
  - [x] 權限頁面導向與登入後導回
  - [ ] 後台列表搜尋/分頁
  - [ ] 後台編輯資料與驗證訊息

## 文件（Docs）
- [ ] 更新 markdown：
  - [ ] 使用者操作手冊（前台會員流程）
  - [ ] 後台管理手冊（會員列表與編輯）
  - [ ] Email/mailer 設定說明與常見錯誤

---

## 驗收標準（Acceptance Criteria）
- 完整打通：登入、登出、忘記密碼、重設密碼流程（含寄信成功）。
- 未登入訪客點擊保護頁，會導向登入且登入後導回原頁。
- 後台可依條件搜尋會員，並能正確編輯與保存資料。
- 以上操作皆有清楚成功/失敗提示，無顯著錯誤訊息或 500。

## 備註（Notes）
- 若需第三方登入（如 Facebook），可於後續擴充；目前先以站內帳密為主。
- 正式環境請確認 HTTPS 與 mailer 生效，避免信件被歸類為垃圾信件。
