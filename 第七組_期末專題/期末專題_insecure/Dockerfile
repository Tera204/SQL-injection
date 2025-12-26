FROM php:8.0-apache
# 安裝 mysqli 擴充套件，讓我們可以用 PHP 連資料庫
RUN docker-php-ext-install mysqli
# 把 src 資料夾裡的網頁複製到伺服器裡
COPY web/src/ /var/www/html/