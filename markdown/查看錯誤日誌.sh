#!/bin/bash
# 快速查看錯誤日誌的腳本

echo "=========================================="
echo "查看 Laradock + Yii2 錯誤日誌"
echo "=========================================="
echo ""

# 檢查 laradock 目錄
LARADOCK_DIR="../laradock"
if [ ! -d "$LARADOCK_DIR" ]; then
    echo "⚠️  找不到 laradock 目錄，請確認路徑"
    echo "   預期位置: $LARADOCK_DIR"
    echo ""
    echo "請手動執行以下命令："
    echo "  cd ../laradock"
    echo "  docker-compose exec php-fpm tail -f /var/log/php-fpm/error.log"
    exit 1
fi

echo "1️⃣  查看 PHP-FPM 錯誤日誌（推薦）"
echo "----------------------------------------"
cd "$LARADOCK_DIR"
docker-compose exec php-fpm tail -n 50 /var/log/php-fpm/error.log
echo ""
echo ""

echo "2️⃣  查看 Nginx 錯誤日誌"
echo "----------------------------------------"
docker-compose exec nginx tail -n 50 /var/log/nginx/error.log
echo ""
echo ""

echo "3️⃣  查看 Yii2 應用日誌"
echo "----------------------------------------"
cd - > /dev/null
if [ -f "frontend/runtime/logs/app.log" ]; then
    tail -n 50 frontend/runtime/logs/app.log
else
    echo "⚠️  日誌文件不存在: frontend/runtime/logs/app.log"
fi
echo ""
echo ""

echo "=========================================="
echo "💡 提示："
echo "   - 使用 'tail -f' 可以即時監控日誌"
echo "   - 例如: docker-compose exec php-fpm tail -f /var/log/php-fpm/error.log"
echo "=========================================="

