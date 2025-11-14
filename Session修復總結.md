# Session 登入問題修復總結

## 問題描述
登入後，session 資料存在但 `Yii::$app->user->isGuest` 返回 `true`，導致登入狀態無法維持。

## 根本原因
1. **類別名稱大小寫問題**：PHP 7.2+ 嚴格要求類別名稱大小寫正確，`yii::$app` 應為 `Yii::$app`
2. **authKey 分隔符問題**：`validateAuthKey` 使用 `_` 作為分隔符，但 User Agent 字串中也包含 `_`，導致解析錯誤
3. **User 組件配置不完整**：缺少明確的 session 啟用和 bootstrap 配置

## 修復內容

### 1. 修正類別名稱大小寫（PHP 7.2+ 兼容性）

#### 修改檔案：
- `frontend/views/layouts/main.php`
  - 修正所有 `yii::$app` → `Yii::$app`
  - 修正 `trim(null)` → `trim(... ?? '')` 以避免 PHP 8.0+ 警告

- `frontend/controllers/FrontendController.php`
  - 修正所有 `yii::$app` → `Yii::$app`

#### 影響：
- **正式環境需要**：✅ 是，必須修正（PHP 7.2 嚴格要求類別名稱大小寫）

---

### 2. 修正 authKey 分隔符問題

#### 修改檔案：
- `common/models/MemberModel.php`

#### 修改內容：

**`generateAuthKey()` 方法**（約第 240-245 行）：
```php
// 舊格式：使用 _ 作為分隔符
$authKey = sprintf("%s_%s_%s_%s", time(), $this->getId(), HttpUtil::ip(), $_SERVER['HTTP_USER_AGENT']);

// 新格式：使用 | 作為分隔符
$authKey = sprintf("%s|%s|%s|%s", time(), $this->getId(), HttpUtil::ip(), $_SERVER['HTTP_USER_AGENT']);
```

**`validateAuthKey()` 方法**（約第 273-310 行）：
- 先嘗試使用 `|` 分隔符（新格式）
- 如果失敗，使用向後兼容邏輯處理 `_` 分隔符（舊格式）
- 正確處理 User Agent 字串中包含 `_` 的情況

#### 影響：
- **正式環境需要**：✅ 是，必須修正
- **注意**：已登入用戶需要重新登入，因為舊的 authKey 格式會被正確解析（向後兼容）

---

### 3. User 組件配置優化

#### 修改檔案：
- `frontend/config/main.php`

#### 修改內容：

**Bootstrap 配置**（第 15 行）：
```php
// 舊配置
'bootstrap' => ['log'],

// 新配置
'bootstrap' => ['log', 'user'], // 確保 user 組件在 bootstrap 階段初始化
```

**User 組件配置**（第 21-40 行）：
```php
'user' => [
    'identityClass' => 'common\models\MemberModel',
    'enableAutoLogin' => true,
    'enableSession' => true, // 明確啟用 session（預設為 true，但明確設定更安全）
    'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
    // ... 其他配置
],
```

#### 影響：
- **正式環境需要**：✅ 是，建議修正（確保 session 正確初始化）

---

## 正式環境部署檢查清單

### 必須修正的檔案：
1. ✅ `frontend/views/layouts/main.php` - 類別名稱大小寫
2. ✅ `frontend/controllers/FrontendController.php` - 類別名稱大小寫
3. ✅ `common/models/MemberModel.php` - authKey 分隔符修正
4. ✅ `frontend/config/main.php` - User 組件配置

### 其他可能受影響的檔案（建議檢查）：
- `frontend/views/facebook/main.php`
- `frontend/views/member/login.php`
- `frontend/controllers/MemberController.php`
- `frontend/views/cs/cooperate.php`
- `frontend/views/member/reset-password.php`
- `frontend/views/member/center.php`
- `frontend/views/member/signup.php`
- `frontend/views/member/forget-password.php`
- `frontend/views/checkout/index.php`
- `frontend/controllers/CheckoutController.php`
- `frontend/controllers/CsController.php`
- `frontend/controllers/OrderController.php`
- `frontend/controllers/ArticleController.php`
- `frontend/widget/views/article-vote.php`
- `frontend/widget/views/service.php`
- `common/models/CooperateModel.php`
- `common/models/CustomerServiceModel.php`

### 部署後注意事項：
1. **用戶需要重新登入**：由於 authKey 格式改變，已登入用戶的 session 可能失效，需要重新登入
2. **向後兼容**：舊格式的 authKey 仍可正常解析，不會影響現有用戶
3. **測試建議**：
   - 測試登入功能
   - 測試登入後換頁是否維持登入狀態
   - 測試登出功能
   - 測試「記住我」功能（如果有的話）

---

## 技術細節

### authKey 格式變更
- **舊格式**：`timestamp_userId_ip_userAgent`（使用 `_` 分隔）
- **新格式**：`timestamp|userId|ip|userAgent`（使用 `|` 分隔）
- **向後兼容**：`validateAuthKey()` 會自動處理兩種格式

### Session 初始化流程
1. Yii2 應用啟動時，bootstrap 階段初始化 `user` 組件
2. `user` 組件從 session 讀取 `__id`（用戶 ID）
3. 調用 `findIdentity($id)` 獲取用戶物件
4. 驗證 `authKey`（如果存在）
5. 設置 `identity` 屬性

### 為什麼需要修正類別名稱？
PHP 7.2+ 對類別名稱大小寫更加嚴格。`yii::$app` 會被視為不同的類別，導致無法正確訪問 Yii2 的應用實例。

---

## 驗證方法

部署後，可以訪問 `/session-test/index`（如果存在）檢查：
- `is_guest` 應為 `false`
- `user_id` 應有值
- `identity` 應有資料
- `authKey_validation.validateResult` 應為 `true`

---

## 相關檔案清單

### 核心修改：
- `frontend/config/main.php`
- `common/models/MemberModel.php`
- `frontend/views/layouts/main.php`
- `frontend/controllers/FrontendController.php`

### 測試/除錯檔案（可選）：
- `frontend/controllers/SessionTestController.php`（僅開發環境使用）

---

**最後更新**：2025-01-13
**PHP 版本要求**：7.2+（正式環境使用 PHP 7.2，開發環境使用 PHP 7.4）

