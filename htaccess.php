RewriteEngine On
#RewriteBase /

#RewriteCond %{HTTP_HOST} !^www\. [NC]
#RewriteRule ^(.*) https://www.%{SERVER_NAME}%{REQUEST_URI} [L,R=301]

#RewriteCond %{HTTP_HOST} www.app.customesignature.com
#RewriteRule (.*) https://app.customesignature.com/$1 [R=301,L]

#RewriteCond %{HTTP:X-Forwarded-Proto} =http
#RewriteCond %{HTTPS} !=on
#RewriteRule .* https://%{HTTP:Host}%{REQUEST_URI} [L,R=permanent]

RewriteRule ^([A-Za-z0-9-]+)/?$  index.php?module=$1 [QSA]
RewriteRule ^([a-zA-Z0-9]+)/([A-Z0-9]+)$ index.php?module=$1&id=$2 [L,QSA]
RewriteRule ^([A-Za-z0-9-]+)/([a-zA-Z0-9-]+)$  index.php?module=$1&category_id=$2 [QSA]
RewriteRule ^([A-Za-z0-9-]+)/([0-9-]+)/([0-9]+)$  index.php?module=$1&gallery_id=$2&id=$3 [NC,L,QSA]
RewriteRule ^([A-Za-z0-9-]+)/([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)$  index.php?module=$1&category_id=$2&id=$3 [NC,L,QSA]
RewriteRule ^([A-Za-z0-9-]+)/([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)$  index.php?module=$1&category_id=$2&id=$3&subid=$4 [NC,L,QSA]
RewriteRule ^([A-Za-z0-9-]+)/([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)$  index.php?module=$1&category_id=$2&id=$3&subid=$4&sid=$5 [NC,L,QSA]


<ifModule mod_gzip.c>
  mod_gzip_on Yes
  mod_gzip_dechunk Yes
  mod_gzip_item_include file \.(html?|txt|css|js|php|pl)$
  mod_gzip_item_include handler ^cgi-script$
  mod_gzip_item_include mime ^text/.*
  mod_gzip_item_include mime ^application/x-javascript.*
  mod_gzip_item_exclude mime ^image/.*
  mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*
</ifModule>

<ifModule mod_headers.c>
SetEnvIf Request_URI "^/images/social/.+\.(png|jpg|gif)$" ENABLE_CACHE
Header set Cache-Control "max-age=31536000, public" env=ENABLE_CACHE

SetEnvIf Request_URI "^/images/custome/.+\.(png|jpg|gif)$" ENABLE_CACHE
Header set Cache-Control "max-age=31536000, public" env=ENABLE_CACHE

SetEnvIf Request_URI "^/images/marketplace/.+\.(png|jpg|gif)$" ENABLE_CACHE
Header set Cache-Control "max-age=31536000, public" env=ENABLE_CACHE

SetEnvIf Request_URI "^/images/video/.+\.(mp4|webm|)$" ENABLE_CACHE
Header set Cache-Control "max-age=31536000, public" env=ENABLE_CACHE

</ifModule>

