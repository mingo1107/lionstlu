# 後台 Session 登入問題修復總結

## 問題描述
後台登入後會立即跳轉回 `/site/login`，無法維持登入狀態。

## 根本原因
與前台相同的問題：
1. **類別名稱大小寫問題**：PHP 7.2+ 嚴格要求類別名稱大小寫正確，`yii::$app` 應為 `Yii::$app`
2. **authKey 分隔符問題**：`UserModel::validateAuthKey` 使用 `_` 作為分隔符，但 User Agent 字串中也包含 `_`，導致解析錯誤
3. **User 組件配置不完整**：缺少明確的 session 啟用和 bootstrap 配置
4. **登入重定向問題**：使用 `goBack()` 可能導致重定向循環

## 修復內容

### 1. 修正後台 User 組件配置

#### 修改檔案：
- `backend/config/main.php`

#### 修改內容：

**Bootstrap 配置**（第 13 行）：
```php
// 舊配置
'bootstrap' => ['log'],

// 新配置
'bootstrap' => ['log', 'user'], // 確保 user 組件在 bootstrap 階段初始化
```

**User 組件配置**（第 19-23 行）：
```php
'user' => [
    'identityClass' => 'common\models\UserModel',
    'enableAutoLogin' => true,
    'enableSession' => true, // 明確啟用 session（預設為 true，但明確設定更安全）
    'identityCookie' => ['name' => '_identity-ball-backend', 'httpOnly' => true],
    // ... 其他配置
],
```

#### 影響：
- **正式環境需要**：✅ 是，必須修正

---

### 2. 修正 UserModel authKey 分隔符問題

#### 修改檔案：
- `common/models/UserModel.php`

#### 修改內容：

**`generateAuthKey()` 方法**（約第 301-305 行）：
```php
// 舊格式：使用 _ 作為分隔符
$authKey = sprintf("%s_%s_%s_%s", time(), $this->getId(), HttpUtil::ip(), $_SERVER['HTTP_USER_AGENT']);

// 新格式：使用 | 作為分隔符
$authKey = sprintf("%s|%s|%s|%s", time(), $this->getId(), HttpUtil::ip(), $_SERVER['HTTP_USER_AGENT']);
```

**`validateAuthKey()` 方法**（約第 233-270 行）：
- 先嘗試使用 `|` 分隔符（新格式）
- 如果失敗，使用向後兼容邏輯處理 `_` 分隔符（舊格式）
- 正確處理 User Agent 字串中包含 `_` 的情況

#### 影響：
- **正式環境需要**：✅ 是，必須修正
- **注意**：已登入用戶需要重新登入，因為舊的 authKey 格式會被正確解析（向後兼容）

---

### 3. 修正類別名稱大小寫（PHP 7.2+ 兼容性）

#### 修改檔案：
- `backend/controllers/BackendController.php`
- `backend/filter/AccessUserFilter.php`
- `backend/views/site/login.php`
- `backend/views/layouts/main.php`

#### 修改內容：

**BackendController.php**：
- 修正所有 `yii::$app` → `Yii::$app`（共 5 處）

**AccessUserFilter.php**：
- 修正 `yii::$app->user->getIdentity()` → `Yii::$app->user->getIdentity()`

**views/site/login.php**：
- 修正 `yii::$app->request` → `Yii::$app->request`（2 處）

**views/layouts/main.php**：
- 修正 `yii::$app->user->getIdentity()->name` → `Yii::$app->user->getIdentity() ? Yii::$app->user->getIdentity()->name : 'Unknown'`
- 同時添加了 null 檢查，避免未登入時出錯

#### 影響：
- **正式環境需要**：✅ 是，必須修正（PHP 7.2 嚴格要求類別名稱大小寫）

---

### 4. 修正登入重定向邏輯

#### 修改檔案：
- `backend/controllers/SiteController.php`

#### 修改內容：

**`actionLogin()` 方法**（第 46-48 行）：
```php
// 舊邏輯
if ($model->load(Yii::$app->request->post()) && $model->login()) {
    return $this->goBack(); // 可能導致重定向循環
}

// 新邏輯
if ($model->load(Yii::$app->request->post()) && $model->login()) {
    // 登入成功，重定向到首頁（避免刷新時重複提交）
    return $this->goHome();
}
```

#### 影響：
- **正式環境需要**：✅ 是，建議修正（避免重定向循環）

---

## 正式環境部署檢查清單

### 必須修正的檔案：
1. ✅ `backend/config/main.php` - User 組件配置
2. ✅ `common/models/UserModel.php` - authKey 分隔符修正
3. ✅ `backend/controllers/BackendController.php` - 類別名稱大小寫
4. ✅ `backend/filter/AccessUserFilter.php` - 類別名稱大小寫
5. ✅ `backend/views/site/login.php` - 類別名稱大小寫
6. ✅ `backend/views/layouts/main.php` - 類別名稱大小寫
7. ✅ `backend/controllers/SiteController.php` - 登入重定向邏輯

### 其他可能受影響的檔案（建議檢查）：
後台還有許多檔案使用 `yii::$app`，建議批量檢查並修正：
- `backend/views/cs/*.php`
- `backend/views/member/*.php`
- `backend/views/article-category/*.php`
- `backend/views/order/*.php`
- `backend/views/role/*.php`
- `backend/views/product/*.php`
- `backend/views/banner/*.php`
- `backend/views/vote/*.php`
- `backend/views/article/*.php`
- `backend/views/slide/*.php`
- `backend/views/user/*.php`
- `backend/controllers/*.php`

**注意**：這些檔案中的 `yii::$app` 可能不會立即導致錯誤，但建議逐步修正以確保 PHP 7.2+ 兼容性。

---

## 部署後注意事項

1. **用戶需要重新登入**：由於 authKey 格式改變，已登入用戶的 session 可能失效，需要重新登入
2. **向後兼容**：舊格式的 authKey 仍可正常解析，不會影響現有用戶
3. **測試建議**：
   - 測試後台登入功能
   - 測試登入後換頁是否維持登入狀態
   - 測試登出功能
   - 測試權限控制功能（AccessUserFilter）

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

## 相關檔案清單

### 核心修改：
- `backend/config/main.php`
- `common/models/UserModel.php`
- `backend/controllers/BackendController.php`
- `backend/filter/AccessUserFilter.php`
- `backend/views/site/login.php`
- `backend/views/layouts/main.php`
- `backend/controllers/SiteController.php`

---

**最後更新**：2025-01-13
**PHP 版本要求**：7.2+（正式環境使用 PHP 7.2，開發環境使用 PHP 7.4）

